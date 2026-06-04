<p align="center">
  <img src="images/favicon.ico" alt="Prio Logo" width="120" height="120">
</p>

<h1 align="center">Prio</h1>

<p align="center">
  A task-prioritisation &amp; time-tracking web app — PHP (MVC) + MySQL, a modern
  dark Tailwind&nbsp;CSS interface, fully containerised with Docker.
</p>

---

## Table of Contents

1. [About](#about)
2. [Tech Stack](#tech-stack)
3. [Architecture](#architecture)
4. [Project Setup](#project-setup)
   - [Prerequisites](#prerequisites)
   - [Installation](#installation)
   - [Environment Configuration](#environment-configuration)
   - [First-time Startup](#first-time-startup)
5. [Docker Usage](#docker-usage)
6. [Frontend / Tailwind Workflow](#frontend--tailwind-workflow)
7. [Deployment Notes](#deployment-notes)
8. [Known Limitations](#known-limitations)

---

## About

Prio helps you actually accomplish the goals you set for yourself. You add tasks
or goals with a title, a description, and the motivation behind them, give each a
deadline and a priority, and then track the time you spend working on them. A time
overview adds up everything you've done per task. Miss your self-imposed deadlines
too often and the app pushes back.

All stored data is **encrypted at rest** (AES-256-CBC), and user e-mails/roles are
HMAC-hashed with a per-user salt, so even the operator can't read user data in clear
text.

> This project began as a school mini-project (MPA) and continues to evolve here.

---

## Tech Stack

| Layer        | Technology                                            |
|--------------|-------------------------------------------------------|
| Backend      | PHP 8.3 (hand-rolled MVC), Apache + `mod_rewrite`     |
| Database     | MySQL 8                                               |
| Frontend     | Tailwind CSS 3 (dark theme), vanilla JS, CKEditor 4   |
| Dependencies | Composer (`vlucas/phpdotenv`), npm (Tailwind, build-time only) |
| Runtime      | Docker + Docker Compose                               |

---

## Architecture

```
.
├── index.php                # Front controller + route table
├── .htaccess                # Rewrites all requests -> index.php?url=...
├── core/                    # Mini-framework: Router, DB, bootstrap, helpers
├── app/
│   ├── Controllers/         # TaskController, TimeController, EssayController, LoginController
│   ├── Models/              # Task, Time, Essay (PDO + encryption)
│   └── Views/               # *.view.php (full pages) + header.php / footer.view.php partials
├── public/
│   ├── css/src/app.css      # Tailwind source -> compiled to public/css/app.css (in Docker build)
│   ├── js/                  # Vanilla JS (nav, stopwatch, validation, search, …)
│   └── fontawesome/         # Icons
├── ckeditor/                # Vendored CKEditor 4 (rich-text editor)
├── images/                  # Static images
├── Prio.sql                 # Database schema (auto-loaded on first DB start)
├── docker/                  # Dockerfile, Apache vhost, entrypoint
├── docker-compose.yml       # Default (client-facing) stack
├── docker-compose.dev.yml   # Development overrides
└── docker-compose.prod.yml  # Production overrides
```

**Request flow:** `.htaccess` → `index.php` (route map) → `Router` → `Controller@method`
→ Model (PDO, encrypted) → `*.view.php`.

---

## Project Setup

### Prerequisites

The **only** thing you need installed on your machine is:

- **[Docker](https://docs.docker.com/get-docker/)** (includes Docker Compose v2)

You do **not** need to install PHP, MySQL, Apache, Composer, or Node.js — they all
run inside containers and the build compiles everything for you.

### Installation

```bash
git clone https://github.com/olivierluethy/Prio
cd Prio
cp .env.example .env
```

### Environment Configuration

Edit `.env` to taste. The defaults work out of the box for local use:

| Variable           | Default                              | Description |
|--------------------|--------------------------------------|-------------|
| `APP_PORT`         | `8080`                               | Host port → app is served at `http://localhost:<APP_PORT>` |
| `APP_ENV`          | `local`                              | `local` or `production` |
| `DB_NAME`          | `prio`                               | Database name (matches `Prio.sql`) |
| `DB_USERNAME`      | `prio`                               | Application DB user |
| `DB_PASSWORD`      | `prio_secret`                        | Application DB password |
| `DB_ROOT_PASSWORD` | `root_secret`                        | MySQL root password |
| `DB_PORT`          | `3306`                               | Host port for MySQL (dev override only) |
| `ENCRYPTION_KEY`   | `change_me_dev_key_0123456789abcd`   | **AES-256-CBC key — keep constant!** |

> ⚠️ **`ENCRYPTION_KEY` must never change once data exists.** Data encrypted with
> one key cannot be decrypted with another. For production, generate a strong key:
> `openssl rand -hex 16` and set it **before** the first user registers.

> If port `8080` is already in use, set a different `APP_PORT` (e.g. `8088`).

### First-time Startup

```bash
docker compose up -d --build
```

This single command will:
1. Build the PHP/Apache image (compiling Tailwind CSS and installing Composer deps).
2. Start MySQL and **automatically create the schema** from `Prio.sql`.
3. Wait until the database is healthy, then start the web app.

Open **http://localhost:8080** (or your `APP_PORT`). Click **Login → Register** to
create your first account.

---

## Docker Usage

| Action | Command |
|--------|---------|
| **Start** (build if needed) | `docker compose up -d --build` |
| **Start** (no rebuild)      | `docker compose up -d` |
| **Stop** (keep data)        | `docker compose down` |
| **Stop + wipe database**    | `docker compose down -v` |
| **Rebuild after code change** | `docker compose up -d --build` |
| **App logs**                | `docker compose logs -f app` |
| **Database logs**           | `docker compose logs -f db` |
| **Container status**        | `docker compose ps` |
| **Open a shell in the app** | `docker compose exec app bash` |
| **Open the MySQL CLI**      | `docker compose exec db mysql -uprio -pprio_secret prio` |

### Database initialisation & migrations

- The schema in **`Prio.sql`** is executed automatically the **first time** the
  database container starts (it is mounted into MySQL's `docker-entrypoint-initdb.d`).
- Because it only runs on a fresh volume, to re-apply the schema you must reset the
  data volume:
  ```bash
  docker compose down -v   # removes the db_data volume
  docker compose up -d     # re-initialises from Prio.sql
  ```
- To apply an **incremental** change to a running database without losing data, pipe
  SQL in directly:
  ```bash
  docker compose exec -T db mysql -uprio -pprio_secret prio < path/to/change.sql
  ```

### Accessing the database

```bash
# Interactive MySQL shell
docker compose exec db mysql -uprio -pprio_secret prio

# In development you can also connect from the host (docker-compose.dev.yml
# publishes port 3306) using any client at 127.0.0.1:3306.
```

---

## Frontend / Tailwind Workflow

The dark design system lives in **`public/css/src/app.css`** and
**`tailwind.config.js`**, and is compiled to `public/css/app.css` **inside the Docker
build** — so end users never need Node.

For active UI development, run the bind-mounted dev stack and a Tailwind watcher:

```bash
# Live PHP editing + exposed DB port
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build

# Recompile CSS on change (requires Node 18+ on the host; dev convenience only)
npm install
npm run watch
```

`npm run build` produces the minified production stylesheet (the same command the
Docker build runs).

**Design conventions** (dark-only — there is no light theme or toggle):
- Palette: `surface.*` (slate) for backgrounds, `brand.*` (indigo/blue) for accents.
- Reusable component classes: `.btn` / `.btn-primary` / `.btn-secondary` /
  `.btn-danger`, `.card`, `.input`, `.label`, `.table`, `.alert*`, `.badge`,
  plus the `.site-header` / `.site-footer` layout classes.

---

## Deployment Notes

### Running in production

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

The production override adds `restart: always` and **does not publish the MySQL
port** (the database stays on the internal Docker network).

### Security recommendations

- **Set a strong `ENCRYPTION_KEY`** (`openssl rand -hex 16`) and strong DB passwords
  via your secret manager / a non-committed `.env`. Never ship the dev defaults.
- **Terminate TLS** in front of the app (reverse proxy such as Nginx, Traefik, or a
  cloud load balancer) and serve over HTTPS.
- Keep the MySQL port unpublished in production (the prod compose file already does).
- Review and rotate credentials; restrict the DB user to the application database.

### Backups

```bash
# Create a logical backup
docker compose exec db mysqldump -uroot -p"$DB_ROOT_PASSWORD" prio > backup_$(date +%F).sql

# Restore
docker compose exec -T db mysql -uroot -p"$DB_ROOT_PASSWORD" prio < backup_2026-01-01.sql
```

The database files persist in the named Docker volume **`db_data`** across restarts.
Back up that volume (or use `mysqldump` as above) on a schedule.

### Environment management

- Keep a separate `.env` per environment; never commit real secrets (`.env` is
  git-ignored).
- Compose variables are injected as real container environment variables, so both
  `$_ENV` (phpdotenv) and `getenv()` resolve them; the container entrypoint also
  generates the app's `.env` automatically on first boot.

---

## Known Limitations

- **CKEditor 4** (rich-text editor for descriptions / motivation / essays) is
  vendored and **intentionally left untouched**. It is end-of-life; upgrading to a
  maintained editor (e.g. CKEditor 5) is planned for a future phase. Its editing area
  is an iframe with its own (light) styling, so those fields don't yet match the dark
  theme.

---

## License

This project is licensed under the [MIT License](LICENSE).

## Contact

Project maintainer: [Olivier Luethy](https://github.com/olivierluethy).
