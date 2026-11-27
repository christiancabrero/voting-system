# Voting System（Pure PHP 高並行処理版）

[![最新リリース](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=最新リリース )](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![SQLite WAL](https://img.shields.io/badge/Storage-SQLite%20WAL-success.svg)](https://sqlite.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](#license)

---

## 📥 [Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)

---

## 📸 ビジュアルギャラリー

![01. Public Reactive Voting Interface](screenshots/01-pure-php-frontend.png)
*01. スタンドアロン公開画面 — 7言語セレクター（EN、ES、CA、PT、FR、JP、CN）、リアルタイム順位、クリアな候補者カード。*

![11. Admin Login Gate](screenshots/11-pure-php-admin-login.png)
*11. 管理コンソールログイン画面 — 認証情報による安全なアクセス制御と設定・復旧案内。*

![02. Web Administration Console](screenshots/02-pure-php-admin-dashboard.png)
*02. Web管理コンソール — WALキュー監視、モード切り替え、手動キュードレイン実行、候補者管理。*

![12. Real-Time Audit Log](screenshots/12-pure-php-audit-log.png)
*12. リアルタイム監査ログ — 処理済みおよび待機中の投票を端末フィンガープリント付きで即時追跡。*

---

## 🔐 管理パネルアクセスと復旧方法

Pure PHP 版には `/admin/` に専用管理コンソールが備わっています。
- **初期URL**: `http://your-domain/pure-php/admin/`
- **初期ユーザー名**: `admin`
- **初期パスワード**: `admin123`

### 認証情報の設定とアクセス復旧
設定は `pure-php/config.php` 内で集中管理されています。
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
```
アクセス情報を紛失または変更する場合は、FTPやファイルマネージャーから `pure-php/config.php` を開き、定数 `ADMIN_PASS` を更新してください。

---

## 🌍 多言語対応 (i18n)
英語（en）、スペイン語（es）、カタルーニャ語（ca）、ポルトガル語（pt）、フランス語（fr）、日本語（ja）、中国語（zh）の7言語に対応。
`languages/{lang}.php` ファイルによる容易な言語追加が可能です。

---

## 📄 ライセンス
- MIT License
