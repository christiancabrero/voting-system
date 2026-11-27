# Voting System

## Pure PHP version & WordPress Plugin version

[![Última Versión](https://img.shields.io/github/v/release/christiancabrero/voting-system?color=blue&label=%C3%9Altima%20Versi%C3%B3n)](https://github.com/christiancabrero/voting-system/releases/latest)
[![Versión de PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![Licencia](https://img.shields.io/badge/License-GPLv2%20%2F%20MIT-green.svg)](#licencia)
[![Idiomas](https://img.shields.io/badge/i18n-7%20Idiomas%20(EN%20%7C%20ES%20%7C%20CA%20%7C%20PT%20%7C%20FR%20%7C%20JA%20%7C%20ZH)-purple.svg)](#internacionalización-i18n)
[![Concurrencia](https://img.shields.io/badge/Throughput-750%2B%20req%2Fsec-success.svg)](#rendimiento-y-concurrencia)

**Voting System** es una arquitectura de votaciones de doble edición preparada para producción, diseñada tanto para ecosistemas basados en WordPress como para entornos independientes de altísima concurrencia:

1. [**Edición Plugin WordPress**](wordpress-plugin/): Plugin integral para WordPress con selector interactivo Modo Simple / Avanzado, votación cerrada con participantes visibles, tarjetas con fotos nítidas sin difuminar y botón de votar fijado al pie, huella digital de dispositivo contra el fraude e hidratación AJAX 100% compatible con caché (WP Super Cache, WP Rocket, LiteSpeed).
2. [**Edición Pure PHP de Alta Concurrencia**](pure-php/): Motor ligero e independiente impulsado por una cola de ingesta SQLite WAL (Productor-Consumidor), consola de administración protegida por sesión, traducciones modulares por archivos (`languages/*.php`) y serialización atómica ACID capaz de procesar **más de 750 solicitudes simultáneas por segundo** sin bloqueos de concurrencia.

---

## 📖 Documentación Multilingüe

- 🇬🇧 **[English Documentation](README.md)**
- 🇪🇸 **[Documentación en Español (Actual)](README.es.md)**
- 🏴 **[Documentació en Català](README.ca.md)**
- 🇵🇹 **[Documentação em Português](README.pt.md)**
- 🇫🇷 **[Documentation en Français](README.fr.md)**
- 🇯🇵 **[日本語ドキュメント](README.ja.md)**
- 🇨🇳 **[中文文档](README.zh.md)**

---

## 📥 Descargas y Versiones

Descarga siempre la versión estable más reciente directamente desde:
👉 **[Descargar Última Versión (Releases)](https://github.com/christiancabrero/voting-system/releases/latest)**

---

## 📸 Galería Visual (Ambas Ediciones)

### 🌟 1. Edición Plugin WordPress

![01. Frontend Abierto (Modo Simple)](screenshots/01-wp-frontend-open-simple-mode.png)
*01. Frontend Abierto (Modo Simple) — Fotografías nítidas sin difuminado, etiqueta de categoría separada y botón de votar siempre en la parte inferior de la card.*

![02. Estado Tras Votar (Agotado)](screenshots/02-wp-vote-cast-exhausted-state.png)
*02. Estado Tras Votar (Agotado) — Confirmación dinámica inmediata; el candidato votado muestra "✓ Votado" y los botones restantes se bloquean.*

![03. Votaciones Cerradas con Participantes](screenshots/03-wp-voting-closed-participants-visible.png)
*03. Votaciones Cerradas con Participantes — Cartel de aviso de cierre manteniendo a todos los participantes visibles y nítidos (el difuminado es una opción configurable).*

![04. Panel de Ajustes y Selector de Modo](screenshots/04-wp-backend-simple-mode-switcher.png)
*04. Panel de Ajustes y Selector de Modo — Selector intuitivo entre Modo Simple (1 voto) y Modo Avanzado con límites por categoría o tipo.*

![05. Guía de Compatibilidad con Caché](screenshots/05-wp-backend-cache-compatibility-guide.png)
*05. Guía de Compatibilidad con Caché — Instrucciones integradas en el panel de administración sobre la hidratación dinámica de nonces y estado del votante.*

![08. Escrutinio y Resultados en Directo](screenshots/08-wp-admin-results-breakdown.png)
*08. Escrutinio y Resultados en Directo — Desglose de votos por opción, porcentajes y barras de participación en tiempo real en WordPress.*

![09. Auditoría de Votos](screenshots/09-wp-admin-audit-votes.png)
*09. Auditoría de Votos — Registro de auditoría individual registrando identificador de dispositivo, IP, token y fecha de cada voto.*

![10. Seguridad y Control Antifraude](screenshots/10-wp-admin-security-settings.png)
*10. Seguridad y Control Antifraude — Restricción por listas de IP, control de tasa de peticiones y límites máximos de votantes.*

---

### ⚡ 2. Edición Pure PHP de Alta Concurrencia

![06. Interfaz Pública Standalone](screenshots/06-pure-php-frontend-live.png)
*06. Interfaz Pública Standalone — Interfaz reactiva con selector de 7 idiomas (EN, ES, CA, PT, FR, JP, CN), ranking en vivo y tarjetas nítidas sin difuminar.*

![11. Pantalla de Acceso al Panel de Administración](screenshots/11-pure-php-admin-login.png)
*11. Pantalla de Acceso al Panel de Administración — Control de acceso seguro por credenciales con instrucciones claras de configuración y recuperación.*

![07. Consola de Administración Web](screenshots/07-pure-php-admin-dashboard.png)
*07. Consola de Administración Web — Monitor de cola WAL, conmutador de modos, vaciado manual de la cola y gestión de participantes.*

![12. Registro de Ingesta y Auditoría](screenshots/12-pure-php-audit-log.png)
*12. Registro de Ingesta y Auditoría — Monitor en tiempo real de votos procesados y en cola con huellas digitales de dispositivo.*

---

## 📝 Documentación de Shortcodes en WordPress

El plugin de WordPress proporciona dos shortcodes flexibles y compatibles con caché:

### 1. `[voting_form]`
Renderiza la cuadrícula de tarjetas de votación pública.

| Atributo | Tipo | Por defecto | Descripción |
| :--- | :--- | :--- | :--- |
| `category` | `string` | `""` | Filtra los participantes por una categoría específica (ej. `category="Simpson"`). |
| `type` | `string` | `""` | Filtra por tipo (ej. `type="Family"`). |
| `limit` | `int` | `0` | Sobrescribe el límite de votos para esta instancia (`0` utiliza el ajuste global). |
| `columns` | `int` | `3` | Número de columnas en escritorio (`2`, `3` o `4`). |
| `show_results` | `int` | `0` | Mostrar `1` para incrustar los resultados justo debajo del formulario. |

**Ejemplo de uso:**
```text
[voting_form category="Simpson" columns="4"]
```

### 2. `[voting_results]`
Muestra el conteo de votos verificado y los porcentajes en tiempo real.

| Atributo | Tipo | Por defecto | Descripción |
| :--- | :--- | :--- | :--- |
| `category` | `string` | `""` | Filtra los resultados por categoría. |
| `type` | `string` | `""` | Filtra los resultados por tipo. |
| `show_percentages` | `int` | `1` | Muestra u oculta las barras de porcentaje (`1` o `0`). |
| `show_numbers` | `int` | `1` | Muestra u oculta los números de votos totales (`1` o `0`). |
| `show_breakdown` | `int` | `1` | Muestra el desglose detallado de participantes. |

**Ejemplo de uso:**
```text
[voting_results show_percentages="1" show_numbers="1"]
```

---

## 🔐 Acceso al Panel de Administración y Recuperación en Pure PHP

La versión Pure PHP incluye una consola de administración independiente en `/admin/`.

### Credenciales por Defecto
- **URL**: `http://tu-dominio/pure-php/admin/`
- **Usuario**: `admin`
- **Contraseña**: `admin123`

### Configuración y Recuperación de Accesos
Las credenciales están centralizadas en `pure-php/config.php`:
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123'); // Cambiar por una contraseña segura en producción
```
- **Para cambiar credenciales**: Edita `ADMIN_USER` y `ADMIN_PASS` en `pure-php/config.php`.
- **Para recuperar acceso perdido**: Abre `pure-php/config.php` mediante FTP, SSH o gestor de archivos y restablece la constante `ADMIN_PASS`. Los cambios se aplican de inmediato sin necesidad de base de datos externa ni reinicios.

---

## 🌍 Internacionalización (i18n)

Ambas versiones cuentan con soporte nativo para **7 idiomas**:
- **Inglés (`en`)**
- **Español (`es`)**
- **Catalán (`ca`)**
- **Portugués (`pt`)**
- **Francés (`fr`)**
- **Japonés (`ja`)**
- **Chino (`zh`)**

### Fácilmente Traducible Mediante Archivos
- **Pure PHP**: Las traducciones residen en archivos independientes dentro de `pure-php/languages/` (`en.php`, `es.php`, `ca.php`, `pt.php`, `fr.php`, `ja.php`, `zh.php`). Añadir un nuevo idioma requiere menos de 2 minutos duplicando cualquiera de ellos y editando los valores del array asociativo.
- **Plugin WordPress**: Las traducciones se gestionan en `includes/dictionary.php` e `includes/i18n.php`, detectando automáticamente el idioma del sitio, el perfil del usuario administrador o plugins multilingües como WPML o Polylang.

---

## 🚀 Inicio Rápido

### Opción A: Plugin WordPress (`wordpress-plugin/`)
1. Descarga el paquete de la versión más reciente en [Releases](https://github.com/christiancabrero/voting-system/releases/latest).
2. Súbelo a `wp-content/plugins/voting-system/` o instálalo desde **Plugins > Añadir nuevo > Subir plugin**.
3. Activa el plugin.
4. Accede a **Votaciones > Configuración** para configurar el Modo Simple o límites avanzados.
5. Inserta `[voting_form]` en cualquier página.

### Opción B: Standalone Pure PHP (`pure-php/`)
1. Descarga el archivo de la última versión en [Releases](https://github.com/christiancabrero/voting-system/releases/latest).
2. Descomprímelo en tu servidor web o ejecútalo en local:
   ```bash
   php -S 127.0.0.1:8088 -t pure-php/
   ```
3. Visita la interfaz pública en `http://localhost:8088/`.
4. Accede al panel de administración en `http://localhost:8088/admin/` (`admin` / `admin123`).
5. Procesa la cola WAL mediante cron o worker continuo:
   ```bash
   php pure-php/worker.php --once
   ```

---

## 📄 Licencia

- Edición Plugin WordPress: **GNU General Public License v2 (GPLv2)**.
- Edición Standalone Pure PHP: **Licencia MIT**.
