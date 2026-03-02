package main

import (
    "database/sql"
    "encoding/json"
    "fmt"
    "log"
    "net/http"
    "os"

    "github.com/joho/godotenv"
    _ "github.com/lib/pq"
)

var db *sql.DB

func main() {
    // Load Laravel's .env from one folder up
    if err := godotenv.Load("../.env"); err != nil {
        log.Println("No ../.env file found, falling back to environment variables")
    }

    // Connect to DB
    var err error
    db, err = connectDB()
    if err != nil {
        log.Printf("Warning: could not connect to DB: %v", err)
    } else {
        log.Println("Connected to PostgreSQL!")
    }

    port := os.Getenv("PORT")
    if port == "" {
        port = "8080"
    }

    http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
        w.Header().Set("Content-Type", "application/json")
        json.NewEncoder(w).Encode(map[string]string{
            "message": "Hello from Go!",
            "status":  "ok",
        })
    })

    http.HandleFunc("/health", func(w http.ResponseWriter, r *http.Request) {
        w.Header().Set("Content-Type", "application/json")
        json.NewEncoder(w).Encode(map[string]string{
            "status": "healthy",
        })
    })

    http.HandleFunc("/db-check", func(w http.ResponseWriter, r *http.Request) {
        w.Header().Set("Content-Type", "application/json")

        if db == nil {
            w.WriteHeader(http.StatusServiceUnavailable)
            json.NewEncoder(w).Encode(map[string]string{
                "status": "error",
                "error":  "database not initialized",
            })
            return
        }

        if err := db.Ping(); err != nil {
            w.WriteHeader(http.StatusServiceUnavailable)
            json.NewEncoder(w).Encode(map[string]string{
                "status": "error",
                "error":  err.Error(),
            })
            return
        }

        json.NewEncoder(w).Encode(map[string]string{
            "status":   "ok",
            "database": "connected",
        })
    })

    fmt.Printf("Server running on port %s\n", port)
    log.Fatal(http.ListenAndServe(":"+port, nil))
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

    db, err := sql.Open("postgres", dsn)
    if err != nil {
        return nil, err
    }

    if err := db.Ping(); err != nil {
        return nil, err
    }

    return db, nil
}
