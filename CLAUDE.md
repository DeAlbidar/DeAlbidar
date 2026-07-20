# CLAUDE.md — DeAlbidar

Guidance for Claude Code sessions working in this repository. Read this fully before making changes.

## Project Overview

This repository is the personal portfolio website and one small automation side-project for **Ebenezer Albidar Narh**, a software engineer in Ghana, served at **https://dealbidar.com**. It is a PHP website built on a small, hand-rolled MVC framework (no Composer, no Laravel/Symfony/etc.) plus a Facebook auto-posting cron subsystem bolted onto the same codebase.

There are two functionally distinct parts living in one repo:

1. **The portfolio site** — static-content pages (Home, About, Projects, Experience, Contact, Download CV, Sitemap) rendered through a custom front-controller MVC framework.
2. **The Facebook poster** — a cron-driven subsystem (`controllers/facebookposter.php`, `models/facebookposter_model.php`, `libs/FacebookAPI.php`, `libs/ContentGenerator.php`) that periodically posts canned "trending content" to one or more configured Facebook Pages via the Graph API and logs results to MySQL.

### Purpose / Goals

- Present Ebenezer's professional profile, experience, and projects to recruiters/clients (SEO-optimized: Open Graph, Twitter cards, JSON-LD structured data, sitemap).
- Auto-populate one or more Facebook Pages (currently "InnInk Limited" and "Ebenezer Albidar Narh") with rotating category content on a schedule, without manual posting.
- Stay a low-maintenance, dependency-free PHP app that can run on cheap shared hosting (cPanel-style, FTP-deployed).

## Tech Stack

- **Language**: PHP (procedural + light OOP), no framework, no Composer autoloading (one `spl_autoload_register` maps class name → `libs/<Class>.php`; controllers/models are `require`d manually by the bootstrap).
- **Database**: MySQL via a custom `Database` class that extends `PDO`.
- **Frontend**: Plain HTML/CSS/JS served from `public/assets/`, Font Awesome via CDN, Google reCAPTCHA v3 on the contact form. No JS framework, no bundler, no `package.json`.
- **Mail**: PHPMailer (vendored under `libs/PHPMailer/`), SMTP via `mail.watchghana.com`.
- **Third-party PHP libs (vendored, not installed via Composer)**: PHPMailer, geoPlugin (IP geolocation for visitor counter), getID3 (media duration), xcrud (CRUD scaffolding — **not wired to any route, effectively dead code**), DataTables (jQuery plugin, multiple versions vendored under `libs/DataTables/`), a `FormHandler/` library (**not referenced anywhere in the codebase — dead code**).
- **External services**: Facebook Graph API (`v18.0`), Google reCAPTCHA v3, a third-party chat/support widget (`widget.vyxelon.com`), a geolocation API (geoPlugin), an SMTP relay (`watchghana.com`, unrelated third-party domain used for outbound mail).
- **Hosting/Deploy**: Shared/cPanel-style hosting, deployed via FTP through GitHub Actions (`SamKirkland/FTP-Deploy-Action`).
- **IDE metadata**: `nbproject/` — this is a NetBeans PHP project; that directory is IDE tooling, not app code.

There is no build step, no package manager, no linter/formatter config, and no automated test suite anywhere in this repo.

## Directory Structure

