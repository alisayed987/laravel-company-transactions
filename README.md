
## About Project

Handle company transactions and the payments done on certain transaction to keep track of company financial state by viewing transactions and payments to the admin user and also generate a quick report in a certain duration.

## Supported Features

> - Login/Register.
> - Users roles and permissions.
> - Admins can create transactions.
> - Admins can add payment on a certain transaction.
> - Admins can view all transactions and payments.
> - Customers can view their own transactions and payments
> - Admins can generate report.

**NOTE:** 
> <sup>All features supported by: 
    - Authenticated APIs. (for Mobile, SPA,..etc)
    - Fullstack user interface </sub>.

## Run Project
1. Run git clone https://github.com/alisayed987/company-transactions.git
2. Run composer install
3. Create Database
4. Run cp .env.example .env
5. Edit .env database credentials
6. Run php artisan key:generate
7. Run php artisan migrate
8. Run php artisan serve
9. Go to served link (mostly "localhost:8000")

## Database Schema
![alt text](/public/ScreenShots/db_schema.png)

## ScreenShots

- **Auth:** Login / Register:
<div>
<img src="/public/ScreenShots/1-login.png" width="380">
<img src="/public/ScreenShots/2-register.png" width="380">
</div>

- **Roles:** Admin vs Customer:
<div>
<img src="/public/ScreenShots/3-viewtransactions.png" width="380">
<img src="/public/ScreenShots/9-customer-options.png" width="380">
</div>

- Payments:

<img src="/public/ScreenShots/4-view-payments.png">

- **Roles:** Create Transaction and Add Payment:
<div>
<img src="/public/ScreenShots/5-create-transaction.png" width="380">
<img src="/public/ScreenShots/6-add-payment.png" width="380">
</div>

- **Report:** Generate Report:
<div>
<img src="/public/ScreenShots/7-report-range.png" width="380">
<img src="/public/ScreenShots/8-generate-report.png" width="380">
</div>
