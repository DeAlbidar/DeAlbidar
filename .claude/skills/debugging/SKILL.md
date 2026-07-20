---
name: debugging
description: Use when something's broken — a route 404s, a page throws a PHP error, the contact form fails, or the Facebook poster doesn't post — covers where errors surface and which logs to check for this codebase.
---

# Debugging — DeAlbidar

No centralized logging/error-tracking service is wired up. Errors surface in one of these places depending on what broke.

## A page shows a blank screen or a raw PHP error/warning

`index.php` sets `error_reporting(E_ALL)` and `display_errors = 'On'`, so PHP errors/warnings/notices render directly inline on the page — check the actual HTML response (`curl -s` or browser view-source), not just a blank-looking rendered page, since the error might be embedded mid-markup where it's not visually obvious. A completely blank page usually means a **fatal error before any output buffer content was echoed**, or a `require` for a missing file (check `error_log` at the PHP/webserver level in that case — `display_errors` doesn't help with a hard crash before headers).

## "Page not found" / route doesn't work

Walk `Bootstrap`'s logic (`libs/Bootstrap.php`) in order:
1. Is `controllers/{name}.php` present, and does it define a class with that exact PascalCase name? (`_loadExistingController()` — missing file or class → `Errors::index()`.)
2. If you're passing a method (`?url=controller/method`), does that method exist on the class? (`_callControllerMethod()` checks `method_exists()` and falls through to `_error()` if not.)
3. Locally with `php -S`, remember there's no `.htaccess` rewrite applied — use `?url=...` directly, not clean paths.
4. On the live server, clean-path routing depends on hosting-level rewrite config that isn't fully defined in this repo's `.htaccess` (that file mainly forces `https://www.dealbidar.com`) — if clean paths break in production but `?url=` works, the issue is likely server config, not this codebase.

## Contact form not submitting / not emailing

1. Check the reCAPTCHA gate first — `Index::contact()` in `controllers/index.php` silently `exit`s with an inline `<h2>Please check the captcha form.</h2>` message if the token is missing, verification fails, or the score is `<= 0.5`. If testing locally, verify the site key in `views/partials/header.php` and the secret key in `controllers/index.php` are for a reCAPTCHA app configured for your test domain.
2. If it passes reCAPTCHA but doesn't email: `Index_Model::contact()` only calls `Libs::Email()` `if ($stmt)` — i.e., only after a successful DB insert into `contact`. Check the DB write succeeded first ($_SESSION['error'] would be set by `Database::insert()` on failure).
3. Email itself goes through PHPMailer via SMTP to `mail.watchghana.com` (see `libs/Libs.php`) — `Libs::Email()` catches `PHPMailer\PHPMailer\Exception` and just `echo`s an error string rather than logging it anywhere durable, so a mail failure is easy to miss unless you're watching the response body.

## Facebook poster not posting

Check in this order:
1. `?url=facebookposter/test[&page=KEY][&category=CAT]` — confirms content generation works without touching Facebook at all.
2. `logs/facebook_posts/{YYYY-MM-DD}.log` — `Facebookposter_Model::log()` writes a structured trace of every attempt (page, category, daily-limit check, dedupe attempts, Facebook API result) here. This is the single best source of truth for what happened on a given day.
3. Confirm which of the three cron triggers is actually scheduled on the server (`crontab -l` on the live host) — see `.claude/skills/api/`. If none are scheduled, nothing will post regardless of code correctness.
4. Check `facebook_pages.local.php` actually has real (non-empty) `page_id`/`access_token` values for the target page — `getFacebookPageConfig()` throws `InvalidArgumentException`/`Exception` if the key is unknown or the file is missing, which the controller catches and reports as a JSON `error`.
5. `?url=facebookposter/status[&page=KEY]` — sanity-check whether posts are actually landing in the `facebook_posts` table even if the live Facebook page doesn't show them (would point to a Graph API-side issue, e.g. an expired/invalid token, rather than a code bug — check the logged Graph API response for a Facebook error message).

## Visitor counter / geoPlugin issues

`Counter()` in `errors_model.php`/`sitemap_model.php` calls `geoPlugin::locate()`, which does an external IP lookup — if that service is slow or down, page loads through those two controllers will be slow or throw, since there's no timeout/fallback handling around that call today.

## Common mistakes to avoid

- Assuming there's a central error log for this app — there isn't; check the specific subsystem's log (Facebook poster) or raw page output (everything else) or the webserver's PHP error log for fatals.
- Debugging the Facebook poster by only testing `/test` and assuming `/post` will behave identically — `/post` has the daily-limit and dedupe checks that `/test` skips entirely (it calls `ContentGenerator` directly, bypassing `Facebookposter_Model`'s wrapper logic).
