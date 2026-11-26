# Voting System (Monorepo)

[![Versión PHP](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)](https://wordpress.org)
[![Licencia](https://img.shields.io/badge/Licencia-GPLv2%20%2F%20MIT-green.svg)](#licencia)
[![Idiomas](https://img.shields.io/badge/i18n-EN%20%7C%20ES%20%7C%20CA-purple.svg)](#soporte-multilingüe)
[![Concurrencia](https://img.shields.io/badge/Throughput-750%2B%20req%2Fseg-success.svg)](#rendimiento-y-concurrencia)

**Voting System** es una arquitectura integral de votaciones en doble edición, desarrollada tanto para el ecosistema WordPress como para entornos independientes de altísimo volumen de tráfico:

1. [**Edición Plugin de WordPress (`v3.4.0`)**](wordpress-plugin/): Plugin completo para WordPress con switch interactivo Modo Simple / Avanzado, votaciones cerradas con participantes visibles, interfaz circular anonimizada y 100% de compatibilidad con plugins de caché (WP Super Cache, WP Rocket, LiteSpeed Cache) mediante hidratación AJAX dinámica.
2. [**Edición PHP Puro de Alta Concurrencia (`v2.0.0`)**](pure-php/): Motor ligero e independiente basado en el patrón Productor-Consumidor (cola SQLite WAL), tokens CSRF HMAC sin bloqueo de sesión PHP y serialización transaccional ACID capaz de procesar **más de 750 peticiones concurrentes por segundo** con cero bloqueos y protección total contra condiciones de carrera.

---

## 📖 Idiomas Disponibles

- 🇬🇧 **[English Documentation](README.md)**
- 🇪🇸 **[Documentación en Español (Actual)](README.es.md)**
- 🇯🇵 **[日本語ドキュメント](README.ja.md)**

---

## 📸 Muestra Visual (Ambas Versiones)

### 🌟 1. Edición Plugin para WordPress (`v3.4.0`)
| Vista | Descripción | Captura de Pantalla |
| :--- | :--- | :--- |
| **01. Frontend Abierto (Modo Simple)** | 1 voto global disponible por votante, cuadrícula responsive | ![WP Frontend Abierto](screenshots/01-wp-frontend-open-simple-mode.png) |
| **02. Estado tras Votar (Voto Agotado)** | Candidato marcado como "✓ Votado", botones restantes deshabilitados | ![WP Voto Emitido](screenshots/02-wp-vote-cast-exhausted-state.png) |
| **03. Votaciones Cerradas con Participantes** | Banner de aviso visible mientras los participantes siguen expuestos | ![WP Votaciones Cerradas](screenshots/03-wp-voting-closed-participants-visible.png) |
| **04. Switch Backend Modo Simple / Avanzado** | Tarjetas interactivas que ocultan opciones complejas de categoría | ![WP Switch Backend](screenshots/04-wp-backend-simple-mode-switcher.png) |
| **05. Guía de Caché en el Backend** | Documentación integrada para configuración con plugins de caché | ![WP Guía de Caché](screenshots/05-wp-backend-cache-compatibility-guide.png) |

### ⚡ 2. Edición PHP Puro de Alta Concurrencia (`v2.0.0`)
| Vista | Descripción | Captura de Pantalla |
| :--- | :--- | :--- |
| **06. Frontend Público Standalone** | Interfaz reactiva ultraligera con selector de idioma (EN/ES/CA) | ![Pure PHP Frontend](screenshots/06-pure-php-frontend-live.png) |
| **07. Panel de Administración Web** | Estado de cola en tiempo real, switch de modo, ranking y auditoría | ![Pure PHP Admin](screenshots/07-pure-php-admin-dashboard.png) |

---

## 📦 Estructura del Monorepo

```text
voting-system/
├── screenshots/               # Muestras visuales de ambas versiones
├── wordpress-plugin/          # Edición Plugin para WordPress (v3.4.0)
│   ├── assets/                # CSS y JS de administración y frontend
│   ├── screenshots/           # Capturas de pantalla anonimizadas
│   ├── voting-system.php      # Archivo principal del plugin de WordPress
│   ├── README.md              # Documentación en Inglés
│   ├── README.es.md           # Documentación en Español
│   └── README.ja.md           # Documentación en Japonés
├── pure-php/                  # Edición Standalone en PHP Puro (v2.0.0)
│   ├── api/                   # Endpoints de voto, estado y resultados
│   ├── admin/                 # Panel de administración web independiente
│   ├── includes/              # Motores de base de datos WAL, cola, votante e i18n
│   ├── tests/                 # Suite de estrés de 200 a 500 peticiones simultáneas
│   ├── worker.php             # Demonio CLI de cola y ejecutor cron
│   ├── index.php              # Interfaz de votación pública (Tema Burdeos Concurso)
│   ├── README.md              # Documentación en Inglés
│   ├── README.es.md           # Documentación en Español
│   └── README.ja.md           # Documentación en Japonés
├── README.md                  # Índice general en Inglés
├── README.es.md               # Índice general en Español
└── README.ja.md               # Índice general en Japonés
```

---

## ⚡ Comparativa: ¿Qué versión utilizar?

| Característica | Plugin de WordPress (`v3.4.0`) | PHP Puro Standalone (`v2.0.0`) |
| :--- | :--- | :--- |
| **Plataforma Objetivo** | WordPress 6.0+ | Cualquier servidor PHP 8.0+ |
| **Dependencias** | Núcleo de WordPress y WPDB | Cero frameworks ni dependencias |
| **Base de Datos** | MySQL / MariaDB | SQLite WAL de alto rendimiento o MySQL PDO |
| **Arquitectura de Concurrencia** | Hidratación AJAX dinámica de nonce y votos | Cola WAL Productor-Consumidor con Workers |
| **Capacidad de Ingesta** | HTML estático cacheado + AJAX dinámico | **755+ req/segundo sostenidas** |
| **Protección contra Carreras** | Validación estricta de alcance en DB | **Serialización transaccional atómica ACID** |
| **Modo Simple** | 1 voto global (oculta categorías) | 1 voto global (estrictamente aplicado) |
| **Cierre con Participantes Visibles**| Sí (aviso superior + botones bloqueados) | Sí (aviso superior + botones bloqueados) |
| **Idiomas Soportados** | Inglés (`en`, principal), Español (`es`), Catalán (`ca`) | Inglés (`en`, principal), Español (`es`), Catalán (`ca`) |
| **Diseño Visual** | Tema Burdeos Concurso (`#6E1F2D`) | Tema Burdeos Concurso (`#6E1F2D`) |

---

## 🚀 Guía Rápida

### Opción A: Plugin de WordPress (`wordpress-plugin/`)
1. Copia la carpeta `wordpress-plugin/` en el directorio `wp-content/plugins/voting-system/`.
2. Activa el plugin en **Plugins > Plugins instalados**.
3. Configura las opciones en **Votaciones > Ajustes**.
4. Inserta el shortcode `[voting_form]` en cualquier página o entrada.

### Opción B: PHP Puro Standalone (`pure-php/`)
1. Despliega la carpeta `pure-php/` en tu servidor web.
2. Inicia el servidor de pruebas:
   ```bash
   php -S localhost:8080 -t pure-php/
   ```
3. Ejecuta el test de concurrencia:
   ```bash
   php pure-php/tests/stress_test.php 200
   ```
4. Accede a la interfaz pública en `http://localhost:8080/` y al panel de administración en `http://localhost:8080/admin/`.

---

## 🏷️ Etiquetas y Tecnologías

`php`, `wordpress`, `wordpress-plugin`, `voting-system`, `high-concurrency`, `sqlite-wal`, `producer-consumer`, `queue-worker`, `race-condition-safe`, `cache-friendly`, `wp-super-cache`, `wp-rocket`, `litespeed`, `i18n`, `multilingual`.

---

## 📄 Licencia

- Edición Plugin de WordPress: Distribuida bajo **GNU General Public License v2 (GPLv2)**.
- Edición PHP Puro de Alta Concurrencia: Distribuida bajo la **Licencia MIT**.
