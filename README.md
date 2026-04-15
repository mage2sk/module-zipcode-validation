<!-- SEO Meta -->
<!--
  Title: Panth Zipcode Validation - ZIP/PIN Code Validation at Checkout for Magento 2 | Panth Infotech
  Description: Panth Zipcode Validation validates ZIP/PIN codes at Magento 2 checkout against configurable country-specific ranges. Pre-loaded ranges for India, US, UK, Canada, Australia, and Europe. Admin grid, CSV/JSON import-export, real-time AJAX validation. Compatible with Magento 2.4.4 - 2.4.8 and PHP 8.1 - 8.4.
  Keywords: magento 2 zipcode validation, pin code validation, postal code validation, india pincode magento, delivery area validation, magento 2 checkout zipcode, magento 2 serviceable area
  Author: Kishan Savaliya (Panth Infotech)
  Canonical: https://github.com/mage2sk/module-zipcode-validation
-->

# ZIP/PIN Code Validation at Checkout — Panth Zipcode Validation for Magento 2 | Panth Infotech

[![Magento 2.4.4 - 2.4.8](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.8-orange?logo=magento&logoColor=white)](https://magento.com)
[![PHP 8.1 - 8.4](https://img.shields.io/badge/PHP-8.1%20--%208.4-blue?logo=php&logoColor=white)](https://php.net)
[![License Proprietary](https://img.shields.io/badge/License-Proprietary-red)]()
[![Packagist](https://img.shields.io/badge/Packagist-mage2kishan%2Fmodule--zipcode--validation-orange?logo=packagist&logoColor=white)](https://packagist.org/packages/mage2kishan/module-zipcode-validation)
[![Upwork Top Rated Plus](https://img.shields.io/badge/Upwork-Top%20Rated%20Plus-14a800?logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/~016dd1767321100e21)
[![Panth Infotech Agency](https://img.shields.io/badge/Agency-Panth%20Infotech-14a800?logo=upwork&logoColor=white)](https://www.upwork.com/agencies/1881421506131960778/)
[![Website](https://img.shields.io/badge/Website-kishansavaliya.com-0D9488)](https://kishansavaliya.com)
[![Get a Quote](https://img.shields.io/badge/Get%20a%20Quote-Free%20Estimate-DC2626)](https://kishansavaliya.com/get-quote)

> **ZIP/PIN Code Validation at Checkout** — validate postal codes against configurable country-specific ranges before customers place an order. Ships with pre-loaded ranges for **India, US, UK, Canada, Australia, and Europe**, a full-featured **admin grid**, **CSV/JSON import-export**, and **real-time AJAX validation** on the storefront.

**Panth Zipcode Validation** lets merchants restrict checkout to serviceable delivery areas by validating ZIP/PIN codes against configurable country-specific ranges. Out of the box you get pre-loaded range data for India (PIN codes), United States (ZIP codes), United Kingdom (postcodes), Canada, Australia, and major European countries. Admins can extend or override any range through the admin grid, and merchants handling thousands of serviceable pincodes can use **CSV/JSON import-export** to manage data in bulk. On the storefront, validation runs in **real time via AJAX** so customers see "out of service area" messages instantly without waiting for full checkout submission.

Whether you're a courier/delivery business limiting to serviceable pincodes in India, a regional retailer restricting shipments to specific US states, or a European merchant validating postcodes per country, Panth Zipcode Validation provides a single configurable engine for all of it.

---

## 🚀 Need Custom Magento 2 Development?

> **Get a free quote for your project in 24 hours** — custom modules, Hyva themes, performance optimization, M1→M2 migrations, and Adobe Commerce Cloud.

<p align="center">
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/Get%20a%20Free%20Quote%20%E2%86%92-Reply%20within%2024%20hours-DC2626?style=for-the-badge" alt="Get a Free Quote" />
  </a>
</p>

<table>
<tr>
<td width="50%" align="center">

### 🏆 Kishan Savaliya
**Top Rated Plus on Upwork**

[![Hire on Upwork](https://img.shields.io/badge/Hire%20on%20Upwork-Top%20Rated%20Plus-14a800?style=for-the-badge&logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/~016dd1767321100e21)

100% Job Success • 10+ Years Magento Experience
Adobe Certified • Hyva Specialist

</td>
<td width="50%" align="center">

### 🏢 Panth Infotech Agency
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

- [Key Features](#key-features)
- [Pre-loaded Country Ranges](#pre-loaded-country-ranges)
- [Compatibility](#compatibility)
- [Installation](#installation)
- [Configuration](#configuration)
- [Admin Grid](#admin-grid)
- [CSV / JSON Import-Export](#csv--json-import-export)
- [Real-time AJAX Validation](#real-time-ajax-validation)
- [FAQ](#faq)
- [Support](#support)
- [About Panth Infotech](#about-panth-infotech)
- [Quick Links](#quick-links)

---

## Key Features

### ZIP / PIN Code Validation Engine

- **Country-specific ranges** — configure valid postal code ranges per country (e.g., 110000-110099 for Delhi, 90001-90210 for Los Angeles)
- **Multiple range types** — numeric ranges, alphanumeric patterns (UK, Canada), exact-match lists, and regex patterns
- **Pre-loaded country data** — India, US, UK, Canada, Australia, and major European countries ready to go
- **Allow-list or block-list mode** — either restrict to serviceable areas or block specific excluded ranges
- **Per-store configuration** — different range sets for different store views (useful for multi-country stores)

### Admin Grid

- **Full-featured UI component grid** — search, filter, sort, bulk actions, pagination
- **Inline add/edit/delete** — manage ranges without page reloads
- **Status toggles** — enable/disable individual ranges on the fly
- **Audit fields** — created-at and updated-at timestamps tracked automatically
- **Mass actions** — enable/disable/delete in bulk

### CSV / JSON Import-Export

- **Bulk import** — upload thousands of pincodes via CSV or JSON in one go
- **Bulk export** — download all configured ranges for backup or editing offline
- **Validation on import** — duplicate detection, format checks, clear error messages
- **Template downloads** — CSV and JSON sample templates provided in admin

### Real-time AJAX Validation

- **Instant feedback at checkout** — customers see validation errors the moment they type a postcode
- **Debounced AJAX requests** — no server flooding while customers type
- **Customizable error messages** — configure per-country "We do not deliver to this area" messaging
- **Graceful fallback** — if AJAX fails, server-side validation still runs on form submit
- **Works on both Hyva and Luma** — via `Panth\Core\Helper\Theme` detection

### Storefront Behaviour

- **Blocks checkout on invalid postcode** — customers cannot proceed to payment if postcode is outside serviceable area
- **Custom message per country** — India, US, UK, etc. each get their own rejection message
- **Guest and logged-in support** — validation runs for both customer types
- **Address book integration** — validates postcodes when customers save new addresses too

### Security & Performance

- **MEQP compliant** — passes Adobe's Magento Extension Quality Program
- **Indexed lookups** — optimized database schema with indexes on country_id and range fields
- **Cached country data** — pre-loaded ranges cached per store view for fast AJAX response
- **Built on Panth Core** — reuses shared admin foundation, zero duplication

---

## Pre-loaded Country Ranges

The module ships with curated postal code range data for the following countries:

| Country | Format | Coverage |
|---|---|---|
| India | 6-digit PIN codes | All 28 states + 8 union territories |
| United States | 5-digit ZIP codes | All 50 states + DC |
| United Kingdom | Alphanumeric postcodes | England, Scotland, Wales, N. Ireland |
| Canada | Alphanumeric postal codes | All 10 provinces + 3 territories |
| Australia | 4-digit postcodes | All 6 states + 2 territories |
| Germany | 5-digit postleitzahl | Nationwide |
| France | 5-digit code postal | Nationwide |
| Italy | 5-digit CAP | Nationwide |
| Spain | 5-digit código postal | Nationwide |
| Netherlands | Alphanumeric postcodes | Nationwide |

You can enable, disable, override, or extend any country dataset from the admin grid.

---

## Compatibility

| Requirement | Versions Supported |
|---|---|
| Magento Open Source | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| Adobe Commerce | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| Adobe Commerce Cloud | 2.4.4 — 2.4.8 |
| PHP | 8.1.x, 8.2.x, 8.3.x, 8.4.x |
| MySQL | 8.0+ |
| MariaDB | 10.4+ |
| Hyva Theme | 1.0+ |
| Luma Theme | Native support |
| Required dependency | `mage2kishan/module-core` (free) |

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

1. Download the release ZIP from [Packagist](https://packagist.org/packages/mage2kishan/module-zipcode-validation) or the [Adobe Commerce Marketplace](https://commercemarketplace.adobe.com)
2. Extract to `app/code/Panth/ZipcodeValidation/`
3. Ensure `Panth_Core` is also installed (required dependency)
4. Run the same commands as above

### Verify Installation

```bash
bin/magento module:status Panth_ZipcodeValidation
# Expected output: Module is enabled
```

After installation, navigate to:
```
Admin → Panth Infotech → Zipcode Validation
Admin → Stores → Configuration → Panth Extensions → Zipcode Validation
```

---

## Configuration

Settings live under **Stores → Configuration → Panth Extensions → Zipcode Validation**:

| Setting | Default | Description |
|---|---|---|
| Enable Module | Yes | Master toggle for all zipcode validation |
| Validation Mode | Allow-list | Allow-list (only listed ranges accepted) or Block-list (listed ranges rejected) |
| Enabled Countries | India, US, UK, CA, AU | Which country datasets to activate for the current store |
| Real-time AJAX Validation | Yes | Validate postcodes as the customer types at checkout |
| AJAX Debounce (ms) | 400 | Delay before firing AJAX validation request |
| Error Message | "Sorry, we do not deliver to this area." | Default message shown on invalid postcode |
| Block Checkout on Invalid | Yes | Prevent customers from proceeding if postcode is out of range |
| Validate Address Book | Yes | Also validate postcodes when customers save new addresses |

---

## Admin Grid

Access the admin grid at **Panth Infotech → Zipcode Validation → Ranges**:

- Search by country, range start, range end, or label
- Filter by country, status, or date
- Sort any column ascending/descending
- Inline edit labels and status
- Bulk enable/disable/delete with mass actions
- Full keyboard navigation
- Exports respect current filters (export only what you searched)

---

## CSV / JSON Import-Export

### Import

1. Go to **Panth Infotech → Zipcode Validation → Import**
2. Download the CSV or JSON template
3. Fill in your pincode/ZIP ranges
4. Upload the file — the module validates format, detects duplicates, and reports errors per row

**CSV format:**
```csv
country_id,range_start,range_end,label,status
IN,110000,110099,Delhi,1
IN,400001,400099,Mumbai,1
US,90001,90210,Los Angeles,1
US,10001,10099,Manhattan,1
```

**JSON format:**
```json
[
  {"country_id": "IN", "range_start": "110000", "range_end": "110099", "label": "Delhi", "status": 1},
  {"country_id": "US", "range_start": "90001", "range_end": "90210", "label": "Los Angeles", "status": 1}
]
```

### Export

Click **Export → CSV** or **Export → JSON** from the ranges grid. Current grid filters are respected, so you can export only the rows you care about.

---

## Real-time AJAX Validation

When a customer types a postcode in the shipping address form, the storefront JS debounces keystrokes and fires an AJAX request to the validation endpoint (`/panth_zipcode/validate/check`). The server responds with either:

- `{"valid": true}` — postcode is in a serviceable range
- `{"valid": false, "message": "Sorry, we do not deliver to this area."}` — postcode is invalid for the selected country

The form shows an inline error under the postcode field and, if **Block Checkout on Invalid** is enabled, the "Next" / "Place Order" button is disabled until the customer enters a serviceable postcode.

Validation is theme-aware — on **Hyva** storefronts the module uses Alpine.js, on **Luma** it uses Knockout.js, detected via `Panth\Core\Helper\Theme`.

---

## FAQ

### Does this work with default Magento checkout?

Yes. The module hooks into the core checkout shipping-address step on both Luma (Knockout) and Hyva (Alpine). No checkout rewrites required.

### Can I validate only for specific countries?

Yes. In configuration, select which countries should be validated. Unlisted countries skip validation entirely (customers from those countries can check out normally).

### Can I use allow-list for India and block-list for US simultaneously?

The global mode setting applies to all countries by default. For per-country logic, use allow-list ranges — any country that has at least one range defined uses allow-list semantics automatically.

### Does the module work for B2B / custom checkouts?

Yes, as long as the checkout collects a postcode field in a standard Magento address form. Custom checkout UIs may need minor JS integration.

### Can I import 50,000+ pincodes?

Yes. The CSV importer processes rows in chunks. For very large imports (100k+), we recommend running via CLI to avoid admin session timeouts.

### Does it slow down checkout?

No. Validation lookups are indexed and cached per country/store view. Typical AJAX response time is under 30ms.

### Does it support multi-store?

Yes. Ranges can be scoped to website/store view, so different store views can have different serviceable areas.

### Is the source code available?

Yes. The source is on GitHub at [github.com/mage2sk/module-zipcode-validation](https://github.com/mage2sk/module-zipcode-validation).

---

## Support

| Channel | Contact |
|---|---|
| Email | kishansavaliyakb@gmail.com |
| Website | [kishansavaliya.com](https://kishansavaliya.com) |
| WhatsApp | +91 84012 70422 |
| GitHub Issues | [github.com/mage2sk/module-zipcode-validation/issues](https://github.com/mage2sk/module-zipcode-validation/issues) |
| Upwork (Top Rated Plus) | [Hire Kishan Savaliya](https://www.upwork.com/freelancers/~016dd1767321100e21) |
| Upwork Agency | [Panth Infotech](https://www.upwork.com/agencies/1881421506131960778/) |

Response time: 1-2 business days.

### 💼 Need Custom Magento Development?

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
  <a href="https://kishansavaliya.com">
    <img src="https://img.shields.io/badge/Visit%20Website-kishansavaliya.com-0D9488?style=for-the-badge" alt="Visit Website" />
  </a>
</p>

---

## About Panth Infotech

Built and maintained by **Kishan Savaliya** — [kishansavaliya.com](https://kishansavaliya.com) — a **Top Rated Plus** Magento developer on Upwork with 10+ years of eCommerce experience.

**Panth Infotech** is a Magento 2 development agency specializing in high-quality, security-focused extensions and themes for both Hyva and Luma storefronts. Our extension suite covers SEO, performance, checkout, product presentation, customer engagement, and store management — over 34 modules built to MEQP standards and tested across Magento 2.4.4 to 2.4.8.

Browse the full catalog on the [Adobe Commerce Marketplace](https://commercemarketplace.adobe.com) or [Packagist](https://packagist.org/packages/mage2kishan/).

---

## Quick Links

- 🌐 **Website:** [kishansavaliya.com](https://kishansavaliya.com)
- 💬 **Get a Quote:** [kishansavaliya.com/get-quote](https://kishansavaliya.com/get-quote)
- 👨‍💻 **Upwork Profile (Top Rated Plus):** [upwork.com/freelancers/~016dd1767321100e21](https://www.upwork.com/freelancers/~016dd1767321100e21)
- 🏢 **Upwork Agency:** [upwork.com/agencies/1881421506131960778](https://www.upwork.com/agencies/1881421506131960778/)
- 📦 **Packagist:** [packagist.org/packages/mage2kishan/module-zipcode-validation](https://packagist.org/packages/mage2kishan/module-zipcode-validation)
- 🐙 **GitHub:** [github.com/mage2sk/module-zipcode-validation](https://github.com/mage2sk/module-zipcode-validation)
- 🛒 **Adobe Marketplace:** [commercemarketplace.adobe.com](https://commercemarketplace.adobe.com)
- 📧 **Email:** kishansavaliyakb@gmail.com
- 📱 **WhatsApp:** +91 84012 70422

---

<p align="center">
  <strong>Ready to upgrade your Magento 2 checkout?</strong><br/>
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/%F0%9F%9A%80%20Get%20Started%20%E2%86%92-Free%20Quote%20in%2024h-DC2626?style=for-the-badge" alt="Get Started" />
  </a>
</p>

---

**SEO Keywords:** magento 2 zipcode validation, pin code validation, postal code validation, india pincode magento, delivery area validation, magento 2 checkout zipcode, magento 2 serviceable area, magento 2 pincode checker, magento 2 zip code restriction, magento 2 postcode validator, india pincode checkout magento, us zip validation magento, uk postcode checkout, canada postal code magento, australia postcode magento, european postcode validation, magento 2 ajax postcode, real-time zipcode check, magento 2 delivery restriction, courier serviceable pincode, magento 2 shipping restriction by pincode, panth zipcode validation, panth infotech, hire magento developer, top rated plus upwork, kishan savaliya magento, mage2kishan, mage2sk, magento marketplace developer, custom magento development india, magento 2 hyva development, magento 2.4.8 module, php 8.4 magento, magento 2 checkout optimization
