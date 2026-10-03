# Panth ZipcodeValidation -- User Guide

This guide is for store administrators who want to configure and manage
ZIP/PIN code validation on their Magento 2 storefront.

---

## Table of contents

1. [Installation](#1-installation)
2. [Verifying the module is active](#2-verifying-the-module-is-active)
3. [Configuration](#3-configuration)
4. [Managing ranges via admin grid](#4-managing-ranges-via-admin-grid)
5. [Import and export](#5-import-and-export)
6. [How validation works on the storefront](#6-how-validation-works-on-the-storefront)
7. [Troubleshooting](#7-troubleshooting)

---

## 1. Installation

### Composer (recommended)

```bash
composer require mage2kishan/module-zipcode-validation
bin/magento module:enable Panth_Core Panth_ZipcodeValidation
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Manual zip

1. Download the extension package zip
2. Extract to `app/code/Panth/ZipcodeValidation` (the `Panth_Core` module must also be
   installed, for example in `app/code/Panth/Core`)
3. Run the same `module:enable ... cache:flush` commands above

---

## 2. Verifying the module is active

```bash
bin/magento module:status Panth_ZipcodeValidation
# Module is enabled
```

After installation, you should see **Zipcode Validation > Manage Ranges**
and **Zipcode Validation > Configuration** under the **Panth Infotech**
menu in the admin sidebar.

---

## 3. Configuration

Navigate to **Stores > Configuration > Panth Extensions > Zipcode
Validation**.

| Setting | Default | What it does |
|---|---|---|
| **Enable Zipcode Validation** | Yes | When No, the storefront script is not loaded and the check endpoint accepts every code for that store view |
| **Enforce on Shipping Information and Order Placement** | No | When Yes, the server rejects shipping information and order placement (storefront, REST, GraphQL) if the shipping postcode is outside the active ranges of its country. Countries without ranges are not checked. Enable only when your ranges are complete, otherwise customers in missing areas cannot check out |
| **Validate on Checkout / Customer Account / Registration** | Yes | Turns the inline check on or off for the checkout (including the checkout postcode field), the customer address pages and the registration page |
| **Error Message** | We couldn't verify this postal code. Please double-check and try again. | Shown when a code is outside every range |
| **Show Success Message**, **Success Message Format** | Yes, Valid PIN code for {state} | Controls the confirmation; `{state}` is replaced with the state name |
| **Success Message Color**, **Error Message Color** | #007a33, #e02b27 | Hex colours of the messages on account, registration and other non-checkout forms |
| **Requests per Window**, **Window Length (seconds)** (Abuse Protection) | 60, 300 | Per-IP rate limit on the check endpoint; 0 disables |

The **ZIP/PIN Code Ranges** group has no fields. Ranges are managed in
the admin grid described below.

---

## 4. Managing ranges via admin grid

Navigate to **Panth Infotech > Zipcode Validation > Manage Ranges** in
the admin sidebar.

### Adding a range

1. Click **Add New Range**
2. Fill in: Country, State/Region Code, State/Region Name, ZIP/PIN
   Start, ZIP/PIN End, Active
3. Click **Save**

### Editing a range

1. Find the range in the grid
2. Click **Edit** in the Actions column
3. Modify fields as needed
4. Click **Save**

### Deleting ranges

- **Single delete:** Click **Delete** in the Actions column and confirm
- **Mass delete:** Select multiple rows using checkboxes, then choose
  **Delete** from the mass actions dropdown

### Searching and filtering

The grid supports full-text search and column-level filtering. Use the
search box above the grid to find ranges by country, state, or ZIP
code.

Ranges are global. They apply to every website and store view.

---

## 5. Import and export

### Exporting ranges

Click **Export JSON** above the grid to download all ranges as a JSON
file (`zipcode_ranges_<date>.json`).

### Importing ranges

There is no import button in this release. Ranges can be imported by
sending a POST request with a JSON array in the request body to the
admin URL `zipcodevalidation/range/import` (admin session, form key and
the Zipcode Validation Configuration permission are required). Each row
is saved separately; rows with missing or invalid values are skipped and
listed in the response message (up to five).

### JSON format

The import uses the same keys as the export file:

```json
[
  {
    "country_id": "IN",
    "state_code": "MH",
    "state_name": "Maharashtra",
    "zip_start": "400001",
    "zip_end": "445402",
    "is_active": 1
  }
]
```

Required fields: `country_id` (two letters), `state_name` (up to 255
characters), `zip_start` and `zip_end` (up to 20 characters each).
`state_code` (up to 10 characters) is optional and `is_active` defaults
to 1.

---

## 6. How validation works on the storefront

When a customer enters a postal code in a Luma-based checkout or
address form:

1. An AJAX request is sent to the validation endpoint
2. The module looks up the postal code in the active ranges for the
   selected country
3. If the postal code matches a range and the selected state, the field
   shows a confirmation with the state name
4. If the postal code does not match any range, an error message is
   shown
5. If the postal code matches a range for a different state, a mismatch
   message is shown with that state name
6. If the country has no active ranges, every code is accepted

Spaces, hyphens and letter case are ignored, so `sw1a 1aa` and
`SW1A1AA` are treated the same. A full code is also accepted when its
leading part falls in a range, for example `W14 1AA` in a range ending
at `W14`, or `90210-1234` in `90001`-`96162`.

The inline messages are informational only; the customer can still
submit the form. To block orders with unsupported postcodes, including
orders placed through the REST or GraphQL APIs, set **Enforce on
Shipping Information and Order Placement** to Yes. On Hyva themes the
inline script is not loaded, but enforcement still applies.

Ranges are global: the same ranges are used for every website and
store view.

### India-specific validation

For India (country_id = IN), additional validation ensures the PIN
code is exactly 6 digits and starts with a digit between 1-9.

---

## 7. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Validation not working on checkout | Setting disabled, "Validate on Checkout" is No, Hyva theme, or JS not loading | Check "Enable Zipcode Validation" and "Validate on Checkout"; flush cache; redeploy static content |
| Checkout fails with "The shipping address postcode cannot be accepted" | Enforcement is on and no active range contains the postcode | Add the missing range, or set "Enforce on Shipping Information and Order Placement" to No |
| Endpoint answers HTTP 429 | Rate limit reached for that IP | Raise "Requests per Window" or configure trusted proxy headers |
| Every code is accepted | No active ranges for that country | Add ranges for the country in the admin grid |
| "We couldn't verify this postal code" | No matching range in database | Add the missing range via the admin grid or the import endpoint |
| State mismatch message shown incorrectly | Range data is inaccurate or ranges overlap | Update the range's state code or bounds in the admin grid |
| Import rows skipped | Missing or invalid field values | Match the JSON format above; compare with an exported file |

---

## Support

For all questions, bug reports, or feature requests:

- **Email:** kishansavaliyakb@gmail.com
- **Website:** https://kishansavaliya.com
- **WhatsApp:** +91 84012 70422

Free email support is provided on a best-effort basis.
