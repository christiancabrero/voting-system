# Voting System

## Pure PHP version & WordPress Plugin version

[![Dernière Version](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=Derni%C3%A8re%20Version)](https://github.com/christiancabrero/voting-system/releases/latest)
[![Version PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![Licence](https://img.shields.io/badge/License-GPLv2%20%2F%20MIT-green.svg)](#licence)
[![Langues](https://img.shields.io/badge/i18n-7%20Langues%20(EN%20%7C%20ES%20%7C%20CA%20%7C%20PT%20%7C%20FR%20%7C%20JA%20%7C%20ZH)-purple.svg)](#internationalisation-i18n)
[![Concurrence](https://img.shields.io/badge/Throughput-750%2B%20req%2Fsec-success.svg)](#performance-et-concurrence)

**Voting System** est une architecture de vote prête pour la production à double édition, conçue tant pour les sites WordPress que pour les environnements autonomes à très fort trafic :

1. [**Édition Plugin WordPress**](wordpress-plugin/): Plugin WordPress complet avec commutateur Mode Simple / Avancé, votes clos avec candidats visibles, cartes photos nettes sans flou avec bouton de vote ancré en bas, empreinte numérique antifraude et hydratation AJAX 100 % compatible avec les caches (WP Super Cache, WP Rocket, LiteSpeed).
2. [**Édition Pure PHP Haute Concurrence**](pure-php/): Moteur autonome ultra-léger propulsé par une file d'attente SQLite WAL (Producteur-Consommateur), console d'administration sécurisée par session, fichiers de traduction modulaires (`languages/*.php`) et sérialisation atomique ACID traitant **plus de 750 requêtes simultanées par seconde** sans verrouillage.

---

## 📖 Documentation Multilingue

- 🇬🇧 **[English Documentation](README.md)**
- 🇪🇸 **[Documentación en Español](README.es.md)**
- 🏴 **[Documentació en Català](README.ca.md)**
- 🇵🇹 **[Documentação em Português](README.pt.md)**
- 🇫🇷 **[Documentation en Français (Actuelle)](README.fr.md)**
- 🇯🇵 **[日本語ドキュメント](README.ja.md)**
- 🇨🇳 **[中文文档](README.zh.md)**

---

## 📥 Téléchargements et Versions

Téléchargez toujours la version stable la plus récente directement depuis :
👉 **[Télécharger la Dernière Version (Releases)](https://github.com/christiancabrero/voting-system/releases/latest)**

---

## 📸 Galerie Visuelle (Les Deux Éditions)

### 🌟 1. Édition Plugin WordPress

![01. Frontend Ouvert (Mode Simple)](screenshots/01-wp-frontend-open-simple-mode.png)
*01. Frontend Ouvert (Mode Simple) — Photographies nettes sans flou, catégorie distincte au-dessus du bouton et action de vote toujours positionnée en bas de carte.*

![02. État Post-Vote (Épuisé)](screenshots/02-wp-vote-cast-exhausted-state.png)
*02. État Post-Vote (Épuisé) — Confirmation dynamique instantanée ; candidat voté affichant « ✓ Voté » et autres boutons désactivés.*

![03. Vote Clos avec Candidats Visibles](screenshots/03-wp-voting-closed-participants-visible.png)
*03. Vote Clos avec Candidats Visibles — Bannière explicative de clôture tout en conservant les participants visibles et nets (floutage optionnel).*

![04. Paramètres et Commutateur de Mode](screenshots/04-wp-backend-simple-mode-switcher.png)
*04. Paramètres et Commutateur de Mode — Sélection intuitive entre Mode Simple (1 vote par votant) et Mode Avancé avec limites par catégorie.*

![05. Guide de Compatibilité Cache](screenshots/05-wp-backend-cache-compatibility-guide.png)
*05. Guide de Compatibilité Cache — Documentation intégrée à l'administration détaillant l'hydratation asynchrone des nonces et états.*

![08. Résultats et Dépouillement en Direct](screenshots/08-wp-admin-results-breakdown.png)
*08. Résultats et Dépouillement en Direct — Décompte en temps réel, barres de pourcentage et statistiques des candidats sur WordPress.*

![09. Audit des Votes](screenshots/09-wp-admin-audit-votes.png)
*09. Audit des Votes — Journal inviolable enregistrant l'identifiant de l'appareil, l'adresse IP, le jeton et l'horodatage.*

![10. Sécurité et Antifraude](screenshots/10-wp-admin-security-settings.png)
*10. Sécurité et Antifraude — Listes d'adresses IP autorisées, régulation du débit de requêtes et limite maximale de votants.*

---

### ⚡ 2. Édition Pure PHP Haute Concurrence

![06. Interface Publique Autonome](screenshots/06-pure-php-frontend-live.png)
*06. Interface Publique Autonome — Interface moderne avec sélecteur de 7 langues (EN, ES, CA, PT, FR, JP, CN), classement en direct et cartes nettes.*

![11. Écran de Connexion Administration](screenshots/11-pure-php-admin-login.png)
*11. Écran de Connexion Administration — Authentification sécurisée avec instructions de récupération d'accès.*

![07. Console d'Administration Web](screenshots/07-pure-php-admin-dashboard.png)
*07. Console d'Administration Web — Surveillance de la file WAL, gestion des modes, purge manuelle et administration des candidats.*

![12. Journal d'Ingestion en Temps Réel](screenshots/12-pure-php-audit-log.png)
*12. Journal d'Ingestion en Temps Réel — Suivi immédiat des votes traités et en attente avec empreintes numériques.*

---

## 📝 Documentation des Shortcodes WordPress

### 1. `[voting_form]`
Génère la grille publique de cartes de vote.

| Attribut | Type | Défaut | Description |
| :--- | :--- | :--- | :--- |
| `category` | `string` | `""` | Filtre par catégorie spécifique (ex. `category="Simpson"`). |
| `type` | `string` | `""` | Filtre par type (ex. `type="Family"`). |
| `limit` | `int` | `0` | Remplace la limite de vote pour cette instance (`0` utilise la valeur globale). |
| `columns` | `int` | `3` | Nombre de colonnes sur ordinateur (`2`, `3` ou `4`). |
| `show_results` | `int` | `0` | Définir à `1` pour afficher les résultats directement sous la grille. |

### 2. `[voting_results]`
Affiche le décompte validé et les pourcentages en temps réel.

| Attribut | Type | Défaut | Description |
| :--- | :--- | :--- | :--- |
| `category` | `string` | `""` | Filtre les résultats par catégorie. |
| `type` | `string` | `""` | Filtre les résultats par type. |
| `show_percentages` | `int` | `1` | Affiche ou masque les barres de pourcentage. |
| `show_numbers` | `int` | `1` | Affiche ou masque les compteurs numériques de votes. |
| `show_breakdown` | `int` | `1` | Affiche le détail complet par candidat. |

---

## 🔐 Accès Administration et Récupération Pure PHP

- **URL d'accès**: `http://votre-domaine/pure-php/admin/`
- **Identifiant**: `admin`
- **Mot de passe**: `admin123`

### Configuration et Récupération des Identifiants
Gérés de manière centralisée dans `pure-php/config.php` :
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');
```
Pour réinitialiser votre accès ou modifier les identifiants, éditez simplement `pure-php/config.php` via FTP, SSH ou votre gestionnaire de fichiers.

---

## 🌍 Internationalisation (i18n)

Prise en charge intégrale de **7 langues** :
`en`, `es`, `ca`, `pt`, `fr`, `ja`, `zh`.
Fichiers modulaires modifiables dans `pure-php/languages/` et `includes/dictionary.php`.

---

## 📄 Licence

- Édition Plugin WordPress: **GPLv2**.
- Édition Pure PHP: **Licence MIT**.
