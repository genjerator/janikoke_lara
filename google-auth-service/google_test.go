package main

import (
	"context"
	"database/sql"
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"os"
	"testing"
	"time"

	"golang.org/x/oauth2"
	googleapi "google.golang.org/api/oauth2/v2"
)

// MockOAuthConfig replaces the real OAuth config for testing
type MockOAuthConfig struct {
	AuthCodeURLFunc func(state string, opts ...oauth2.AuthCodeOption) string
	ExchangeFunc    func(ctx context.Context, code string, opts ...oauth2.AuthCodeOption) (*oauth2.Token, error)
	ClientFunc      func(ctx context.Context, t *oauth2.Token) *http.Client
}

func (m *MockOAuthConfig) AuthCodeURL(state string, opts ...oauth2.AuthCodeOption) string {
	if m.AuthCodeURLFunc != nil {
		return m.AuthCodeURLFunc(state, opts...)
	}
	return "https://mock-google.com/auth?state=" + state
}

func (m *MockOAuthConfig) Exchange(ctx context.Context, code string, opts ...oauth2.AuthCodeOption) (*oauth2.Token, error) {
	if m.ExchangeFunc != nil {
		return m.ExchangeFunc(ctx, code, opts...)
	}
	return &oauth2.Token{
		AccessToken:  "mock_access_token",
		TokenType:    "Bearer",
		RefreshToken: "mock_refresh_token",
		Expiry:       time.Now().Add(time.Hour),
	}, nil
}

func (m *MockOAuthConfig) Client(ctx context.Context, t *oauth2.Token) *http.Client {
	if m.ClientFunc != nil {
		return m.ClientFunc(ctx, t)
	}
	return http.DefaultClient
}

// MockGoogleUserInfoServer creates a test server that returns mock Google user info
func MockGoogleUserInfoServer() *httptest.Server {
	return httptest.NewServer(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		// Mock the Google OAuth2 userinfo endpoint
		if r.URL.Path == "/oauth2/v2/userinfo" {
			w.Header().Set("Content-Type", "application/json")
			json.NewEncoder(w).Encode(map[string]interface{}{
				"id":      "12345678901234567890",
				"email":   "test@example.com",
				"name":    "Test User",
				"picture": "https://example.com/avatar.jpg",
			})
			return
		}
		http.NotFound(w, r)
	}))
}

// setupTestDB creates an in-memory test database
func setupTestDB(t *testing.T) *sql.DB {
	// For real tests, you'd use a test database or docker container
	// This is a placeholder - you can use testcontainers or a local postgres
	t.Skip("Skipping DB test - requires postgres test instance")
	return nil
}

// TestHandleGoogleCallback_MockedSuccess tests the callback with mocked Google response
func TestHandleGoogleCallback_MockedSuccess(t *testing.T) {
	// Setup mock Google API server
	mockServer := MockGoogleUserInfoServer()
	defer mockServer.Close()

	// Save original config and restore after test
	originalConfig := oauthConfig
	defer func() { oauthConfig = originalConfig }()

	// Create mock OAuth config that returns a mock token
	mockConfig := &oauth2.Config{
		ClientID:     "test-client-id",
		ClientSecret: "test-client-secret",
		RedirectURL:  "http://localhost:8080/auth/google/callback",
		Scopes:       []string{"email", "profile"},
		Endpoint: oauth2.Endpoint{
			AuthURL:  mockServer.URL + "/auth",
			TokenURL: mockServer.URL + "/token",
		},
	}
	oauthConfig = mockConfig

	// Mock token exchange to avoid real token endpoint calls
	originalExchangeFunc := exchangeCodeForToken
	exchangeCodeForToken = func(ctx context.Context, code string) (*oauth2.Token, error) {
		return &oauth2.Token{
			AccessToken:  "mock_access_token",
			TokenType:    "Bearer",
			RefreshToken: "mock_refresh_token",
			Expiry:       time.Now().Add(time.Hour),
		}, nil
	}
	defer func() { exchangeCodeForToken = originalExchangeFunc }()

	// Mock the fetchGoogleUserInfo function for this test
	originalFetchFunc := fetchGoogleUserInfo
	fetchGoogleUserInfo = func(token *oauth2.Token) (*googleapi.Userinfo, error) {
		return &googleapi.Userinfo{
			Id:      "12345678901234567890",
			Email:   "test@example.com",
			Name:    "Test User",
			Picture: "https://example.com/avatar.jpg",
		}, nil
	}
	defer func() { fetchGoogleUserInfo = originalFetchFunc }()

	// Mock the upsertUser function to avoid database dependency
	originalUpsertFunc := upsertUser
	upsertUser = func(g *googleapi.Userinfo) (*User, error) {
		return &User{
			ID:        1,
			GoogleID:  g.Id,
			Email:     g.Email,
			Name:      g.Name,
			Avatar:    g.Picture,
			CreatedAt: time.Now(),
		}, nil
	}
	defer func() { upsertUser = originalUpsertFunc }()

	// Mock session creation
	originalCreateSession := createSession
	createSession = func(user *User) (string, error) {
		return "mock_session_id_123", nil
	}
	defer func() { createSession = originalCreateSession }()

	// Create test request with state cookie and code
	state := "test_state_123"
	req := httptest.NewRequest("GET", "/auth/google/callback?code=test_code&state="+state, nil)
	req.AddCookie(&http.Cookie{
		Name:  "oauth_state",
		Value: state,
	})

	// Create response recorder
	rr := httptest.NewRecorder()

	// Call the handler
	handleGoogleCallback(rr, req)

	// Check status code
	if status := rr.Code; status != http.StatusOK {
		t.Errorf("handler returned wrong status code: got %v want %v", status, http.StatusOK)
		t.Logf("Response body: %s", rr.Body.String())
	}

	// Check response body
	var response map[string]interface{}
	if err := json.NewDecoder(rr.Body).Decode(&response); err != nil {
		t.Fatalf("Failed to decode response: %v", err)
	}

	if response["status"] != "ok" {
		t.Errorf("Expected status 'ok', got '%v'", response["status"])
	}

	user, ok := response["user"].(map[string]interface{})
	if !ok {
		t.Fatal("Expected user object in response")
	}

	if user["email"] != "test@example.com" {
		t.Errorf("Expected email 'test@example.com', got '%v'", user["email"])
	}

	// Check session cookie was set
	cookies := rr.Result().Cookies()
	foundSessionCookie := false
	for _, cookie := range cookies {
		if cookie.Name == "session_id" {
			foundSessionCookie = true
			if cookie.Value != "mock_session_id_123" {
				t.Errorf("Expected session_id 'mock_session_id_123', got '%v'", cookie.Value)
			}
		}
	}
	if !foundSessionCookie {
		t.Error("Expected session_id cookie to be set")
	}
}

