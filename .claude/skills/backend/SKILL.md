---
name: backend
description: Use when adding or modifying a controller or model in this PHP MVC codebase — covers the exact conventions controllers/models must follow to work with the custom Bootstrap router.
---

# Backend — Controllers & Models

Read `.claude/skills/architecture/` first for how the router works. This skill is the concrete "how do I add one" recipe.

## Adding a new controller (new page or endpoint)

1. Create `controllers/{name}.php` (lowercase filename), containing `class {Name} extends Controller` (PascalCase, matches filename case-insensitively — PHP class names aren't case sensitive for autoload matching here, but match the existing capitalization convention: `About`, `Download_Cv`, `Facebookposter`).
2. Constructor: call `parent::__construct();` first, then (for HTML pages) set `$this->view->css = [...]` and `$this->view->js = [...]` arrays of asset paths relative to the site root (they get prefixed with `URL` in the header/footer partials).
3. For every HTML-rendering method, set on `$this->view` before calling render: `title`, `description`, `url`, `canonical`, `image`, `author`, `keywords`. This drives SEO meta tags, Open Graph, Twitter cards, and JSON-LD in `views/partials/header.php` — skipping these means the page falls back to site-wide defaults defined there, which is sometimes fine (e.g. `errors.php` doesn't set `canonical`).
4. Call `$this->view->render('{name}/index');` at the end of the method (or `$this->view->render('{name}/index', true)` to skip the header/nav/footer wrap, as `sitemap.php` does for XML output).
5. For a JSON API endpoint instead (like the Facebook poster), skip the view entirely: set `http_response_code()` as needed and `echo json_encode([...]);`.

## Adding a new model

1. Create `models/{name}_model.php` where `{name}` **matches the controller's filename**, not the specific method — Bootstrap loads it once per request based on `url[0]`.
2. `class {Name}_Model extends Model` — constructor calls `parent::__construct();`, which opens the PDO connection via `$this->db`.
3. Every public method is a discrete "query or write" operation. Keep business logic (validation, external API calls, retries) in the model, not the controller — follow `Facebookposter_Model`'s pattern (it owns dedupe logic, daily limits, logging, and the Facebook API call itself; the controller just shapes the JSON response).
4. Use `$this->db->select($sql, $paramsArray)` for reads, `$this->db->insert($table, $assocArray)` for writes. Never string-concatenate user input into `$sql` — always bind via the params array (named `:param` placeholders, matched against array keys).
5. If you need `update()`/`delete()`, remember their `$where` argument is raw SQL — only build it from hardcoded/constant strings, never from request input, since it isn't parameterized (see `.claude/skills/security/`).

## Passing data from controller to view

There's no explicit "pass data to view" API beyond setting public properties on `$this->view`. If a controller method needs to hand query results to its view, set a custom public property (e.g. `$this->view->findMostRecent = $this->model->findMostRecent();` as `sitemap.php` does), then read `$this->findMostRecent` inside `views/sitemap/index.php` (remember: inside a rendered view, `$this` refers to the `View` instance).

## Common mistakes to avoid

- Forgetting `parent::__construct()` in a new controller/model — you'll lose `$this->view` or `$this->db` respectively.
- Naming a model file to match the *method* instead of the *controller* — it won't be autoloaded by Bootstrap.
- Relying on more than 3 positional URL params (`url[2]`, `url[3]`, `url[4]`) — `Bootstrap::_callControllerMethod()` has no case beyond `length === 5`, so a 4th param is silently ignored.
- Adding a page without also adding its nav link in `views/partials/navigation.php` (see `.claude/skills/frontend/`) — the route will work but be unreachable from the UI.
- Putting raw HTML output directly in a controller (like the older `Index::contact()` method does with inline `echo '<h2>...'`) instead of rendering a proper view — it works, but it's inconsistent with every other controller in this codebase; prefer rendering a view or returning JSON.
