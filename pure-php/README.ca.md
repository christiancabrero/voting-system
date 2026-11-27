# Voting System (Edició Pure PHP d'Alta Concurrència)

[![Última Versió](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=Última Versió )](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![SQLite WAL](https://img.shields.io/badge/Storage-SQLite%20WAL-success.svg)](https://sqlite.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](#license)

---

## 📥 [Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)

---

## 📸 Galeria Visual

![01. Public Reactive Voting Interface](screenshots/01-pure-php-frontend.png)
*01. Interfície Pública Reactiva — Selector de 7 idiomes (EN, ES, CA, PT, FR, JP, CN), rànquing en temps real i targetes nítides.*

![11. Admin Login Gate](screenshots/11-pure-php-admin-login.png)
*11. Accés al Panell d'Administració — Control segur per credencials amb informació de recuperació.*

![02. Web Administration Console](screenshots/02-pure-php-admin-dashboard.png)
*02. Consola d'Administració Web — Monitorització de cua WAL, buidatge manual i gestió de participants.*

![12. Real-Time Audit Log](screenshots/12-pure-php-audit-log.png)
*12. Registre d'Auditoria en Temps Real — Registre d'ingesta ràpida amb identificadors de dispositiu.*

---

## 🔐 Accés al Panell d'Administració i Recuperació

La versió Pure PHP inclou una consola d'administració a `/admin/`.
- **URL per defecte**: `http://el-teu-domini/pure-php/admin/`
- **Usuari per defecte**: `admin`
- **Contrasenya per defecte**: `admin123`

### Configuració i Recuperació d'Accessos
Definides a `pure-php/config.php`:
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
```
Per modificar credencials o restablir l'accés en cas de pèrdua, només cal editar el fitxer `pure-php/config.php` mitjançant FTP, SSH o gestor d'arxius.

---

## 🌍 Internacionalització (i18n)
Completament operatiu en 7 idiomes: anglès (en), espanyol (es), català (ca), portuguès (pt), francès (fr), japonès (ja) i xinès (zh).
Facilitat de traducció mitjançant fitxers independents a `languages/{lang}.php`.

---

## 📄 Llicència
- Llicència MIT
