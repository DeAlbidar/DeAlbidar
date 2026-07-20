---
name: architecture
description: Use when you need to understand or explain how requests flow through this codebase — the custom front-controller MVC framework, routing, and base classes — before adding routes, controllers, or models.
---

# Architecture — DeAlbidar's Custom MVC Framework

There is no named framework here — it's a small, hand-rolled front-controller MVC, similar in spirit to early CodeIgniter. Read `libs/Bootstrap.php`, `libs/Controller.php`, `libs/Model.php`, `libs/View.php`, `libs/Database.php` before assuming behavior — they're short (under 200 lines combined) and it's faster to read them than to guess.

## Request lifecycle

1. `index.php` — starts output buffering + session, sets `error_reporting(E_ALL)` / `display_errors On` (errors are visible directly on the page — a debugging convenience but note it for the Security skill), sets timezone `Africa/Accra`, `require`s `config.php` and `util/Auth.php`, registers `spl_autoload_register` for `libs/{Class}.php`, then `new Bootstrap()` → `->init()`.
2. `Bootstrap::_getUrl()` reads `$_GET['url']`, splits on `/`.
3. `Bootstrap::_loadExistingController()` — `url[0]` maps to `controllers/{url[0]}.php` (file must exist or you get `Errors::index()`), and instantiates `new {Ucfirst(url[0])}()`. It then calls `$controller->loadModel(url[0], 'models/')`, which is a no-op if `models/{url[0]}_model.php` doesn't exist (no error — `Controller::loadModel()` only loads the model `if (file_exists($path))`).
4. `Bootstrap::_callControllerMethod()` — `url[1]` is the method (defaults to `index()` if absent), `url[2..4]` are up to 3 positional string arguments. **There is no support for more than 3 params via this router** — plan URL/method signatures accordingly.
5. Controller method sets public properties on `$this->view` (title, description, css, js, structuredData, etc.), then calls `$this->view->render('folder/index')`.
6. `View::render()` `require`s `views/partials/header.php`, `views/partials/navigation.php`, `views/{folder}/index.php`, `views/partials/footer.php` in sequence — they share scope with `View` (i.e., inside those partials, `$this` is the `View` instance, so `$this->title`, `$this->css`, etc. are readable there). Pass `$noInclude = true` to skip the header/nav/footer wrap (used for the XML sitemap).

## Base classes — what they give you for free, and what they don't

- **`Controller`**: constructs a `View` for you (`$this->view`). Does *not* auto-load a model — that's Bootstrap's job, and only if a matching `_model.php` file exists.
- **`Model`**: constructs a `Database` (PDO) connection for you (`$this->db`) using the `DB_*` constants. **Every model instantiated opens its own DB connection** — there's no shared/pooled connection, so avoid instantiating models you don't need.
- **`Database extends PDO`**: adds `insert($table, $assocArray)`, `update($table, $assocArray, $whereRawSql)`, `delete($table, $whereRawSql, $limit=1)`, `select($sql, $paramsArray, $fetchMode)`. `insert`/`update`/`select` bind values safely; `update`/`delete`'s `$where` argument is raw SQL concatenated into the query string — never build that from unsanitized input (see `.claude/skills/security/`).
- **`View`**: no logic beyond `render()`/`custom_render()` and public properties. There's no templating engine — views are plain PHP files with direct `<?php ?>` interpolation.

## Adding a new route

There is no route table to edit. A route `controller/method` exists the moment:
- `controllers/{controller}.php` defines `class {Controller}`, and
- that class has a public method `{method}`.

If you want `models/{controller}_model.php` loaded automatically, name it to match the controller (not the method) — the model is tied to the controller as a whole, loaded once regardless of which method is called.

## Known deviations / things that look unfinished

- `util/Auth.php` implements a login/logout guard pattern (`Auth::handleLogin()`/`handleLogout()`) but **no login controller exists anywhere in `controllers/`** — this class is currently unreachable dead code. Don't assume authentication exists anywhere on this site.
- `controllers/libs.php` (`class Libs extends Controller`, method `xcrud($pram)`) is a passthrough to `View::custom_render('xcrud/'.$pram)`, wiring in the vendored `libs/xcrud/` CRUD-scaffolding library — but nothing in `views/partials/navigation.php` or elsewhere links to it. Treat as legacy/unused unless you find an actual caller.
- The Facebook-poster subsystem (`controllers/facebookposter.php`) reuses this exact same MVC machinery to serve a JSON API instead of HTML — it's not a separate app, just a controller whose methods `echo json_encode(...)` instead of calling `$this->view->render()`. See `.claude/skills/api/`.
