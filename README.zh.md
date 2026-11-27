# Voting System

## Pure PHP version & WordPress Plugin version

[![最新版本](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=%E6%9C%80%E6%96%B0%E7%89%88%E6%9C%AC)](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP版本](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![开源协议](https://img.shields.io/badge/License-GPLv2%20%2F%20MIT-green.svg)](#开源协议)
[![国际化语言](https://img.shields.io/badge/i18n-7%E7%A7%8D%E8%AF%AD%E8%A8%80%20(EN%20%7C%20ES%20%7C%20CA%20%7C%20PT%20%7C%20FR%20%7C%20JA%20%7C%20ZH)-purple.svg)](#国际化多语言支持-i18n)
[![高并发吞吐量](https://img.shields.io/badge/Throughput-750%2B%20req%2Fsec-success.svg)](#性能与高并发)

**Voting System** 是一套面向生产环境设计的双版本投票系统架构，专为 WordPress 内容管理生态系统以及超高并发独立部署环境量身打造：

1. [**WordPress 插件版**](wordpress-plugin/)：功能完备的 WP 插件，支持简易/高级模式切换、投票截止时依然展示候选人、去虚化的高清选手照片、固定在卡片底部的投票按钮、防刷票设备指纹追踪，以及 100% 兼容各类全页缓存（WP Super Cache、WP Rocket、LiteSpeed）的动态 AJAX 水合架构。
2. [**Pure PHP 纯代码高并发版**](pure-php/)：基于生产者-消费者（Producer-Consumer）模型的 SQLite WAL 内存队列独立引擎，支持 Session 安全验证管理后台、基于文件的独立语言包（`languages/*.php`），具备完全无锁冲突的 ACID 原子性串行化能力，轻松承载**每秒 750+ 次连续高并发投票写入**。

---

## 📖 多语言文档导航

- 🇬🇧 **[English Documentation](README.md)**
- 🇪🇸 **[Documentación en Español](README.es.md)**
- 🏴 **[Documentació en Català](README.ca.md)**
- 🇵🇹 **[Documentação em Português](README.pt.md)**
- 🇫🇷 **[Documentation en Français](README.fr.md)**
- 🇯🇵 **[日本語ドキュメント](README.ja.md)**
- 🇨🇳 **[中文文档（当前）](README.zh.md)**

---

## 📥 下载与最新版本

请始终通过下方官方链接下载最新的稳定发行包：
👉 **[下载最新版本 (Releases)](https://github.com/christiancabrero/voting-system/releases/latest)**

---

## 📸 功能实测截图

### 🌟 1. WordPress 插件版

![01. 前台开放投票（简易模式）](screenshots/01-wp-frontend-open-simple-mode.png)
*01. 前台开放投票（简易模式） — 选手照片清晰无模糊，分类标签独立排列在按钮上方，投票按钮固定在卡片底部。*

![02. 投票完成状态（名额用尽）](screenshots/02-wp-vote-cast-exhausted-state.png)
*02. 投票完成状态（名额用尽） — 动态实时响应；已投选手高亮展示“✓ 已投”，其余卡片投票按钮自动锁定。*

![03. 投票关闭状态（候选人依然可见）](screenshots/03-wp-voting-closed-participants-visible.png)
*03. 投票关闭状态（候选人依然可见） — 顶部显示截止提示横幅，所有候选人依然清晰展示（虚化效果为可配置项）。*

![04. 管理后台模式切换与参数配置](screenshots/04-wp-backend-simple-mode-switcher.png)
*04. 管理后台模式切换与参数配置 — 简易模式（严格每人1票）与高级多分类投票模式一键切换。*

![05. 缓存插件兼容性设置指南](screenshots/05-wp-backend-cache-compatibility-guide.png)
*05. 缓存插件兼容性设置指南 — 后台内置指南，详细说明动态 Nonce 鉴权与用户投票状态异步加载机制。*

![08. 实时票数与开票结果明细](screenshots/08-wp-admin-results-breakdown.png)
*08. 实时票数与开票结果明细 — WordPress 后台直观展示得票数、百分比进度条与选手排名。*

![09. 投票审计与风控追踪日志](screenshots/09-wp-admin-audit-votes.png)
*09. 投票审计与风控追踪日志 — 独立记录选票的设备指纹识别码、客户端 IP、投票 Token 与精确时间戳。*

![10. 安全与防刷票策略设置](screenshots/10-wp-admin-security-settings.png)
*10. 安全与防刷票策略设置 — IP 白名单/黑名单过滤、请求速率限制以及最大选民数配额控制。*

---

### ⚡ 2. Pure PHP 纯代码高并发版

![06. 独立公开前台界面](screenshots/06-pure-php-frontend-live.png)
*06. 独立公开前台界面 — 现代化响应式卡片，支持 7 种语言切换（EN、ES、CA、PT、FR、JP、CN），实时榜单排名。*

![11. 管理后台登录验证网关](screenshots/11-pure-php-admin-login.png)
*11. 管理后台登录验证网关 — 安全的凭证登录界面，内置配置与找回说明。*

![07. Web 管理控制台](screenshots/07-pure-php-admin-dashboard.png)
*07. Web 管理控制台 — WAL 队列状态监控、模式切换、手动执行队列消费与参赛选手管理。*

![12. 实时审计日志监控](screenshots/12-pure-php-audit-log.png)
*12. 实时审计日志监控 — 毫秒级展示已处理及排队中的选票记录与设备识别指纹。*

---

## 📝 WordPress 简码（Shortcodes）使用文档

插件提供两个高性能且兼容全页缓存的简码：

### 1. `[voting_form]`
生成前台参赛选手投票卡片网格。

| 参数 | 类型 | 默认值 | 作用说明 |
| :--- | :--- | :--- | :--- |
| `category` | `string` | `""` | 按特定分类筛选选手（例如：`category="Simpson"`）。 |
| `type` | `string` | `""` | 按类型筛选选手（例如：`type="Family"`）。 |
| `limit` | `int` | `0` | 覆盖当前表单实例的投票上限（`0` 表示采用全局配置）。 |
| `columns` | `int` | `3` | 桌面端卡片网格列数（支持 `2`、`3` 或 `4`）。 |
| `show_results` | `int` | `0` | 设为 `1` 时在投票网格正下方直接内嵌开票结果。 |

**使用示例：**
```text
[voting_form category="Simpson" columns="4"]
```

### 2. `[voting_results]`
展示验证后的实时计票结果与百分比。

| 参数 | 类型 | 默认值 | 作用说明 |
| :--- | :--- | :--- | :--- |
| `category` | `string` | `""` | 按分类筛选开票结果。 |
| `type` | `string` | `""` | 按类型筛选开票结果。 |
| `show_percentages` | `int` | `1` | 显示或隐藏百分比进度条（`1` 或 `0`）。 |
| `show_numbers` | `int` | `1` | 显示或隐藏得票数值计数（`1` 或 `0`）。 |
| `show_breakdown` | `int` | `1` | 显示选手详细得分列表。 |

**使用示例：**
```text
[voting_results show_percentages="1" show_numbers="1"]
```

---

## 🔐 Pure PHP 管理后台访问与凭证配置/恢复

Pure PHP 版本在 `/admin/` 提供了独立的后台管理控制台。

### 默认管理员凭证
- **后台地址**：`http://your-domain/pure-php/admin/`
- **默认用户名**：`admin`
- **默认密码**：`admin123`

### 凭证配置与找回恢复
所有凭证由 `pure-php/config.php` 集中管理：
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123'); // 生产环境请修改为强密码
```
- **修改凭证**：直接在 `pure-php/config.php` 中修改 `ADMIN_USER` 和 `ADMIN_PASS` 常量。
- **找回密码/恢复访问**：若忘记密码，无需操作数据库，只需通过 FTP、SSH 或文件管理器编辑 `pure-php/config.php` 并重设 `ADMIN_PASS` 即可即时生效。

---

## 🌍 国际化多语言支持 (i18n)

系统全面原生支持 **7 种语言**：
- **英语 (`en`)**
- **西班牙语 (`es`)**
- **加泰罗尼亚语 (`ca`)**
- **葡萄牙语 (`pt`)**
- **法语 (`fr`)**
- **日语 (`ja`)**
- **中文 (`zh`)**

### 模块化语言包机制
- **Pure PHP**：语言字典以独立 PHP 文件存放在 `pure-php/languages/`（`en.php`、`zh.php` 等），新增语言只需复制 `en.php` 并翻译键值数组即可，无需数据库配置。
- **WordPress 插件**：在 `includes/dictionary.php` 与 `includes/i18n.php` 集中处理，自动适配站点语言、管理员语言偏好以及 WPML/Polylang 等多语言插件。

---

## 📄 开源协议

- WordPress 插件版：采用 **GNU General Public License v2 (GPLv2)** 开源许可。
- Pure PHP 纯代码高并发版：采用 **MIT License** 开源许可。
