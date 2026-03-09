package main

import (
	"context"
	"encoding/json"
	"net/http"

	"golang.org/x/oauth2"
	googleapi "google.golang.org/api/oauth2/v2"
	"google.golang.org/api/option"
)

// GET /auth/google
// Redirects the user to Google's OAuth consent screen.
func handleGoogleAuth(w http.ResponseWriter, r *http.Request) {
	// Use a random state token to prevent CSRF
	state, err := randomHex(16)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "failed to generate state")
		return
	}

	// Store state in a short-lived cookie for verification in the callback
	http.SetCookie(w, &http.Cookie{
		Name:     "oauth_state",
		Value:    state,
		Path:     "/",
		MaxAge:   300, // 5 minutes
		HttpOnly: true,
		SameSite: http.SameSiteLaxMode,
	})

	url := oauthConfig.AuthCodeURL(state, oauth2.AccessTypeOffline)
	http.Redirect(w, r, url, http.StatusTemporaryRedirect)
}

// GET /auth/google/callback
// Handles the redirect from Google. Exchanges the code for tokens,
// fetches the user profile, upserts the user, and creates a session.
func handleGoogleCallback(w http.ResponseWriter, r *http.Request) {
	// Verify CSRF state
	stateCookie, err := r.Cookie("oauth_state")
	if err != nil || stateCookie.Value != r.URL.Query().Get("state") {
		writeError(w, http.StatusBadRequest, "invalid oauth state")
		return
	}
	// Clear the state cookie
	http.SetCookie(w, &http.Cookie{Name: "oauth_state", MaxAge: -1, Path: "/"})

	code := r.URL.Query().Get("code")
	if code == "" {
		writeError(w, http.StatusBadRequest, "missing code")
		return
	}

	// Exchange authorization code for OAuth2 tokens
	token, err := oauthConfig.Exchange(context.Background(), code)
	if err != nil {
		writeError(w, http.StatusUnauthorized, "failed to exchange token: "+err.Error())
		return
	}

	// Use the token to fetch the Google user profile
	googleUser, err := fetchGoogleUserInfo(token)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "failed to fetch user info: "+err.Error())
		return
	}

	// Upsert: find existing user or create a new one (auto-register)
	user, err := upsertUser(googleUser)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "database error")
		return
	}

	// Create server-side session
	sessionID, err := createSession(user)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "failed to create session")
		return
	}

	// Set session cookie
	http.SetCookie(w, &http.Cookie{
		Name:     "session_id",
		Value:    sessionID,
		Path:     "/",
		MaxAge:   86400, // 24 hours
		HttpOnly: true,
		SameSite: http.SameSiteLaxMode,
		// Secure: true, // Uncomment in production (HTTPS only)
	})

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]any{
		"status": "ok",
		"user":   user,
	})
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

func fetchGoogleUserInfo(token *oauth2.Token) (*googleapi.Userinfo, error) {
	httpClient := oauthConfig.Client(context.Background(), token)
	svc, err := googleapi.NewService(context.Background(), option.WithHTTPClient(httpClient))
	if err != nil {
		return nil, err
	}
	return svc.Userinfo.Get().Do()
}
