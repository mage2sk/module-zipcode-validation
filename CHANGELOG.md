# Changelog

All notable changes to this extension are documented here. The format
is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.0] -- Initial release

### Added
- **Real-time AJAX ZIP/PIN code validation** on checkout, customer
  address, and shipping-estimation forms.
- **State/region auto-detection** from postal code.
- **State mismatch detection** with user-friendly warning messages.
- **Admin grid** (`Panth Infotech > Manage ZIP/PIN Code Ranges`) with
  full CRUD, search, filtering, mass delete, and pagination.
- **Dynamic-rows config field** for quick range management in
  `Stores > Configuration > Panth Extensions > Zipcode Validation`.
- **JSON import/export** with full validation, error reporting, and
  sample file download.
- **Data patches** that pre-load ranges for India (all 38 states/UTs),
  US (15 major states), UK, Canada, Australia, Germany, France, Italy,
  Spain, and the Netherlands.
- **India-specific PIN code format validation** (6 digits, first
  digit 1-9).
- **Multi-country support** with numeric and alphanumeric range
  comparison.
- **DB-backed range storage** (`panth_zipcode_range` table) with
  country and active-status indexes for fast lookups.

### Compatibility
- Magento Open Source / Commerce / Cloud 2.4.4 -- 2.4.8
- PHP 8.1, 8.2, 8.3, 8.4

---

## Support

For all questions, bug reports, or feature requests:

- **Email:** kishansavaliyakb@gmail.com
- **Website:** https://kishansavaliya.com
- **WhatsApp:** +91 84012 70422
