# WordPress Voting System（モダン投票コンテスト版）

[![最新リリース](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=最新リリース )](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](#license)

---

## 📥 [Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)

---

## 📸 ビジュアルギャラリー

![01. Frontend Open](screenshots/01-frontend-open-simple-mode.png)
*01. フロントエンド（シンプルモード受付中） — ぼかしのない鮮明な候補者写真、ボタン上部のカテゴリタグ、最下部に固定された投票ボタン。*

![02. Post-Vote Exhausted State](screenshots/02-vote-cast-exhausted-state.png)
*02. 投票後（投票権行使完了状態） — リアルタイム確認表示；投票先カードに「✓ 投票済み」が表示され、残りは自動ロック。*

![03. Closed Voting with Participants](screenshots/03-voting-closed-participants-visible.png)
*03. 投票終了時の候補者表示 — 投票締切通知バナーを表示しつつ、すべての候補者写真と情報を鮮明に公開。*

![04. Backend Simple / Advanced Mode Switch](screenshots/04-backend-simple-mode-switcher.png)
*04. 管理画面モード切り替え設定 — シンプルモード（1人1票）とカテゴリ別上限を設定できるアドバンスドモードの対話型切り替え。*

![05. Cache Compatibility Guide](screenshots/05-backend-cache-compatibility-guide.png)
*05. キャッシュ互換性ガイド — 管理画面内に統合された、動的Nonceおよび投票者状態ハイドレーションの解説。*

![08. Results & Breakdown](screenshots/08-wp-admin-results-breakdown.png)
*08. リアルタイム開票結果 — 候補者ごとの得票数、得票率プログレスバー、集計一覧をリアルタイム表示。*

![09. Audit & Votes Trail](screenshots/09-wp-admin-audit-votes.png)
*09. 投票監査ログ — 端末識別子、IPアドレス、トークン、投票日時を改ざん防止形式で記録。*

![10. Security Settings](screenshots/10-wp-admin-security-settings.png)
*10. セキュリティ・不正対策設定 — IPホワイトリスト/ブラックリスト、リクエスト頻度制限、最大投票者数上限の設定。*

---

## 📝 ショートコード仕様書

### 1. `[voting_form]`
- `category=""`: Filter contestants by category.
- `type=""`: Filter contestants by type.
- `limit="0"`: Custom vote quota for this form.
- `columns="3"`: Grid desktop columns (`2`, `3`, `4`).
- `show_results="0"`: Display results below voting cards.

### 2. `[voting_results]`
- `category=""`: Filter results by category.
- `type=""`: Filter results by type.
- `show_percentages="1"`: Show percentage bars.
- `show_numbers="1"`: Show total vote counts.
- `show_breakdown="1"`: Detailed contestants breakdown.

---

## 🌍 多言語対応 (i18n)
英語（en）、スペイン語（es）、カタルーニャ語（ca）、ポルトガル語（pt）、フランス語（fr）、日本語（ja）、中国語（zh）の7言語に対応。
`languages/{lang}.php` ファイルによる容易な言語追加が可能です。

---

## 📄 ライセンス
- GNU General Public License v2 (GPLv2)