// TestHandleGoogleCallback_InvalidState tests CSRF protection
func TestHandleGoogleCallback_InvalidState(t *testing.T) {
	req := httptest.NewRequest("GET", "/auth/google/callback?code=test_code&state=wrong_state", nil)
	req.AddCookie(&http.Cookie{
		Name:  "oauth_state",
		Value: "correct_state",
	})

	rr := httptest.NewRecorder()
	handleGoogleCallback(rr, req)

	if status := rr.Code; status != http.StatusBadRequest {
		t.Errorf("handler returned wrong status code: got %v want %v", status, http.StatusBadRequest)
	}
}

// TestHandleGoogleCallback_MissingCode tests missing authorization code
func TestHandleGoogleCallback_MissingCode(t *testing.T) {
	state := "test_state"
	req := httptest.NewRequest("GET", "/auth/google/callback?state="+state, nil)
	req.AddCookie(&http.Cookie{
		Name:  "oauth_state",
		Value: state,
	})

	rr := httptest.NewRecorder()
	handleGoogleCallback(rr, req)

	if status := rr.Code; status != http.StatusBadRequest {
		t.Errorf("handler returned wrong status code: got %v want %v", status, http.StatusBadRequest)
	}
}

// TestHandleGoogleAuth tests the initial Google auth redirect
func TestHandleGoogleAuth(t *testing.T) {
	// Save original and create mock
	originalConfig := oauthConfig
	defer func() { oauthConfig = originalConfig }()

	oauthConfig = &oauth2.Config{
		ClientID:     "test-client-id",
		ClientSecret: "test-client-secret",
		RedirectURL:  "http://localhost:8080/auth/google/callback",
		Scopes:       []string{"email", "profile"},
		Endpoint: oauth2.Endpoint{
			AuthURL:  "https://accounts.google.com/o/oauth2/auth",
			TokenURL: "https://oauth2.googleapis.com/token",
		},
	}

	req := httptest.NewRequest("GET", "/auth/google", nil)
	rr := httptest.NewRecorder()

	handleGoogleAuth(rr, req)

	// Should redirect
	if status := rr.Code; status != http.StatusTemporaryRedirect {
		t.Errorf("handler returned wrong status code: got %v want %v", status, http.StatusTemporaryRedirect)
	}

	// Check that oauth_state cookie was set
	cookies := rr.Result().Cookies()
	foundStateCookie := false
	for _, cookie := range cookies {
		if cookie.Name == "oauth_state" {
			foundStateCookie = true
			if cookie.Value == "" {
				t.Error("oauth_state cookie should not be empty")
			}
		}
	}
	if !foundStateCookie {
		t.Error("Expected oauth_state cookie to be set")
	}

	// Check redirect location contains Google auth URL
	location := rr.Header().Get("Location")
	if location == "" {
		t.Error("Expected Location header to be set")
	}
}

// Helper function to run tests with environment variables
func TestMain(m *testing.M) {
	// Set test environment variables
	os.Setenv("GOOGLE_CLIENT_ID", "test-client-id")
	os.Setenv("GOOGLE_CLIENT_SECRET", "test-client-secret")
	os.Setenv("GOOGLE_REDIRECT_URL", "http://localhost:8080/auth/google/callback")

	// Run tests
	code := m.Run()

	os.Exit(code)
}