```
/var/www/html/DeAlbidar/
├── config.php                    # DB credentials + app constants (TRACKED IN GIT — see Security)
├── facebook_pages.php             # Facebook page config defaults (placeholders), tracked
├── facebook_pages.local.php       # Real Facebook credentials, gitignored, NOT tracked
├── facebook_pages.local.php.example
├── index.php                      # Front controller entrypoint (session_start, requires config, boots app)
├── jobs.php                       # Trivial cron trigger: curls the live facebookposter/post URL
├── robots.txt, .htaccess          # SEO + Apache rewrite/caching rules (forces https://www.dealbidar.com)
├── controllers/                   # One class per route, extends Controller
│   ├── index.php                  # Home page + contact form POST handler
│   ├── about.php, projects.php, experience.php, contact.php, download_cv.php
│   ├── sitemap.php                # Renders XML sitemap (no header/footer wrap)
│   ├── errors.php                 # 404 fallback controller
│   ├── facebookposter.php         # JSON API for the FB auto-poster (post/status/last/test)
│   └── libs.php                   # Thin passthrough to render arbitrary libs/xcrud/* views (legacy)
├── models/                        # One *_model.php per controller that needs DB access
│   ├── index_model.php            # Contact form persistence + email notification
│   ├── errors_model.php, sitemap_model.php   # Visitor counter (geoPlugin) + legacy PAGE/NEWS queries
│   └── facebookposter_model.php   # Facebook posting business logic, dedupe, daily limits, logging
├── views/
│   ├── partials/header.php        # <head>, meta/OG/Twitter/JSON-LD, opens <body>
│   ├── partials/navigation.php    # Site nav
│   ├── partials/footer.php        # Footer, closes </body></html>, loads JS
│   └── {about,contact,download,errors,experience,index,projects,sitemap}/index.php
├── libs/                          # Framework core + all vendored third-party libraries
│   ├── Bootstrap.php              # Front controller / router
│   ├── Controller.php, Model.php, View.php, Database.php   # Framework base classes
│   ├── Libs.php                   # Grab-bag of static helpers (Email, Input, Date, seoUrl, Hash, …)
│   ├── FacebookAPI.php, ContentGenerator.php, FacebookPageConfig.php   # FB poster subsystem
│   ├── Session.php, Uploads.php, MultiPicture.php   # Misc helpers (Uploads/MultiPicture look legacy)
│   ├── PHPMailer/, geoplugin/, getid3/, xcrud/, DataTables/, FormHandler/, xml/  # Vendored 3rd-party
├── cron/                          # Facebook poster cron tooling + docs (see cron/README_FRAMEWORK_SETUP.md)
├── util/Auth.php                  # Session-based login guard — unused, no login controller exists
├── public/assets/                 # css, js, images, fonts, cv (static assets)
├── logs/facebook_posts/           # Daily log files written by facebookposter_model.php
└── .github/workflows/deploy.yml   # FTP deploy on push to `main`
```

## Architecture

Custom front-controller MVC, roughly the classic "CodeIgniter-lite" pattern:

