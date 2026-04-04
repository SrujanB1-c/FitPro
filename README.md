# FitPro - Online Fitness Management System

FitPro is a web-based fitness management platform designed to help fitness enthusiasts track their goals, trainers, and diets effectively. 

## Features

1. **Responsive Web Design:** Built using CSS Flexbox and custom Media Queries to ensure a flexible and responsive interface across all devices and screen sizes.
2. **Client-Side Validation:** Robust Javascript form validation on registration and login elements, checking for minimum lengths, structure, and dynamically handling CSS error states.
3. **Database Integration & Sessions:** 
   - Secure PDO MySQL connections.
   - User authentication securely handles hashed passwords.
   - PHP Session management tracks logged-in states across pages.
   - Full CRUD operations available over user profiles (Create, Read, Update, Delete).
4. **AJAX Filtering:** Integration of the JavaScript `fetch` API for asynchronous class scheduling. Filters communicate with a PHP backend REST endpoint to update DOM elements dynamically without reloading.
5. **Interactive UI Elements:** Parallax scrolling effects, embedded HTML5 videos, and Google Maps iframe integrations.

## Setup Instructions

This project relies on PHP and MySQL. It is recommended to run this locally using tools like XAMPP or WAMP.

1. Download and install XAMPP.
2. Clone or move the `FitPro` directory into your `htdocs` folder (e.g., `C:\xampp\htdocs\FitPro`).
3. Start the **Apache** and **MySQL** modules from the XAMPP Control Panel.
4. Navigate to `http://localhost/phpmyadmin/`.
5. Create a new database named `fitpro_db`.
6. Import the included `database.sql` file into `fitpro_db` to set up tables and mock data.
7. Access the application in your browser at `http://localhost/FitPro/index.php`.

## Alternative: Run with PHP Dev Server (No Apache Required)

If XAMPP Apache is not available, use PHP's built-in development server:

1. Open Command Prompt and run:
   ```
   C:\xampp\php\php.exe -S localhost:8000 -t "C:\path\to\FitPro"
   ```
2. Access the application at `http://localhost:8000/index.php`.

> **Note:** MySQL must still be running (via XAMPP Control Panel) for database features to work.