# Voting System (Édition Pure PHP Haute Concurrence)

[![Dernière Version](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=Dernière Version )](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![SQLite WAL](https://img.shields.io/badge/Storage-SQLite%20WAL-success.svg)](https://sqlite.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](#license)

---

## 📥 [Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)

---

## 📸 Galerie Visuelle

![01. Public Reactive Voting Interface](screenshots/01-pure-php-frontend.png)
*01. Interface Publique Réactive — Sélecteur de 7 langues (EN, ES, CA, PT, FR, JP, CN), classement en direct et cartes nettes.*

![11. Admin Login Gate](screenshots/11-pure-php-admin-login.png)
*11. Connexion au Panneau d'Administration — Authentification sécurisée avec instructions de récupération.*

![02. Web Administration Console](screenshots/02-pure-php-admin-dashboard.png)
*02. Console d'Administration Web — Surveillance de la file WAL, gestion des modes, purge manuelle et candidats.*

![12. Real-Time Audit Log](screenshots/12-pure-php-audit-log.png)
*12. Journal d'Ingestion en Temps Réel — Suivi immédiat des votes traités et en attente avec empreintes.*

---

## 🔐 Accès Administration et Récupération

La version Pure PHP propose une console d'administration sur `/admin/`.
- **URL par défaut**: `http://votre-domaine/pure-php/admin/`
- **Identifiant par défaut**: `admin`
- **Mot de passe par défaut**: `admin123`

### Configuration et Récupération des Identifiants
Gérés dans `pure-php/config.php` :
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
```
Pour modifier les identifiants ou regagner l'accès, éditez simplement `pure-php/config.php` via FTP, SSH ou gestionnaire de fichiers.

---

## 🌍 Internationalisation (i18n)
Prise en charge intégrale de 7 langues : anglais (en), espagnol (es), catalan (ca), portugais (pt), français (fr), japonais (ja) et chinois (zh).
Fichiers modulaires modifiables dans `languages/{lang}.php`.

---

## 📄 Licence
- Licence MIT
