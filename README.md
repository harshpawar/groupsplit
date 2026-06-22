# 💰 Group Split - Expense Sharing Made Easy

A modern, colorful web application for splitting bills and managing shared expenses among groups. Perfect for roommates, friends, travel groups, or any group that needs to track who owes whom.

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-3.1-06B6D4?style=flat-square&logo=tailwindcss)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql)

---

## ✨ Features

### 👥 Group Management
- **Create groups** with a memorable name (e.g., "Summer Trip 2024", "Apartment Expenses")
- **Add members** by mobile number or search existing users
- **Real-time member search** by name or mobile number
- **Invite members via QR code** - easy sharing on mobile
- **Track member status** - see who joined vs who's still invited
- **Edit groups** - add more members anytime

### 💳 Bill Splitting
- **Create bills** and automatically split among group members
- **Equal split** - amount divided equally among all members
- **Track payments** - see who paid and how much was approved
- **Payment history** - view all submissions and approval status
- **Member-specific splits** - each member sees their share

### ✅ Payment Management
- **Members submit payments** with optional notes
- **Admin approves/rejects** payments with rejection reasons
- **Admin can create payments** - record cash payments, transfers, etc.
- **Auto-approval for admin** - admin payments are instantly approved
- **Payment approval history** - track who approved when

### 🎯 Admin Controls
- **Change split status** directly - Pending, Partial, Approved, Settled, Rejected, Overpaid
- **Manage group** - edit name, add members, generate invite QR code
- **View all payments** - see complete payment history
- **Approve/reject payments** - control payment workflow
- **Delete bills** - remove bills if needed

### 📊 Settlement Tracking
- **Settlement progress** - visual progress bar showing percentage settled
- **Status badges** - see "All Settled ✓" or "X Pending" at a glance
- **Unsettled splits counter** - know exactly how many splits need attention
- **Colorful status indicators** - Emerald for settled, Amber for pending

### 🎨 Beautiful UI
- **Gradient headers** - modern, eye-catching design
- **Color-coded badges** - blue for admin, purple for members, pink for bills
- **Interactive elements** - hover effects, smooth transitions
- **Mobile-responsive** - fully optimized for all device sizes
- **Floating action buttons** - quick access on mobile
- **Dark borders and shadows** - high contrast for visibility

---

## 📱 Screenshots

