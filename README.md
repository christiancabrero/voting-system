# Voting System

## Pure PHP version & WordPress Plugin version

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/License-GPLv2%20%2F%20MIT-green.svg)](#license)
[![Languages](https://img.shields.io/badge/i18n-EN%20%7C%20ES%20%7C%20CA-purple.svg)](#internationalization)
[![Concurrency](https://img.shields.io/badge/Throughput-750%2B%20req%2Fsec-success.svg)](#performance--benchmarks)

**Voting System** is a production-ready, dual-edition voting architecture engineered for both content-managed WordPress ecosystems and ultra-high-throughput standalone environments:

1. [**WordPress Plugin Edition (`v3.4.0`)**](wordpress-plugin/): Full-featured WP plugin with Simple/Advanced switcher, closed voting with visible participants, circular blurred/anonymized UI, and 100% cache-compatible dynamic AJAX hydration (WP Super Cache, WP Rocket, LiteSpeed).
2. [**Pure PHP High-Concurrency Edition (`v2.0.0`)**](pure-php/): Lightweight standalone engine powered by a Producer-Consumer WAL queue, stateless HMAC CSRF protection, and SQLite/MySQL ACID serialization supporting **750+ concurrent requests/sec** with zero lock contention and 100% race condition immunity.

---

## 📖 Translations / Documentación / ドキュメント / 文档

- 🇬🇧 **[English Documentation (Current)](README.md)**
- 🇪🇸 **[Documentación en Español](README.es.md)**
- 🇯🇵 **[日本語ドキュメント](README.ja.md)**
- 🇨🇳 **[中文文档](README.zh.md)**

---

## 📸 Visual Showcase (Both Editions)

### 🌟 1. WordPress Plugin Edition (`v3.4.0`)

![01. Frontend Open (Simple Mode)](screenshots/01-wp-frontend-open-simple-mode.png)
*01. Frontend Open (Simple Mode) — 1 global vote available per voter, responsive grid*

![02. Post-Vote Exhausted State](screenshots/02-wp-vote-cast-exhausted-state.png)
*02. Post-Vote Exhausted State — Voted candidate marked as "✓ Voted", remaining buttons locked*

![03. Closed Voting with Participants](screenshots/03-wp-voting-closed-participants-visible.png)
*03. Closed Voting with Participants — Status notice banner displayed while contestants remain visible*

![04. Admin Simple / Advanced Switch](screenshots/04-wp-backend-simple-mode-switcher.png)
*04. Admin Simple / Advanced Switch — Interactive mode cards hiding complex category options*

![05. Cache Compatibility Guide](screenshots/05-wp-backend-cache-compatibility-guide.png)
*05. Cache Compatibility Guide — In-admin performance and caching setup documentation*

### ⚡ 2. Pure PHP High-Concurrency Edition (`v2.0.0`)

![06. Standalone Public UI](screenshots/06-pure-php-frontend-live.png)
*06. Standalone Public UI — Lightweight reactive frontend with language switcher (EN/ES/CA)*

![07. Web Administration Console](screenshots/07-pure-php-admin-dashboard.png)
*07. Web Administration Console — Live queue status, mode switcher, contestant ranking & audit log*

---

## 📦 Project Structure

```text
voting-system/
├── screenshots/               # Visual showcases for both editions
├── wordpress-plugin/          # WordPress Plugin Edition (v3.4.0)
│   ├── assets/                # Admin & Frontend CSS/JS
│   ├── screenshots/           # WP anonymized UI captures
│   ├── voting-system.php      # Main WordPress plugin entrypoint
│   ├── README.md              # English documentation
│   ├── README.es.md           # Spanish documentation
│   ├── README.ja.md           # Japanese documentation
│   └── README.zh.md           # Chinese documentation
├── pure-php/                  # Standalone Pure PHP Edition (v2.0.0)
│   ├── api/                   # High-concurrency vote, state, and stats endpoints
│   ├── admin/                 # Standalone web administration console
│   ├── includes/              # Database (SQLite WAL), Queue, Voter, and I18n engines
│   ├── tests/                 # 200-500 concurrent request stress test suite
│   ├── worker.php             # CLI queue worker daemon & cron runner
│   ├── index.php              # Public voting interface (Burgundy Competition theme)
│   ├── README.md              # English documentation
│   ├── README.es.md           # Spanish documentation
│   ├── README.ja.md           # Japanese documentation
│   └── README.zh.md           # Chinese documentation
├── README.md                  # Master English project overview
├── README.es.md               # Master Spanish project overview
├── README.ja.md               # Master Japanese project overview
└── README.zh.md               # Master Chinese project overview
```

---

## ⚡ Comparison: Which Edition Should You Use?

| Feature | WordPress Plugin (`v3.4.0`) | Pure PHP Standalone (`v2.0.0`) |
| :--- | :--- | :--- |
| **Target Platform** | WordPress 6.0+ | Any PHP 8.0+ Server (Apache/Nginx/Caddy) |
| **Dependencies** | WordPress Core & WPDB | Zero Framework Dependencies |
| **Database** | MySQL / MariaDB | High-Performance SQLite WAL or MySQL PDO |
| **Concurrency Architecture** | Dynamic AJAX Nonce & Voter Hydration | Producer-Consumer WAL Queue & Workers |
| **Throughput Capacity** | Cached static HTML + AJAX hydration | **755+ req/sec continuous ingestion** |
| **Race Condition Immunity** | Strict DB scope limit validation | **Atomic ACID transaction queue serialization** |
| **Simple Mode** | 1 vote per voter global (hides categories) | 1 vote per voter global (strictly enforced) |
| **Closed State with Participants** | Yes (alert banner + locked buttons) | Yes (alert banner + locked buttons) |
| **Languages Supported** | English (`en`, primary), Spanish (`es`), Catalan (`ca`) | English (`en`, primary), Spanish (`es`), Catalan (`ca`) |
| **Visual Design** | Competition Burgundy theme (`#6E1F2D`) | Competition Burgundy theme (`#6E1F2D`) |

---

## 🚀 Quick Start

### Option A: WordPress Plugin (`wordpress-plugin/`)
1. Copy `wordpress-plugin/` to your `wp-content/plugins/voting-system/` directory.
2. Activate via **Plugins > Installed Plugins**.
3. Configure settings at **Voting System > Settings**.
4. Embed using shortcode `[voting_form]` on any page.

### Option B: Pure PHP Standalone (`pure-php/`)
1. Deploy `pure-php/` to any web directory.
2. Start PHP server:
   ```bash
   php -S localhost:8080 -t pure-php/
   ```
3. Run the concurrency benchmark suite:
   ```bash
   php pure-php/tests/stress_test.php 200
   ```
4. Access the voting page at `http://localhost:8080/` and administration at `http://localhost:8080/admin/`.

---

## 🏷️ Tags & Keywords

`php`, `wordpress`, `wordpress-plugin`, `voting-system`, `high-concurrency`, `sqlite-wal`, `producer-consumer`, `queue-worker`, `race-condition-safe`, `cache-friendly`, `wp-super-cache`, `wp-rocket`, `litespeed`, `i18n`, `multilingual`.

---

## 📄 License

- WordPress Plugin Edition: Distributed under the **GNU General Public License v2 (GPLv2)**.
- Pure PHP High-Concurrency Edition: Distributed under the **MIT License**.
