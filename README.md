# 🐛 Issue Tracker

A lightweight Laravel issue tracker built for the **PRITECH Laravel Task**.  
Built to track issues — not create them. 😎

![funny bug gif](https://media.giphy.com/media/l41YtZOb9EUABnuqA/giphy.gif)

---

## 🚀 Features

### Core
- ✅ Projects CRUD (with start date & deadline)
- ✅ Issues CRUD (status, priority, due date, project)
- ✅ Tags CRUD (unique name + optional color)
- ✅ Comments on issues (AJAX load + add, prepend on add)
- ✅ Relationships: Projects ⇄ Issues ⇄ Tags, Issues ⇄ Comments
- ✅ Auth system (login/logout, CSRF protection)

### Bonus
- ✅ Assignees (many-to-many Users ⇄ Issues, AJAX attach/detach)
- ✅ Authorization: only project owners can edit/delete, attach tags, assign users
- ❌ AJAX search with debounce (skipped — optional)

---

## 🧑‍💻 Tech Stack

- **Laravel 12** (task asked for 10/11+, we went newer 🚀)
- Blade templates (Bootstrap 5.3)
- Vanilla JS + `fetch()` for AJAX
- SQLite (default) or any DB supported by Laravel
- Minimal auth with custom `LoginController`

---

## 📦 Installation

Clone the repo and install dependencies:

```bash
git clone https://github.com/argzonn/issue-tracker.git
cd issue-tracker
composer install
npm install && npm run build
Set up environment:

bash
Copy code
cp .env.example .env
php artisan key:generate
Migrate & seed:

bash
Copy code
php artisan migrate --seed
Run the dev server:

bash
Copy code
php artisan serve
Visit http://127.0.0.1:8000

🔑 Demo Logins
All accounts use password password.

owner@example.com → Owner Demo (can edit owned projects)

member@example.com → Member Demo

demo1@example.com … demo5@example.com

💡 Note: Only project owners (or admins) can edit tags/assignees. Some seeded projects belong to Member Demo, so buttons may be hidden when logged in as Owner Demo.

📋 Requirements Checklist
Requirement	Status
Projects CRUD	✅
Issues CRUD (filters, show, etc.)	✅
Tags CRUD + AJAX attach/detach	✅
Comments AJAX (list + add, prepend)	✅
Auth (login/logout)	✅
Authorization (owners only)	✅
Bonus: Assignees (AJAX attach/detach)	✅
Bonus: AJAX search with debounce	❌ (not implemented, optional)

🧪 Testing
Feature tests cover:

Project/Issue/Tag/Comment creation & deletion

Policies (only owners can update/delete)

Pivots (issue_tag, issue_user) enforce uniqueness & cascade deletes

Run tests:

bash
Copy code
php artisan test
🤓 Notes
💡 **Authorization Note:**  
Only the project owner can edit or delete a project, issue, or manage tags/assignees.  
This is by design (Laravel Policies).  
Since the seeders create projects owned by different demo users, you may not see edit/delete buttons on every project unless you log in as its owner.

Logout uses a POST form (not a link) → CSRF safe.

AJAX endpoints all return JSON with rendered Blade partials.

Seeders create demo projects/issues/tags so the UI isn’t empty.

💡 Remember: every “feature” is just an issue waiting to be tracked. 