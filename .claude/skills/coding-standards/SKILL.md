---
name: coding-standards
description: Use when writing or editing any PHP file in this repo — the exact naming, structure, and style conventions to match, distilled from the existing controllers/models/libs.
---

# Coding Standards — DeAlbidar

This is a small, consistently-styled legacy codebase maintained by one person. The goal when editing it is **fit in**, not modernize. Don't introduce PSR-12, Composer autoloading, namespaces, strict typing, or a different indentation style unless the user explicitly asks for a broader modernization pass.

## Naming

- **Controller files**: lowercase, matching the route segment (`controllers/download_cv.php` for route `download_cv`).
- **Controller classes**: PascalCase / Ucfirst of the filename, with underscores preserved and capitalized per-word (`Download_Cv`, `Facebookposter`, `Errors`).
- **Model files**: `models/{controller_name}_model.php`.
- **Model classes**: `{Controller_Name}_Model` (matches `Bootstrap::_loadExistingController()`'s exact construction: `$modelName = $name . '_Model';`).
- **Methods**: `camelCase` for newer code (`postTrendingContent`, `hasReachedDailyPostLimit`), but some older framework/library methods use `PascalCase` (`Libs::Input()`, `Libs::Date_Format()`, `Model::Counter()`) — match whichever file you're in; don't rename existing methods to "fix" the inconsistency without being asked.
- **Constants**: `SCREAMING_SNAKE_CASE` via `define()`, no `enum`/class-constant usage anywhere.

## Structure

- 4-space indentation, no tabs, throughout the framework core and most application code.
- Every controller constructor calls `parent::__construct();` first, then sets `$this->view->css`/`$this->view->js`.
- Every model constructor calls `parent::__construct();` first (opens the DB connection).
- No namespaces, no `use` statements except where a vendored library requires it (`libs/Libs.php` uses `use PHPMailer\PHPMailer\PHPMailer;` because PHPMailer is namespaced internally).
- No dependency injection — classes instantiate their own collaborators directly (e.g. `Facebookposter_Model` does `require_once 'libs/FacebookAPI.php'; ... new ContentGenerator($this->db)` inside its own constructor).

## Comments

- Many older files (`errors.php`, `sitemap.php`, `libs.php`, both older models) carry a large boilerplate "W3 Multimedia Ghana Limited" company/mission-statement header comment. This is legacy attribution from the original template this project was built on — **don't copy this into new files**.
- Newer files (the entire Facebook-poster subsystem) use short, functional docblocks (`/** Post trending content to Facebook */`) above each method — prefer this style for new code.
- Follow this repo's general house style (and your own default): comment the *why*, not the *what* — the existing code mostly doesn't comment obvious operations, and new code shouldn't either.

## Error handling

- The framework relies on PHP's default error visibility (`error_reporting(E_ALL)`, `display_errors On` set in `index.php`) rather than a custom error handler or logging framework — errors/warnings render directly on the page.
- The Facebook-poster subsystem is the only part of the codebase with structured `try/catch` + logging (`Facebookposter_Model::log()` writes to `logs/facebook_posts/{date}.log`). If adding error handling to other parts of the app, this is the closest existing pattern to follow — but note it's local to that one subsystem, not a shared logging utility.
- `Database::insert()`/`update()` communicate failure via `$_SESSION['error']` flash messages rather than exceptions or return-value checks — match this when using those methods directly; don't wrap them in unnecessary `try/catch` since they don't throw on a failed row-count.

## Common mistakes to avoid

- Introducing PHP 8-only syntax (enums, readonly properties, named arguments-as-a-style-choice) inconsistently with the rest of the file — check the file's existing PHP-version feel before assuming what's safe (there's no `composer.json`/`.php-version` pinning a minimum version anywhere in this repo, so match neighboring code rather than guessing).
- Adding a namespace or `use` statement to a plain framework class (`Controller`, `Model`, `View`, etc.) — none of them are namespaced, and mixing namespaced and non-namespaced core classes will break the `spl_autoload_register` callback in `index.php`, which expects to `include LIBS . $class . '.php'` for a bare class name.
- Copy-pasting the W3 Multimedia boilerplate header into new files.
