# Front-End Documentation for Trabit

## Overview
The front-end of Trabit is designed to provide a responsive and user-friendly interface for tracking habits, managing friends, and customizing themes. It utilizes HTML, CSS, and JavaScript to deliver an engaging user experience.

## Key Components

### `index1.php`
- **Purpose**: Main dashboard page where users can view their habits.
- **Features**:
  - Checks user login status and redirects to the login page if not authenticated.
  - Displays a welcome notification.
  - Fetches and displays the user's habits dynamically.
  - Navigation buttons for different sections of the app.

### `createhab1.php`
- **Purpose**: Allows users to create new habits.
- **Features**:
  - Checks if the user is logged in; displays a message if not.
  - Form for entering the habit name and duration.
  - Uses a dropdown for selecting the duration unit (days, months, years).

### `account1.php`
- **Purpose**: Displays user account information and allows updates.
- **Features**:
  - Checks user login status and redirects to the login page if not authenticated.
  - Displays user details such as username and email.
  - Form to update user information including username, email, and password.

### `challenge1.php`
- **Purpose**: Displays habit challenges between the user and their friends.
- **Features**:
  - Checks user login status and redirects to the login page if not authenticated.
  - Displays progress bars for user and friend habits.
  - Fetches challenge data and updates progress dynamically.

### `friendlist1.php`
- **Purpose**: Manages the user's friends list.
- **Features**:
  - Checks user login status and redirects to the login page if not authenticated.
  - Displays a form to add friends by name and email.
  - Fetches and displays the list of friends with an option to view their habits.

### `friendprogress1.php`
- **Purpose**: Displays the progress of a specific friend's habits.
- **Features**:
  - Checks user login status and redirects to the login page if not authenticated.
  - Fetches and displays the friend's habit progress visually.

### `profile1.php`
- **Purpose**: Displays user profile information.
- **Features**:
  - Checks user login status and redirects to the login page if not authenticated.
  - Placeholder for user profile details and options to update them.

### `progress1.php`
- **Purpose**: Displays the user's habit progress visually.
- **Features**:
  - Checks user login status and redirects to the login page if not authenticated.
  - Fetches and displays the user's habits with progress bars.
  - Notifications for habit updates and progress changes.

### `rewards1.php`
- **Purpose**: Allows users to claim rewards and select themes.
- **Features**:
  - Checks user login status and redirects to the login page if not authenticated.
  - Displays the user's coin balance and options to claim daily coins.
  - Button options for purchasing themes with coins.

### `settings1.php`
- **Purpose**: Allows users to manage their settings.
- **Features**:
  - Checks user login status and redirects to the login page if not authenticated.
  - Toggles for enabling dark mode and notifications.
  - Saves user settings and updates the interface accordingly.

## Project Structure
- `index1.php`: Main dashboard page.
- `createhab1.php`: Form for creating new habits.
- `account1.php`: Displays user account information.
- `challenge1.php`: Displays habit challenges with friends.
- `friendlist1.php`: Manages friends and their habits.
- `friendprogress1.php`: Displays a friend's habit progress.
- `profile1.php`: User profile information placeholder.
- `progress1.php`: Displays user's habit progress.
- `rewards1.php`: Claim rewards and select themes.
- `settings1.php`: User settings management.

## Additional Notes
- Ensure all external libraries (e.g., CSS frameworks, JavaScript libraries) are properly linked in the HTML files.
- Use semantic HTML for better accessibility and SEO.
- Maintain consistent styling across all pages using a central stylesheet.