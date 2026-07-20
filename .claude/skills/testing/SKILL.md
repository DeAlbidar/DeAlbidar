---
name: testing
description: Use when asked to "test" a change in this repo — there is no automated test suite, so this defines the manual verification checklist to use instead, before anything is merged to main.
---

# Testing — DeAlbidar

There is **no automated test suite** in this repository — no PHPUnit, no test directory, nothing in CI beyond the FTP deploy. "Testing" here always means manual verification. Since merges to `main` deploy instantly to production (see `.claude/skills/deployment/`), treat this checklist as the actual quality gate, not a nice-to-have.

## Before considering any change "tested"

### Portfolio pages
- Load every route touched by the change (and, for anything framework-level, all of them): `/`, `?url=about`, `?url=projects`, `?url=experience`, `?url=contact`, `?url=download_cv`, `?url=sitemap`.
- Confirm no PHP warnings/notices/errors appear inline in the page (they will, visibly, since `display_errors` is on — see `.claude/skills/debugging/`).
- Confirm the page title/meta/canonical look right in view-source if you touched controller SEO properties or `header.php`.
- Check both a page that sets `loadRecaptcha = true` (contact) and one that doesn't, if you touched anything in `header.php`.

### Contact form
- Submit with a valid reCAPTCHA pass — confirm a row lands in the `contact` table and an email arrives (or, if SMTP isn't reachable in your test environment, confirm `Libs::Email()` is reached and only fails at the SMTP layer, not before).
- Submit without completing the captcha — confirm the friendly rejection message shows and nothing is written to the DB.

### Facebook poster (if touched)
1. `?url=facebookposter/test[&page=KEY][&category=CAT]` first — always, before anything that actually posts. Confirms content generation and page-config resolution without side effects.
2. Check `logs/facebook_posts/{today}.log` after any real `/post` call, even a successful one — confirm the trace looks right (right page, right category, no unexpected dedupe/limit skips).
3. `?url=facebookposter/status` and `?url=facebookposter/last` to confirm the DB write matches what you expect.
4. Only run a real `?url=facebookposter/post` against a test/throwaway Facebook Page config if you don't want to post to the real "InnInk Limited" / "Ebenezer Albidar Narh" pages while iterating.

### Routing/framework changes
- If you touched `libs/Bootstrap.php`, `Controller.php`, `Model.php`, or `View.php`, re-test **every** route, not just the one you were working on — these are shared by the entire site.

## What "done" looks like before asking for a merge to `main`

- Every route touched loads cleanly, with no visible PHP errors.
- Any form submitted through its full flow (captcha pass + fail).
- Any DB-writing code confirmed to actually write (query the table, or use the corresponding `status`/`last` endpoint for the Facebook subsystem).
- If secrets/config were touched, confirm you didn't add anything to a **tracked** file that should be in `facebook_pages.local.php`-style untracked config instead (see `.claude/skills/security/`).

## Common mistakes to avoid

- Treating "the code runs without throwing" as equivalent to "tested" — most of this codebase's real failure modes (wrong SEO meta, silently-skipped Facebook post due to a daily limit, contact email not arriving) don't throw PHP errors at all; they just silently produce the wrong (non-)result.
- Testing the Facebook poster's `/post` endpoint against the real, live-configured Facebook Pages when you only meant to verify code behavior — use `/test` for iteration.
- Assuming a change is "tested" just because it was tested on `localhost` in a way that doesn't match production's environment (e.g., PHP built-in server skips `.htaccess` rewrites, or a different PHP version is installed) — call this out to the user if there's a meaningful environment gap for the specific change.
