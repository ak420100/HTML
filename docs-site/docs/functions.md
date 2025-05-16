# Welcome to Trabit JavaScript Functions Documentation

## Overview
This documentation covers the JavaScript functions used in the Trabit web application, providing functionality for user settings, themes, rewards, and user interactions.

## Features
- 🌐 Load user settings and apply dark mode
- 🎨 Load and apply user themes
- 💰 Manage coin balance, theme purchases, and claims
- 🔑 Handle user authentication and friends management

## JavaScript Files

### `loadSetting.js`
- **Purpose**: Loads user settings and applies dark mode if enabled.
- **Key Functions**:
  - Fetches settings from `settings.php`.
  - Adds "dark-mode" class to the body if dark mode is enabled.

### `loadTheme.js`
- **Purpose**: Loads and applies the user's selected theme.
- **Key Functions**:
  - Fetches the theme from `get_theme.php` upon DOM content load.
  - Removes existing theme classes and adds the new theme class based on the response.

### `rewards.js`
- **Purpose**: Manages coin balance, theme application, and theme purchasing.
- **Key Functions**:
  - Fetches coin balance and theme from `rewards.php`.
  - Updates UI for purchased themes and applies the current theme.
  - Handles theme purchases and updates the coin balance dynamically.
  - Manages claiming coins through a button click.

### `script.js`
- **Purpose**: Provides global navigation and manages user login, signup, and friends list functionality.
- **Key Functions**:
  - Handles login and signup forms using `login.php` and `signup.php`.
  - Manages the friends list, allowing users to add and remove friends.
  - Toggles dark mode based on the response from `darkmode.php`.
  - Provides a function for page navigation.

## Project Structure
- `loadSetting.js`: Loads user settings and applies dark mode.
- `loadTheme.js`: Loads the user's theme and applies it to the document.
- `rewards.js`: Manages coin balance and theme purchasing.
- `script.js`: Handles user authentication and friends management.