# Session-Login-Signup

A user authentication system with registration, login, and profile management built with PHP, jQuery, and multiple databases.

## Tech Stack

- **Frontend**: HTML, CSS, Bootstrap 5, jQuery
- **Backend**: PHP
- **Databases**: MySQL, MongoDB, Redis

## Architecture

| Component | Database | Purpose |
|-----------|----------|---------|
| User Registration & Login | MySQL | Stores credentials (username, email, hashed password) |
| User Profiles | MongoDB | Stores profile details (age, dob, contact, address, bio) |
| Session Management | Redis | Stores session tokens with 24-hour TTL |

## Project Structure

```
├── index.html              # Signup page
├── login.html              # Login page
├── profile.html            # Profile page
├── css/
│   └── style.css           # Custom styles
├── js/
│   ├── signup.js           # Signup form logic
│   ├── login.js            # Login form logic
│   └── profile.js          # Profile load & update logic
└── php/
    ├── signup.php          # Register user (MySQL)
    ├── login.php           # Authenticate user via email (MySQL + Redis)
    ├── get_profile.php     # Fetch profile (Redis + MongoDB)
    ├── update_profile.php  # Update profile (Redis + MongoDB)
    ├── session_login.php   # Validate session token (Redis)
    ├── session_logout.php  # Delete session token (Redis)
    └── config/
        ├── database.php    # MySQLi connection (auto-creates DB & table)
        ├── mongodb.php     # MongoDB native driver connection
        └── redis.php       # Redis native extension connection
```

## Prerequisites

- PHP 8.3+ with the following extensions enabled:
  - `mysqli`
  - `mongodb`
  - `redis`
- MySQL Server
- MongoDB Server
- Redis Server
- Apache (with `mod_php` enabled)

## Setup

1. **Clone the repository**
   ```bash
   git clone <repo-url>
   ```

2. **No manual database setup needed** — the application automatically creates the MySQL database (`guvitask`) and `users` table on first connection.

3. **Update database credentials** in `php/config/database.php` if needed (defaults to `root`).

4. **Ensure Redis is running** on `127.0.0.1:6379`.

5. **Ensure MongoDB is running** on `localhost:27017`.

6. **Point Apache document root** to the project folder and access via browser.

## API Endpoints

| Endpoint | Method | Auth Required | Description |
|----------|--------|---------------|-------------|
| `php/signup.php` | POST | No | Register a new user |
| `php/login.php` | POST | No | Login via email and receive session token |
| `php/session_login.php` | POST | Yes | Validate session token |
| `php/session_logout.php` | POST | Yes | Invalidate session token |
| `php/get_profile.php` | POST | Yes | Fetch user profile from MongoDB |
| `php/update_profile.php` | POST | Yes | Create or update user profile in MongoDB |

## Session Flow

1. User logs in via email + password using jQuery AJAX
2. PHP verifies credentials against MySQL (mysqli with prepared statements)
3. On success, PHP generates a random token and stores it in Redis with user data (24h expiry)
4. Token is saved in browser `localStorage` via JavaScript
5. Profile page validates the token against Redis before loading
6. Every subsequent request sends the token in the request body
7. Backend validates the token against Redis before processing
8. Logout deletes the token from both Redis and `localStorage`

## Security

- MySQL queries use prepared statements (mysqli) — no raw SQL
- Passwords are hashed with `password_hash()` (bcrypt)
- Login is email-based — no username exposure
- Session tokens are random 32-byte hex strings stored in Redis with TTL
- No PHP sessions used — session state managed via Redis + localStorage
