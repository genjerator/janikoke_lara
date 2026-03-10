package main

import (
	"context"
	"net/http"
	"sync"
	"time"
)

// --- In-memory session store ---
type Session struct {
	UserID    int64
	Email     string
	Name      string
	ExpiresAt time.Time
}

var (
	sessionStore = map[string]*Session{}
	sessionMu    sync.RWMutex
)

type contextKey string

const sessionKey contextKey = "session"

func requireSession(next http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		cookie, err := r.Cookie("session_id")
		if err != nil {
			writeError(w, http.StatusUnauthorized, "not authenticated")
			return
		}
		session := getSession(cookie.Value)
		if session == nil {
			writeError(w, http.StatusUnauthorized, "session expired or invalid")
			return
		}
		ctx := context.WithValue(r.Context(), sessionKey, session)
		next(w, r.WithContext(ctx))
	}
}

func sessionFromContext(ctx context.Context) *Session {
	s, _ := ctx.Value(sessionKey).(*Session)
	return s
}

func createSessionImpl(user *User) (string, error) {
	id, err := randomHex(32)
	if err != nil {
		return "", err
	}
	sessionMu.Lock()
	sessionStore[id] = &Session{
		UserID:    user.ID,
		Email:     user.Email,
		Name:      user.Name,
		ExpiresAt: time.Now().Add(24 * time.Hour),
	}
	sessionMu.Unlock()
	return id, nil
}

func getSession(id string) *Session {
	sessionMu.RLock()
	s := sessionStore[id]
	sessionMu.RUnlock()
	if s == nil || time.Now().After(s.ExpiresAt) {
		deleteSession(id)
		return nil
	}
	return s
}

func deleteSession(id string) {
	sessionMu.Lock()
	delete(sessionStore, id)
	sessionMu.Unlock()
}

func purgeExpiredSessions() {
	sessionMu.Lock()
	for id, s := range sessionStore {
		if time.Now().After(s.ExpiresAt) {
			delete(sessionStore, id)
		}
	}
	sessionMu.Unlock()
}
