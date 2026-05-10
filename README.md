# E-Wallet - Digital Wallet System

## Introduction
A web-based digital wallet application built with PHP and MySQL. The system simulates core features of a real e-wallet including deposit, withdrawal, transfer, and OTP verification.

## Features
- User registration with ID card upload (front & back)
- Login (email or phone number)
- First-time password change
- Deposit money (test cards)
- Withdraw money (5% fee)
- Transfer money via phone number (with OTP email verification)
- Transaction history
- Profile management

## Technologies
- PHP 8
- MySQL
- Bootstrap 5
- PHPMailer
- Composer

## Installation & Run

1. **Install Dependencies**
   ```bash
   composer install

2. **Database**
- Create database: ewallet
- Import database if available

3. **Configuration**
- Edit config/db_config.php
- Create uploads folder and set permission chmod 777 uploads

4. **Run the application**
Open browser and go to:texthttp://localhost/[your-folder-name]/pages/auth/login.php

5. **Test card**
- 111111 → Success
- 222222 → Max 1,000,000 VND per transaction
- 333333 → Card out of money

6. **Notes**
- Transactions over 5,000,000 VND require Admin approval
- OTP is sent via email
