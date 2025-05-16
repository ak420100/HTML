# Backend Architecture

## Overview

The backend of this project is built using **PHP** and connects to a **MySQL** database. It handles user authentication, habit management, settings, friend interactions, and data retrieval for visualizations.

---

## Folder Structure

php/
├── account.php # Handles user account updates
├── claim_coins.php # Handles daily coin claims
├── conn.php # Database connection logic
├── createhab.php # Creates new habits
├── darkmode.php # Updates dark mode settings
├── fetch_habits.php # Retrieves habit data
├── friendlist.php # Manages friends list
├── get_challenge_data.php # Fetches challenge progress
├── get_progress.php # Returns progress for a habit
├── get_theme.php # Retrieves theme preference
├── increment_progress.php # Increments a habit's progress
├── login.php # Authenticates user credentials
├── logout.php # Ends user session
├── settings.php # Updates settings (e.g. username, theme)


---

## Technologies Used

- **PHP 8+**
- **MySQL** (via `mysqli`)
- **Sessions** for user login state
- **JSON** for input/output communication
- **AJAX** on the frontend to send/receive backend data

---

## Key Functionalities

### 🔐 Authentication
- `login.php` verifies email & password, starts session.
- `logout.php` destroys session and redirects to login.

### 🧠 Habit Management
- `createhab.php`: Inserts new habits into the database.
- `fetch_habits.php`: Returns all user habits as JSON.
- `increment_progress.php`: Increases progress count for a habit.

### 🎨 User Settings
- `darkmode.php`: Saves the user's dark mode preference.
- `get_theme.php`: Returns the current color theme.
- `settings.php`: Updates username and theme.

### 👫 Friends & Challenges
- `friendlist.php`: Manages friend relationships.
- `get_challenge_data.php`: Retrieves challenge progress between two users.

### 💰 Daily Rewards
- `claim_coins.php`: Ensures users can only claim coins once per day.

---

## Security Considerations

- **Prepared Statements** used to prevent SQL injection.
- **Sessions** secured by regenerating IDs after login.
- **Input Validation** is enforced both client-side and server-side.

---

## Future Improvements

- Token-based API access
- Role-based access control
- Improved error logging



