# website

[![CI](https://github.com/AMBONOLA/website/actions/workflows/ci.yml/badge.svg)](https://github.com/AMBONOLA/website/actions/workflows/ci.yml)
[![Build & Push Image](https://github.com/AMBONOLA/website/actions/workflows/cd.yml/badge.svg)](https://github.com/AMBONOLA/website/actions/workflows/cd.yml)

My personal portfolio site, and a hands-on experiment for learning **Docker** and **CI/CD**.
The site itself is intentionally small for now; the interesting part is how it gets built, tested, packaged and shipped.

## Stack

| Layer | Tech |
|---|---|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | React + TypeScript via Inertia.js, Tailwind CSS, Vite 8 |
| Web server | [FrankenPHP](https://frankenphp.dev) (Caddy + PHP in one binary) |
| Database | Postgres on [Neon](https://neon.tech) |
| Tests | Pest (in-memory SQLite) |
| CI/CD | GitHub Actions → GitHub Container Registry (GHCR) |

## How it fits together

```
 git push to main
        │
        ▼
 ┌──────────────┐   fail → stop, nothing is published
 │   CI         │   Pint (PHP style) · ESLint · tsc + Vite build · Pest tests
 └──────┬───────┘
        │ pass
        ▼
 ┌──────────────┐
 │   CD         │   builds the production Docker image
 └──────┬───────┘
        ▼
 ghcr.io/ambonola/website:latest  (+ one tag per commit)
        │
        ▼
 hosting provider pulls and runs the image   ← next step
```

## Running it locally

Requires [Docker Desktop](https://www.docker.com/products/docker-desktop/) and a `.env` file (copy `.env.example`, set `APP_KEY` and `DB_URL`).

```bash
docker compose up -d --build   # first run, or after changing the Dockerfile / composer.json
docker compose up -d           # everyday start
```

- App: <http://localhost:8000>
- Vite dev server (hot reload): <http://localhost:5173>

| Task | Command |
|---|---|
| Follow logs | `docker compose logs -f app` |
| Artisan | `docker compose exec app php artisan <command>` |
| Tests | `docker compose exec -e APP_ENV=testing -e SESSION_DRIVER=array -e CACHE_STORE=array app php vendor/bin/pest` |
| Stop | `docker compose down` |
| Reclaim Docker disk space | `docker system df` then `docker system prune` |

## What I built and why

A running log of the decisions behind this setup: the part of the project I actually wanted to learn.

### One Dockerfile, multiple stages

The [`Dockerfile`](Dockerfile) is split into stages: `vendor` installs Composer packages, `assets` builds the frontend with Node, and `production` copies only the results onto FrankenPHP.
**Why:** the final image doesn't carry Node, Composer or build tools, just what's needed to serve the site, so it's smaller and has less to go wrong. Build caches keep repeat builds fast.

### Local development with Docker Compose

[`docker-compose.yml`](docker-compose.yml) runs two containers: `app` (the same production image, with the code mounted in so edits show up instantly) and `vite` (the dev server for hot reloading React/CSS).
**Why:** local dev runs on the same PHP version, extensions and web server as production, so "works on my machine" means something.

Tuning that came from actually measuring things:
- **Vite polling** was using ~47% CPU because file-change events don't cross from Windows into containers, so it was re-scanning `vendor/` constantly. Ignoring backend-only folders brought it down to ~2.5%.
- **`npm ci` on every start** wiped `node_modules` each time. Now it only reinstalls when `package-lock.json` changes.
- **Cache and sessions** were stored in the remote Neon database, which added network round-trips to every request. Local dev now uses files instead (production is unchanged).

### CI: prove it works before shipping it

[`ci.yml`](.github/workflows/ci.yml) runs on every push and pull request: code style (Pint), linting (ESLint), a type-checked production build, and the test suite.
**Why:** catch mistakes automatically, on a clean machine, before they reach anyone.

### CD: publish only what passed

[`cd.yml`](.github/workflows/cd.yml) runs **only after CI passes** on `main`, builds the production image, and pushes it to GHCR tagged `latest` plus the commit SHA.
**Why:** a broken commit can never become a deployable image, and every version is traceable back to the exact commit it came from (and can be rolled back to).

### Lessons from the trenches

- GHCR requires lowercase image names, but the GitHub username has capitals, so the workflow uses `docker/metadata-action` to generate the tags.
- `@vitejs/plugin-react` v4 didn't support Vite 8, which caused a confusing `Invalid input options … "jsx"` warning. Upgrading to v6 fixed it.
