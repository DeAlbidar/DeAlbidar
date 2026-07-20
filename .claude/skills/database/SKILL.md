---
name: database
description: Use when writing queries, adding tables, or touching the Database class — covers the PDO wrapper's insert/update/delete/select methods, the known schema, and the lack of a migration system.
---

# Database — DeAlbidar

MySQL, accessed exclusively through `libs/Database.php` (`class Database extends PDO`). No ORM, no query builder beyond the four convenience methods below, and **no migrations directory** — schema changes are one-off scripts or, in one case, applied automatically at runtime.

## The `Database` API

```php
$this->db->select($sql, $paramsArray = [], $fetchMode = PDO::FETCH_ASSOC);
$this->db->insert($table, $assocArray);   // builds INSERT from array keys, binds all values, sets $_SESSION['success'|'error']
$this->db->update($table, $assocArray, $whereRawSql);  // $where is concatenated RAW into the query — see Security
$this->db->delete($table, $whereRawSql, $limit = 1);
```

- `select()`/`insert()` are safe by default — values are always bound, never interpolated.
- `update()`/`delete()`'s `$where` parameter is a **raw SQL fragment**, string-concatenated straight into the query (`"UPDATE $table SET $fieldDetails WHERE $where"`). Only ever build this from hardcoded/constant strings. If you need to filter by user-controlled input in an update/delete, either extend `Database` with a properly parameterized variant or hand-write a prepared statement instead of using `$where`.
- `insert()`/`update()` set `$_SESSION['success']`/`$_SESSION['error']` flash messages as a side effect — be aware if you're calling these outside a typical page-render flow (e.g. from the JSON-only Facebook poster endpoints, this side effect is harmless but unused).

## Known schema (reverse-engineered from code — no schema file exists)

| Table | Created by | Notes |
|---|---|---|
| `facebook_posts` | `cron/install_database.php` (idempotent `CREATE TABLE IF NOT EXISTS`) | `id, title, category, link, image, facebook_post_id, posted_date, created_at, updated_at, status`. Columns `page_key`, `page_id` are added automatically at runtime by `Facebookposter_Model::ensurePageMetadataColumns()` on first use if missing — i.e. this one table self-migrates on request, unlike everything else. |
| `contact` | — (no script in repo) | Written by `Index_Model::contact()`: `name, email, subject, message, created_at`. If you need to recreate this table, infer columns from that method. |
| `tbl_visitors_counter` | — (no script in repo) | Written by `Counter()` in both `errors_model.php` and `sitemap_model.php`: `visits, city, region, country, latitude, longitude, timezone, ip_address, page`. Fired on every request routed through those two controllers (visitor analytics via geoPlugin IP lookup). |
| `PAGE`, `SUBPAGE`, `tbl_public_notice`, `NEWS` | — (no script in repo) | Queried by unused methods (`Pages()`, `Sub_Pages()`, `PublicNotice()`, `findMostRecent()`) in `errors_model.php`/`sitemap_model.php`. **Nothing currently calls these methods** — likely leftovers from a boilerplate/template. Don't assume they exist in the live DB without checking; don't build new features on top of them without confirming they're real, current tables first.

## Adding a new table

Follow the `cron/install_database.php` pattern: a small standalone PHP script using `CREATE TABLE IF NOT EXISTS`, documented with what it creates and how to run it (`php path/to/install_script.php`). This keeps setup reproducible in the absence of a migration framework. Don't silently assume a table exists — either write the install script or confirm with the user that it's already present on the live DB.

## Common mistakes to avoid

- Passing user-controlled data into `update()`/`delete()`'s `$where` argument — it's not parameterized.
- Assuming a shared/pooled DB connection — every `Model` subclass opens its own `PDO` connection in its constructor (`Model::__construct()`), so instantiating multiple models in one request opens multiple connections.
- Adding a query for `PAGE`/`SUBPAGE`/`NEWS`/`tbl_public_notice` assuming they're part of the live schema without verifying — see above.
- Forgetting that `insert()`/`update()` return `null` (not `false`) and set a `$_SESSION` flash message on failure instead of throwing — check return values loosely (`if ($result)` not `if ($result !== false)`), matching existing call sites.
