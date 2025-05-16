# 📊 Database Documentation

This section documents the database schema used in the Habit Tracker app. It covers the structure of each table and their relationships.

---

## 📁 Tables Overview

The application uses the following main tables:

- `users`: Stores user account information.
- `habits`: Contains user-defined habits and related tracking info.
- `progress`: Logs each time a habit is marked as completed.
- `friends`: Manages friend relationships between users.
- `theme_settings`: (Optional) Stores user-specific UI preferences.
- `coin_claims`: Tracks daily coin rewards for users.
- `challenges`: (Optional) Tracks shared habit challenges.

---

## 🧑‍💼 `users` Table

| Column     | Type          | Description                       |
|------------|---------------|-----------------------------------|
| `id`       | `INT`         | Primary key, auto-incremented     |
| `email`    | `VARCHAR(255)`| Unique email address              |
| `password` | `VARCHAR(255)`| Hashed user password              |
| `username` | `VARCHAR(255)`| Display name                      |
| `theme`    | `VARCHAR(50)` | UI theme selection (e.g. light)   |
| `dark_mode`| `TINYINT(1)`  | 1 = dark mode, 0 = light mode     |

---

## 📅 `habits` Table

| Column         | Type           | Description                          |
|----------------|----------------|--------------------------------------|
| `id`           | `INT`          | Primary key, auto-incremented        |
| `user_id`      | `INT`          | Foreign key referencing `users(id)`  |
| `name`         | `VARCHAR(255)` | Name of the habit                    |
| `duration`     | `INT`          | Duration in days                     |
| `progress_count`| `INT`         | How many times completed             |
| `last_updated` | `DATETIME`     | Last time this habit was updated     |

---

## ⏱️ `progress` Table

| Column      | Type        | Description                            |
|-------------|-------------|----------------------------------------|
| `id`        | `INT`       | Primary key, auto-incremented          |
| `habit_id`  | `INT`       | Foreign key referencing `habits(id)`   |
| `timestamp` | `DATETIME`  | When the habit was marked as complete  |

---

## 🧑‍🤝‍🧑 `friends` Table

| Column      | Type          | Description                            |
|-------------|---------------|----------------------------------------|
| `id`        | `INT`         | Primary key, auto-incremented          |
| `user_id`   | `INT`         | The user sending the request           |
| `friend_id` | `INT`         | The friend being added                 |
| `status`    | `VARCHAR(50)` | Request status (e.g. pending, accepted)|

---

## 💰 `coin_claims` Table

| Column     | Type        | Description                          |
|------------|-------------|--------------------------------------|
| `id`       | `INT`       | Primary key, auto-incremented        |
| `user_id`  | `INT`       | Foreign key referencing `users(id)`  |
| `claimed_at`| `DATETIME` | Last date when coins were claimed    |

---

## 🏁 `challenges` Table *(Optional)*

| Column         | Type           | Description                          |
|----------------|----------------|--------------------------------------|
| `id`           | `INT`          | Primary key, auto-incremented        |
| `habit_id_1`   | `INT`          | First participant’s habit ID         |
| `habit_id_2`   | `INT`          | Second participant’s habit ID        |
| `winner_id`    | `INT`          | ID of the user who won the challenge |

---

## 🔗 Relationships

- Each `habit` belongs to a `user`.
- `progress` entries are linked to individual `habits`.
- `friends` table allows users to connect and share progress.
- `coin_claims` and `challenges` enhance gamification.

---

## 🔒 Notes

- All passwords are stored as **hashed values**.
- Ensure `user_id` foreign keys are **ON DELETE CASCADE** if cleanup is needed.
- Indexing `user_id`, `habit_id` and `timestamp` columns will improve performance.


