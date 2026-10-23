# Student Management System

A web-based Student Management System built with PHP, MariaDB/MySQL, HTML, and CSS. This project helps users manage student records through a simple, user-friendly interface.

## Features

- **User Authentication:** Login and session-based access control.
- **Dashboard:** View student statistics.
- **Register Students:** Add new student records.
- **View Students:** Display and search student records.
- **Edit Records:** Update existing student information.
- **Delete Records:** Remove student records when necessary.
- **Database Integration:** Store and manage records using MariaDB/MySQL.
- **Input Validation:** Validate submitted student information.

## Technologies Used

- PHP
- MariaDB / MySQL
- HTML5
- CSS3
- SQL
- Apache Web Server
- Git and GitHub

## Project Structure

```text
student-management-system/
├── index.php
├── login.php
├── logout.php
├── auth.php
├── db.php
├── add_student.php
├── view_students.php
├── edit_student.php
└── delete_student.php
```

*Note: The file list above shows the main application files. Your actual repository may contain additional files.*

## Database Setup

1. Install PHP, MariaDB/MySQL, and a compatible web server.
2. Create a database named `student_database`.
3. Create the `students` and `users` tables required by the application.
4. Configure the database connection in `db.php`.
5. Place the project in your web server's document root.
6. Open the login page in your browser.

## Security Note

Configure your own database credentials locally. Do not commit database passwords, secret keys, or other sensitive information to GitHub.

## Learning Objectives

This project demonstrates practical experience with:

- PHP web application development
- Database design and SQL queries
- CRUD operations (Create, Read, Update, Delete)
- User authentication and sessions
- Form validation and error troubleshooting
- Integrating a web application with a relational database

## Future Improvements

- Role-based access control
- Student profile photos
- Export student records to CSV or PDF
- Improved responsive design
- Pagination for large student lists

## Author

**Samuel Wakesho**

GitHub: [samuelwakesho4237-code](https://github.com/samuelwakesho4237-code)

This project was developed as a practical portfolio project to demonstrate PHP and database-driven web application development.
