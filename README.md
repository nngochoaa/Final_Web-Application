# E-Wallet — Digital Wallet System

A web-based digital wallet application built with PHP and MySQL. The system simulates core features of a real e-wallet, including user registration with ID verification, deposits, withdrawals, transfers with OTP confirmation, and a comprehensive admin management panel.

🌐 Live Demo (72h)
URL: https://e-wallet.infinityfreeapp.com/login.php

👤 Test Accounts
Admin Account
| Field | Value |
| :--- | :--- |
| Email | admin@gmail.com |
| Password | 12345678 |

User Account (sample)
| Field | Value |
| :--- | :--- |
| Phone | 0983423848 |
| Password | 123456 |

✨ Features
User:
* Register with ID card upload (front & back — CCCD).
* Login via email or phone number.
* Forced password change on the first login attempt.
* Deposit money using simulated test cards.
* Withdraw money (A 5% transaction fee applies).
* Transfer money to another user via phone number (Requires email OTP verification).
* Purchase mobile top-up cards.
* View transaction history and specific transaction details.
* Update personal profile & change passwords.
* Re-upload ID card images if rejected by the administrator.

Admin:
* Approve or reject new user registration requests.
* Approve large-value transactions (> 5,000,000 VND).
* Manage locked accounts (View logs & unlock manually).

🛠️ Technologies Used
* PHP 8
* MySQL
* Bootstrap 5
* PHPMailer (Installed via Composer)
* Composer

⚙️ Installation & Run (Local Environment)
1. Clone the repository
git clone https://github.com/nngochoaa/Final_Web-Application.git
cd Final_Web-Application

2. Install PHP dependencies
composer install

3. Set up the database
* Open phpMyAdmin (or any preferred MySQL client).
* Create a new database named: ewallet
* Import the following schema file: e_wallet (1).sql

4. Configure database connection
Open db_config.php at the root of the project and update your connection details:
$host     = 'localhost';
$dbname   = 'ewallet';
$username = 'root';
$password = '';

5. Set up the uploads folder
mkdir uploads
chmod 777 uploads

6. Run the application
Place the project folder inside your web server root directory, then navigate to:
http://localhost/Final_Web-Application/login.php

💳 Test Cards (For Deposits)
| Card Number | Expected Result |
| :--- | :--- |
| 111111 | ✅ Success |
| 222222 | ⚠️ Max 1,000,000 VND per transaction |
| 333333 | ❌ Card out of money |

📝 Important Notes
* Any transaction over 5,000,000 VND requires Admin approval before being processed.
* OTP verification is handled via email — You must configure your SMTP settings for PHPMailer inside send_mail.php for this feature to work.
* Accounts will be temporarily locked for 1 minute after 3 consecutive failed login attempts.
* Accounts will be permanently locked after more than 3 subsequent failed attempts — An Admin must unlock them manually from the dashboard.
