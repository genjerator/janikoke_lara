package main

import (
	"context"
	"database/sql"
	"encoding/json"
	"fmt"
	"net/http"
	"net/http/httptest"
	"os"
	"strings"
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

	// Handler now redirects to Laravel success page instead of returning JSON
	if status := rr.Code; status != http.StatusTemporaryRedirect {
		t.Errorf("handler returned wrong status code: got %v want %v", status, http.StatusTemporaryRedirect)
		t.Logf("Response body: %s", rr.Body.String())
	}

	location := rr.Header().Get("Location")
	if location == "" {
		t.Error("Expected Location header to be set")
	}
	if !strings.Contains(location, "/auth/google/success") {
		t.Errorf("Expected redirect to /auth/google/success, got %s", location)
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

	// Errors now redirect to the Laravel failure page
	if status := rr.Code; status != http.StatusTemporaryRedirect {
		t.Errorf("handler returned wrong status code: got %v want %v", status, http.StatusTemporaryRedirect)
	}
	if !strings.Contains(rr.Header().Get("Location"), "/auth/google/failed") {
		t.Errorf("Expected redirect to /auth/google/failed, got %s", rr.Header().Get("Location"))
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

	// Errors now redirect to the Laravel failure page
	if status := rr.Code; status != http.StatusTemporaryRedirect {
		t.Errorf("handler returned wrong status code: got %v want %v", status, http.StatusTemporaryRedirect)
	}
	if !strings.Contains(rr.Header().Get("Location"), "/auth/google/failed") {
		t.Errorf("Expected redirect to /auth/google/failed, got %s", rr.Header().Get("Location"))
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

// TestHandleGoogleCallback_WithDatabaseStorage tests that user data is actually stored in the database
func TestHandleGoogleCallback_WithDatabaseStorage(t *testing.T) {
	// Check if database is available via environment variables
	dbHost := os.Getenv("DB_HOST")
	if dbHost == "" {
		t.Skip("Skipping DB test - DB_HOST not set. Set DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE to run this test")
	}

	// Initialize database connection
	dbConn, err := connectDB()
	if err != nil {
		t.Fatalf("Failed to connect to database: %v", err)
	}
	defer dbConn.Close()
	db = dbConn

	// Create users table if it doesn't exist
	createTableSQL := `
		CREATE TABLE IF NOT EXISTS users (
			id SERIAL PRIMARY KEY,
			google_id VARCHAR(255) UNIQUE NOT NULL,
			email VARCHAR(255) NOT NULL,
			name VARCHAR(255),
			avatar VARCHAR(512),
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		);
	`
	if _, err := db.Exec(createTableSQL); err != nil {
		t.Fatalf("Failed to create users table: %v", err)
	}

	// Clean up test data before test
	testGoogleID := "test-user-" + fmt.Sprintf("%d", time.Now().Unix())
	testEmail := "test-" + fmt.Sprintf("%d", time.Now().Unix()) + "@example.com"
	db.Exec("DELETE FROM users WHERE google_id = $1", testGoogleID)

	// Setup mock Google API server
	mockServer := MockGoogleUserInfoServer()
	defer mockServer.Close()

	// Save original config and restore after test
	originalConfig := oauthConfig
	defer func() { oauthConfig = originalConfig }()

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

	// Mock token exchange
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

	// Mock fetchGoogleUserInfo to return our test data
	originalFetchFunc := fetchGoogleUserInfo
	fetchGoogleUserInfo = func(token *oauth2.Token) (*googleapi.Userinfo, error) {
		return &googleapi.Userinfo{
			Id:      testGoogleID,
			Email:   testEmail,
			Name:    "Test User",
			Picture: "https://example.com/avatar.jpg",
		}, nil
	}
	defer func() { fetchGoogleUserInfo = originalFetchFunc }()

	// Don't mock upsertUser - use the real implementation
	// Don't mock createSession - use the real implementation

	// Create test request with state cookie and code
	state := "test_state_" + fmt.Sprintf("%d", time.Now().Unix())
	req := httptest.NewRequest("GET", "/auth/google/callback?code=test_code&state="+state, nil)
	req.AddCookie(&http.Cookie{
		Name:  "oauth_state",
		Value: state,
	})

	// Call the handler
	rr := httptest.NewRecorder()
	handleGoogleCallback(rr, req)

	// Verify response is successful
	if status := rr.Code; status != http.StatusOK {
		t.Errorf("handler returned wrong status code: got %v want %v, body: %s", status, http.StatusOK, rr.Body.String())
		return
	}

	// Verify response contains the user data
	var response map[string]interface{}
	if err := json.NewDecoder(rr.Body).Decode(&response); err != nil {
		t.Fatalf("Failed to decode response: %v", err)
	}

	if response["status"] != "ok" {
		t.Errorf("Expected status 'ok', got '%v'", response["status"])
	}

	// Verify user data in response
	respUser, ok := response["user"].(map[string]interface{})
	if !ok {
		t.Fatal("Expected user object in response")
	}

	respEmail := respUser["email"].(string)
	respName := respUser["name"].(string)
	respGoogleID := respUser["google_id"].(string)

	if respEmail != testEmail {
		t.Errorf("Response email mismatch: got %s, want %s", respEmail, testEmail)
	}
	if respName != "Test User" {
		t.Errorf("Response name mismatch: got %s, want %s", respName, "Test User")
	}
	if respGoogleID != testGoogleID {
		t.Errorf("Response google_id mismatch: got %s, want %s", respGoogleID, testGoogleID)
	}

	// === NOW VERIFY DATA WAS ACTUALLY STORED IN DATABASE ===
	storedUser := &User{}
	err = db.QueryRow(`
		SELECT id, google_id, email, name, avatar, created_at
		FROM users
		WHERE google_id = $1
	`, testGoogleID).Scan(
		&storedUser.ID,
		&storedUser.GoogleID,
		&storedUser.Email,
		&storedUser.Name,
		&storedUser.Avatar,
		&storedUser.CreatedAt,
	)

	if err != nil {
		t.Fatalf("Failed to query user from database: %v", err)
	}

	// Verify all fields match
	if storedUser.GoogleID != testGoogleID {
		t.Errorf("Stored google_id mismatch: got %s, want %s", storedUser.GoogleID, testGoogleID)
	}
	if storedUser.Email != testEmail {
		t.Errorf("Stored email mismatch: got %s, want %s", storedUser.Email, testEmail)
	}
	if storedUser.Name != "Test User" {
		t.Errorf("Stored name mismatch: got %s, want %s", storedUser.Name, "Test User")
	}
	if storedUser.Avatar != "https://example.com/avatar.jpg" {
		t.Errorf("Stored avatar mismatch: got %s, want %s", storedUser.Avatar, "https://example.com/avatar.jpg")
	}

	t.Logf("✓ User successfully stored in database with ID: %d", storedUser.ID)

	// Test second callback with same google_id: must return same user, no new row
	state2 := "test_state_" + fmt.Sprintf("%d", time.Now().Unix())
	req2 := httptest.NewRequest("GET", "/auth/google/callback?code=test_code&state="+state2, nil)
	req2.AddCookie(&http.Cookie{
		Name:  "oauth_state",
		Value: state2,
	})

	rr2 := httptest.NewRecorder()
	handleGoogleCallback(rr2, req2)

	if status := rr2.Code; status != http.StatusOK {
		t.Errorf("Second callback failed with status: %v, body: %s", status, rr2.Body.String())
		return
	}

	var response2 map[string]interface{}
	if err := json.NewDecoder(rr2.Body).Decode(&response2); err != nil {
		t.Fatalf("Failed to decode second response: %v", err)
	}
	respUser2, ok := response2["user"].(map[string]interface{})
	if !ok {
		t.Fatal("Expected user object in second response")
	}

	// Should be the exact same DB row — same ID, email unchanged
	returnedID := int64(respUser2["id"].(float64))
	if returnedID != storedUser.ID {
		t.Errorf("Second callback returned different user ID: got %d, want %d", returnedID, storedUser.ID)
	}

	// Verify row count — must still be exactly 1 row with this google_id
	var count int
	if err := db.QueryRow(`SELECT COUNT(*) FROM users WHERE google_id = $1`, testGoogleID).Scan(&count); err != nil {
		t.Fatalf("Failed to count users: %v", err)
	}
	if count != 1 {
		t.Errorf("Expected exactly 1 user row, got %d", count)
	}

	t.Logf("✓ Second callback returned same user (ID=%d), no duplicate inserted", storedUser.ID)

	// Clean up test data
	db.Exec("DELETE FROM users WHERE google_id = $1", testGoogleID)
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
