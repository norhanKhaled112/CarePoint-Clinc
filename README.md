# 🏥 CarePoint Clinic

A web-based clinic management system that allows patients to book appointments with doctors across multiple specialties.

---

## ✨ Features

- **Patient Registration & Login** — Patients can create an account and log in securely
- **Doctor Registration & Login** — Doctors register with their specialty/department
- **Book Appointments** — Patients can browse doctors and book appointments
- **Patient Dashboard** — View all upcoming and past appointments
- **Doctor Dashboard** — View patient appointments and update their status (Confirm / Cancel)
- **Role-Based Access** — Separate experience for Patients and Doctors

---

## 🛠️ Tech Stack

| Layer    | Technology        |
|----------|-------------------|
| Frontend | HTML, CSS, JavaScript |
| Backend  | PHP               |
| Database | MySQL (phpMyAdmin) |
| Server   | XAMPP (Apache)    |

---

## 🗄️ Database Tables

- `patients` — stores patient info
- `doctors` — stores doctor info and department
- `appointments` — links patients to doctors with date, time, and status

---

## 🚀 Getting Started

### Requirements
- XAMPP installed on your machine

### Steps

1. Clone the repository
   ```bash
   git clone https://github.com/your-username/carepoint-clinic.git
   ```

2. Move the project folder to:
   ```
   C:\xampp\htdocs\
   ```

3. Open XAMPP and start **Apache** and **MySQL**

4. Open your browser and go to:
   ```
   http://localhost/phpmyadmin
   ```
   Create a database named `carepoint_db` and import `database.sql`

5. Open the app:
   ```
   http://localhost/carepoint-clinic/registerform.php
   ```

---

## 📁 Project Structure

```
carepoint-clinic/
├── registerform.php     # Login & Register page
├── profile.php          # User profile + appointments
├── book.php             # Book an appointment
├── login.php            # Login handler
├── register.php         # Register handler
├── logout.php           # Logout handler
├── db.php               # Database connection
├── database.sql         # SQL file to create tables
├── script.js            # Frontend logic
├── style2.css           # Styles
└── img/                 # Images
```

---

## 👩‍💻 Developer

developed by Norhan Khaled as a university project
