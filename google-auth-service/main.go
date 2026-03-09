package main

import (
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"os"
	"time"

	"github.com/joho/godotenv"
	"golang.org/x/oauth2"
	"golang.org/x/oauth2/google"
)

var oauthConfig *oauth2.Config

func main() {
	if err := godotenv.Load("../.env"); err != nil {
		log.Println("No ../.env file found, falling back to environment variables")
	}

	// Configure Google OAuth2
	oauthConfig = &oauth2.Config{
		ClientID:     os.Getenv("GOOGLE_CLIENT_ID"),
		ClientSecret: os.Getenv("GOOGLE_CLIENT_SECRET"),
		RedirectURL:  os.Getenv("GOOGLE_REDIRECT_URL"), // e.g. http://localhost:8080/auth/google/callback
		Scopes: []string{
			"https://www.googleapis.com/auth/userinfo.email",
			"https://www.googleapis.com/auth/userinfo.profile",
		},
		Endpoint: google.Endpoint,
	}

	var err error
	db, err = connectDB()
	if err != nil {
		log.Printf("Warning: could not connect to DB: %v", err)
	} else {
		log.Println("Connected to PostgreSQL!")
	}

	// Background goroutine to clean up expired sessions
	go func() {
		for range time.Tick(10 * time.Minute) {
			purgeExpiredSessions()
		}
	}()

	rateLimiter := newRateLimiterFromEnv()
	rateLimiter.startCleanup(5 * time.Minute)

	port := os.Getenv("PORT")
	if port == "" {
		port = "8080"
	}

	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(map[string]string{"message": "Hello from Go!", "status": "ok"})
	})

	http.HandleFunc("/health", func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(map[string]string{"status": "healthy"})
	})

	http.HandleFunc("/db-check", func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		db, err = connectDB()
		fmt.Println("db")
		if db == nil {
			w.WriteHeader(http.StatusServiceUnavailable)
			json.NewEncoder(w).Encode(map[string]string{"status": "error", "error": "database not initialized"})
			return
		}
		if err := db.Ping(); err != nil {
			w.WriteHeader(http.StatusServiceUnavailable)
			json.NewEncoder(w).Encode(map[string]string{"status": "error", "error": err.Error()})
			return
		}
		json.NewEncoder(w).Encode(map[string]string{"status": "ok", "database": "connected"})
	})

	// Step 1: Redirect user to Google's consent screen
	http.HandleFunc("/auth/google", rateLimiter.middleware(handleGoogleAuth))

	// Step 2: Google redirects back here with a code
	http.HandleFunc("/auth/google/callback", rateLimiter.middleware(handleGoogleCallback))

	// Logout: destroy the session
	http.HandleFunc("/auth/logout", rateLimiter.middleware(handleLogout))

	// Example protected route
	http.HandleFunc("/me", rateLimiter.middleware(requireSession(handleMe)))

	fmt.Printf("Server running on port %s\n", port)
	log.Fatal(http.ListenAndServe(":"+port, nil))
}
