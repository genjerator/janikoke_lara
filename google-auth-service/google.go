package main

import (
	"context"
	"encoding/json"
	"log"
	"net/http"
	"net/url"
	"os"

	"golang.org/x/oauth2"
	googleapi "google.golang.org/api/oauth2/v2"
	"google.golang.org/api/option"
)

// Function variables for dependency injection / mocking in tests
var (
	fetchGoogleUserInfo  = fetchGoogleUserInfoImpl
	upsertUser           = upsertUserImpl
	createSession        = createSessionImpl
	exchangeCodeForToken = exchangeCodeForTokenImpl
)

func laravelURL(path string) string {
	base := os.Getenv("LARAVEL_APP_URL")
	if base == "" {
		base = "http://localhost"
	}
	return base + path
}

func redirectToFailure(w http.ResponseWriter, r *http.Request, reason string) {
	http.Redirect(w, r, laravelURL("/auth/google/failed?error="+url.QueryEscape(reason)), http.StatusTemporaryRedirect)
}

// GET /auth/google
func handleGoogleAuth(w http.ResponseWriter, r *http.Request) {
	state, err := randomHex(16)
	if err != nil {
		redirectToFailure(w, r, "failed to generate state")
		return
	}

	http.SetCookie(w, &http.Cookie{
		Name:     "oauth_state",
		Value:    state,
		Path:     "/",
		MaxAge:   300,
		HttpOnly: true,
		SameSite: http.SameSiteLaxMode,
	})

	url := oauthConfig.AuthCodeURL(state, oauth2.AccessTypeOffline)
	http.Redirect(w, r, url, http.StatusTemporaryRedirect)
}

// GET /auth/google/callback
func handleGoogleCallback(w http.ResponseWriter, r *http.Request) {
	// Verify CSRF state
	stateCookie, err := r.Cookie("oauth_state")
	if err != nil || stateCookie.Value != r.URL.Query().Get("state") {
		redirectToFailure(w, r, "invalid oauth state")
		return
	}

	http.SetCookie(w, &http.Cookie{Name: "oauth_state", MaxAge: -1, Path: "/"})

	code := r.URL.Query().Get("code")
	if code == "" {
		redirectToFailure(w, r, "missing code")
		return
	}

	token, err := exchangeCodeForToken(context.Background(), code)
	if err != nil {
		redirectToFailure(w, r, "failed to exchange token")
		return
	}

	googleUser, err := fetchGoogleUserInfo(token)
	if err != nil {
		redirectToFailure(w, r, "failed to fetch user info")
		return
	}

	user, err := upsertUser(googleUser)
	if err != nil {
		log.Printf("ERROR: upsertUser failed: %v", err)
		redirectToFailure(w, r, "database error")
		return
	}

	sessionID, err := createSession(user)
	if err != nil {
		log.Printf("ERROR: createSession failed: %v", err)
		redirectToFailure(w, r, "failed to create session")
		return
	}

	// Set session cookie
	http.SetCookie(w, &http.Cookie{
		Name:     "session_id",
		Value:    sessionID,
		Path:     "/",
		MaxAge:   86400,
		HttpOnly: true,
		SameSite: http.SameSiteLaxMode,
	})

	// Redirect to Laravel success page
	http.Redirect(w, r, laravelURL("/auth/google/success"), http.StatusTemporaryRedirect)
}

// POST /auth/logout
// Destroys the session server-side and clears the cookie.
func handleLogout(w http.ResponseWriter, r *http.Request) {
	cookie, err := r.Cookie("session_id")
	if err == nil {
		deleteSession(cookie.Value)
	}
	http.SetCookie(w, &http.Cookie{Name: "session_id", MaxAge: -1, Path: "/"})
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]string{"status": "logged_out"})
}

// GET /me — example protected endpoint
func handleMe(w http.ResponseWriter, r *http.Request) {
	session := sessionFromContext(r.Context())
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]any{
		"user_id": session.UserID,
		"email":   session.Email,
		"name":    session.Name,
	})
}

// --- Google API ---

func fetchGoogleUserInfoImpl(token *oauth2.Token) (*googleapi.Userinfo, error) {
	httpClient := oauthConfig.Client(context.Background(), token)
	svc, err := googleapi.NewService(context.Background(), option.WithHTTPClient(httpClient))
	if err != nil {
		return nil, err
	}
	return svc.Userinfo.Get().Do()
}

func exchangeCodeForTokenImpl(ctx context.Context, code string) (*oauth2.Token, error) {
	return oauthConfig.Exchange(ctx, code)
}
