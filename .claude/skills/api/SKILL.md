---
name: api
description: Use when working on the Facebook auto-poster subsystem — its JSON endpoints, the three redundant cron triggers, content generation, and the Facebook Graph API wrapper.
---

# API — Facebook Auto-Poster Subsystem

This is the one "API" in the codebase (everything else is server-rendered HTML). It lives inside the same MVC framework as the portfolio site — `controllers/facebookposter.php` is a normal `Controller` subclass whose methods `echo json_encode(...)` instead of rendering a view.

## Endpoints (`?url=facebookposter/{method}`)

| Method | Query params | Behavior |
|---|---|---|
| `post` | `page` (optional page key), `category` (optional, overrides the page's default rotation) | If `page` given, posts to just that page; if omitted, loops over **every** configured page (`postAllTrendingContent`). This is the cron target. |
| `status` | `page` (optional) | Returns post counts grouped by category (+ total) for one or all pages. |
| `last` | `page` (optional) | Returns the most recently posted item. |
| `test` | `page`, `category` (both optional) | Generates content **without posting** — safe to call repeatedly while developing. |

**No authentication on any of these** — see `.claude/skills/security/`. If you're asked to add auth, a shared-secret query param or header check in the controller (before calling into the model) is the minimal-diff approach consistent with this codebase's style.

## Page configuration

`libs/FacebookPageConfig.php` defines `getFacebookPageTargets()`, `getDefaultFacebookPageKey()`, `getFacebookPageConfig($key)`, all reading from the `FACEBOOK_PAGE_TARGETS` / `FACEBOOK_DEFAULT_PAGE_KEY` constants. Those constants are defined with **empty placeholder values** in the tracked `facebook_pages.php`, and overridden with real values in the gitignored `facebook_pages.local.php` (see `.claude/skills/security/` and root `CLAUDE.md`). To add or change a Facebook Page target, edit `facebook_pages.local.php` — never put real tokens in `facebook_pages.php`.

Each page config: `label`, `page_id`, `access_token`, `default_category` (string or array — array means "rotate randomly among these"), `posts_per_day` (enforced by `Facebookposter_Model::hasReachedDailyPostLimit()`).

## Content generation

`libs/ContentGenerator.php` maps each of 7 categories (`tech_ai`, `funny_memes`, `relationship_stories`, `motivational_content`, `news_trending`, `football_highlights`, `health_tips`) to a private method returning a hardcoded message from a small array plus a stock Unsplash image URL. **This is not live "trending" content** — it's a fixed rotation of canned strings. `cron/setup_instructions.md` documents (but doesn't implement) how to wire in real sources like NewsAPI or ESPN. If asked to make content "actually trending," that's the extension point — add a new private method following the existing ones' shape (`return ['title' => ..., 'content' => ..., 'category' => ..., 'link' => ..., 'image' => ...];`).

To add a category: add a case to the `$methodMap` in `generateContent()`, add the private generator + image-picker methods, and add the key to `getCategories()`.

## Duplicate/rate limiting logic (in `Facebookposter_Model`)

- `hasReachedDailyPostLimit()` — counts today's rows in `facebook_posts` for the page, compares to `posts_per_day`.
- `isContentAlreadyPosted()` — checks for the same title posted in the last 24h for that page.
- `generateUniqueContent()` — retries content generation up to 5 times if it keeps colliding with `isContentAlreadyPosted()`, then throws.

## `FacebookAPI` (`libs/FacebookAPI.php`)

Thin wrapper over Graph API `v18.0`. `postContent()` (feed post, optional link), `postImage()` (photos endpoint), `postVideo()` (feed with video source). Tries cURL first, falls back to PHP streams (`makeRequestWithStreams`) if cURL isn't available — **both paths disable SSL peer/host verification** (`CURLOPT_SSL_VERIFYPEER => false`, `verify_peer => false`), which is a real weakening of transport security worth flagging if you touch this file (see `.claude/skills/security/`).

## The three cron triggers — pick one, not all

All three hit the exact same `facebookposter/post` route and will double/triple-post if more than one is scheduled simultaneously:
1. `cron/facebook_poster_cron.sh` — `curl`s the live production URL.
2. `cron/facebook_poster_cron.php` — re-bootstraps the PHP framework in CLI and routes in-process (no HTTP round trip).
3. `jobs.php` (repo root) — one-line `file_get_contents()` of the live production URL.

Before changing posting cadence or logic, check the server's actual crontab (not just this repo) to see which one is installed — `cron/README_FRAMEWORK_SETUP.md` documents `crontab -e` usage for the `.sh` variant specifically.

## Common mistakes to avoid

- Scheduling more than one of the three triggers.
- Adding a new content category without adding it to `getCategories()` — `resolveCategory()` falls back to a random category from that list if the requested one isn't found there, silently ignoring your new category.
- Forgetting `hasPageMetadataColumns` gating — several queries in `Facebookposter_Model` conditionally add `page_key`/`page_id` filters only if `ensurePageMetadataColumns()` successfully confirmed those columns exist; don't assume they're always present without going through that check.
- Testing against the real Facebook Page — always use `?url=facebookposter/test` first, which generates but does not post.
