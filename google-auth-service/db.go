package main

import (
	"database/sql"
	"fmt"
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

func upsertUser(g *googleapi.Userinfo) (*User, error) {
	user := &User{}
	err := db.QueryRow(`
		INSERT INTO users (google_id, email, name, avatar, created_at)
		VALUES ($1, $2, $3, $4, NOW())
		ON CONFLICT (google_id) DO UPDATE
			SET email  = EXCLUDED.email,
			    name   = EXCLUDED.name,
			    avatar = EXCLUDED.avatar
		RETURNING id, google_id, email, name, avatar, created_at`,
		g.Id, g.Email, g.Name, g.Picture,
	).Scan(&user.ID, &user.GoogleID, &user.Email, &user.Name, &user.Avatar, &user.CreatedAt)
	return user, err
}
