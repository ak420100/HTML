# Back-End Documentation for Trabit

## Overview
The back-end of Trabit is built using PHP and handles user authentication, data management, and interactions with the database. It provides a RESTful API for the front-end to communicate with.

## Key Components

### `account.php`
- **Purpose**: Updates user account information including username, email, and password.
- **Key Features**:
  - Validates user input and checks if the user is logged in.
  - Hashes passwords before updating them in the database.
  - Optionally updates user habits if habit data is provided.

### `AccountTest.php`
- **Purpose**: Unit tests for the `account.php` functionality.
- **Key Features**:
  - Tests for password mismatch errors.
  - Tests for failed account updates.
  - Tests for successful account updates.
  - Uses mocks to simulate database interactions.

### `claim_coins.php`
- **Purpose**: Allows users to claim daily coins.
- **Key Features**:
  - Checks if the user is logged in.
  - Prevents multiple claims within 24 hours.
  - Updates the user’s coin balance in the database.

### `conn.php`
- **Purpose**: Establishes a database connection.
- **Key Features**:
  - Contains the database credentials and initializes the connection.
  - Ensures the connection is available globally.

### `createhab.php`
- **Purpose**: Handles the creation of new habits.
- **Key Features**:
  - Checks if the user is logged in and validates input data.
  - Inserts new habits into the database.

### `fetch_habits.php`
- **Purpose**: Fetches the user's habits from the database.
- **Key Features**:
  - Checks if the user is logged in.
  - Retrieves habits and their progress status.
  - Returns the data as a JSON response.

### `friendlist.php`
- **Purpose**: Manages the addition of friends.
- **Key Features**:
  - Checks if the user is logged in.
  - Validates the input for adding friends.
  - Inserts friends into the database if they exist.

### `get_challenge_data.php`
- **Purpose**: Retrieves challenge data for the user and a friend.
- **Key Features**:
  - Checks if the user is logged in.
  - Fetches and compares the user's progress with a friend's habit.
  - Returns progress data as a JSON response.

### `get_progress.php`
- **Purpose**: Fetches the user's habit progress.
- **Key Features**:
  - Checks if the user is logged in.
  - Retrieves the user's habits and their progress.
  - Returns the progress data as JSON.

### `get_theme.php`
- **Purpose**: Retrieves the user's selected theme.
- **Key Features**:
  - Checks if the user is logged in.
  - Fetches the current theme from the database.
  - Returns the theme preference as a JSON response.

### `increment_progress.php`
- **Purpose**: Updates the progress of a specific habit.
- **Key Features**:
  - Checks if the user is logged in.
  - Validates the input for the habit name.
  - Updates the progress only if 24 hours have passed since the last update.

### `index.php`
- **Purpose**: Retrieves the user's habits for the dashboard.
- **Key Features**:
  - Checks if the user is logged in.
  - Fetches habits and their details from the database.
  - Returns the data as JSON.

### `load_user_settings.php`
- **Purpose**: Loads user settings related to dark mode and notifications.
- **Key Features**:
  - Checks if the user is logged in.
  - Fetches user settings from the database and returns them as JSON.

### `loadfriends.php`
- **Purpose**: Loads the user's friends list.
- **Key Features**:
  - Checks if the user is logged in.
  - Retrieves the friends list from the database and returns it as JSON.

### `login.php`
- **Purpose**: Handles user login.
- **Key Features**:
  - Validates input and checks for existing users.
  - Establishes a user session upon successful login.
  - Returns success or error messages as JSON.

### `logout.php`
- **Purpose**: Logs the user out by destroying the session.
- **Key Features**:
  - Destroys the session and redirects to the login page.

### `rewards.php`
- **Purpose**: Manages user rewards and theme purchases.
- **Key Features**:
  - Checks if the user is logged in.
  - Handles both GET and POST requests for fetching and updating rewards and themes.

### `settings.php`
- **Purpose**: Manages user settings, including dark mode and notifications.
- **Key Features**:
  - Allows fetching and saving user settings.
  - Checks if the user is logged in before processing requests.

### `signup.php`
- **Purpose**: Handles user registration.
- **Key Features**:
  - Validates input and checks for existing emails.
  - Hashes the password and stores user information in the database.
  - Returns success or error messages as JSON.

### `upload_profile_picture.php`
- **Purpose**: Handles the upload of user profile pictures.
- **Key Features**:
  - Checks if the user is logged in.
  - Validates file uploads and updates the database with the new profile picture path.

### `view_friend_habits.php`
- **Purpose**: Retrieves a friend's habits for viewing.
- **Key Features**:
  - Checks if the user is logged in.
  - Validates the friend ID and fetches their habits if they are friends.

## Project Structure
- `account.php`: Updates user account information.
- `AccountTest.php`: Unit tests for account functionality.
- `claim_coins.php`: Allows users to claim daily coins.
- `conn.php`: Establishes database connection.
- `createhab.php`: Handles habit creation.
- `fetch_habits.php`: Fetches user habits.
- `friendlist.php`: Manages the friends list.
- `get_challenge_data.php`: Retrieves challenge data.
- `get_progress.php`: Fetches user progress.
- `get_theme.php`: Retrieves user theme.
- `increment_progress.php`: Updates habit progress.
- `index.php`: Retrieves user habits for the dashboard.
- `load_user_settings.php`: Loads user settings.
- `loadfriends.php`: Loads friends list.
- `login.php`: Handles user login.
- `logout.php`: Manages user logout.
- `rewards.php`: Manages rewards and theme purchases.
- `settings.php`: Manages user settings.
- `signup.php`: Handles user registration.
- `upload_profile_picture.php`: Handles profile picture uploads.
- `view_friend_habits.php`: Retrieves friend's habits.

## Additional Notes
- Ensure proper validation and error handling in all API endpoints.
- Use prepared statements to prevent SQL injection.
- Maintain consistency in response formats (JSON).
- Implement logging for error tracking and debugging.