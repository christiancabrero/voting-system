# WordPress Voting System（现代竞赛投票插件版）

[![最新版本](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=最新版本 )](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](#license)

---

## 📥 [Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)

---

## 📸 功能截图

![01. Frontend Open](screenshots/01-frontend-open-simple-mode.png)
*01. 前台开放投票（简易模式） — 选手照片清晰无模糊，分类标签独立排列在按钮上方，投票按钮固定在卡片底部。*

![02. Post-Vote Exhausted State](screenshots/02-vote-cast-exhausted-state.png)
*02. 投票完成状态（名额用尽） — 动态实时响应；已投选手高亮展示“✓ 已投”，其余卡片投票按钮自动锁定。*

![03. Closed Voting with Participants](screenshots/03-voting-closed-participants-visible.png)
*03. 投票关闭状态（候选人依然可见） — 顶部显示截止提示横幅，所有候选人依然清晰展示（虚化效果为可配置项）。*

![04. Backend Simple / Advanced Mode Switch](screenshots/04-backend-simple-mode-switcher.png)
*04. 管理后台模式切换与参数配置 — 简易模式（严格每人1票）与高级多分类投票模式一键切换。*

![05. Cache Compatibility Guide](screenshots/05-backend-cache-compatibility-guide.png)
*05. 缓存插件兼容性设置指南 — 后台内置指南，详细说明动态 Nonce 鉴权与用户投票状态异步加载机制。*

![08. Results & Breakdown](screenshots/08-wp-admin-results-breakdown.png)
*08. 实时票数与开票结果明细 — WordPress 后台直观展示得票数、百分比进度条与选手排名。*

![09. Audit & Votes Trail](screenshots/09-wp-admin-audit-votes.png)
*09. 投票审计与风控追踪日志 — 独立记录选票的设备指纹识别码、客户端 IP、投票 Token 与精确时间戳。*

![10. Security Settings](screenshots/10-wp-admin-security-settings.png)
*10. 安全与防刷票策略设置 — IP 白名单/黑名单过滤、请求速率限制以及最大选民数配额控制。*

---

## 📝 简码（Shortcodes）使用文档

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

## 🌍 国际化多语言支持 (i18n)
系统全面原生支持 7 种语言：英语 (en)、西班牙语 (es)、加泰罗尼亚语 (ca)、葡萄牙语 (pt)、法语 (fr)、日语 (ja)、中文 (zh)。
语言字典以独立 PHP 文件存放在 `languages/{lang}.php`，扩展极其简便。

---

## 📄 开源协议
- GNU General Public License v2 (GPLv2)
