# CLAUDE.md

Repository: public

Follows the **headwall-wp-plugin** skill. Load it before working on this plugin; this file holds
only what is specific to Export Woo Customer Data.

## Project Overview

A Dashboard widget with two buttons that download users as CSV: every user, or registered
users with at least one placed WooCommerce order. Six columns by default, changeable through
one filter.

- **Namespace:** `Export_Woo_Customer_Data` · **Text domain:** `export-woo-customer-data` · **Prefixes:** `export_woo_customer_data_` (options, hooks), `EXPORT_WOO_CUSTOMER_DATA_` (constants)
- **PHP / WordPress floor:** 8.2 / 6.2. `wp_cache_delete_multiple()` needs 6.0
- **WooCommerce:** optional, 9.0+. Without it only the all-users export is offered
- **No build system, no settings page, no assets, no WP-CLI commands**

## Request Flow

1. `wp_dashboard_setup`: `Dashboard_Widget::register()` adds the widget for `manage_options`.
   `render()` builds each button's URL with `get_export_url()`:
   `admin-post.php?action=export_woo_customer_data_export&report=<type>&_wpnonce=...`
2. `admin_post_export_woo_customer_data_export`: `Plugin::handle_export_request()` checks the
   capability, then `verify_admin_action_nonce()`, then `is_available_report_type()`, and
   `wp_die()`s with 403 or 400. Anything else in `wp-admin` that runs on `admin_init` (Quick
   2FA, for one) runs first
3. `CSV_Exporter::send()`: for customers, `get_customer_ids()` first (404 if none, before any
   header). Then headers, and `write_csv()` streams to `php://output`: BOM, header row, then
   `USER_BATCH_SIZE` users per `get_users()` call, with only the needed `users` columns. Meta
   is primed per batch with `update_meta_cache()`, read through `get_user_meta()` so
   `get_user_metadata` filters apply, and evicted with `wp_cache_delete_multiple()`
4. Every cell goes through `to_csv_cell()`: scalars only, formula triggers get a leading `'`
   unless the value matches `CSV_NUMERIC_PATTERN`
5. `template_redirect`: `redirect_legacy_url()` sends the 1.x addresses
   (`LEGACY_URL_PATTERN`) to the Dashboard. 1.x served files from them with no nonce

`get_customer_ids()` reads the orders table directly (HPOS, or `posts`/`postmeta`), excluding
`NON_ORDER_STATUSES` and non-`shop_order` types. `wc_get_orders()` would load every order
object.

## Key Files

| File | Purpose |
|------|---------|
| `export-woo-customer-data.php` | Header, constants, requires, updater, activation |
| `constants.php` | Keys, report types and filenames, default fields, column allow-list, batch size, statuses, CSV patterns |
| `functions-private.php` | `get_export_url()`, `verify_admin_action_nonce()`, `is_available_report_type()`, `to_csv_cell()`, logging |
| `includes/class-plugin.php` | Wiring, request handler, legacy redirect, WooCommerce compatibility, textdomain, first run |
| `includes/class-dashboard-widget.php` | Widget registration and render |
| `includes/class-csv-exporter.php` | Customer IDs, field mapping and validation, batching, CSV writing |
| `includes/class-github-updater.php` | Copied from Quick 2FA; change only `namespace` and `@package` |
| `views/dashboard-widget.php` | The widget's markup |

## Data Storage

- Option `export_woo_customer_data_version`: installed version, written on activation and by
  `Plugin::check_first_run()`. Its first write also flushes rewrite rules, which removes the
  1.x rule
- Updater transients, named from the slug: `export_woo_customer_data_github_release` and
  `export_woo_customer_data_github_failed`
- Nothing else. Exports are never written to disk
- `uninstall.php` deletes the version option and the updater cache

## Public Contracts

The skill's `public-contracts.md` has the rules. This plugin's:

