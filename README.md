# Sister Concern Brand CDN Manager

A lightweight, custom MVC PHP application for managing brand assets (logos, images, colors, typography) across multiple sister concerns, with a built-in CDN for delivering assets directly to websites and applications.

## Features
- **Multi-Brand Management:** Manage multiple sister concerns in one place.
- **Brandfetch-style UI:** Beautiful, modern interface for browsing and downloading assets.
- **Asset CDN:** Deliver logos and images via persistent, tokenized, and versioned URLs.
- **SVG Sanitization:** Automatically sanitize SVG files on upload to prevent XSS.
- **Format Support:** Supports SVG, PNG, JPG, WEBP, and more.
- **Color Palettes:** Store HEX colors and automatically generate RGB, HSL, and CMYK formats.
- **Web Fonts:** Document brand typography and web font links.
- **Audit Logging:** Track user actions and changes.
- **Zip Downloads:** Download all assets for a brand or all formats of a specific logo in a single ZIP file.
- **No Heavy Frameworks:** Built with raw PHP (Custom MVC), SQLite, Vanilla JS, and Tailwind CSS.

## Requirements
- PHP 8.2 or higher
- PHP Extensions: `pdo_sqlite`, `gd`, `zip`, `mbstring`, `fileinfo`

## Installation

1. **Clone the repository.**
2. **Copy `.env.example` to `.env`** and configure your settings (especially `APP_URL`).
3. **Run Migrations & Seeder:**
   ```bash
   php scripts/migrate.php
   php scripts/seed.php
   ```
4. **Create required storage directories and set permissions:**
   ```bash
   mkdir -p storage/database storage/uploads storage/thumbnails storage/backups storage/temp
   chmod -R 775 storage
   ```
5. **Serve the application:**
   You can use PHP's built-in web server for local development:
   ```bash
   php -S localhost:8000 -t public
   ```

## Default Login
- **Email:** admin@example.com
- **Password:** password

*(Make sure to change the password after first login!)*

## CDN Usage
The system generates 3 types of CDN links for each file:
1. **Latest (Persistent):** `http://localhost:8000/cdn/{company-slug}/{theme}/{asset-slug}.{ext}`
   - Use this on your websites. It will always return the newest version if you replace the file.
2. **Tokenized:** `http://localhost:8000/cdn/file/{token}.{ext}`
   - An alternative short link.
3. **Versioned (Immutable):** `http://localhost:8000/cdn/v/{version}/{asset-slug}.{ext}`
   - Use this if you want to permanently link to a specific version of a file.

## Directory Structure
- `app/` - MVC Core, Controllers, Models, Middleware, Services
- `database/` - SQLite database and SQL migration files
- `public/` - Web root, `index.php`, CSS, JS
- `routes/` - Route definitions
- `scripts/` - CLI scripts for migration and seeding
- `storage/` - Uploads, thumbnails, backups, and SQLite database
