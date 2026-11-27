# Voting System (Edición Pure PHP de Alta Concurrencia)

[![Última Versión](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=Última Versión )](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![SQLite WAL](https://img.shields.io/badge/Storage-SQLite%20WAL-success.svg)](https://sqlite.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](#license)

---

## 📥 [Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)

---

## 📸 Galería Visual

![01. Public Reactive Voting Interface](screenshots/01-pure-php-frontend.png)
*01. Interfaz Pública Reactiva — Selector de 7 idiomas (EN, ES, CA, PT, FR, JP, CN), ranking en vivo y tarjetas nítidas.*

![11. Admin Login Gate](screenshots/11-pure-php-admin-login.png)
*11. Acceso al Panel de Administración — Control seguro por credenciales con instrucciones claras de configuración y recuperación.*

![02. Web Administration Console](screenshots/02-pure-php-admin-dashboard.png)
*02. Consola de Administración Web — Monitor de cola WAL, conmutador de modos, vaciado manual de cola y participantes.*

![12. Real-Time Audit Log](screenshots/12-pure-php-audit-log.png)
*12. Registro de Ingesta y Auditoría — Monitor en tiempo real de votos procesados y en cola con huellas digitales.*

---

## 🔐 Acceso al Panel de Administración y Recuperación

La versión Pure PHP incluye una consola de administración protegida en `/admin/`.
- **URL por defecto**: `http://tu-dominio/pure-php/admin/`
- **Usuario por defecto**: `admin`
- **Contraseña por defecto**: `admin123`

### Configuración y Recuperación de Accesos
Las credenciales se gestionan en `pure-php/config.php`:
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
```
Para cambiar credenciales o recuperar el acceso en caso de olvido, edita `ADMIN_USER` y `ADMIN_PASS` en `pure-php/config.php` mediante FTP, SSH o gestor de archivos.

---

## 🌍 Internacionalización (i18n)
Disponible en 7 idiomas: inglés (en), español (es), catalán (ca), portugués (pt), francés (fr), japonés (ja) y chino (zh).
Fácilmente extensible mediante archivos modulares en `languages/{lang}.php`.

---

## 📄 Licencia
- Licencia MIT
