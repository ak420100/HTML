
---

## API Overview

### `POST /login.php`
- **Description**: Authenticates user and starts session
- **Input**: JSON `{ email, password }`
- **Output**: `{ success: string, userId: int }` or `{ error: string }`

### `POST /account.php`
- **Description**: Updates user account info
- **Input**: `{ username, email, new_password, confirm_password }`
- **Output**: `{ success: string }` or `{ error: string }`

### `POST /createhab.php`
- **Description**: Creates a new habit
- **Input**: `habName`, `durationNumber`, `durationUnit`
- **Output**: Redirect or error

### `GET /fetch_habits.php`
- **Description**: Retrieves all user habits
- **Output**: Habit data in JSON

(Continue listing the endpoints you have…)

---

## Database Schema (Simplified)

```sql
-- users
id INT PRIMARY KEY AUTO_INCREMENT,
email VARCHAR(255),
password VARCHAR(255),
username VARCHAR(255),
theme VARCHAR(50),
dark_mode TINYINT(1)

-- habits
id INT PRIMARY KEY AUTO_INCREMENT,
user_id INT,
name VARCHAR(255),
duration INT,
progress_count INT,
last_updated DATETIME

-- friends
id INT PRIMARY KEY AUTO_INCREMENT,
user_id INT,
friend_id INT,
status VARCHAR(50)

-- progress
id INT PRIMARY KEY AUTO_INCREMENT,
habit_id INT,
timestamp DATETIME
