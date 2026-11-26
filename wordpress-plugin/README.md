# WordPress Voting System (CIM Barcelona Edition)

High-performance voting plugin for WordPress featuring **Simple Mode** (1 vote per device/voter), **Advanced Category Limits**, **Closed Voting with Visible Participants**, and **100% Cache-Compatible Dynamic AJAX Hydration** (WP Super Cache, WP Rocket, LiteSpeed Cache). Built with privacy protection, anti-fraud device fingerprinting, and internationalization (English, Spanish, Catalan).

---

## 🌟 Key Features

### 1. Simple Mode vs. Advanced Mode Switch
- **Simple Mode (1 vote per voter global)**:
  - Treats all contestants as a unified pool regardless of categories or types.
  - Strictly limits each voter/device to exactly **1 vote**.
  - Interactive backend switcher in `Settings > Voting System` toggles between Simple and Advanced modes.
  - When Simple Mode is activated, category/type limit fields and multi-vote options are automatically hidden in the backend to ensure a clean, distraction-free administration experience.
- **Advanced Mode**:
  - Set independent voting limits per category, per type, or global limits.
  - Optionally allows multiple votes per contestant.

### 2. Closed Voting with Visible Participants
- New option: **"Show participants when voting is closed"** (`vs_closed_show_participants`).
- When voting closes (manually or via scheduled start/end dates):
  - An informative status alert banner appears at the top.
  - All contestants remain 100% visible with their pictures, titles, categories, and types.
  - All voting buttons are locked (`disabled`) with `.vs-btn-closed` styling and localized text: **"Voting closed"** (EN) / **"Votaciones cerradas"** (ES) / **"Votacions tancades"** (CA).

### 3. High-Performance Static Caching & Dynamic AJAX Hydration
- **Problem Solved**: Full-page caching plugins often serve stale HTML with outdated vote counters (e.g., displaying *"0 of 1 votes cast"* to someone who already voted) or expired security nonces.
- **The Solution**:
  - The contestant grid, photos, and markup are **100% statically cached** in memory or on disk by WP Super Cache, WP Rocket, LiteSpeed Cache, or CDNs (Cloudflare), dramatically slashing server load under massive traffic.
  - Upon page load, an ultra-lightweight asynchronous endpoint (`vs_get_voter_state`) queries the voter's device state.
  - Dynamically updates the security nonce so it never expires on cached pages.
  - Hydrates real-time remaining votes, locks exhausted buttons, and adds **"✓ Voted"** badges to options the user voted for.
  - Includes a comprehensive **Cache Compatibility Guide** directly inside the WordPress admin settings.

### 4. Multilingual Engine with Automatic Detection
- **Primary Language**: English (`en`).
- **Supported Languages**: English (`en`), Spanish (`es`), and Catalan (`ca`).
- **Detection Hierarchy**:
  1. URL parameter (`?lang=en|es|ca`) or cookie (`vs_lang`).
  2. Polylang / WPML active language.
  3. WordPress site locale (`determine_locale()` / `get_locale()`).
  4. Browser `Accept-Language` header.
  5. Default fallback: English (`en`).

### 5. CIM Barcelona Visual Design & Privacy
- Inspired by the official [CIM Barcelona](https://www.cimbarcelona.com/concurs/) competition UI:
  - Signature burgundy branding (`#6E1F2D`) with gold hover accents (`#a67c4c`).
  - Circular avatar images (`aspect-ratio: 1/1; border-radius: 50%`).
  - Built-in visual privacy blur for demo and audit environments.
  - Mobile-first, fully responsive grid.

---

## 📸 Screenshots

| View | Screenshot |
| :--- | :--- |
| **01. Frontend in Simple Mode (Open)** | ![Frontend Open](screenshots/01-frontend-open-simple-mode.png) |
| **02. Post-Vote State (Votes Exhausted)** | ![Vote Exhausted](screenshots/02-vote-cast-exhausted-state.png) |
| **03. Closed Voting with Participants Visible** | ![Voting Closed](screenshots/03-voting-closed-participants-visible.png) |
| **04. Backend Simple / Advanced Mode Switch** | ![Backend Switch](screenshots/04-backend-simple-mode-switcher.png) |
| **05. Cache Compatibility Guide in Backend** | ![Cache Guide](screenshots/05-backend-cache-compatibility-guide.png) |

---

## 🚀 Installation & Usage

1. Copy the `voting-system` folder into `wp-content/plugins/`.
2. Activate the plugin from the WordPress admin menu (`Plugins > Installed Plugins`).
3. Navigate to **Voting System > Settings**:
   - Choose **Simple Mode** (1 vote total) or **Advanced Mode**.
   - Enable **"Show participants when voting is closed"** if desired.
   - Configure automatic schedule or control voting manually.
4. Add contestants under **Voting System > Add Contestant**.
5. Embed the voting form on any page or post using the shortcode:
   ```text
   [voting_form]
   ```