### Groups Dashboard
![My Groups](https://via.placeholder.com/600x400?text=My+Groups+Dashboard)
*View all your groups with settlement progress and member count*

### Bill Details with Payment Management
![Bill Details](https://via.placeholder.com/600x400?text=Bill+Details+Page)
*Track splits, payments, and admin controls for each bill*

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.4+
- MySQL 5.7+
- Node.js 18+
- Composer

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/group-split.git
cd group-split
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
```bash
# Edit .env with your database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=group_split
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Build assets**
```bash
npm run build
```

7. **Start the application**
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

---

## 📖 Usage Guide

### Creating a Group
1. Click **"+ Create Group"** button
2. Enter a group name
3. **Search and add members** by name/mobile (optional)
4. Or **manually enter mobile numbers** (one per line or comma-separated)
5. Click **"Create Group"**
6. Share the **QR code** with members to invite them

### Creating a Bill
1. Open a group
2. Click **"Add Bill"** button
3. Enter bill title, description, and total amount
4. Amount is automatically split equally among all members
5. Click **"Create Bill"**

### Making Payments
#### As a Member:
1. View a bill
2. See your split amount and remaining balance
3. Enter payment amount and optional note
4. Click **"Mark as Paid"**
5. Admin will review and approve/reject

#### As an Admin:
1. View a bill
2. For any member, enter payment amount and note
3. Click **"✅ Approve & Record Payment"**
4. Payment is instantly approved and recorded

### Approving Payments
1. View a bill
2. Scroll to payment history
3. See pending payments (blue banner)
4. Click **"Approve"** to accept or **"Reject"** to refuse
5. If rejecting, provide rejection reason

### Changing Split Status
1. View a bill (as admin)
2. See status change buttons for each split
3. Quick buttons: Pending, Partial, Approved, Settled, Reject, Overpaid
4. Current status is highlighted in blue
5. Click to change immediately

---

## 🏗️ Architecture

### Frontend
- **Tailwind CSS 3.1** - Utility-first CSS framework
- **Blade Templates** - Laravel's templating engine
- **Alpine.js** - Lightweight JavaScript framework
- **Vite** - Fast build tool with hot module replacement

### Backend
- **Laravel 13** - PHP web framework
- **Eloquent ORM** - Database abstraction
- **Form Requests** - Built-in validation
- **Services** - Business logic separation

### Database
- **MySQL** - Relational database
- **Migrations** - Version-controlled schema
- **Enums** - Type-safe status values

### Key Models
- **User** - Application users
- **Group** - Expense groups
- **GroupMember** - Members of groups
- **Bill** - Expense bills
- **BillSplit** - Individual shares in a bill
- **Payment** - Payment submissions
- **PaymentApproval** - Approval records

---

## 🔐 Security

- **Authentication** - Laravel's built-in auth system
- **Authorization** - Role-based access control (admin/member)
- **Validation** - Input validation on all forms
- **CSRF Protection** - Cross-site request forgery tokens
- **Password Hashing** - Secure password storage

---

## 📝 Enums & Status Values

### Bill Split Status
- `pending` - Awaiting payment
- `partially_paid` - Some amount approved
- `approved` - Full amount approved
- `settled` - Completely settled ✓
- `rejected` - Payment rejected
- `overpaid` - More than share paid

### Payment Status
- `pending` - Awaiting admin review
- `approved` - Approved by admin
- `rejected` - Rejected by admin

### Approval Action
- `approved` - Payment approved
- `rejected` - Payment rejected

---

## 🛠️ Development

### Running Tests
```bash
php artisan test
```

### Building Assets
```bash
npm run build      # Production build
npm run dev        # Development with hot reload
```

### Database Commands
```bash
php artisan migrate          # Run migrations
php artisan migrate:rollback # Rollback migrations
php artisan tinker          # Interactive shell
```

---

## 📦 Project Structure

```
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Group.php
│   │   ├── Bill.php
│   │   ├── BillSplit.php
│   │   ├── Payment.php
│   │   └── PaymentApproval.php
│   ├── Http/
│   │   └── Controllers/
│   │       ├── GroupController.php
│   │       ├── BillController.php
│   │       └── PaymentController.php
│   ├── Enums/
│   │   ├── SplitStatus.php
│   │   ├── PaymentStatus.php
│   │   └── ApprovalAction.php
│   └── Services/
│       └── BillSplitStatusService.php
├── resources/
│   ├── views/
│   │   ├── groups/
│   │   ├── bills/
│   │   └── components/
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── database/
│   ├── migrations/
│   └── factories/
├── routes/
│   └── web.php
└── config/
```

---

## 🎯 Features Roadmap

- [ ] Expense notifications via SMS/Email
- [ ] Expense categories and filtering
- [ ] Recurring bills
- [ ] Bulk payment processing
- [ ] Export to PDF/CSV
- [ ] Mobile app (React Native)
- [ ] Payment gateway integration
- [ ] Two-factor authentication
- [ ] Dark mode
- [ ] Multiple languages

---

## 🐛 Bug Reports & Feature Requests

Found a bug? Have an idea? Please open an [issue](https://github.com/yourusername/group-split/issues).

---

## 📄 License

This project is open-source software licensed under the MIT license.

---

## 👨‍💻 Author

**Harsh Pawar**

---

## 💡 Support

For questions or support, please open an issue on GitHub or contact the author.

---

**Made with ❤️ for expense sharing communities**


## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
