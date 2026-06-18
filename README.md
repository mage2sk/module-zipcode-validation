<!-- SEO Meta -->
<!--
  Title: Magento 2 ZIP PIN Code Validation Extension at Checkout | Panth Infotech
  Description: Panth Zipcode Validation validates ZIP and PIN codes at Magento 2 checkout, customer account, and registration against configurable country-specific ranges. Pre-loaded data for India, US, UK, Canada, Australia, and Europe. Admin grid, CSV/JSON import-export, real-time AJAX validation. Works on Magento 2.4.4 to 2.4.8 and PHP 8.1 to 8.4.
  Keywords: magento 2 zipcode validation, magento 2 pin code validation, magento 2 postal code validation, india pincode magento, magento 2 checkout zipcode, magento 2 serviceable area, magento 2 delivery restriction, magento 2 zip code restriction, real time postcode validation magento
  Author: Kishan Savaliya (Panth Infotech)
  Canonical: https://kishansavaliya.com/magento-2-zipcode-validation.html
-->

# Magento 2 ZIP and PIN Code Validation Extension: Restrict Checkout by Delivery Area

[![Magento 2.4.4 - 2.4.8](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.8-orange?logo=magento&logoColor=white)](https://magento.com)
[![PHP 8.1 - 8.4](https://img.shields.io/badge/PHP-8.1%20--%208.4-blue?logo=php&logoColor=white)](https://php.net)
[![Luma](https://img.shields.io/badge/Theme-Luma-14b8a6)](https://magento.com)
[![Live Demo & Details](https://img.shields.io/badge/Live%20Demo%20%26%20Details-magento--2--zipcode--validation-0D9488?style=flat)](https://kishansavaliya.com/magento-2-zipcode-validation.html)
[![Packagist](https://img.shields.io/badge/Packagist-mage2kishan%2Fmodule--zipcode--validation-orange?logo=packagist&logoColor=white)](https://packagist.org/packages/mage2kishan/module-zipcode-validation)
[![Upwork Top Rated Plus](https://img.shields.io/badge/Upwork-Top%20Rated%20Plus-14a800?logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/~016dd1767321100e21)
[![Website](https://img.shields.io/badge/Website-kishansavaliya.com-0D9488)](https://kishansavaliya.com)

> **Block orders from areas you cannot ship to.** Panth Zipcode Validation checks ZIP and PIN codes at checkout, on customer account pages, and at registration against a configurable database of country-specific ranges. Pre-loaded data covers India, US, UK, Canada, Australia, and major European countries. Admins can add and edit ranges from a grid, or bulk-load thousands of codes via CSV or JSON import.

**Product page:** [kishansavaliya.com/magento-2-zipcode-validation.html](https://kishansavaliya.com/magento-2-zipcode-validation.html)

---

## Quick Answer

**What is Panth Zipcode Validation?** It is a Magento 2 extension that validates ZIP and PIN codes entered at checkout, on customer address pages, and at registration against a database of allowed ranges. Customers outside your delivery area see an error message and cannot proceed.

**What does it add to my store?**

- **Real-time AJAX validation** at checkout so customers see the error the moment they type a postcode that is outside your service area.
- **State/region auto-detection** that reads the state name from the PIN code and shows it to the customer.
- **An admin grid** under Panth Infotech where you can add, edit, and delete country/state ranges one at a time.
- **CSV and JSON import/export** so you can load thousands of serviceable codes in one go.
- **Pre-loaded range data** for India (all states and union territories), US, UK, Canada, Australia, Germany, France, Italy, Spain, and the Netherlands.

**Which themes are supported?** Luma and compatible checkouts that use standard Magento address forms.

**What does it need?** Magento 2.4.4 to 2.4.8, PHP 8.1 to 8.4, and the free `mage2kishan/module-core` package.

---

## Need Custom Magento 2 Development?

> **Get a free quote for your project in 24 hours** for custom modules, Hyva themes, performance work, M1 to M2 migrations, and Adobe Commerce Cloud.

<p align="center">
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/Get%20a%20Free%20Quote%20%E2%86%92-Reply%20within%2024%20hours-DC2626?style=for-the-badge" alt="Get a Free Quote" />
  </a>
</p>

<table>
<tr>
<td width="50%" align="center">

### Kishan Savaliya
**Top Rated Plus on Upwork**

[![Hire on Upwork](https://img.shields.io/badge/Hire%20on%20Upwork-Top%20Rated%20Plus-14a800?style=for-the-badge&logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/~016dd1767321100e21)

100% Job Success • 10+ Years Magento Experience
Adobe Certified • Hyva Specialist

</td>
<td width="50%" align="center">

### Panth Infotech Agency
**Magento Development Team**

[![Visit Agency](https://img.shields.io/badge/Visit%20Agency-Panth%20Infotech-14a800?style=for-the-badge&logo=upwork&logoColor=white)](https://www.upwork.com/agencies/1881421506131960778/)

Custom Modules • Theme Design • Migrations
Performance • SEO • Adobe Commerce Cloud

</td>
</tr>
</table>

**Visit our website:** [kishansavaliya.com](https://kishansavaliya.com) &nbsp;|&nbsp; **Get a quote:** [kishansavaliya.com/get-quote](https://kishansavaliya.com/get-quote)

---

## Table of Contents

- [Who Is It For](#who-is-it-for)
- [Key Features](#key-features)
- [Pre-loaded Country Ranges](#pre-loaded-country-ranges)
- [Compatibility](#compatibility)
- [Installation](#installation)
- [Configuration](#configuration)
- [How It Works](#how-it-works)
- [Admin Grid](#admin-grid)
- [CSV and JSON Import-Export](#csv-and-json-import-export)
- [FAQ](#faq)
- [Support](#support)
- [About Panth Infotech](#about-panth-infotech)
- [Quick Links](#quick-links)

---

## Who Is It For

- **Indian eCommerce stores** that deliver only to certain PIN code ranges and need to stop orders from unserviceable areas before they reach the warehouse.
- **Regional retailers** in the US, UK, Canada, Australia, or Europe that ship to defined ZIP or postal code zones.
- **Courier and logistics businesses** running their own Magento storefront and needing a fast way to configure hundreds of serviceable codes.
- **Multi-country merchants** who need different delivery rules per store view or website scope.
- **Stores with large serviceable code lists** who want to load and update ranges via CSV or JSON rather than entering them one at a time.

---

## Key Features

### Real-time Validation at Checkout

- **AJAX validation as the customer types** so they see the result without submitting the form.
- **Debounced requests** so the server is not hit on every keystroke.
- **Configurable error message** shown inline under the postcode field.
- **Optional success message** that can include the detected state name using the `{state}` placeholder.
- **Validates on checkout**, on the **customer account address page**, and at **customer registration** (each can be turned on or off separately).

### State and Region Auto-detection

- **State name read from the postal code** and shown to the customer, so they can confirm the right area.
- **State mismatch detection** that warns the customer when the detected state does not match the state field they selected.
- **Numeric and alphanumeric range comparison** to handle both Indian PIN codes and formats like UK postcodes.

### Admin Grid for Range Management

- **Full UI component grid** at Panth Infotech > Zipcode Validation > Manage Ranges.
- **Add, edit, and delete ranges** with a form that captures country, state code, state name, and ZIP/PIN start and end.
- **Enable or disable individual ranges** with the status toggle.
- **Search, filter, sort, and paginate** across all saved ranges.
- **Mass delete** for bulk cleanup.

### CSV and JSON Import-Export

- **Bulk import** from a CSV or JSON file so you can load all your serviceable codes in one operation.
- **Bulk export** to CSV or JSON to back up your current ranges or edit them offline.
- **Sample template download** from the admin so you always have the correct column order.
- **Validation on import** with row-level error reporting for bad format or duplicate entries.

### Pre-loaded Country Data

- **Data patches on install** load range data for India (all 38 states and union territories), US (15 major states), UK, Canada, Australia, Germany, France, Italy, Spain, and the Netherlands.
- All pre-loaded data is editable and extendable from the admin grid.

### Built to Last

- **DB-backed storage** in the `panth_zipcode_range` table with indexes on country and active status for fast lookups.
- **Configurable message colors** for both success and error states.
- **Clean DI-only code** that follows MEQP standards.
- **Translation ready** using Magento's `__()` function.

---

## Pre-loaded Country Ranges

The module ships data patches that load postal code ranges for the following countries on install:

| Country | Format | Coverage |
|---|---|---|
| India | 6-digit PIN codes | All 38 states and union territories |
| United States | 5-digit ZIP codes | 15 major states |
| United Kingdom | Alphanumeric postcodes | England, Scotland, Wales, Northern Ireland |
| Canada | Alphanumeric postal codes | All provinces and territories |
| Australia | 4-digit postcodes | All states and territories |
| Germany | 5-digit postleitzahl | Nationwide |
| France | 5-digit code postal | Nationwide |
| Italy | 5-digit CAP | Nationwide |
| Spain | 5-digit codigo postal | Nationwide |
| Netherlands | Alphanumeric postcodes | Nationwide |

You can enable, disable, edit, or remove any pre-loaded range from the admin grid after install.

---

## Compatibility

| Requirement | Versions Supported |
|---|---|
| Magento Open Source | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| Adobe Commerce | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| Adobe Commerce Cloud | 2.4.4 to 2.4.8 |
| PHP | 8.1.x, 8.2.x, 8.3.x, 8.4.x |
| MySQL | 8.0+ |
| MariaDB | 10.4+ |
| Luma Theme | Native support |
| Required Dependency | `mage2kishan/module-core` (free) |

---

## Installation

### Composer Installation (Recommended)

```bash
composer require mage2kishan/module-zipcode-validation
bin/magento module:enable Panth_Core Panth_ZipcodeValidation
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Manual Installation via ZIP

1. Download the latest release from [Packagist](https://packagist.org/packages/mage2kishan/module-zipcode-validation) or from the [product page](https://kishansavaliya.com/magento-2-zipcode-validation.html).
2. Extract it to `app/code/Panth/ZipcodeValidation/` in your Magento install.
3. Make sure `Panth_Core` is installed too (required dependency).
4. Run the commands above starting from `bin/magento module:enable`.

### Verify Installation

```bash
bin/magento module:status Panth_ZipcodeValidation
# Expected: Module is enabled
```

After install, open:
```
Admin -> Panth Infotech -> Zipcode Validation -> Manage Ranges
Admin -> Stores -> Configuration -> Panth Extensions -> Zipcode Validation
```

---

## Configuration

Go to **Stores -> Configuration -> Panth Extensions -> Zipcode Validation**.

| Setting | Group | Default | Description |
|---|---|---|---|
| Enable Zipcode Validation | General Settings | Yes | Master toggle. Turns all validation on or off. |
| Validate on Checkout | General Settings | Yes | Run validation on the checkout shipping address step. |
| Validate on Customer Account | General Settings | Yes | Run validation when a customer saves an address in their account. |
| Validate on Registration | General Settings | Yes | Run validation during customer registration. |
| Error Message | General Settings | "Please enter a valid Indian PIN code (6 digits)." | Message shown when a postcode fails validation. |
| Show Success Message | General Settings | Yes | Show a green confirmation message when the postcode is valid. |
| Success Message Format | General Settings | "Valid PIN code for {state}" | Text of the success message. Use `{state}` to insert the detected state name. |
| Success Message Color | Display Settings | #007a33 | Hex color for the success message text. |
| Error Message Color | Display Settings | #e02b27 | Hex color for the error message text. |

The **ZIP/PIN Code Ranges** group in the same configuration section links you to the dedicated admin grid where you manage all serviceable ranges.

---

## How It Works

1. A customer types a postcode in the shipping address field on checkout, in their address book, or at registration.
2. The storefront JavaScript debounces the input and sends an AJAX request to `/zipcodevalidation/validate/pincode` with the postcode and country code.
3. The server looks up the `panth_zipcode_range` table for an active range that covers the given postcode for that country.
4. If a match is found, the response includes the state name. The field shows the success message with the state name if that option is enabled.
5. If no match is found, the inline error message is shown and the form prevents submission.
6. If the detected state does not match the state the customer selected, a mismatch warning is shown.

---

## Admin Grid

Open **Panth Infotech -> Zipcode Validation -> Manage Ranges** from the admin menu.

The grid shows all rows in the `panth_zipcode_range` table:

- **Search** by country code, state name, or range values.
- **Filter** by country, active status, or creation date.
- **Sort** any column ascending or descending.
- **Add a new range** with the Add Range button.
- **Edit or delete** a single range from the row actions menu.
- **Mass delete** selected rows with the mass actions dropdown.

Each range record stores: country code, state code (optional), state name, ZIP/PIN start, ZIP/PIN end, and active status.

---

## CSV and JSON Import-Export

### Import

1. Go to the Manage Ranges grid and click **Import**.
2. Download the CSV or JSON sample template to see the required columns.
3. Fill in your postal code ranges.
4. Upload the file. The module validates each row, reports errors by row number, and skips duplicates.

**CSV format example:**
```csv
country_id,state_code,state_name,zip_start,zip_end,is_active
IN,DL,Delhi,110001,110096,1
IN,MH,Maharashtra,400001,400099,1
US,CA,California,90001,96162,1
```

**JSON format example:**
```json
[
  {"country_id": "IN", "state_code": "DL", "state_name": "Delhi", "zip_start": "110001", "zip_end": "110096", "is_active": 1},
  {"country_id": "US", "state_code": "CA", "state_name": "California", "zip_start": "90001", "zip_end": "96162", "is_active": 1}
]
```

### Export

Click **Export** from the grid toolbar. Choose CSV or JSON. The export respects any active grid filters, so you can export a single country or a filtered subset.

---

## FAQ

### Does this work with default Magento checkout?

Yes. The module hooks into the standard Magento checkout shipping address form using a RequireJS mixin on the postcode UI component. No checkout rewrites are needed.

### Can I validate only for specific countries?

Yes. Only the countries that have at least one active range in the grid are validated. Customers entering postcodes for countries with no ranges pass through without an error.

### How do I load PIN codes for all of India?

The module pre-loads Indian PIN code ranges for all 38 states and union territories via a data patch during `setup:upgrade`. You can also add or update ranges via CSV import.

### Can I import 50,000 pincodes at once?

Yes. The CSV importer processes rows in batches. For very large files (100k+ rows), run the import from CLI to avoid admin session timeouts.

### Does it slow down checkout?

No. Lookups hit the `panth_zipcode_range` table which has indexes on `country_id` and `is_active`. Typical AJAX response is fast. The JS debounce prevents requests on every keystroke.

### Can guests check out without a valid postcode?

No. Validation runs for both guests and logged-in customers whenever the configured form types are enabled.

### Does it support multi-store setups?

Yes. The configuration is scoped to default, website, and store view so different stores can have different validation settings.

### Does validation run on customer registration?

Yes. The **Validate on Registration** setting in General Settings turns this on or off. When enabled, customers cannot complete registration with an invalid postcode.

### Does Panth Zipcode Validation need Panth Core?

Yes. `mage2kishan/module-core` is a free, required dependency that Composer installs for you automatically.

---

## Support

| Channel | Contact |
|---|---|
| Product Page | [kishansavaliya.com/magento-2-zipcode-validation.html](https://kishansavaliya.com/magento-2-zipcode-validation.html) |
| Email | kishansavaliyakb@gmail.com |
| Website | [kishansavaliya.com](https://kishansavaliya.com) |
| WhatsApp | +91 84012 70422 |
| GitHub Issues | [github.com/mage2sk/module-zipcode-validation/issues](https://github.com/mage2sk/module-zipcode-validation/issues) |
| Upwork (Top Rated Plus) | [Hire Kishan Savaliya](https://www.upwork.com/freelancers/~016dd1767321100e21) |
| Upwork Agency | [Panth Infotech](https://www.upwork.com/agencies/1881421506131960778/) |

Response time: 1-2 business days.

### Need Custom Magento Development?

Looking for **custom Magento module development**, **Hyva theme work**, **store migrations**, or **performance tuning**? Get a free quote in 24 hours:

<p align="center">
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/%F0%9F%92%AC%20Get%20a%20Free%20Quote-kishansavaliya.com%2Fget--quote-DC2626?style=for-the-badge" alt="Get a Free Quote" />
  </a>
</p>

<p align="center">
  <a href="https://www.upwork.com/freelancers/~016dd1767321100e21">
    <img src="https://img.shields.io/badge/Hire%20Kishan-Top%20Rated%20Plus-14a800?style=for-the-badge&logo=upwork&logoColor=white" alt="Hire on Upwork" />
  </a>
  &nbsp;&nbsp;
  <a href="https://www.upwork.com/agencies/1881421506131960778/">
    <img src="https://img.shields.io/badge/Visit-Panth%20Infotech%20Agency-14a800?style=for-the-badge&logo=upwork&logoColor=white" alt="Visit Agency" />
  </a>
  &nbsp;&nbsp;
  <a href="https://kishansavaliya.com/magento-2-zipcode-validation.html">
    <img src="https://img.shields.io/badge/View%20Product%20Page-magento--2--zipcode--validation-0D9488?style=for-the-badge" alt="View Product Page" />
  </a>
</p>

---

## About Panth Infotech

Built and maintained by **Kishan Savaliya** ([kishansavaliya.com](https://kishansavaliya.com)), a **Top Rated Plus** Magento developer on Upwork with 10+ years of eCommerce experience.

**Panth Infotech** is a Magento 2 development agency that builds high quality, security focused extensions and themes for both Hyva and Luma storefronts. The extension suite covers SEO, performance, checkout, product presentation, customer engagement, and store management, with each module built to MEQP standards and tested across Magento 2.4.4 to 2.4.8.

Browse the full extension catalog on our [Magento extensions page](https://kishansavaliya.com/magento-extensions.html) or on [Packagist](https://packagist.org/packages/mage2kishan/).

---

## Quick Links

| Resource | Link |
|---|---|
| **Product Page** | [magento-2-zipcode-validation.html](https://kishansavaliya.com/magento-2-zipcode-validation.html) |
| **Packagist** | [mage2kishan/module-zipcode-validation](https://packagist.org/packages/mage2kishan/module-zipcode-validation) |
| **GitHub** | [mage2sk/module-zipcode-validation](https://github.com/mage2sk/module-zipcode-validation) |
| **Website** | [kishansavaliya.com](https://kishansavaliya.com) |
| **Free Quote** | [kishansavaliya.com/get-quote](https://kishansavaliya.com/get-quote) |
| **Upwork (Top Rated Plus)** | [Hire Kishan Savaliya](https://www.upwork.com/freelancers/~016dd1767321100e21) |
| **Upwork Agency** | [Panth Infotech](https://www.upwork.com/agencies/1881421506131960778/) |
| **Email** | kishansavaliyakb@gmail.com |
| **WhatsApp** | +91 84012 70422 |

---

<p align="center">
  <strong>Ready to restrict checkout to your delivery areas?</strong><br/>
  <a href="https://kishansavaliya.com/magento-2-zipcode-validation.html">
    <img src="https://img.shields.io/badge/%F0%9F%9A%80%20See%20Zipcode%20Validation%20%E2%86%92-Product%20Page%20%26%20Details-DC2626?style=for-the-badge" alt="See Zipcode Validation" />
  </a>
</p>

---

**SEO Keywords:** magento 2 zipcode validation, magento 2 pin code validation, magento 2 postal code validation, india pincode magento, magento 2 checkout zipcode, magento 2 serviceable area, magento 2 delivery restriction, magento 2 zip code restriction, real time postcode validation magento, magento 2 pincode checker, magento 2 postcode validator, india pincode checkout magento, us zip validation magento, uk postcode checkout, canada postal code magento, australia postcode magento, european postcode validation, magento 2 ajax postcode validation, magento 2 shipping restriction by pincode, courier serviceable pincode magento, magento 2 address validation, magento 2 registration postcode, panth zipcode validation, panth infotech, hire magento developer, top rated plus upwork, kishan savaliya magento, custom magento development, mage2kishan, magento 2.4.8 module, php 8.4 magento, magento 2 checkout optimization