| Contract | Names |
|----------|-------|
| Filters | `export_woo_customer_data_fields`, `export_woo_customer_data_updater_enabled` |
| Field config shape | `table` (`users` or `usermeta`), `column` or `meta_key`; the array keys are the CSV headings |
| Download URL | `admin-post.php?action=export_woo_customer_data_export&report=all-users\|customers-only` |
| Report types and filenames | `all-users`, `customers-only`; `all-users.csv`, `customers-only.csv` |
| Dashboard widget ID | `export_woo_customer_data_widget`. Users' widget order and hidden state are stored under it |
| Stored keys | `export_woo_customer_data_version` |
| Legacy redirect | `/wp-admin/exportwcd-reports/*.csv` → Dashboard. Keep until 3.0.0 at least |

Hook names are written literally at each `apply_filters()` call, so phpcs can check their
prefix. `FILTER_UPDATER_ENABLED` is the exception because it is passed to `Github_Updater`.
Both filters are documented in `docs/developers/hooks-and-filters.md`.

### High-Impact Code

These paths decide who can download every user's personal data, and what goes in the file:

- `Plugin::handle_export_request()` and `verify_admin_action_nonce()`
- `CSV_Exporter::get_field_mapping()`, `is_valid_field_config()` and `EXPORTABLE_USER_COLUMNS`
- `to_csv_cell()`

Never log user data. `log_error()` and `log_debug()` take messages about counts, field names
and report types only.

## Testing

Harnesses live in `dev-notes/testing/`. Run each with
`wp eval-file wp-content/plugins/export-woo-customer-data/dev-notes/testing/<file>.php`:

- `test-functions-private.php`: the cell guard, report types, URL and nonce helpers
- `test-csv-exporter.php`: field validation, customer IDs against `wc_get_orders()`, both
  exports, paging across deleted-user gaps, meta-cache eviction. Read-only
- `test-plugin.php`: refusals (403/400), `send()`, the legacy redirect, the first-run check,
  the widget

The success path of `handle_export_request()` ends in `exit()`, so it is tested over HTTP: on
the dev site Quick 2FA challenges `admin-post.php`, so a test needs a session token stamped
with Quick 2FA's `SESSION_KEY_VERIFIED`, both the `secure_auth` and `logged_in` cookies, and
the link taken from the rendered Dashboard (a nonce made in WP-CLI has no session token and
fails). Destroy the token afterwards.

All of them must pass before a release and after any change to high-impact code.

## Release

Follow the skill's `release.md` (public). The updater's repository is `UPDATER_GITHUB_REPO` in
`constants.php`. The default branch is `main`.

## Reference Files

`docs/` (tracked, public):

- `docs/how-it-works.md`: site owners
- `docs/developers/hooks-and-filters.md`, `docs/developers/extending.md`: developers

`dev-notes/` (private, untracked):

- `dev-notes/testing/`: the harnesses
- `dev-notes/archive/`: the 1.x project tracker and generic coding notes, superseded by the
  skill. Not authoritative

<!-- wp-translate:begin v=1.2.0 hash=6ec5c4fb2dc4ebcbec767b1e941f34ce5cf86ed0fa957d0eaa5986c13b8d53bd -->
## Translating this plugin (wp-translate conventions)

