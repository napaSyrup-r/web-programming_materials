<div align="center">

<img src="assets/img/logo.png" alt="EWU-L&F" width="90" style="border-radius:16px;" />

<h1>EWU‑L&F · Lost & Found Information System</h1>

Minimal, friendly, and responsive Lost & Found for campus or small orgs. Built with HTML5, CSS3, JS, PHP (mysqli), and MySQL (XAMPP). Clean UI, subtle animations, and a practical admin workflow.

</div>

---

## ✨ Highlights
- Modern responsive UI with animated hero and themed styling (light/dark/neon)
- Public catalog with filters and item detail pages
- Claim workflow: submit evidence, admin review approve/reject/fulfill
- Admin tools: items, categories, users, pages, messages, settings
- Hide/unhide items, status badges, date sorting and search
- Custom 403/404 pages, service/maintenance mode, sticky footer

## 🧰 Requirements
- XAMPP (Apache, MySQL)
- PHP 7.4+ (or newer)

## 🚀 Quick Start
1. Start Apache and MySQL in XAMPP.
2. Visit `http://localhost/phpmyadmin` and create a database `lf_db`.
3. Import the schema: `database/azmi_lf_db.sql` into `lf_db`.
4. Open the app:
   - Landing: `http://localhost/`
   - Public: `http://localhost/public/`
   - Admin: `http://localhost/admin/`

### Default Admin (first run)
- Username: `admin`
- Password: `admin123`

You should switch to hashed passwords:
- Generate a hash with `tools/hash_password.php` and update the `users` table.

## 📂 Project Structure
```
EWU-L&F/
  admin/
    index.php           # login (password_verify)
    dashboard.php       # metrics + cards
    items.php           # filters, hide/unhide, status, date sort/search
    categories.php
    users.php
    messages.php
    pages.php
    settings.php
    logout.php
    includes/
      auth.php          # session + role guards
      header.php        # admin layout header
      footer.php        # admin layout footer
  public/
    index.php           # catalog + category filter
    about.php
    contact.php         # send_message API
    terms.php
    privacy.php
    submit.php          # submit_item API (pending)
    item.php            # item details + claim form
    403.php, 404.php    # error routes
  api/
    submit_item.php     # create item (pending)
    send_message.php    # contact form
    submit_claim.php    # claim requests
  includes/
    db.php              # mysqli wrapper
    helpers.php         # utils, settings, error pages, notify_admins
  assets/
    css/styles.css      # themes, layout, animations
    js/app.js           # menu + theme toggles
    img/logo.png        # favicon/logo
  tools/
    hash_password.php   # generate password_hash for seeds
  uploads/              # images / logs
  database/
    azmi_lf_db.sql      # schema and seeds
```

## 🔐 Security & Hardening (recommended)
- Use `password_hash` + `password_verify` (already wired in admin login)
- Add CSRF tokens to forms and stricter server-side validation
- Validate uploads (MIME/type/size), store outside webroot in production
- Configure pagination and rate-limit sensitive endpoints

## 🗂 Features Overview
- Public
  - Animated landing, mobile menu, theme toggle
  - Category filtering, item grid, badges (claimed/pending)
  - Item details with claim request (evidence + optional attachment)
- Admin
  - Items: publish/claim, hide/unhide, filters (title/category/status), date range
  - Claims: approve/reject/fulfill with item state transitions
  - Messages, Categories, Users, Pages, Settings, Account
  - Maintenance mode (service OFF with reason)

## ⚙️ Configuration Tips
- DB connection: see `includes/db.php`
- Site name/org: edit `settings` table or admin Settings page
- Error pages: generated via `includes/helpers.php` (`render_forbidden`, `render_not_found`)

## 🧪 Troubleshooting
- Login fails: confirm `admin` exists and that DB creds in `includes/db.php` are correct
- Images not showing: ensure `uploads/` exists and Apache can write there
- Paths: if `&` in folder name causes issues, rename to `EWU-LF` and update paths

## 👥 The Team

This project is developed by:

<table style="width: 100%; border-collapse: collapse; margin: 2rem 0;">
  <tr style="text-align: center;">
    <td style="padding: 1rem;">
      <img src="https://github.com/azizulabedinazmi.png" width="100" style="border-radius: 50%;">
      <br><br>
      <a href="https://github.com/azizulabedinazmi">Azizul Abedin Azmi</a>
      <br>
      <small>Lead Developer</small>
    </td>
    <td style="padding: 1rem;">
      <img src="https://github.com/Tanzila-Afrin.png" width="100" style="border-radius: 50%;">
      <br><br>
      <a href="https://github.com/Tanzila-Afrin">Tanzila Afrin</a>
      <br>
      <small>Developer</small>
    </td>
    <td style="padding: 1rem;">
      <img src="https://github.com/napaSyrup-r.png" width="100" style="border-radius: 50%;">
      <br>
      <a href="https://github.com/napaSyrup-r">Fayroz Tasnim Rowza</a>
      <br>
      <small>Developer</small>
    </td>
  </tr>
</table>

We are a team of passionate developers who love creating clean, open‑source tools.

## 📜 License
[![License: Open Source](https://img.shields.io/badge/License-Open%20Source-blue?style=for-the-badge)](/LICENCE)

<p align="center" style="margin-top:0.5rem;color:var(--muted,#6b7280);font-size:0.95rem;">
  This project is open-source. See the license for full terms.
</p>

## 🙌 Credits
- UI inspiration from modern dashboard patterns and campus workflows
- Built on XAMPP with a focus on beginner-friendly PHP + MySQL

