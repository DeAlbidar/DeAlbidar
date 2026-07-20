---
name: security
description: Use before touching config/secrets, auth, SQL, or the Facebook API integration, and when doing any security review of this repo — covers known exposed secrets, unauthenticated endpoints, and the SQL-injection footgun in Database::update/delete.
---

# Security — DeAlbidar

This is a small solo-maintainer site, and its security posture reflects that: several things below are genuine, currently-live issues, not hypothetical. Read this before touching config, secrets, or the Facebook integration, and definitely before doing anything that would increase the repo's visibility (forking publicly, opening a PR from an external fork, sharing a zip/gist of the code, etc.).

## Known exposed secrets (tracked in git)

| Location | What | Risk |
|---|---|---|
| `config.php` (root, tracked) | `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` in plaintext, plus `HASH_GENERAL_KEY`/`HASH_PASSWORD_KEY` | Looks like a real production DB credential (cPanel-style naming). If this repo is public, this is a live exposed credential. |
| `controllers/index.php` | Google reCAPTCHA **secret** key hardcoded inline in `contact()` | Secret (not the public site key) checked into git. |
| `libs/Libs.php` | SMTP host/username/password for `mail.watchghana.com` hardcoded in `Libs::Email()` | Checked into git; format looks possibly redacted (`<PAUSA0000>`) but treat as sensitive until confirmed otherwise. |

**Do not** "fix" these unilaterally (rotate credentials, rewrite git history, move to env vars) without the user's explicit go-ahead — those are exactly the kind of high-impact, hard-to-reverse, shared-system-affecting actions that require confirmation first. Do flag it if you notice you're about to do something that increases exposure (e.g., committing a new file that echoes `config.php`, or preparing to make the repo/a fork public).

By contrast, **Facebook credentials are handled correctly**: `facebook_pages.php` (tracked) only has empty placeholder strings; real tokens go in `facebook_pages.local.php`, which is in `.gitignore` and confirmed not tracked (`git ls-files` excludes it). Use this file as the template for how secrets *should* be handled elsewhere in this codebase if asked to improve things.

## Unauthenticated endpoints

`controllers/facebookposter.php`'s `post`/`status`/`last`/`test` methods have zero auth checks — anyone who finds the URL can trigger a real Facebook post or read internal stats. See `.claude/skills/api/` for endpoint details. If asked to add protection, the minimal-diff approach is a shared-secret query param or header, checked at the top of each method before calling into the model.

## SQL injection surface

`libs/Database.php`'s `select()` and `insert()` always bind parameters — safe. `update()` and `delete()` take a raw `$where` **SQL fragment** that is string-concatenated directly into the query with no escaping:

```php
$sth = $this->prepare("UPDATE $table SET $fieldDetails WHERE $where");
```

Every current call site builds `$where` from hardcoded strings, so there's no live vulnerability today — but any new code that builds `$where` from `$_GET`/`$_POST`/route params would be a direct SQL injection. Flag this pattern if you see it introduced, and prefer parameterized queries (via `select()`) or a hand-written prepared statement instead.

## TLS verification disabled in the Facebook API client

`libs/FacebookAPI.php`'s `makeRequestWithCurl()` sets `CURLOPT_SSL_VERIFYPEER => false` and `CURLOPT_SSL_VERIFYHOST => 0`; the streams fallback does the equivalent (`verify_peer => false`). This disables certificate validation on outbound HTTPS calls to Facebook's Graph API, which weakens protection against MITM interception of the access token and post content. If touching this file, consider whether this was a deliberate workaround (e.g., for a broken CA bundle on the host) before "fixing" it — but it's worth surfacing to the user either way.

## Input handling

`Libs::Input($data)` (`trim` → `stripslashes` → `htmlspecialchars`) is the existing sanitization helper, used in `Index_Model::contact()`. Use it for any new user-submitted form field that gets stored or echoed back. Note it does not validate format (email shape, length limits, etc.) — it's an XSS/whitespace cleanup helper, not a validator.

## Contact form protections

`Index::contact()` in `controllers/index.php` verifies Google reCAPTCHA v3 server-side (score threshold `<= 0.5` rejected) before accepting a submission — reasonable bot protection for a public form. If adding new public forms, follow this same pattern (verify server-side, don't trust a client-side-only check).

## Common mistakes to avoid

- Don't print full secret values from `config.php`/`Libs.php`/`controllers/index.php` into chat output, logs, or new files "for reference" — treat them as sensitive even though they're already in the repo.
- Don't assume `facebookposter/*` is safe to leave open just because it currently is — flag it if the user is adding new capabilities to that controller (e.g., an endpoint that deletes posts or changes page config) since the blast radius of "unauthenticated" grows with what the endpoint can do.
- Don't re-enable insecure defaults elsewhere in the codebase by copy-pasting the disabled-TLS-verification pattern from `FacebookAPI.php` into new HTTP client code.
