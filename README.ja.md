# Voting System - PHP & WordPress Plugin

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/License-GPLv2%20%2F%20MIT-green.svg)](#ライセンス)
[![Languages](https://img.shields.io/badge/i18n-EN%20%7C%20ES%20%7C%20CA-purple.svg)](#多言語対応)
[![Concurrency](https://img.shields.io/badge/Throughput-750%2B%20req%2Fsec-success.svg)](#パフォーマンスと並行処理)

**Voting System** は、WordPress CMS環境と超高並行処理を求める独立型Web環境の双方に向けて設計された、デュアルエディション構成の投票プラットフォームです:

1. [**WordPress プラグイン版 (`v3.4.0`)**](wordpress-plugin/): シンプル/アドバンス切り替えスイッチ、投票終了時の参加者一覧表示、プライバシー保護ぼかしアバター、各種キャッシュプラグイン（WP Super Cache、WP Rocket、LiteSpeed Cache）に100%適合する動的AJAXハイドレーションを完備したWordPress向け公式プラグイン。
2. [**高並行処理 Pure PHP 版 (`v2.0.0`)**](pure-php/): Producer-Consumerキュー（SQLite WAL）、PHPセッションロックフリーのHMAC CSRF保護、ACIDトランザクションシリアル化を採用し、**毎秒750件以上の同時リクエスト**をデータベース競合ゼロ・レースコンディション完全耐性で処理するフレームワーク不要の超高速エンジン。

---

## 📖 言語選択 / Translations / Documentación

- 🇬🇧 **[English Documentation](README.md)**
- 🇪🇸 **[Documentación en Español](README.es.md)**
- 🇯🇵 **[日本語ドキュメント（現在表示中）](README.ja.md)**

---

## 📸 スクリーンショット一覧（両バージョン）

### 🌟 1. WordPress プラグイン版 (`v3.4.0`)
| 画面 | 説明 | スクリーンショット |
| :--- | :--- | :--- |
| **01. シンプルモード（投票受付中）** | 1人1票限定、レスポンシブな参加者一覧グリッド | ![WP 受付中](screenshots/01-wp-frontend-open-simple-mode.png) |
| **02. 投票完了後（上限到達状態）** | 投票した候補者に「✓ Voted」を表示し他ボタンを無効化 | ![WP 投票完了](screenshots/02-wp-vote-cast-exhausted-state.png) |
| **03. 投票終了（参加者表示維持）** | 告知バナーを表示しつつ参加者情報を閲覧可能に維持 | ![WP 投票終了](screenshots/03-wp-voting-closed-participants-visible.png) |
| **04. 管理画面 シンプル/アドバンス切替** | 直感的なカード式UIで不要なカテゴリ設定を自動非表示 | ![WP 管理画面](screenshots/04-wp-backend-simple-mode-switcher.png) |
| **05. 管理画面 キャッシュ最適化ガイド** | WP Super Cache等の最適化手順を管理画面内に内蔵 | ![WP キャッシュガイド](screenshots/05-wp-backend-cache-compatibility-guide.png) |

### ⚡ 2. 高並行処理 Pure PHP 独立版 (`v2.0.0`)
| 画面 | 説明 | スクリーンショット |
| :--- | :--- | :--- |
| **06. スタンドアロン公開画面** | 言語切り替えバー付き（EN/ES/CA）超軽量リアクティブUI | ![Pure PHP フロントエンド](screenshots/06-pure-php-frontend-live.png) |
| **07. Web管理コンソール** | リアルタイムキュー監視、モード切替、ランキング、監査ログ | ![Pure PHP 管理画面](screenshots/07-pure-php-admin-dashboard.png) |

---

## 📦 プロジェクトのディレクトリ構成

```text
voting-system/
├── screenshots/               # 両エディションの画像一覧
├── wordpress-plugin/          # WordPress プラグイン版 (v3.4.0)
│   ├── assets/                # 管理画面・フロントエンド用 CSS / JS
│   ├── screenshots/           # 匿名化済みスクリーンショット
│   ├── voting-system.php      # WordPress プラグインのエントリーファイル
│   ├── README.md              # 英語ドキュメント
│   ├── README.es.md           # スペイン語ドキュメント
│   └── README.ja.md           # 日本語ドキュメント
├── pure-php/                  # 独立型 Pure PHP 版 (v2.0.0)
│   ├── api/                   # 投票・状態・統計APIエンドポイント
│   ├── admin/                 # 独立したWeb管理コンソール
│   ├── includes/              # DB（SQLite WAL）、キュー、有権者認証、i18n
│   ├── tests/                 # 200〜500件同時リクエストストレステスト
│   ├── worker.php             # CLIキュー処理デーモン & Cronランナー
│   ├── index.php              # コンクール仕様（ワインレッド）の投票画面
│   ├── README.md              # 英語ドキュメント
│   ├── README.es.md           # スペイン語ドキュメント
│   └── README.ja.md           # 日本語ドキュメント
├── README.md                  # プロジェクト全体概要（英語）
├── README.es.md               # プロジェクト全体概要（スペイン語）
└── README.ja.md               # プロジェクト全体概要（日本語）
```

---

## ⚡ エディション比較: どちらを選ぶべきか？

| 項目 | WordPress プラグイン版 (`v3.4.0`) | Pure PHP 独立版 (`v2.0.0`) |
| :--- | :--- | :--- |
| **対象プラットフォーム** | WordPress 6.0 以上 | 任意の PHP 8.0 以上サーバー（Nginx/Apache） |
| **依存関係** | WordPress コアおよび WPDB | 外部フレームワーク・依存ライブラリ ゼロ |
| **データベース** | MySQL / MariaDB | 高性能 SQLite WAL または MySQL PDO |
| **並行処理設計** | 静的HTMLキャッシュ + 動的AJAXハイドレーション | Producer-Consumer WAL キュー + ワーカー |
| **スループット性能** | キャッシュプラグインによる大量配信 | **毎秒755リクエスト以上の持続的インジェスチョン** |
| **レースコンディション防止**| DBスコープ検証による制御 | **ACIDトランザクションによるアトミック完全防止** |
| **シンプルモード** | 全体で1票（不要なカテゴリ設定を自動非表示） | 全体で1票（厳格な強制適用） |
| **投票終了時の候補者表示** | 対応（上部告知バナー + ボタン無効化） | 対応（上部告知バナー + ボタン無効化） |
| **多言語対応** | 英語（主言語）、スペイン語、カタルーニャ語 | 英語（主言語）、スペイン語、カタルーニャ語 |
| **デザイン仕様** | コンクールスタイル（ワインレッド `#6E1F2D`） | コンクールスタイル（ワインレッド `#6E1F2D`） |

---

## 🚀 クイックスタート

### A: WordPress プラグイン (`wordpress-plugin/`)
1. `wordpress-plugin/` フォルダを `wp-content/plugins/voting-system/` に配置します。
2. WordPress管理画面「プラグイン」から有効化します。
3. **投票システム > 設定** から動作モードや表示条件を設定します。
4. ショートコード `[voting_form]` を任意の固定ページ等に貼り付けます。

### B: Pure PHP 独立版 (`pure-php/`)
1. `pure-php/` ディレクトリをWebサーバーに配置します。
2. PHP開発用サーバーで起動する場合:
   ```bash
   php -S localhost:8080 -t pure-php/
   ```
3. 並行ストレステストを実行してスループットを計測:
   ```bash
   php pure-php/tests/stress_test.php 200
   ```
4. `http://localhost:8080/` で投票画面、`http://localhost:8080/admin/` で管理画面が開きます。

---

## 🏷️ タグ & キーワード

`php`, `wordpress`, `wordpress-plugin`, `voting-system`, `high-concurrency`, `sqlite-wal`, `producer-consumer`, `queue-worker`, `race-condition-safe`, `cache-friendly`, `wp-super-cache`, `wp-rocket`, `litespeed`, `i18n`, `multilingual`.

---

## 📄 ライセンス

- WordPress プラグイン版: **GNU General Public License v2 (GPLv2)** に基づき配布されます。
- Pure PHP 高並行処理版: **MIT License** に基づき配布されます。
