# WordPress Voting System (Edición Moderna de Competición)

[![Última Versión](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=Última Versión )](https://github.com/christiancabrero/voting-system/releases/latest)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](#license)

---

## 📥 [Download Latest Release](https://github.com/christiancabrero/voting-system/releases/latest)

---

## 📸 Galería Visual

![01. Frontend Open](screenshots/01-frontend-open-simple-mode.png)
*01. Frontend en Modo Simple (Abierto) — Fotografías nítidas sin difuminar, categoría destacada y botón de votar fijado al pie.*

![02. Post-Vote Exhausted State](screenshots/02-vote-cast-exhausted-state.png)
*02. Estado Tras Votar (Agotado) — Confirmación dinámica inmediata; candidato con '✓ Votado' y botones restantes bloqueados.*

![03. Closed Voting with Participants](screenshots/03-voting-closed-participants-visible.png)
*03. Votaciones Cerradas con Participantes Visibles — Cartel de aviso de cierre manteniendo participantes visibles y nítidos.*

![04. Backend Simple / Advanced Mode Switch](screenshots/04-backend-simple-mode-switcher.png)
*04. Selector de Modo Simple / Avanzado — Selector intuitivo entre Modo Simple (1 voto) y Modo Avanzado con límites.*

![05. Cache Compatibility Guide](screenshots/05-backend-cache-compatibility-guide.png)
*05. Guía de Compatibilidad con Caché — Instrucciones integradas en el panel sobre la hidratación dinámica con plugins de caché.*

![08. Results & Breakdown](screenshots/08-wp-admin-results-breakdown.png)
*08. Escrutinio y Resultados en Directo — Recuento en tiempo real, porcentajes y desglose por participante en WordPress.*

![09. Audit & Votes Trail](screenshots/09-wp-admin-audit-votes.png)
*09. Auditoría de Votos — Registro de auditoría individual con identificador de dispositivo, IP, token y fecha.*

![10. Security Settings](screenshots/10-wp-admin-security-settings.png)
*10. Seguridad y Control Antifraude — Restricción por listas de IP, control de tasa de peticiones y límites de votantes.*

---

## 📝 Documentación de Shortcodes

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

## 🌍 Internacionalización (i18n)
Disponible en 7 idiomas: inglés (en), español (es), catalán (ca), portugués (pt), francés (fr), japonés (ja) y chino (zh).
Fácilmente extensible mediante archivos modulares en `languages/{lang}.php`.

---

## 📄 Licencia
- GNU General Public License v2 (GPLv2)