This plugin's `.po`/`.mo` files are generated from source by
[wp-translate](https://github.com/headwalluk/wp-translate-tool), which
machine-translates strings with DeepL. Machine translation is only as good as
the strings you give it — follow these conventions when adding or editing
user-facing text.

### 1. Disambiguate short or ambiguous strings with `_x()`

DeepL handles full sentences well but guesses badly on short, context-free
labels. Give it context with `_x()` (or `esc_html_x()`, `_ex()`):

```php
// Ambiguous out of context — DeepL may read "Sent" as "late", "Folder" as "leaflet"
__( 'Sent', 'export-woo-customer-data' );

// Disambiguated — the context is passed to the translator and to DeepL
_x( 'Sent', 'email delivery status', 'export-woo-customer-data' );
_x( 'Folder', 'IMAP mailbox', 'export-woo-customer-data' );
_x( 'Open', 'verb; button label', 'export-woo-customer-data' );
```

The context (2nd argument) is never shown to users. Use it whenever a string is a
single word, a short label, or has more than one plausible meaning.

### 2. Use placeholders, never concatenation

Build dynamic text with `printf`/`sprintf` so the whole sentence translates as a
unit, and add a `translators:` comment to explain each placeholder:

```php
/* translators: %s is the user's display name */
printf( esc_html__( 'Welcome back, %s', 'export-woo-customer-data' ), $name );
```

Never split a sentence across multiple translation calls — word order differs
between languages.

### 3. Use `_n()` for anything that can be counted

Never build a count-dependent sentence by hand, and never settle for a single
form that reads correctly only for one number. Languages differ in how many
plural forms they have — English and German have two, French treats 0 as
singular, Polish and Russian have three, Japanese has one, Arabic has six — and
`_n()` is the only way to express that.

```php
// Wrong — "1 reviews", and untranslatable into languages with other forms
printf( esc_html__( '%d reviews', 'export-woo-customer-data' ), $count );

// Right — wp-translate fills every form the target locale needs
printf(
    esc_html( _n( '%d review', '%d reviews', $count, 'export-woo-customer-data' ) ),
    $count
);
```

Keep the placeholder in **both** forms, even when the singular reads fine
without it (`'%d review'`, not `'One review'`) — some locales use the singular
slot for other numbers too.

For a short or ambiguous countable noun, use `_nx()` — the plural equivalent of
`_x()` — so the context reaches DeepL:

```php
// "Review" alone is ambiguous: critique? opinion? inspection?
_nx( '%d review', '%d reviews', $count, 'customer feedback on a company', 'export-woo-customer-data' );
```

**Locales needing more than two forms will have their extra slots left empty for
a human translator.** DeepL supplies a singular and a plural; nobody can invent
Polish's third form from those, and wp-translate deliberately leaves it blank
rather than filling it with a plausible guess. Expect to see empty
`msgstr[2]` entries in `pl_PL` — that is correct behaviour, not a failure.

### 4. Acronyms and technical tokens

wp-translate keeps common acronyms (`TLS`, `API`, `SMTP`, `URL`, `ID`, `UTC`, …)
verbatim automatically. If you introduce an unusual acronym or product name that
must not be translated, keep it as its own standalone string so it is recognised,
or ask the maintainer to add it to the tool's acronym list.

### 5. Don't translate dates — let WordPress localise them

Never add month or day-of-week names (full or abbreviated) as translatable
strings. DeepL frequently mistranslates short forms like `Mon`, `Tue`, `Jan`,
`Feb` even with context hints. WordPress already ships locale-aware names — use
`$wp_locale`:

```php
global $wp_locale;
$wp_locale->get_month( $month_number );        // "January" (1-based)
$wp_locale->get_month_abbrev( $month_name );   // "Jan"
$wp_locale->get_weekday( $weekday_number );     // "Monday" (0 = Sunday)
$wp_locale->get_weekday_abbrev( $weekday_name ); // "Mon"
```

For formatted dates, prefer `wp_date()` / `date_i18n()`, which localise month and
day names automatically.

### 6. English source dialect

Write source strings in standard English. wp-translate handles English targets
locally (no DeepL): `en`/`en_US` use the source as-is, and `en_GB`/`en_AU`/… get
American spellings converted to British automatically (`color` → `colour`).

### Running wp-translate

After changing strings, regenerate translations:

```bash
wp-translate /path/to/this-plugin              # auto-detect locales from languages/
wp-translate /path/to/this-plugin en_GB,fr_FR  # explicit locales
wp-translate /path/to/this-plugin --dry-run    # preview; no API calls, no writes
```

Requires WP-CLI (`wp`) and a DeepL API key at `~/.config/deepl.env`. The tool
regenerates the `.pot` from source, translates new/changed strings for each
locale, and compiles the `.mo` files.
<!-- wp-translate:end -->
