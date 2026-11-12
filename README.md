# Export Woo Customer Data

A small WordPress plugin that puts two download buttons on the Dashboard: one for every user
account on the site, and one for WooCommerce customers who have placed an order. Each gives you
a CSV file you can open in Excel, Numbers or Google Sheets.

It's for the times you just need a list: names, email addresses and phone numbers for a
mailing, a CRM import or a quick check, without setting up a full reporting plugin.

## What it does

- **Dashboard widget**: an **Export Customer Data** box with an **Export All Users** button and,
  when WooCommerce is active, an **Export Customers Only** button
- **Six columns by default**: user ID, first name, last name, email, billing phone and shipping
  phone. A developer can add or remove columns with a filter
- **Customers only** means registered users with at least one order. Guest orders, abandoned
  Checkout block drafts and trashed orders don't count
- **Opens cleanly in Excel**: UTF-8 with a byte-order mark, so accented names display properly,
  and any value that a spreadsheet would run as a formula is written as plain text
- **Nothing is stored**: the file is built as it downloads, a page of users at a time, so large
  sites don't run out of memory

Only administrators (users with the `manage_options` capability) see the widget or can
download a file. Every download link carries a nonce, so it only works for the person it was
shown to, for a limited time.

## Requirements

- WordPress 6.2 or later
- PHP 8.2 or later
- WooCommerce 9.0 or later, for the customers-only export. The all-users export works without it

It's compatible with WooCommerce High-Performance Order Storage.

## Installation

1. Download `export-woo-customer-data.zip` from the
   [latest release](https://github.com/headwalluk/export-woo-customer-data/releases/latest)
2. In WordPress, go to **Plugins → Add New Plugin → Upload Plugin** and upload the zip
3. Activate it, and go to the **Dashboard**

There are no settings. Updates arrive through the normal WordPress updates screen.

**Upgrading from 1.x:** version 1.x had no updater, so install 2.0.0 by hand once. The old
download addresses (`/wp-admin/exportwcd-reports/...csv`) now take you to the Dashboard; use the
widget's buttons instead. See [CHANGELOG.md](CHANGELOG.md).

## Documentation

- [How it works](docs/how-it-works.md): what each export contains, who counts as a customer,
  and what to keep in mind with personal data
- [Hooks and filters](docs/developers/hooks-and-filters.md) and
  [recipes](docs/developers/extending.md), for developers who want different columns

## Licence

GPL v2 or later. See [LICENSE](LICENSE).
