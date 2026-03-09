package main

import (
	"net/http"
	"os"
	"strconv"
	"sync"
	"time"

	"golang.org/x/time/rate"
)

type visitor struct {
	limiter  *rate.Limiter
	lastSeen time.Time
}

type rateLimiter struct {
	mu       sync.Mutex
	limit    rate.Limit
	burst    int
	ttl      time.Duration
	visitors map[string]*visitor
}

func newRateLimiterFromEnv() *rateLimiter {
	rps := envFloat("RATE_LIMIT_RPS", 5)
	burst := envInt("RATE_LIMIT_BURST", 10)
	ttl := envDuration("RATE_LIMIT_TTL", 15*time.Minute)

	return &rateLimiter{
		limit:    rate.Limit(rps),
		burst:    burst,
		ttl:      ttl,
		visitors: make(map[string]*visitor),
	}
}

func (rl *rateLimiter) middleware(next http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		ip := clientIP(r)
		if ip == "" {
			ip = "unknown"
		}

		limiter := rl.getLimiter(ip)
		if !limiter.Allow() {
			writeError(w, http.StatusTooManyRequests, "rate limit exceeded")
			return
		}

		next(w, r)
	}
}

func (rl *rateLimiter) getLimiter(ip string) *rate.Limiter {
	rl.mu.Lock()
	defer rl.mu.Unlock()

	if v, exists := rl.visitors[ip]; exists {
		v.lastSeen = time.Now()
		return v.limiter
	}

	limiter := rate.NewLimiter(rl.limit, rl.burst)
	rl.visitors[ip] = &visitor{limiter: limiter, lastSeen: time.Now()}
	return limiter
}

func (rl *rateLimiter) startCleanup(interval time.Duration) {
	go func() {
		ticker := time.NewTicker(interval)
		defer ticker.Stop()
		for range ticker.C {
			rl.cleanup()
		}
	}()
}

func (rl *rateLimiter) cleanup() {
	cutoff := time.Now().Add(-rl.ttl)

	rl.mu.Lock()
	defer rl.mu.Unlock()
	for ip, v := range rl.visitors {
		if v.lastSeen.Before(cutoff) {
			delete(rl.visitors, ip)
		}
	}
}

func envFloat(key string, fallback float64) float64 {
	value := os.Getenv(key)
	if value == "" {
		return fallback
	}
	parsed, err := strconv.ParseFloat(value, 64)
	if err != nil {
		return fallback
	}
	return parsed
}

func envInt(key string, fallback int) int {
	value := os.Getenv(key)
	if value == "" {
		return fallback
	}
	parsed, err := strconv.Atoi(value)
	if err != nil {
		return fallback
	}
	return parsed
}

func envDuration(key string, fallback time.Duration) time.Duration {
	value := os.Getenv(key)
	if value == "" {
		return fallback
	}
	parsed, err := time.ParseDuration(value)
	if err != nil {
		return fallback
	}
	return parsed
}
