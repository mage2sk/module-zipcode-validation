# Panth ZipcodeValidation

[![Magento 2.4.4 - 2.4.8](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.8-orange)]()
[![PHP 8.1 - 8.4](https://img.shields.io/badge/PHP-8.1%20--%208.4-blue)]()

**Real-time ZIP/PIN code validation** for Magento 2 checkout, shipping,
and billing address forms. Validates postal codes against configurable
country-specific ranges and auto-detects the customer's state/region.

Ships with pre-loaded ranges for **India (all 38 states/UTs), US, UK,
Canada, Australia, Germany, France, Italy, Spain, and the Netherlands**.
Additional countries and ranges can be added via the admin grid or
JSON import.

---

## Features

- **Real-time AJAX validation** on checkout, customer address, and
  shipping-estimation forms.
- **State/region auto-detection** -- when a customer enters a postal
  code, the correct state is automatically suggested.
- **State mismatch detection** -- warns the customer when their postal
  code does not match the selected state.
- **Admin grid** for managing ZIP/PIN code ranges with full CRUD,
  search, filtering, mass delete, and pagination.
- **JSON import/export** -- bulk-manage ranges via JSON files. Sample
  file download included.
- **Pre-loaded data** -- data patches seed the DB with ranges for
  India, US, UK, Canada, Australia, and major European countries.
- **Multi-country support** -- works with any country; not limited to
  Indian PIN codes.
- **Configurable** via `Stores > Configuration > Panth Extensions >
  Zipcode Validation`.

---

## Installation

```bash
composer require mage2kishan/module-zipcode-validation
bin/magento module:enable Panth_ZipcodeValidation
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Verify

```bash
bin/magento module:status Panth_ZipcodeValidation
# Module is enabled
```

---

## Requirements

| | Required |
|---|---|
| Magento | 2.4.4 -- 2.4.8 (Open Source / Commerce / Cloud) |
| PHP | 8.1 / 8.2 / 8.3 / 8.4 |
| Panth_Core | ^1.0 (pulled in automatically via Composer) |

---

## Configuration

Navigate to **Stores > Configuration > Panth Extensions > Zipcode
Validation**.

| Setting | Description |
|---|---|
| Enable Module | Enable/disable the extension globally |
| PIN/ZIP Code Ranges | Dynamic-rows field for quick range management |
| Import/Export | Upload or download ranges as JSON |

### Admin Grid

Navigate to **Panth Infotech > Manage ZIP/PIN Code Ranges** in the
admin sidebar for full CRUD management of validation ranges.

---

## Support

| Channel | Contact |
|---|---|
| Email | kishansavaliyakb@gmail.com |
| Website | https://kishansavaliya.com |
| WhatsApp | +91 84012 70422 |

Free email support is provided on a best-effort basis.

---

## License

Commercial -- see `LICENSE.txt`. Distribution is restricted to the
Adobe Commerce Marketplace.

---

## About the developer

Built and maintained by **Kishan Savaliya** -- https://kishansavaliya.com.
Builds high-quality, security-focused Magento 2 extensions and themes
for both Hyva and Luma storefronts.
