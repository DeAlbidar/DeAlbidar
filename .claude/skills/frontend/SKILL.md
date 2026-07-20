---
name: frontend
description: Use when editing views, partials, navigation, or static assets — covers how views/header/footer share scope, the SEO meta-tag pattern, and asset conventions in this codebase.
---

# Frontend — Views, Partials, and Assets

There is no templating engine (no Blade/Twig/etc.) — views are plain `.php` files interpolating HTML directly, `require`d from inside the `View` class, so **inside any view or partial, `$this` refers to the `View` object**, not the controller.

## Structure

- `views/partials/header.php` — opens `<!DOCTYPE html>`, builds all `<head>` content (title, meta description/keywords/robots, canonical, Open Graph, Twitter Card, JSON-LD structured data), includes Font Awesome from CDN, conditionally loads the reCAPTCHA v3 script if `$this->loadRecaptcha` is true, opens `<body>`.
- `views/partials/navigation.php` — the sticky nav + dark-mode toggle button markup. Every top-level page link lives here as a plain `<a href="<?php echo URL.'{route}'; ?>">`.
- `views/{page}/index.php` — the actual page content for that route.
- `views/partials/footer.php` — closes the page, echoes `<script>` tags for every entry in `$this->js`, includes a small inline dark-mode-highlighting script, and loads a third-party chat widget (`widget.vyxelon.com`) unconditionally on every page.

## SEO meta-tag pattern

Every controller that renders an HTML page should set these public properties on `$this->view` before calling `render()`, because `header.php` reads them with sane defaults if unset:
`title`, `description`, `url`, `canonical`, `image`, `author`, `keywords`, `robots` (defaults to `index,follow,max-image-preview:large`), `structuredData` (array, merged into the default WebSite/Person/WebPage JSON-LD blocks already defined in `header.php`).

If you don't set these, the page silently falls back to site-wide defaults hardcoded in `header.php` (`$defaultTitle`, `$defaultDescription`, etc.) — fine for something like the errors page, wrong for a real content page (every existing content page sets all of these explicitly — follow that pattern).

## Adding a new page's frontend half

1. Create `views/{name}/index.php`.
2. Add a nav link in `views/partials/navigation.php` if it should be reachable from the main menu.
3. Reference `public/assets/css/style.css` and `public/assets/js/main.js` (or a page-specific asset) via `$this->view->css`/`$this->view->js` in the controller — see `.claude/skills/backend/`.

## Assets

- `public/assets/{css,js,images,fonts,cv}/` — served statically, no build/bundling step. CSS/JS changes take effect immediately on refresh; there's no cache-busting hash in filenames, so browser caching (`.htaccess` sets 1-month/1-year `Expires` headers) can mask changes to CSS/JS on repeat visits — bump a query string or filename if you need to force a refresh for returning visitors.
- `Libs::MinifierCSS()` exists in `libs/Libs.php` but is not called by any current controller/view — CSS ships unminified. Don't assume it's part of the live request path.

## Common mistakes to avoid

- Forgetting that `$this` inside a view/partial is the `View` object, not the controller — you cannot call controller or model methods directly from a view; the controller must set whatever data the view needs as a public property on `$this->view` beforehand.
- Adding a page without setting `canonical`/`title`/`description` — every real content page in this site sets these explicitly for SEO; skipping them is a regression, not a shortcut.
- Adding a new nav link without a matching controller/route, or vice versa — keep `views/partials/navigation.php` and `controllers/` in sync manually; nothing enforces this automatically.
- Loading heavy new third-party scripts in `header.php`/`footer.php` unconditionally (like the current chat widget) without checking whether they should be page-specific — currently only the reCAPTCHA script is conditionally loaded via `$this->loadRecaptcha`.
