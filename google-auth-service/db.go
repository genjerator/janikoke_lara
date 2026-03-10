package main

import (
	"database/sql"
	"fmt"
	"log"
	"os"
	"time"

	_ "github.com/lib/pq"
	googleapi "google.golang.org/api/oauth2/v2"
)

var db *sql.DB

type User struct {
	ID        int64     `json:"id"`
	GoogleID  string    `json:"google_id"`
	Email     string    `json:"email"`
	Name      string    `json:"name"`
	Avatar    string    `json:"avatar"`
	CreatedAt time.Time `json:"created_at"`
}

func connectDB() (*sql.DB, error) {
	host := os.Getenv("DB_HOST")
	port := os.Getenv("DB_PORT")
	name := os.Getenv("DB_DATABASE")
	user := os.Getenv("DB_USERNAME")
	password := os.Getenv("DB_PASSWORD")
	sslmode := os.Getenv("DB_SSLMODE")

	if sslmode == "" {
		sslmode = "disable"
	}
	if port == "" {
		port = "5432"
	}

	dsn := fmt.Sprintf(
		"host=%s port=%s dbname=%s user=%s password=%s sslmode=%s",
		host, port, name, user, password, sslmode,
	)
	fmt.Println(dsn)
	dbConn, err := sql.Open("postgres", dsn)
	if err != nil {
		return nil, err
	}
	if err := dbConn.Ping(); err != nil {
		return nil, err
	}
	return dbConn, nil
}

func upsertUserImpl(g *googleapi.Userinfo) (*User, error) {
	if db == nil {
		return nil, fmt.Errorf("database connection not initialized")
	}

	user := &User{}

	// 1) Already linked via google_id — just log in, no changes
	err := db.QueryRow(`
		SELECT id, google_id, email, name, avatar, created_at
		FROM users
		WHERE google_id = $1
		LIMIT 1`,
		g.Id,
	).Scan(&user.ID, &user.GoogleID, &user.Email, &user.Name, &user.Avatar, &user.CreatedAt)

	if err == nil {
		log.Printf("upsertUserImpl: existing user found by google_id=%s (id=%d)", g.Id, user.ID)
		return user, nil
	}
	if err != sql.ErrNoRows {
		log.Printf("upsertUserImpl select by google_id error - GoogleID: %s - Error: %v", g.Id, err)
		return nil, err
	}

	// 2) Email already registered (e.g. via password) — attach google_id and bump updated_at
	err = db.QueryRow(`
		UPDATE users
		SET updated_at = NOW(),
		    google_id  = CASE WHEN (google_id IS NULL OR google_id = '') THEN $1 ELSE google_id END
		WHERE email = $2
		RETURNING id, google_id, email, name, avatar, created_at`,
		g.Id, g.Email,
	).Scan(&user.ID, &user.GoogleID, &user.Email, &user.Name, &user.Avatar, &user.CreatedAt)

	if err == nil {
		log.Printf("upsertUserImpl: existing user found by email=%s (id=%d), updated_at bumped", g.Email, user.ID)
		return user, nil
	}
	if err != sql.ErrNoRows {
		log.Printf("upsertUserImpl update by email error - Email: %s - Error: %v", g.Email, err)
		return nil, err
	}

	// 3) Brand-new user — insert once
	err = db.QueryRow(`
		INSERT INTO users (google_id, email, name, avatar, password, created_at, updated_at)
		VALUES ($1, $2, $3, $4, '', NOW(), NOW())
		RETURNING id, google_id, email, name, avatar, created_at`,
		g.Id, g.Email, g.Name, g.Picture,
	).Scan(&user.ID, &user.GoogleID, &user.Email, &user.Name, &user.Avatar, &user.CreatedAt)

	if err != nil {
		log.Printf("upsertUserImpl insert error - GoogleID: %s, Email: %s - Error: %v", g.Id, g.Email, err)
		return nil, err
	}

	log.Printf("upsertUserImpl: new user created id=%d email=%s", user.ID, user.Email)
	return user, nil
}
