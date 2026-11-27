# Voting System (Edição Pure PHP de Alta Concorrência)

[![Última Versão](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=Última Versão )](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![SQLite WAL](https://img.shields.io/badge/Storage-SQLite%20WAL-success.svg)](https://sqlite.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](#license)

---

## 📥 [Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)

---

## 📸 Galeria Visual

![01. Public Reactive Voting Interface](screenshots/01-pure-php-frontend.png)
*01. Interface Pública Reativa — Seletor de 7 idiomas (EN, ES, CA, PT, FR, JP, CN), classificação em direto e cartões nítidos.*

![11. Admin Login Gate](screenshots/11-pure-php-admin-login.png)
*11. Acesso à Consola de Administração — Autenticação segura por credenciais com instruções de recuperação.*

![02. Web Administration Console](screenshots/02-pure-php-admin-dashboard.png)
*02. Consola de Administração Web — Monitor de fila WAL, alternador de modos, esvaziamento manual e participantes.*

![12. Real-Time Audit Log](screenshots/12-pure-php-audit-log.png)
*12. Registo de Auditoria em Tempo Real — Acompanhamento em direto dos votos processados com impressões digitais.*

---

## 🔐 Acesso à Administração e Recuperação

A edição Pure PHP inclui uma consola de administração em `/admin/`.
- **URL predefinido**: `http://o-seu-dominio/pure-php/admin/`
- **Utilizador predefinido**: `admin`
- **Palavra-passe predefinida**: `admin123`

### Configuração e Recuperação
Geridas em `pure-php/config.php`:
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
```
Para alterar as credenciais ou recuperar o acesso, edite o ficheiro `pure-php/config.php` via FTP, SSH ou gestor de ficheiros.

---

## 🌍 Internacionalização (i18n)
Suporte em 7 idiomas: inglês (en), espanhol (es), catalão (ca), português (pt), francês (fr), japonês (ja) e chinês (zh).
Ficheiros modulares em `languages/{lang}.php`.

---

## 📄 Licença
- Licença MIT
