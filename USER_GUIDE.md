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
bin/magento module:enable Panth_ZipcodeValidation
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Manual zip

1. Download the extension package zip
2. Extract to `app/code/Panth/ZipcodeValidation`
3. Run the same `module:enable ... cache:flush` commands above

---

## 2. Verifying the module is active

```bash
bin/magento module:status Panth_ZipcodeValidation
# Module is enabled
```

After installation, you should see the **Manage ZIP/PIN Code Ranges**
entry under the **Panth Infotech** menu in the admin sidebar.

---

## 3. Configuration

Navigate to **Stores > Configuration > Panth Extensions > Zipcode
Validation**.

| Setting | Default | What it does |
|---|---|---|
| **Enable Module** | Yes | Enable/disable ZIP/PIN code validation globally |
| **PIN/ZIP Code Ranges** | Pre-loaded | Dynamic-rows field for managing validation ranges inline |
| **Import/Export** | -- | Upload or download validation ranges as JSON files |

---

## 4. Managing ranges via admin grid

Navigate to **Panth Infotech > Manage ZIP/PIN Code Ranges** in the
admin sidebar.

### Adding a range

1. Click **Add New Range**
2. Fill in: Country, State/Region Code, State/Region Name, ZIP Start,
   ZIP End, Is Active
3. Click **Save**

### Editing a range

1. Find the range in the grid
2. Click **Edit** in the Actions column
3. Modify fields as needed
4. Click **Save**

### Deleting ranges

- **Single delete:** Click **Delete** in the Actions column
- **Mass delete:** Select multiple rows using checkboxes, then choose
  **Delete** from the mass actions dropdown

### Searching and filtering

The grid supports full-text search and column-level filtering. Use the
search box above the grid to find ranges by country, state, or ZIP
code.

---

## 5. Import and export

Navigate to **Stores > Configuration > Panth Extensions > Zipcode
Validation > Import/Export**.

### Exporting ranges

Click **Export Current Ranges** to download all configured ranges as
a JSON file.

### Importing ranges

1. Click **Download Sample** to see the expected JSON format
2. Prepare your JSON file with the correct structure
3. Click **Choose JSON file** and select your file
4. Click **Import JSON**
5. The system validates all rows before importing -- any errors are
   reported with row numbers

### JSON format

```json
[
  {
    "country_id": "IN",
    "state_code": "MH",
    "state_name": "Maharashtra",
    "pincode_start": "400001",
    "pincode_end": "445402"
  }
]
```

Required fields: `country_id`, `state_name`, `pincode_start`,
`pincode_end`. The `state_code` field is optional.

---

## 6. How validation works on the storefront

When a customer enters a postal code during checkout:

1. An AJAX request is sent to the validation endpoint
2. The module looks up the postal code in the active ranges for the
   selected country
3. If the postal code is valid and matches the selected state, the
   form proceeds normally
4. If the postal code does not match any range, an error message is
   shown
5. If the postal code is valid but belongs to a different state, a
   mismatch warning is shown with the correct state name

### India-specific validation

For India (country_id = IN), additional validation ensures the PIN
code is exactly 6 digits and starts with a digit between 1-9.

---

## 7. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Validation not working on checkout | Module disabled or JS not loading | Check module status; flush cache; redeploy static content |
| "We couldn't verify this postal code" | No matching range in database | Add the missing range via admin grid or JSON import |
| State mismatch warning shown incorrectly | Range data is inaccurate | Update the range's state_code in the admin grid |
| Import fails with validation errors | JSON format incorrect | Download the sample file and match the format exactly |

---

## Support

For all questions, bug reports, or feature requests:

- **Email:** kishansavaliyakb@gmail.com
- **Website:** https://kishansavaliya.com
- **WhatsApp:** +91 84012 70422

Free email support is provided on a best-effort basis.
