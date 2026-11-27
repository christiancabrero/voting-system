# Voting System

## Pure PHP 独立版与 WordPress 插件版

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/License-GPLv2%20%2F%20MIT-green.svg)](#许可证)
[![Languages](https://img.shields.io/badge/i18n-EN%20%7C%20ES%20%7C%20CA-purple.svg)](#国际化支持)
[![Concurrency](https://img.shields.io/badge/Throughput-750%2B%20req%2Fsec-success.svg)](#性能与并发基准测试)

**Voting System** 是一套可直接用于生产环境的双版本投票系统架构，专为 WordPress 内容管理生态系统以及超高并发独立 Web 环境量身打造：

1. [**WordPress 插件版 (`v3.4.0`)**](wordpress-plugin/)：功能完备的 WordPress 官方插件，具备极简/高级模式无缝切换、投票截止后参赛者保持可见、圆形磨砂模糊隐私设计，以及 100% 兼容各类全页缓存插件（WP Super Cache、WP Rocket、LiteSpeed Cache）的动态 AJAX 状态水合技术。
2. [**高并发 Pure PHP 独立版 (`v2.0.0`)**](pure-php/)：零框架依赖的超轻量独立引擎，采用生产者-消费者（Producer-Consumer）WAL 队列架构、无会话锁定的 HMAC CSRF 防御机制以及 SQLite/MySQL ACID 事务串行化，支持 **每秒 750+ 次并发请求**，实现零数据库锁争用与 100% 竞态条件免疫。

---

## 📖 文档语言 / Translations / Documentación / ドキュメント

- 🇬🇧 **[English Documentation](README.md)**
- 🇪🇸 **[Documentación en Español](README.es.md)**
- 🇯🇵 **[日本語ドキュメント](README.ja.md)**
- 🇨🇳 **[中文文档（当前语言）](README.zh.md)**

---

## 📸 界面展示（两个版本）

### 🌟 1. WordPress 插件版 (`v3.4.0`)

![01. 前台开放投票（极简模式）](screenshots/01-wp-frontend-open-simple-mode.png)
*01. 前台开放投票（极简模式） — 每位投票者全局限投 1 票，自适应响应式网格布局*

![02. 投票完成后（投票额度耗尽状态）](screenshots/02-wp-vote-cast-exhausted-state.png)
*02. 投票完成后（投票额度耗尽状态） — 已投选项标记为“✓ Voted”，其余按钮自动锁定禁用*

![03. 投票截止后（参赛者依然可见）](screenshots/03-wp-voting-closed-participants-visible.png)
*03. 投票截止后（参赛者依然可见） — 顶部显示截止提示横幅，参赛者图文信息保持完整展示*

![04. 管理后台极简 / 高级模式切换](screenshots/04-wp-backend-simple-mode-switcher.png)
*04. 管理后台极简 / 高级模式切换 — 交互式卡片设计，激活极简模式时自动隐藏复杂分类限制*

![05. 缓存插件兼容性优化指南](screenshots/05-wp-backend-cache-compatibility-guide.png)
*05. 缓存插件兼容性优化指南 — 后台内置针对 WP Super Cache 等插件的配置与性能优化指引*

### ⚡ 2. 高并发 Pure PHP 独立版 (`v2.0.0`)

![06. 独立公开前台界面](screenshots/06-pure-php-frontend-live.png)
*06. 独立公开前台界面 — 带有语言切换栏（EN/ES/CA）的超轻量响应式投票界面*

![07. Web 管理控制台](screenshots/07-pure-php-admin-dashboard.png)
*07. Web 管理控制台 — 实时队列状态监控、模式切换、参赛者排名与完整审计日志*

---

## 📦 项目目录结构

```text
voting-system/
├── screenshots/               # 两个版本的界面展示截图
├── wordpress-plugin/          # WordPress 插件版 (v3.4.0)
│   ├── assets/                # 后台与前台 CSS/JS 资源
│   ├── screenshots/           # WP 匿名化界面截图
│   ├── voting-system.php      # WordPress 插件主入口文件
│   ├── README.md              # 英文文档
│   ├── README.es.md           # 西班牙文文档
│   ├── README.ja.md           # 日文文档
│   └── README.zh.md           # 中文文档
├── pure-php/                  # 独立 Pure PHP 版 (v2.0.0)
│   ├── api/                   # 高并发投票、状态水合与统计接口
│   ├── admin/                 # 独立的 Web 管理控制台
│   ├── includes/              # SQLite WAL 数据库、队列、指纹识别及国际化引擎
│   ├── tests/                 # 200 至 500 并发请求压力测试套件
│   ├── worker.php             # CLI 队列消费守护进程与 Cron 执行器
│   ├── index.php              # 公开投票页面（酒红竞赛质感主题）
│   ├── README.md              # 英文文档
│   ├── README.es.md           # 西班牙文文档
│   ├── README.ja.md           # 日文文档
│   └── README.zh.md           # 中文文档
├── README.md                  # 英文项目全局总览
├── README.es.md               # 西班牙文项目全局总览
├── README.ja.md               # 日文项目全局总览
└── README.zh.md               # 中文项目全局总览
```

---

## ⚡ 版本对比：应该选择哪个版本？

| 特性 | WordPress 插件版 (`v3.4.0`) | Pure PHP 独立版 (`v2.0.0`) |
| :--- | :--- | :--- |
| **目标平台** | WordPress 6.0 及以上 | 任意 PHP 8.0+ 服务器（Nginx/Apache/Caddy） |
| **外部依赖** | WordPress 核心与 WPDB | 零第三方框架与依赖 |
| **数据库** | MySQL / MariaDB | 高性能 SQLite WAL 或 MySQL PDO |
| **并发架构设计** | 动态 AJAX Nonce 与投票者状态水合 | 生产者-消费者 WAL 队列与后台 Worker |
| **吞吐能力** | 静态 HTML 全页缓存 + 异步 AJAX | **持续摄入 755+ 请求/秒** |
| **竞态条件免疫** | 严格数据库作用域配额验证 | **原子级 ACID 事务队列串行化处理** |
| **极简模式** | 全局限投 1 票（自动隐藏分类选项） | 全局限投 1 票（严格强力执行） |
| **截止后保留展示** | 支持（顶部提示横幅 + 按钮禁用） | 支持（顶部提示横幅 + 按钮禁用） |
| **支持的语言** | 英语（主语言）、西班牙语、加泰罗尼亚语 | 英语（主语言）、西班牙语、加泰罗尼亚语 |
| **视觉设计** | 经典酒红竞赛主题风格（`#6E1F2D`） | 经典酒红竞赛主题风格（`#6E1F2D`） |

---

## 🚀 快速入门

### 方案 A：WordPress 插件 (`wordpress-plugin/`)
1. 将 `wordpress-plugin/` 目录复制到 WordPress 的 `wp-content/plugins/voting-system/` 路径下。
2. 在 WordPress 后台 **插件 > 已安装插件** 中启用该插件。
3. 在 **投票系统 > 设置** 中配置相关选项（极简模式、展示规则等）。
4. 在任意页面或文章中使用简码 `[voting_form]` 嵌入投票表单。

### 方案 B：Pure PHP 独立版 (`pure-php/`)
1. 将 `pure-php/` 部署至任意 Web 服务器目录。
2. 启动 PHP 内置开发服务器：
   ```bash
   php -S localhost:8080 -t pure-php/
   ```
3. 运行并发压力测试套件：
   ```bash
   php pure-php/tests/stress_test.php 200
   ```
4. 访问前台投票界面 `http://localhost:8080/` 以及后台管理控制台 `http://localhost:8080/admin/`。

---

## 🏷️ 标签与关键词

`php`, `wordpress`, `wordpress-plugin`, `voting-system`, `high-concurrency`, `sqlite-wal`, `producer-consumer`, `queue-worker`, `race-condition-safe`, `cache-friendly`, `wp-super-cache`, `wp-rocket`, `litespeed`, `i18n`, `multilingual`.

---

## 📄 许可证

- WordPress 插件版：采用 **GNU General Public License v2 (GPLv2)** 协议开源。
- Pure PHP 高并发版：采用 **MIT License** 协议开源。
