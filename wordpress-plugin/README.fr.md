# WordPress Voting System (Édition Compétition Moderne)

[![Dernière Version](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=Dernière Version )](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](#license)

---

## 📥 [Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)

---

## 📸 Galerie Visuelle

![01. Frontend Open](screenshots/01-frontend-open-simple-mode.png)
*01. Frontend en Mode Simple (Ouvert) — Photos nettes sans flou, catégorie distincte et bouton de vote au bas de la carte.*

![02. Post-Vote Exhausted State](screenshots/02-vote-cast-exhausted-state.png)
*02. État Post-Vote (Épuisé) — Confirmation dynamique immédiate ; candidat avec '✓ Voté' et boutons verrouillés.*

![03. Closed Voting with Participants](screenshots/03-voting-closed-participants-visible.png)
*03. Vote Clos avec Participants Visibles — Bannière d'alerte tout en maintenant les candidats visibles et nets.*

![04. Backend Simple / Advanced Mode Switch](screenshots/04-backend-simple-mode-switcher.png)
*04. Sélecteur de Mode Simple / Avancé — Sélection intuitive entre Mode Simple (1 vote) et Mode Avancé avec limites.*

![05. Cache Compatibility Guide](screenshots/05-backend-cache-compatibility-guide.png)
*05. Guide de Compatibilité Cache — Documentation intégrée sur l'hydratation asynchrone des nonces et états.*

![08. Results & Breakdown](screenshots/08-wp-admin-results-breakdown.png)
*08. Résultats et Dépouillement en Direct — Décompte en temps réel, pourcentages et statistiques sur WordPress.*

![09. Audit & Votes Trail](screenshots/09-wp-admin-audit-votes.png)
*09. Audit des Votes — Journal inviolable avec identifiant de l'appareil, IP, jeton et horodatage.*

![10. Security Settings](screenshots/10-wp-admin-security-settings.png)
*10. Sécurité et Antifraude — Listes d'adresses IP autorisées, régulation du débit et limite de votants.*

---

## 📝 Documentation des Shortcodes

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

## 🌍 Internationalisation (i18n)
Prise en charge intégrale de 7 langues : anglais (en), espagnol (es), catalan (ca), portugais (pt), français (fr), japonais (ja) et chinois (zh).
Fichiers modulaires modifiables dans `languages/{lang}.php`.

---

## 📄 Licence
- GNU General Public License v2 (GPLv2)
