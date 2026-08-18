Prerequisites

- PHP >= 8.1 and Composer installed
- Node.js and npm installed

Quick Setup (Windows PowerShell)

1. Open a PowerShell terminal at the project root `c:\xampp\htdocs\DOLE\app`

2. Verify tools:

```powershell
php -v
composer --version
node -v
npm -v
```

3. Create the environment file (if missing):

```powershell
if (!(Test-Path .env)) { Copy-Item .env.example .env -Force }
```

4. Create SQLite database file (used by default in `.env.example`):

```powershell
if (!(Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite }
```

5. Generate the app key:

```powershell
php artisan key:generate
```

6. Run migrations and seeders:

```powershell
php artisan migrate --seed --force
```

7. Install Node dependencies:

```powershell
npm install
```

8. Start Vite (assets) and Laravel dev server (run each in its own terminal):

```powershell
# Terminal 1
npm run dev

# Terminal 2
php artisan serve --host=127.0.0.1 --port=8000
```

9. Open the app in your browser:

- http://127.0.0.1:8000

Optional: Using MySQL

- Update `.env` DB_* values with your MySQL credentials, set `DB_CONNECTION=mysql` and run `php artisan migrate --seed`.

Stopping servers

- Press Ctrl+C in the terminal running the server(s).

Notes

- The repository `.env.example` already uses SQLite by default, so the steps above should work without extra DB setup.
- If you prefer a reproducible Node install, create a `package-lock.json` by running `npm install` then commit it and prefer `npm ci` on CI.

Troubleshooting

- If migrations fail, check `.env` DB settings and ensure write permissions for `database/database.sqlite`.
- If `npm run dev` errors, check Node/npm versions and install missing packages.
