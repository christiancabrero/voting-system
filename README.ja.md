# Voting System

## Pure PHP version & WordPress Plugin version

[![最新リリース](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=%E6%9C%80%E6%96%B0%E3%83%AA%E3%83%AA%E3%83%BC%E3%82%B9)](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHPバージョン](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![ライセンス](https://img.shields.io/badge/License-GPLv2%20%2F%20MIT-green.svg)](#ライセンス)
[![対応言語](https://img.shields.io/badge/i18n-7%E8%A8%80%E8%AA%9E%20(EN%20%7C%20ES%20%7C%20CA%20%7C%20PT%20%7C%20FR%20%7C%20JA%20%7C%20ZH)-purple.svg)](#多言語対応-i18n)
[![高並行処理能力](https://img.shields.io/badge/Throughput-750%2B%20req%2Fsec-success.svg)](#高スループット性能)

**Voting System** は、WordPress エコシステムおよび超高並行処理が求められるスタンドアロン環境の両方に向けて設計された、本番運用対応のデュアルエディション投票システムアーキテクチャです。

1. [**WordPress プラグイン版**](wordpress-plugin/): シンプル/アドバンスド切り替え、投票終了時の候補者表示、ぼかしのない高精細写真カード、カード最下部に配置された投票ボタン、不正防止端末指紋認証、100%キャッシュ互換の動的AJAXハイドレーション（WP Super Cache、WP Rocket、LiteSpeed）を備えたフル機能プラグイン。
2. [**Pure PHP 高並行処理版**](pure-php/): 生産者・消費者（Producer-Consumer）モデルの SQLite WAL キュー、セッション保護された管理コンソール、モジュール式言語ファイル（`languages/*.php`）、ロック競合なく**毎秒750件以上の同時リクエスト**を安全に処理するACIDシリアライゼーションを搭載した軽量スタンドアロンエンジン。

---

## 📖 多言語ドキュメント

- 🇬🇧 **[English Documentation](README.md)**
- 🇪🇸 **[Documentación en Español](README.es.md)**
- 🏴 **[Documentació en Català](README.ca.md)**
- 🇵🇹 **[Documentação em Português](README.pt.md)**
- 🇫🇷 **[Documentation en Français](README.fr.md)**
- 🇯🇵 **[日本語ドキュメント（現在）](README.ja.md)**
- 🇨🇳 **[中文文档](README.zh.md)**

---

## 📥 ダウンロードとリリース

最新の安定版パッケージは常に以下から直接ダウンロードしてください。
👉 **[最新リリースをダウンロード](https://github.com/christiancabrero/voting-system/releases/latest)**

---

## 📸 ビジュアルギャラリー

### 🌟 1. WordPress プラグイン版

![01. フロントエンド（シンプルモード受付中）](screenshots/01-wp-frontend-open-simple-mode.png)
*01. フロントエンド（シンプルモード受付中） — ぼかしのない鮮明な候補者写真、ボタン上部に独立配置されたカテゴリタグ、カード最下部に固定された投票ボタン。*

![02. 投票後（投票権行使完了状態）](screenshots/02-wp-vote-cast-exhausted-state.png)
*02. 投票後（投票権行使完了状態） — リアルタイム確認表示；投票先カードに「✓ 投票済み」が表示され、残りのボタンは自動ロック。*

![03. 投票終了時の候補者表示](screenshots/03-wp-voting-closed-participants-visible.png)
*03. 投票終了時の候補者表示 — 投票締切通知バナーを表示しつつ、すべての候補者写真と情報を鮮明に公開（ぼかしは任意設定）。*

![04. 管理画面モード切り替え設定](screenshots/04-wp-backend-simple-mode-switcher.png)
*04. 管理画面モード切り替え設定 — シンプルモード（1人1票）とカテゴリ別上限を設定できるアドバンスドモードの対話型切り替え。*

![05. キャッシュ互換性ガイド](screenshots/05-wp-backend-cache-compatibility-guide.png)
*05. キャッシュ互換性ガイド — 管理画面内に統合された、動的Nonceおよび投票者状態ハイドレーションの解説。*

![08. リアルタイム開票結果](screenshots/08-wp-admin-results-breakdown.png)
*08. リアルタイム開票結果 — 候補者ごとの得票数、得票率プログレスバー、集計一覧をリアルタイム表示。*

![09. 投票監査ログ](screenshots/09-wp-admin-audit-votes.png)
*09. 投票監査ログ — 端末識別子、IPアドレス、トークン、投票日時を改ざん防止形式で記録。*

![10. セキュリティ・不正対策設定](screenshots/10-wp-admin-security-settings.png)
*10. セキュリティ・不正対策設定 — IPホワイトリスト/ブラックリスト、リクエスト頻度制限、最大投票者数上限の設定。*

---

### ⚡ 2. Pure PHP 高並行処理版

![06. スタンドアロン公開画面](screenshots/06-pure-php-frontend-live.png)
*06. スタンドアロン公開画面 — 7言語セレクター（EN、ES、CA、PT、FR、JP、CN）、リアルタイム順位、クリアな候補者カード。*

![11. 管理コンソールログイン画面](screenshots/11-pure-php-admin-login.png)
*11. 管理コンソールログイン画面 — 認証情報による安全なアクセス制御と設定・復旧案内。*

![07. Web管理コンソール](screenshots/07-pure-php-admin-dashboard.png)
*07. Web管理コンソール — WALキュー監視、モード切り替え、手動キュードレイン実行、候補者管理。*

![12. リアルタイム監査ログ](screenshots/12-pure-php-audit-log.png)
*12. リアルタイム監査ログ — 処理済みおよび待機中の投票を端末フィンガープリント付きで即時追跡。*

---

## 📝 WordPress ショートコード仕様

### 1. `[voting_form]`
公開投票カード一覧を出力します。

| 属性 | 型 | 初期値 | 説明 |
| :--- | :--- | :--- | :--- |
| `category` | `string` | `""` | 特定のカテゴリーで候補者を絞り込みます（例: `category="Simpson"`）。 |
| `type` | `string` | `""` | タイプで候補者を絞り込みます（例: `type="Family"`）。 |
| `limit` | `int` | `0` | このフォーム固有の投票上限を設定（`0`で全体設定を適用）。 |
| `columns` | `int` | `3` | PC画面でのグリッド列数（`2`、`3`、または `4`）。 |
| `show_results` | `int` | `0` | `1` を指定するとフォーム直下に結果を埋め込みます。 |

### 2. `[voting_results]`
集計結果および得票率を表示します。

| 属性 | 型 | 初期値 | 説明 |
| :--- | :--- | :--- | :--- |
| `category` | `string` | `""` | カテゴリーで結果を絞り込み。 |
| `type` | `string` | `""` | タイプで結果を絞り込み。 |
| `show_percentages` | `int` | `1` | 得票率バーの表示/非表示（`1` または `0`）。 |
| `show_numbers` | `int` | `1` | 得票数の表示/非表示（`1` または `0`）。 |
| `show_breakdown` | `int` | `1` | 候補者別の詳細内訳の表示。 |

---

## 🔐 Pure PHP 管理パネルアクセスと復旧方法

- **管理画面URL**: `http://your-domain/pure-php/admin/`
- **初期ユーザー名**: `admin`
- **初期パスワード**: `admin123`

### 認証情報の設定とアクセス復旧
設定は `pure-php/config.php` 内で集中管理されています。
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123'); // 本番環境では強固なパスワードに変更してください
```
アクセス情報を紛失または変更する場合は、FTPやファイルマネージャーから `pure-php/config.php` を開き、定数 `ADMIN_PASS` を更新するだけで即時反映されます。

---

## 🌍 多言語対応 (i18n)

**7言語**を標準サポート：
英語（`en`）、スペイン語（`es`）、カタルーニャ語（`ca`）、ポルトガル語（`pt`）、フランス語（`fr`）、日本語（`ja`）、中国語（`zh`）。

### ファイルによる容易な翻訳追加
`pure-php/languages/` 内の個別ファイル（例: `ja.php`）を編集・追加するだけで、簡単に新しい言語へローカライズ可能です。

---

## 📄 ライセンス

- WordPress プラグイン版: **GNU General Public License v2 (GPLv2)**
- Pure PHP 高並行処理版: **MIT License**
