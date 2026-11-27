# Voting System (Pure PHP High-Concurrency Edition)

[![Latest Release](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=Latest Release )](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![SQLite WAL](https://img.shields.io/badge/Storage-SQLite%20WAL-success.svg)](https://sqlite.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](#license)

---

## 📥 [Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)

---

## 📸 Visual Showcase

![01. Public Reactive Voting Interface](screenshots/01-pure-php-frontend.png)
*01. Public Reactive Voting Interface — 7-language selector (EN, ES, CA, PT, FR, JP, CN), live rankings, and clean cards.*

![11. Admin Login Gate](screenshots/11-pure-php-admin-login.png)
*11. Admin Login Gate — Secure credential challenge with access recovery instructions.*

![02. Web Administration Console](screenshots/02-pure-php-admin-dashboard.png)
*02. Web Administration & Live Queue Monitor — WAL queue monitor, mode switcher, queue drain trigger, and contestants.*

![12. Real-Time Audit Log](screenshots/12-pure-php-audit-log.png)
*12. Real-Time Audit Log — High-concurrency ingestion monitor with device fingerprints and processed states.*

---

## 🔐 Administration Console & Credential Recovery

The Pure PHP edition includes a secure session-gated administration panel at `/admin/`.
- **Default URL**: `http://your-domain/pure-php/admin/`
- **Default Username**: `admin`
- **Default Password**: `admin123`

### Configuration & Recovery
Credentials are stored in `pure-php/config.php`:
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
```
To change credentials or regain access if forgotten, edit `ADMIN_USER` and `ADMIN_PASS` in `pure-php/config.php` via FTP, SSH, or file manager.

---

## 🌍 Internationalization (i18n)
Supported in 7 languages: English (en), Spanish (es), Catalan (ca), Portuguese (pt), French (fr), Japanese (ja), Chinese (zh).
Easily extensible via clean array files in `languages/{lang}.php`.

---

## 📄 License
- MIT License
