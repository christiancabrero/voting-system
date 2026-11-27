# Voting System

## Pure PHP version & WordPress Plugin version

[![Latest Release](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=Latest%20Release)](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/License-GPLv2%20%2F%20MIT-green.svg)](#license)
[![Languages](https://img.shields.io/badge/i18n-7%20Languages%20(EN%20%7C%20ES%20%7C%20CA%20%7C%20PT%20%7C%20FR%20%7C%20JA%20%7C%20ZH)-purple.svg)](#internationalization)
[![Concurrency](https://img.shields.io/badge/Throughput-750%2B%20req%2Fsec-success.svg)](#performance--benchmarks)

**Voting System** is a production-ready, dual-edition voting architecture engineered for both content-managed WordPress ecosystems and ultra-high-throughput standalone environments:

1. [**WordPress Plugin Edition**](wordpress-plugin/): Full-featured WP plugin with Simple/Advanced switcher, closed voting with visible participants, unblurred photo cards with vote buttons pinned to the bottom, anti-fraud device fingerprinting, and 100% cache-compatible dynamic AJAX hydration (WP Super Cache, WP Rocket, LiteSpeed).
2. [**Pure PHP High-Concurrency Edition**](pure-php/): Lightweight standalone engine powered by a Producer-Consumer SQLite WAL queue, session-gated administration console, modular file-based translations (`languages/*.php`), and atomic ACID serialization supporting **750+ concurrent requests/sec** with zero lock contention.

---

## 📖 Multilingual Documentation / Documentación

- 🇬🇧 **[English Documentation (Current)](README.md)**
- 🇪🇸 **[Documentación en Español](README.es.md)**
- 🏴 **[Documentació en Català](README.ca.md)**
- 🇵🇹 **[Documentação em Português](README.pt.md)**
- 🇫🇷 **[Documentation en Français](README.fr.md)**
- 🇯🇵 **[日本語ドキュメント](README.ja.md)**
- 🇨🇳 **[中文文档](README.zh.md)**

---

## 📥 Downloads & Releases

Always download the latest stable release package directly from:
👉 **[Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)**

---

## 📸 Visual Showcase (Both Editions)

### 🌟 1. WordPress Plugin Edition

![01. Frontend Open (Simple Mode)](screenshots/01-wp-frontend-open-simple-mode.png)
*01. Frontend Open (Simple Mode) — Crisp unblurred contestant photos, category tags properly stacked above buttons, and vote action pinned to the bottom of the card.*

![02. Post-Vote Exhausted State](screenshots/02-wp-vote-cast-exhausted-state.png)
*02. Post-Vote Exhausted State — Immediate dynamic confirmation; candidate marked as "✓ Voted" and remaining buttons locked.*

![03. Closed Voting with Participants](screenshots/03-wp-voting-closed-participants-visible.png)
*03. Closed Voting with Participants — Informative closure banner while contestants remain visible and unblurred (optional blur setting available).*

![04. Admin Simple / Advanced Switch](screenshots/04-wp-backend-simple-mode-switcher.png)
*04. Admin Simple / Advanced Switch — Intuitive mode switcher cleanly isolating simple 1-vote setups from multi-category rules.*

![05. Cache Compatibility Guide](screenshots/05-wp-backend-cache-compatibility-guide.png)
*05. Cache Compatibility Guide — In-admin reference detailing dynamic nonce and voter state hydration for caching plugins.*

![08. Live Results & Breakdown](screenshots/08-wp-admin-results-breakdown.png)
*08. Live Results & Breakdown — Real-time vote tallies, percentage bars, and contestant status inside WordPress.*

![09. Audit & Votes Trail](screenshots/09-wp-admin-audit-votes.png)
*09. Audit & Votes Trail — Tamper-evident individual vote log recording device identifiers, IP addresses, tokens, and timestamps.*

![10. Security & Anti-Fraud](screenshots/10-wp-admin-security-settings.png)
*10. Security & Anti-Fraud — Granular IP access controls, voter limits, and token configuration.*

---

### ⚡ 2. Pure PHP High-Concurrency Edition

![06. Standalone Public UI](screenshots/06-pure-php-frontend-live.png)
*06. Standalone Public UI — Reactive competition interface with 7-language selector (EN, ES, CA, PT, FR, JP, CN), live rankings, and clean unblurred contestant cards.*

![11. Admin Login Gate](screenshots/11-pure-php-admin-login.png)
*11. Admin Login Gate — Secure credential challenge protecting the administration console with built-in recovery instructions.*

![07. Web Administration Console](screenshots/07-pure-php-admin-dashboard.png)
*07. Web Administration Console — WAL queue monitor, competition mode toggles, manual queue drain trigger, and contestant management.*

![12. Real-Time Audit Log](screenshots/12-pure-php-audit-log.png)
*12. Real-Time Audit Log — High-concurrency ingestion log tracking processed and queued votes with device fingerprints.*

---

## 📝 WordPress Shortcodes Documentation

The WordPress plugin provides two responsive, cache-safe shortcodes:

### 1. `[voting_form]`
Renders the public contestant voting cards.

| Attribute | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `category` | `string` | `""` | Filter contestants by specific category (e.g. `category="Simpson"`). |
| `type` | `string` | `""` | Filter contestants by type (e.g. `type="Family"`). |
| `limit` | `int` | `0` | Overrides the active vote limit for this specific form instance (`0` uses global setting). |
| `columns` | `int` | `3` | Number of grid columns on desktop (`2`, `3`, or `4`). |
| `show_results` | `int` | `0` | Set `1` to render results directly beneath the voting grid. |

**Example Usage:**
```text
[voting_form category="Simpson" columns="4"]
```

### 2. `[voting_results]`
Renders real-time verified vote counts and percentages.

| Attribute | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `category` | `string` | `""` | Filter results by category. |
| `type` | `string` | `""` | Filter results by type. |
| `show_percentages` | `int` | `1` | Show/hide percentage bars (`1` or `0`). |
| `show_numbers` | `int` | `1` | Show/hide total vote counters (`1` or `0`). |
| `show_breakdown` | `int` | `1` | Show detailed contestant breakdown list. |

**Example Usage:**
```text
[voting_results show_percentages="1" show_numbers="1"]
```

---

## 🔐 Pure PHP Admin Access & Credential Recovery

The Pure PHP standalone edition includes a dedicated administration console at `/admin/`.

### Default Credentials
- **URL**: `http://your-domain/pure-php/admin/`
- **Username**: `admin`
- **Password**: `admin123`

### Configuration & Access Recovery
Credentials are centrally managed in `pure-php/config.php`:
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123'); // Change to a strong password in production
```
- **To change credentials**: Edit `ADMIN_USER` and `ADMIN_PASS` in `pure-php/config.php`.
- **To recover lost access**: Open `pure-php/config.php` via FTP, SSH, or file manager, and reset the `ADMIN_PASS` constant. Changes take effect immediately without requiring database queries or cache clearing.

---

## 🌍 Internationalization (i18n)

Both editions provide native support for **7 languages**:
- **English (`en`)**
- **Spanish (`es`)**
- **Catalan (`ca`)**
- **Portuguese (`pt`)**
- **French (`fr`)**
- **Japanese (`ja`)**
- **Chinese (`zh`)**

### Easily Extensible Architecture
- **Pure PHP**: Translations reside in individual files inside `pure-php/languages/` (`en.php`, `es.php`, `ca.php`, `pt.php`, `fr.php`, `ja.php`, `zh.php`). Adding a new language takes under 2 minutes: duplicate `en.php` as `de.php` and return the translated key-value dictionary.
- **WordPress Plugin**: Translations are managed in `includes/dictionary.php` and `includes/i18n.php`, supporting automatic site locale detection, admin profile locale, and multilingual plugins (Polylang, WPML).

---

## ⚡ Concurrency & Architecture Highlights

| Feature | WordPress Plugin | Pure PHP Standalone |
| :--- | :--- | :--- |
| **Target Platform** | WordPress 6.0+ | Any PHP 8.0+ Server (Apache/Nginx/Caddy) |
| **Database** | MySQL / MariaDB | High-Performance SQLite WAL or MySQL PDO |
| **Concurrency Engine** | Dynamic AJAX Hydration | Producer-Consumer WAL Queue & Workers |
| **Throughput** | Caching-optimized AJAX | **750+ continuous requests/sec** |
| **Race Condition Safety** | Database transaction checks | **Strict FIFO queue atomic serialization** |
| **Contestant Blur** | Optional closed toggle | Optional closed toggle |

---

## 🚀 Quick Start

### Option A: WordPress Plugin (`wordpress-plugin/`)
1. Download latest release archive from [Releases](https://github.com/christiancabrero/voting-system/releases/latest).
2. Upload to `wp-content/plugins/voting-system/` or install via **Plugins > Add New > Upload Plugin**.
3. Activate the plugin.
4. Navigate to **Voting System > Settings** to configure Simple Mode or category limits.
5. Add `[voting_form]` to any page.

### Option B: Pure PHP Standalone (`pure-php/`)
1. Download latest release archive from [Releases](https://github.com/christiancabrero/voting-system/releases/latest).
2. Extract to your web server root or run locally:
   ```bash
   php -S 127.0.0.1:8088 -t pure-php/
   ```
3. Access the public voting UI at `http://localhost:8088/`.
4. Access the admin console at `http://localhost:8088/admin/` (`admin` / `admin123`).
5. Process the WAL queue in production via cron or daemon:
   ```bash
   php pure-php/worker.php --once
   ```

---

## 📄 License

- WordPress Plugin Edition: **GNU General Public License v2 (GPLv2)**.
- Pure PHP High-Concurrency Edition: **MIT License**.
