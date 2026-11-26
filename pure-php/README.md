# Voting System (Pure PHP High-Concurrency Edition)

Ultra-fast, standalone voting system written in pure PHP (no framework dependencies). Architected using the **Producer-Consumer Queue Pattern** with high-performance SQLite Write-Ahead Logging (WAL) and optional MySQL PDO support. Built specifically to handle **100 to 1,000+ concurrent voting requests per second** with **zero database lock contention** and **100% thread safety against race conditions**.

---

## 📸 Visual Showcase

| View | Screenshot |
| :--- | :--- |
| **01. Public Reactive Voting Interface** | ![Pure PHP Public UI](screenshots/01-pure-php-frontend.png) |
| **02. Web Administration & Live Queue Monitor** | ![Pure PHP Admin Console](screenshots/02-pure-php-admin-dashboard.png) |

---

## 🚀 Key Architectural Advantages

```
[ Client Requests (100 - 1,000 req/s) ]
                  │
                  ▼
   [ Fast Ingestion Layer (api/vote.php) ]
    - Stateless Device-Bound HMAC CSRF
    - Sliding Window IP Rate Limiting
    - Atomic Memory/Fast Deduplication
                  │
                  ▼
     [ Append-Only SQLite WAL Queue ]  <-- Sub-millisecond non-blocking writes!
                  │
                  ▼
   [ Worker Consumer Engine (worker.php) ]
    - Serialized ACID Database Transactions
    - Guaranteed Exactly-Once Vote Processing
    - Live Aggregation & Audit Logging
                  │
                  ▼
    [ Confirmed Votes Storage & Results ]
```

### 1. High-Concurrency Queue Architecture
- Traditional database writes under high concurrency trigger connection pool exhaustion, table lock escalations, and deadlocks.
- This engine decouples **Request Ingestion** from **Transaction Processing**:
  - The voter's request appends to an append-only WAL queue in under **1 millisecond**.
  - Worker batches process queued votes inside serialized ACID transactions.
  - Can run continuously as a background daemon (`php worker.php --daemon`), as a periodic cron job (`php worker.php --once`), or through inline micro-batch auto-drain for zero-configuration hosting.

### 2. Bulletproof Race Condition & Double-Vote Immunity
- If a malicious client fires 50 or 100 simultaneous requests at the exact same millisecond:
  - Fast-path memory deduplication flags redundant in-flight entries.
  - The worker's transactional unique constraints ensure that **strictly 1 vote** is ever confirmed, cleanly rejecting all concurrent duplicates.

### 3. Stateless Device-Bound HMAC CSRF
- Standard PHP sessions (`session_start()`) serialize requests from the same user and create disk I/O lock contention (`sess_*` files).
- This engine uses a **Stateless Device-Bound HMAC CSRF Token** (`hash_hmac('sha256', $deviceId . date('YmdH'), SECRET)`).
- Eliminates PHP session locking entirely while maintaining complete cross-site request forgery protection.

### 4. Multilingual Engine (EN, ES, CA)
- English (`en`) is the primary reference language.
- Full native translations in English, Spanish (`es`), and Catalan (`ca`).
- Automatic detection via query parameter (`?lang=`), cookie (`vs_lang`), or browser `Accept-Language` headers.

---

## ⚡ Concurrency Benchmark Results

Benchmark executed with the included testing suite (`tests/stress_test.php`):

| Metric | 200 Concurrent Requests | 500 Concurrent Requests |
| :--- | :--- | :--- |
| **Duration** | **0.345 seconds** | **0.662 seconds** |
| **Throughput** | **580.17 requests/sec** | **755.61 requests/sec** |
| **Ingested Votes** | 200 / 200 (**100.0%**) | 500 / 500 (**100.0%**) |
| **Failed / Timeouts** | **0** | **0** |
| **Race Attack (50 simultaneous from same device)** | **1 Confirmed, 49 Rejected** | **1 Confirmed, 49 Rejected** |
| **Race Condition Verdict** | **PASSED (100% Idempotent)** | **PASSED (100% Idempotent)** |

---

## 📁 Project Structure

```text
pure-php/
├── index.php                 # Responsive frontend UI (CIM Barcelona theme)
├── api/
│   ├── vote.php             # High-concurrency vote ingestion endpoint
│   ├── state.php            # Dynamic voter state hydration (uncached)
│   ├── participants.php     # Public participants list (ETag cacheable)
│   └── stats.php            # Real-time statistics & results endpoint
├── admin/
│   ├── index.php            # Admin control center (Simple/Advanced switcher, metrics)
│   └── action.php           # Admin action processor
├── worker.php               # CLI queue consumer daemon & cron runner
├── config.php               # System settings, secret keys, DB driver
├── includes/
│   ├── Database.php         # SQLite WAL & MySQL PDO engine
│   ├── Queue.php            # Thread-safe queue manager
│   ├── Storage.php          # Settings and option storage
│   ├── Voter.php            # Device fingerprinting, rate limiting, HMAC CSRF
│   └── I18n.php             # Internationalization dictionary (EN, ES, CA)
├── assets/
│   ├── css/style.css        # Modern, responsive mobile-first styling
│   └── js/app.js            # Reactive asynchronous voting client
├── data/                    # Protected database storage directory
│   └── .htaccess            # Prevents direct web downloads of SQLite DB
├── screenshots/             # Visual showcases
└── tests/
    └── stress_test.php      # Concurrency & race condition benchmark suite
```

---

## 🛠️ Getting Started

### Requirements
- PHP 8.0 or higher
- PDO with SQLite (`pdo_sqlite`) or MySQL (`pdo_mysql`)
- cURL extension (for stress testing suite)

### Quick Start
1. Deploy `pure-php/` to any web directory.
2. Start PHP server:
   ```bash
   php -S localhost:8080 -t .
   ```
3. Access the voting page at `http://localhost:8080/` and administration at `http://localhost:8080/admin/`.
4. Run the benchmark:
   ```bash
   php tests/stress_test.php 200
   ```
