# How it works

For site owners and the people who look after their sites.

## The widget

The plugin adds an **Export Customer Data** widget to the WordPress Dashboard. Only users with
the `manage_options` capability see it, which normally means administrators. Shop managers
don't, because the export includes every user account, not only customers.

The widget has two buttons:

- **Export All Users** downloads `all-users.csv`: every user account on the site, whatever its
  role
- **Export Customers Only** downloads `customers-only.csv`: registered users who have placed at
  least one order. Without WooCommerce, the widget says so instead of showing this button

You can move or hide the widget like any other, from **Screen Options** at the top of the
Dashboard.

## The columns

By default each file has six columns, in this order:

- `user_id`: the WordPress user ID
- `first_name`, `last_name`: from the user's profile
- `email`: the account's email address
- `billing_phone`, `shipping_phone`: from the WooCommerce customer details

A value that isn't set is an empty cell. A developer can add or remove columns: see
[Hooks and filters](developers/hooks-and-filters.md).

Users are listed in order of user ID.

## Who counts as a customer

A customer is a registered user with at least one order of any status: pending, processing,
on hold, completed, cancelled, refunded, failed, or a status another plugin adds. These don't
count:

- Guest orders, because there's no user account to export
- Checkout drafts, which the Checkout block creates as soon as someone reaches the checkout,
  whether or not they order
- Orders in the bin
- Refunds on their own

If a customer's account has since been deleted, they aren't in the file, even though their
orders still exist. If nobody has an order yet, the button shows a message instead of an empty
file.

It works the same whether WooCommerce stores orders in its own tables (High-Performance Order
Storage) or as posts.

## The file

- UTF-8, with a byte-order mark at the start so that Excel shows accented names correctly
- Comma-separated, with standard quoting: a value containing a comma, a quote or a line break
  is wrapped in double quotes
- **Formulas are written as text.** Anyone can type anything into their own name or phone
  number. A value starting with `=`, `+`, `-` or `@` could run as a formula when the file is
  opened in a spreadsheet, so the plugin puts an apostrophe in front of it (`'=SUM(A1)`), which
  spreadsheets treat as "this is text". Phone numbers such as `+44 7700 900123` and plain
  numbers such as `-12.50` are left alone
- The file is built as it downloads and is never saved on the server. Users are read 500 at a
  time, so a site with tens of thousands of accounts doesn't run out of memory

## The download links

Each button links to `wp-admin/admin-post.php` with a nonce, a one-off code tied to your login.
A link only works for the person it was shown to, and stops working after a day or so. If a
link has expired, reload the Dashboard and use the button again.

The download also goes through anything else that protects `wp-admin`, such as two-factor
authentication.

Version 1.x used addresses like `/wp-admin/exportwcd-reports/all-users.csv`. They had no nonce,
so 2.0.0 stopped serving files from them; they now redirect to the Dashboard. Update any
bookmarks to the Dashboard itself.

## Personal data

The export contains names, email addresses and phone numbers, which are personal data under
UK and EU GDPR. The plugin only makes the file; what happens to it afterwards is up to you:

- Have a lawful basis for what you're going to do with it, such as sending a mailing to people
  who agreed to receive one
- Keep the file somewhere secure, and delete it when you've finished
- Don't send it by email or upload it anywhere you wouldn't put the original data

## Updates

The plugin checks GitHub for new releases, at most every 12 hours, and offers them on the
normal **Dashboard → Updates** screen. That request goes to `api.github.com`. Like every request
WordPress makes, it carries your site's address in its user agent; it sends nothing about your
users. A developer can switch it off with `export_woo_customer_data_updater_enabled`.

## Uninstalling

Deleting the plugin from the **Plugins** screen removes the one option it stores (its installed
version) and its update-check cache. Your users and orders are never touched, and there are no
export files to clean up.