1. **`index.php`** starts the session/output buffering, sets the timezone (`Africa/Accra`), requires `config.php` and `util/Auth.php`, registers an autoloader for `libs/*`, then instantiates `Bootstrap` and calls `->init()`.
2. **`Bootstrap::init()`** (`libs/Bootstrap.php`) reads `$_GET['url']`, splits it on `/`:
   - `url[0]` = controller name (file `controllers/{url0}.php`, class `Ucfirst(url0)`)
   - `url[1]` = method name
   - `url[2..4]` = up to 3 positional params passed to that method
   - Empty URL → loads `controllers/index.php` → `Index::index()`.
   - Missing controller file, or method that doesn't exist → `controllers/errors.php` → `Errors::index()`.
   - Routing is driven entirely by `?url=controller/method/p1/p2/p3` query string, rewritten to clean paths via `.htaccess`/hosting-level rewrite (not present as a local rewrite rule in this repo's `.htaccess` — that file only forces `https://www.dealbidar.com`, so the actual `/about` → `?url=about` rewrite likely happens via a hosting panel setting or is implicit — verify on the live server if adding routes).
3. **`Controller`** (`libs/Controller.php`) is the base class every controller extends. Its constructor creates a `View`. `loadModel($name)` is called automatically by Bootstrap and `require`s `models/{name}_model.php`, instantiating `{Ucfirst(name)}_Model`.
4. **`Model`** (`libs/Model.php`) base class opens a `Database` (PDO) connection in its constructor using the `DB_*` constants from `config.php`. Every model instance = its own DB connection.
5. **`Database`** (`libs/Database.php`) extends `PDO` and adds `insert()`, `update()`, `delete()`, `select()` convenience methods, all using prepared statements/bound params (good — SQL injection is generally avoided **as long as callers don't interpolate raw `$_GET`/`$_POST` into the `$where` string of `update`/`delete`**, which is a foot-gun since `$where` is a raw SQL fragment).
6. **`View`** (`libs/View.php`) has `render($name, $noInclude = false)`: by default wraps `views/{name}.php` with `views/partials/header.php` + `navigation.php` + `footer.php`. `$noInclude = true` (used by `sitemap.php`) skips the header/nav/footer wrap for non-HTML output. `custom_render()` is a similar mechanism for `libs/*` includes (used only by the legacy `Libs` controller's `xcrud` passthrough).
7. View data is passed by setting public properties on `$this->view` inside the controller (`title`, `description`, `css`, `js`, `structuredData`, etc.) before calling `render()`; the view partials read `$this->{property}` because they are `require`d from inside `View`.

### Facebook poster subsystem (secondary app inside the same MVC)

- Entry point: `controllers/facebookposter.php`, a normal controller reached via `?url=facebookposter/{post|status|last|test}`. It's a JSON API, not an HTML page.
- `Facebookposter_Model::postTrendingContent()` resolves a page config (`libs/FacebookPageConfig.php`, driven by the `FACEBOOK_PAGE_TARGETS` constant), checks a per-page daily post cap, asks `ContentGenerator` for a canned message from one of 7 categories (`tech_ai`, `funny_memes`, `relationship_stories`, `motivational_content`, `news_trending`, `football_highlights`, `health_tips`), checks it wasn't posted in the last 24h, posts via `FacebookAPI` (Graph API `v18.0`, cURL with a stream-based fallback), and logs the result to the `facebook_posts` MySQL table and to `logs/facebook_posts/{date}.log`.
- `ContentGenerator` currently returns **hardcoded sample strings and stock Unsplash images** per category — it is not actually pulling live "trending" content from any external source. `cron/setup_instructions.md` documents how to wire in real sources (NewsAPI, ESPN, etc.) but that has not been implemented.
- Three different ways exist to trigger a post, all hitting the same public URL/controller — pick **one** for the live cron, don't run more than one on a schedule or you'll double-post:
  1. `cron/facebook_poster_cron.sh` — curls `https://dealbidar.com/?url=facebookposter/post` (with optional `page`/`category` args), logs to `/var/log/facebook_poster.log`.
  2. `cron/facebook_poster_cron.php` — re-bootstraps the framework in CLI and simulates the same route in-process.
  3. `jobs.php` (repo root) — a one-liner `file_get_contents()` of the same live URL.
- `cron/install_database.php` creates the `facebook_posts` table (idempotent, `CREATE TABLE IF NOT EXISTS`). `Facebookposter_Model::ensurePageMetadataColumns()` also self-migrates two columns (`page_key`, `page_id`) onto that table at runtime if missing — i.e. **schema migrations for this table happen automatically at request time**, not via a separate migration step.

## Coding Standards / Conventions

Follow the existing style exactly — this is a small, consistently-styled legacy codebase, not a place to introduce a new style:

- **Controllers**: `class Name extends Controller`, PascalCase class name matching the lowercase filename (`controllers/about.php` → `class About`). Constructor calls `parent::__construct()` then sets `$this->view->css` / `$this->view->js` arrays. `index()` is the default action; every page-rendering action sets `title`, `description`, `url`, `canonical`, `image`, `author`, `keywords` on `$this->view` before calling `$this->view->render('folder/index')`.
- **Models**: `class Name_Model extends Model` in `models/name_model.php`, one file per controller that needs one. Constructor calls `parent::__construct()`. All DB access goes through `$this->db->select/insert/update/delete`.
- **Indentation**: 4 spaces, no tabs (framework core), though some newer Facebook-poster files mix conventions slightly — match the file you're editing.
- **Input sanitization**: use `Libs::Input($data)` (`trim` → `stripslashes` → `htmlspecialchars`) on user-submitted form fields before storing/emailing them, matching `models/index_model.php`.
- **New pages**: adding a page means adding **all** of: a controller in `controllers/`, a view folder `views/{name}/index.php`, a nav link in `views/partials/navigation.php`, and (if it needs data) a model in `models/`.
- **Secrets/config**: constants belong in `config.php` (checked in) or the Facebook-specific split of `facebook_pages.php` (tracked, placeholders) / `facebook_pages.local.php` (gitignored, real values). See Security below — this pattern is inconsistently followed (DB credentials are **not** split out this way).
- **Comments**: existing files carry a large boilerplate "W3 Multimedia Ghana Limited" company header in many controllers/models — this is legacy attribution, not something to replicate in new files. Newer files (Facebook poster subsystem) use short docblocks instead — prefer that style going forward.

## Development Workflow

See the dedicated skills at `.claude/skills/git-workflow/` and `.claude/skills/deployment/` for full detail — summarized here:

- Two permanent branches: **`localhost`** (all development) and **`main`** (production, auto-deployed).
- **Always assume work happens on `localhost`** unless the user explicitly says otherwise.
- Test locally (there is no automated test suite — "testing" here means manually exercising the site and the `facebookposter/test` endpoint) before anything is merged to `main`.
- `main` is merged into only when a change is fully verified — pushing to `main` immediately deploys to the live production server.

## Git Workflow (required — see `.claude/skills/git-workflow/`)

1. All development, bug fixes, refactors, and experiments happen on `localhost`.
2. Test everything locally first.
3. Only merge `localhost` → `main` once confirmed working.
4. Do not commit directly to `main`.
5. `main` = production-ready code only.

## Deployment Workflow (required — see `.claude/skills/deployment/`)

- `.github/workflows/deploy.yml` triggers on every push to `main`.
- It runs `SamKirkland/FTP-Deploy-Action@4.3.3`, syncing the **entire repository** (minus `.git*` and a couple of excluded files) directly to the FTP root of the live server. There is no build step, no staging environment, and no rollback mechanism beyond re-deploying a previous commit.
- **Because of this, nothing should ever be merged into `main` that hasn't been fully tested** — a bad push to `main` is a bad push to production, immediately, with no gate in between.
- Secrets used by the workflow (`FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`) live in GitHub Actions repo secrets, not in this codebase.

## Environment Variables / Configuration

There is **no `.env` file and no environment-variable-based configuration** — everything is PHP `define()` constants in checked-in files:

| File | Tracked? | Contents |
|---|---|---|
| `config.php` | **Yes** | `DB_TYPE/HOST/NAME/USER/PASS`, status code constants, `HASH_GENERAL_KEY`, `HASH_PASSWORD_KEY`, `URL`/`ACTUAL_URL` logic, `MAILER_DIR`. **Contains live-looking production DB credentials in plaintext, committed to git.** |
| `facebook_pages.php` | Yes | Default/placeholder Facebook config (empty `page_id`/`access_token` strings) — safe to be tracked. |
| `facebook_pages.local.php` | **No** (gitignored) | Real Facebook App ID/Secret and per-page access tokens, loaded only if present. |
| `facebook_pages.local.php.example` | Yes | Template for the above, placeholders only — safe. |
| `libs/Libs.php` | Yes | Hardcoded SMTP host/username/password for outbound mail (`mail.watchghana.com`). |
| `controllers/index.php` | Yes | Hardcoded Google reCAPTCHA **secret** key inline in the contact-form handler. |
| `views/partials/header.php` | Yes | Hardcoded reCAPTCHA **site** key (public key — fine to be public). |

See **Security Considerations** below — several of the "tracked" files above should not be.

## Database

- Single MySQL database (`DB_NAME` from `config.php`), accessed via the custom `Database extends PDO` class — no ORM.
- Tables observed/referenced in code (no central schema file/migrations directory exists — the schema is reverse-engineered from usage):
  - `facebook_posts` — created by `cron/install_database.php`; columns: `id, title, category, link, image, facebook_post_id, posted_date, created_at, updated_at, status`, plus `page_key`/`page_id` self-migrated at runtime by `Facebookposter_Model::ensurePageMetadataColumns()`.
  - `contact` — written by `Index_Model::contact()` (`name, email, subject, message, created_at`). No corresponding `CREATE TABLE` script in the repo.
  - `tbl_visitors_counter` — written by the visitor `Counter()` method in both `errors_model.php` and `sitemap_model.php` (geoPlugin-based IP geolocation logging on every page view routed through those controllers). No `CREATE TABLE` script in the repo.
  - `PAGE`, `SUBPAGE`, `tbl_public_notice`, `NEWS` — referenced in `errors_model.php`/`sitemap_model.php` (`Pages()`, `Sub_Pages()`, `PublicNotice()`, `findMostRecent()`) but **these methods are not called anywhere else in the codebase** — likely leftovers from a template/boilerplate this project was originally built from, not part of the live portfolio site's actual data model. Treat as probably-dead code; confirm against the live DB before relying on or removing them.
- There are no migration files and no seed data — any new table needs its own one-off setup script (following the `cron/install_database.php` pattern) or a manual `CREATE TABLE`.

## APIs

### Public HTML routes (via `?url=controller/method/...`)
`index`, `about`, `projects`, `experience`, `contact` (GET page + `index/contact` POST handler for the form), `download_cv`, `sitemap` (XML, no header/footer wrap), `errors` (404 fallback).

### Facebook Poster JSON API (`controllers/facebookposter.php`)
| Route | Purpose |
|---|---|
| `?url=facebookposter/post[&page=KEY][&category=CAT]` | Post one item; omit `page` to post to **all** configured pages. Main cron target. |
| `?url=facebookposter/status[&page=KEY]` | Post counts grouped by category. |
| `?url=facebookposter/last[&page=KEY]` | Last posted item. |
| `?url=facebookposter/test[&page=KEY][&category=CAT]` | Generate content without posting (dry run). |

These endpoints are **unauthenticated** — anyone who knows the URL can trigger a real Facebook post or read posting stats. There is no API key/auth check in `Facebookposter` controller.

### External APIs consumed
- **Facebook Graph API** `v18.0` (`libs/FacebookAPI.php`) — posting only (feed/photos), no read-back of engagement metrics.
- **Google reCAPTCHA v3** — verified server-side in `controllers/index.php::contact()` via `https://www.google.com/recaptcha/api/siteverify`.
- **geoPlugin** (`libs/geoplugin/`) — IP → city/region/country/lat/long/timezone lookups for the visitor counter.

## Important Commands

There is no build tool. Common operations:

```bash
# Run the site locally (from repo root)
php -S localhost:8000

# Install the Facebook-poster DB table
php cron/install_database.php

# Test Facebook content generation without posting
curl "http://localhost:8000/?url=facebookposter/test"

# Trigger a real post manually
curl "http://localhost:8000/?url=facebookposter/post"

# Check posting status/history
curl "http://localhost:8000/?url=facebookposter/status"
```

Note: `php -S`'s built-in router does not apply `.htaccess` rewrites, so locally you'll use `?url=...` query strings directly rather than clean `/about`-style paths.

## Testing

**There is no automated test suite** (no PHPUnit, no test directory, nothing in CI beyond deploy). "Testing" in this project means manual verification:
- Load each page (`/`, `/about`, `/projects`, `/experience`, `/contact`, `/download_cv`, `/sitemap`) and confirm it renders without PHP warnings/errors (`error_reporting(E_ALL)` + `display_errors On` are enabled in `index.php`, so errors surface directly in the page).
- Submit the contact form and confirm the reCAPTCHA gate, DB insert, and email all fire.
- Use `?url=facebookposter/test` to validate content generation changes without spamming the real Facebook Page.
- See `.claude/skills/testing/` for a fuller manual checklist.

## Build Process

None. No transpilation, bundling, minification pipeline, or asset pipeline beyond the ad hoc `Libs::MinifierCSS()` helper (not obviously wired into any controller — verify before relying on it). Assets in `public/assets/` are served as-is. Deployment is a raw file sync (see Deployment Workflow).

## Common Tasks

- **Add a new static page**: add `controllers/{name}.php` (copy an existing simple controller like `about.php`), `views/{name}/index.php`, and a nav entry in `views/partials/navigation.php`.
- **Add a Facebook content category**: add a case to `ContentGenerator::generateContent()`'s `$methodMap`, add the corresponding private method + image method, add the key to `getCategories()`.
- **Add/rotate a Facebook Page target**: edit `facebook_pages.local.php` (never commit real tokens — that file is gitignored on purpose).
- **Change the FB posting schedule**: edit whichever of the three cron triggers is actually installed on the server's crontab (see `.claude/skills/api/`), not all three.
- **Query posting history**: `SELECT * FROM facebook_posts ORDER BY posted_date DESC;` or hit `?url=facebookposter/status`.

## Things to Avoid

- Don't merge to `main` without the change having been run and manually verified — it deploys immediately with no review gate.
- Don't add real secrets to `facebook_pages.php` (tracked) — use `facebook_pages.local.php` (gitignored). This split exists precisely to keep Facebook tokens out of git; don't undermine it by hardcoding a token elsewhere.
- Don't build raw SQL strings for the `$where` parameter of `Database::update()`/`delete()` from unsanitized request input — those are string-concatenated into the query, unlike `select()`/`insert()` which fully parameterize.
- Don't assume `xcrud`, `DataTables`, or `FormHandler` (all vendored under `libs/`) are active features — nothing in `controllers/` or `views/` currently wires them up except the single legacy `Libs::xcrud()` passthrough, which nothing links to.
- Don't run more than one of the three Facebook-poster cron triggers (`facebook_poster_cron.sh`, `facebook_poster_cron.php`, `jobs.php`) on the same schedule — each hits the same endpoint and will cause duplicate/extra posts and could blow through Facebook rate limits.
- Don't treat `PAGE`/`SUBPAGE`/`NEWS`/`tbl_public_notice` queries in `errors_model.php`/`sitemap_model.php` as load-bearing without checking whether those tables exist in the live DB — nothing currently calls those methods.

## Best Practices (for this codebase specifically)

- Match the file's existing style before introducing anything new — this is a small, single-maintainer codebase.
- Route all DB writes through `Database::insert/update/select` rather than raw `PDO::query`, and always bind parameters.
- Sanitize any new user-facing input with `Libs::Input()` the way `index_model.php::contact()` does.
- Keep `ContentGenerator` categories' tone/hashtags consistent with the existing hardcoded arrays if adding more canned content, or replace the whole generator with a real API integration per `cron/setup_instructions.md`'s suggestions rather than mixing the two approaches.
- When adding a new DB table, follow the `cron/install_database.php` pattern (idempotent `CREATE TABLE IF NOT EXISTS` script) so it's re-runnable and documented, since there's no migration framework.

## Future Improvements (recommendations only — not implemented)

- Introduce a `.env` + `getenv()`/`vlucas/phpdotenv` pattern (or at minimum move DB credentials out of tracked `config.php` the same way Facebook credentials already are) and rotate the exposed DB password.
- Add authentication (even a simple shared-secret query param or header check) to the `facebookposter/*` endpoints — they're currently open to anyone.
- Replace `ContentGenerator`'s hardcoded content arrays with real "trending content" sources as `cron/setup_instructions.md` already outlines.
- Add a minimal automated test pass (even simple PHP CLI smoke tests hitting each route and asserting HTTP 200 / no PHP warnings) since there's currently zero test coverage and every deploy is a direct-to-production event.
- Remove or clearly quarantine the dead vendored libs (`FormHandler/`, `xcrud/`) and the seemingly-unused `PAGE`/`SUBPAGE`/`NEWS`/`tbl_public_notice` queries and `util/Auth.php`, if confirmed unused, to reduce confusion for future maintainers (including future Claude sessions).
- Consider a staging environment or at least a manual approval step in `deploy.yml` given deploys are instant and irreversible via the workflow itself.

## Known Issues

- **`config.php` (tracked in git) contains what appear to be live production database credentials and hashing keys in plaintext.** If this repository is public (it is hosted at `github.com:DeAlbidar/DeAlbidar`, which the README frames as a public GitHub profile repo), these are exposed. This needs the repo owner's attention (rotate credentials, scrub git history, move to an untracked/local config) — **do not "fix" this unilaterally without the user's explicit go-ahead**, since rewriting git history and rotating live production credentials are both high-impact, hard-to-reverse actions.
- The Google reCAPTCHA **secret** key is hardcoded inline in `controllers/index.php` (tracked in git) rather than pulled from `config.php`.
- The SMTP password in `libs/Libs.php` is hardcoded and tracked in git.
- The Facebook-poster JSON endpoints have no authentication.
- `Database::update()`/`delete()` take a raw SQL `$where` fragment — safe only as long as every caller hand-builds it from trusted/constant strings, which is true today but is a footgun for future contributions.
- Dead/legacy code present: `util/Auth.php` (no login controller exists to use it), `libs/FormHandler/` (unreferenced), `libs/xcrud/` (only reachable through an unlinked legacy passthrough), `PAGE`/`SUBPAGE`/`NEWS`/`tbl_public_notice` query methods in two models (unreferenced).
- `Libs::MinifierCSS()` exists but no controller/view currently calls it — CSS is served unminified directly from `public/assets/css/`.
- No `CREATE TABLE` script exists in the repo for the `contact` or `tbl_visitors_counter` tables (only for `facebook_posts`) — schema for those two is undocumented/implicit.

## Notes for Future Claude Sessions

- This is a **solo-maintainer personal portfolio site**, not a team codebase — optimize for consistency with existing patterns over introducing new abstractions or frameworks.
- Assume **`localhost` branch** for all work unless told otherwise (see Git Workflow above and `.claude/skills/git-workflow/`).
- **Never push to `main`** without the user explicitly confirming the change is tested and ready to deploy — that push is instantaneous production deployment via GitHub Actions.
- Treat the exposed credentials in `config.php`/`libs/Libs.php`/`controllers/index.php` as sensitive: don't print them in full in chat output, don't copy them into new files, and flag it to the user again if you're about to do anything (like sharing a file, opening a PR from a fork, or making the repo more visible) that would increase their exposure.
- When in doubt about whether a piece of code (`xcrud`, `FormHandler`, the `PAGE`/`NEWS` queries, `Auth.php`) is actually live, grep for callers before assuming it's part of the working site — several vendored/legacy pieces are not wired up.
- Full topic-specific guidance lives in `.claude/skills/` — check there before re-deriving conventions from scratch.
