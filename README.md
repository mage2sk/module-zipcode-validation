# Magento 2 Zipcode Validation

Panth ZipcodeValidation checks the postal code a customer types into a Magento 2 address form against a table of ZIP/PIN code ranges stored in the database. The check runs as an AJAX request while the customer is still on the form: the checkout postcode field (through a RequireJS mixin on the standard `Magento_Ui/js/form/element/post-code` component) and any other storefront form with a postcode field (through a script loaded on every frontend page) send the code, country and region to the module's controller, and the field shows an inline error or a confirmation with the matched state/region name.

Ranges are managed in a dedicated admin grid and form and can be exported as JSON. The module ships a data patch that pre-loads ranges for India, the United States, the United Kingdom, Canada, Australia, Germany, France, Italy, Spain and the Netherlands. It is aimed at merchants who deliver only to defined postal areas and want customers to see a problem with their postcode before an order is placed. The storefront code is written for the Luma theme and themes based on it (RequireJS, jQuery and Magento UI components). On Hyva themes the page script block is removed by `view/frontend/layout/hyva_default.xml`, so no inline storefront validation runs there; the endpoint, the helper and the optional server-side enforcement still work.

Product page: [kishansavaliya.com/magento-2-zipcode-validation.html](https://kishansavaliya.com/magento-2-zipcode-validation.html)

## Features

- AJAX validation of the postcode field on the checkout address forms via a mixin on `Magento_Ui/js/form/element/post-code`, debounced by 800 ms after the last change.
- A second script, loaded on every storefront page, validates postcode inputs in any other form (for example customer address book and registration forms) on `change` and `blur`, and re-validates when the country or region field changes.
- Country-aware matching: ranges are looked up per country; if a country has no active range, every code for that country passes.
- Numeric ranges are compared as integers; alphanumeric ranges (for example UK or Canadian codes) are compared as case-insensitive strings. A code longer than the range bounds is compared by its leading characters, so `W14 1AA` matches a range ending at `W14` and the US ZIP+4 code `90210-1234` matches `90001`-`96162`.
- Region check: when the form has a region selected, the code must fall in a range whose `state_code` matches the region code, otherwise the response names the region the code belongs to.
- India-specific format rule: for country `IN` the code must be six digits and must not start with 0.
- Admin grid "Manage ZIP/PIN Code Ranges" with full-text search, column filters, sorting, paging, bookmarks, column controls, row edit/delete and a "Delete" mass action.
- Admin form to add or edit a range (country, state/region code and name, start, end, active flag).
- "Export JSON" button on the grid that downloads all ranges as a JSON file.
- JSON import endpoint (`zipcodevalidation/range/import`) that accepts an array of range objects in the request body and inserts them row by row, reporting per-row errors.
- Data patch that pre-loads 83 ranges (34 for India, 15 for the US, 8 for the UK, 7 for Canada, 8 for Australia, 11 for Germany, France, Italy, Spain and the Netherlands) the first time the table is empty.
- Helper class `Panth\ZipcodeValidation\Helper\Data` exposing the validator to other modules.
- Optional server-side enforcement on shipping information and order placement (storefront, REST and GraphQL), off by default.
- Per-IP rate limit on the storefront check endpoint.

## Compatibility

| Requirement | Supported versions |
|---|---|
| Magento Open Source | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| Adobe Commerce | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| PHP | 8.1, 8.2, 8.3, 8.4 (`~8.1.0||~8.2.0||~8.3.0||~8.4.0`) |
| Themes | Luma and Luma-based themes (RequireJS / Magento UI checkout) |

Composer constraints from `composer.json`: `magento/framework ^103.0`, `magento/module-backend ^102.0`, `magento/module-config ^101.2`, `magento/module-store ^101.1`, `magento/module-directory ^100.4`, `magento/module-customer ^103.0`, `magento/module-checkout ^100.4`, `magento/module-quote ^101.2`, `magento/module-ui ^101.2`.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.4 to 2.4.8.
- PHP 8.1, 8.2, 8.3 or 8.4.
- `mage2kishan/module-core` `^1.0` (module `Panth_Core`). It provides the parent admin menu and the theme configuration hook this module registers with; Composer installs it automatically.
- The Magento modules listed under Compatibility. `module.xml` declares a load sequence after `Magento_Customer`, `Magento_Checkout`, `Magento_Quote` and `Panth_Core`.

## Installation

```bash
composer require mage2kishan/module-zipcode-validation
bin/magento module:enable Panth_Core Panth_ZipcodeValidation
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

`setup:di:compile` is only needed when the store runs in production mode. `setup:static-content:deploy` is needed because the module ships JavaScript under `view/frontend/web`. `setup:upgrade` creates the `panth_zipcode_range` table and runs the data patch that pre-loads the ranges.

Check that the module is enabled:

```bash
bin/magento module:status Panth_ZipcodeValidation
```

## Configuration

The configuration section is at Stores > Configuration > Panth Extensions > "Zipcode Validation" (section id `zipcode_validation`, also reachable from the "Zipcode Validation" > "Configuration" admin menu entry). All fields can be set at default, website and store view scope.

### General Settings

| Setting | Default | What it does |
|---|---|---|
| "Enable Zipcode Validation" | Yes | Yes/No select, read at store view scope. When No, the page script block is not rendered, the checkout mixin does nothing, the endpoint answers every request with `{"valid": true, "message": ""}` and no server-side enforcement runs. |
| "Enforce on Shipping Information and Order Placement" | No | When Yes, the server rejects saving shipping information and placing an order (storefront, REST and GraphQL) if the shipping postcode is outside the active ranges of its country. See "Server-side enforcement" below before enabling it. Path `zipcode_validation/general/enforce_on_order`. |
| "Validate on Checkout" | Yes | Runs the inline check on checkout (`checkout_*` and `multishipping_*` pages, including the cart shipping estimate and the checkout postcode mixin). |
| "Validate on Customer Account" | Yes | Runs the inline check on the customer address add/edit pages (`customer_address_form`, `customer_address_new`, `customer_address_edit`). |
| "Validate on Registration" | Yes | Runs the inline check on `customer_account_create`. Other pages with a postcode field are always checked while the module is enabled. |
| "Error Message" | We couldn't verify this postal code. Please double-check and try again. | Message returned when a code is outside every active range of its country (endpoint and enforcement). The India format and region mismatch messages are built in. |
| "Show Success Message" | Yes | When No, a valid code only clears the error; no confirmation is shown. |
| "Success Message Format" | Valid PIN code for {state} | Confirmation text; `{state}` is replaced with the matched state name. Used by the page script and the checkout mixin. |

### Display Settings

| Setting | Default | What it does |
|---|---|---|
| "Success Message Color" | #007a33 | Colour of the confirmation added by the page script (account, registration and other non-checkout forms). Must be a hex colour (`#rgb`, `#rrggbb` or `#rrggbbaa`); other values fall back to the default. |
| "Error Message Color" | #e02b27 | Colour of the error added by the page script, same rules. The checkout mixin uses the theme's field error styling. |

### Abuse Protection

| Setting | Default | What it does |
|---|---|---|
| "Requests per Window" | 60 | Maximum check requests one client IP address may send to `zipcodevalidation/validate/pincode` per window. Further requests get HTTP 429 with `{"valid": true, "rate_limited": true}` and are not validated; the storefront scripts ignore that answer. 0 disables the limit. |
| "Window Length (seconds)" | 300 | Length of the fixed rate limit window. |

Counters are kept in the Magento cache. The client IP is read with `Magento\Framework\HTTP\PhpEnvironment\RemoteAddress`, so behind a proxy or load balancer configure the trusted forwarded-for headers in `app/etc/env.php`; otherwise all visitors share the proxy's address.

### ZIP/PIN Code Ranges

This group has no fields. Its comment points to the "Manage Ranges" grid, which is where the ranges are maintained. Ranges are global: the `panth_zipcode_range` table has no website or store view column, so the same ranges apply to every store view.

Config paths: `zipcode_validation/general/enabled`, `zipcode_validation/general/enforce_on_order`, `zipcode_validation/general/validate_on_checkout`, `zipcode_validation/general/validate_on_account`, `zipcode_validation/general/validate_on_registration`, `zipcode_validation/general/error_message`, `zipcode_validation/general/show_success_message`, `zipcode_validation/general/success_message_format`, `zipcode_validation/display/success_color`, `zipcode_validation/display/error_color`, `zipcode_validation/security/rate_limit`, `zipcode_validation/security/rate_limit_window`.

### Managing ranges

Open "Zipcode Validation" > "Manage Ranges" in the admin menu (route `zipcodevalidation/range/index`, page title "Manage ZIP/PIN Code Ranges"). The grid lists the rows of the `panth_zipcode_range` table with the columns "ID", "Country", "State Code", "State/Region Name", "ZIP/PIN Start", "ZIP/PIN End", "Active" and "Created", plus an "Actions" column with "Edit" and "Delete".

- "Add New Range" opens the form "Range Information" with the fields "Country" (select, required), "State/Region Code", "State/Region Name" (required), "ZIP/PIN Start" (required), "ZIP/PIN End" (required) and "Active" (Yes/No, required). The server checks the same rules as the JSON import before saving: a two-letter country code, a State/Region Name of 1 to 255 characters, a State/Region Code of up to 10 characters, and ZIP/PIN Start and End of 1 to 20 characters that contain at least one letter or digit. An invalid form is not saved and the errors are shown above the form; saving an ID that no longer exists shows "This range no longer exists."
- "Export JSON" downloads `zipcode_ranges_<date>.json`, an array of objects with the keys `country_id`, `state_code`, `state_name`, `zip_start`, `zip_end` and `is_active`.
- The row action "Delete" and the mass action "Delete" remove rows. Both send a POST request; `Save`, `Delete`, `MassDelete` and `Import` accept POST only.

Import format: `POST` a JSON array with the same keys as the export file to `zipcodevalidation/range/import` (admin route, requires the admin form key and the `Panth_ZipcodeValidation::config` ACL resource). `country_id` (two letters), `state_name` (up to 255 characters), `zip_start` and `zip_end` (up to 20 characters each) are required per row; `state_code` (up to 10 characters) defaults to an empty string and `is_active` to 1. Rows that break these rules are skipped and reported. The response is `{"success": true, "message": "Successfully imported N range(s). ...", "imported": N}`; the message lists up to five row errors. There is no button for this endpoint in the grid in this release.

## Usage

### Where the check runs

- Checkout: the mixin on the postcode UI component reads `country_id` and `region_id` from the same address fieldset (falling back to `shippingAddress.*`) and calls the endpoint 800 ms after the last change to the postcode value. An invalid code sets the field's error text; a valid code clears it and, where the component supports notices, shows the "Success Message Format" text (nothing when "Show Success Message" is No). The mixin does nothing when "Validate on Checkout" is No.
- Other storefront forms: `view/frontend/layout/default.xml` adds the block `zipcode.validator` (template `Panth_ZipcodeValidation::zipcode-validator.phtml`) before the end of the body on every page. Its script binds to inputs whose name contains `postcode` or `zip` (and to `#zip` / `#postcode`) in any form, reads the form's `country_id` and `region_id` fields, and appends a `.field-error` or `.field-success` element under the field. The handlers are delegated from `document`, so forms loaded later by AJAX are covered as well.
- The scripts only display messages. They do not add a validation rule to the form, so a customer can still submit the form after an error is shown.
- By default there is no server-side check when an address is saved or an order is placed. Turn on "Enforce on Shipping Information and Order Placement" to add one (see below).

### Server-side enforcement

With `zipcode_validation/general/enforce_on_order` = 1 (and the module enabled for the store view):

- A `before` plugin on `Magento\Checkout\Model\ShippingInformationManagement::saveAddressInformation` validates the shipping address. This covers the Luma checkout and the REST endpoints `POST /V1/carts/mine/shipping-information` and `POST /V1/guest-carts/:cartId/shipping-information` (the guest service delegates to the same class).
- A `before` plugin on `Magento\Quote\Model\QuoteManagement::submit` validates the quote's shipping address when an order is placed from the storefront, REST (`payment-information`, `order`) or GraphQL (`placeOrder`).
- Only the shipping address is checked, and only when its country has at least one active range. The region is compared only when the address has a numeric `region_id`. Virtual quotes are skipped.
- A rejected request fails with "The shipping address postcode cannot be accepted: <reason>" (HTTP 400 on REST).

Enable it only after the ranges cover every postcode you deliver to: a missing range blocks checkout for that area.

### AJAX endpoint

`POST` (or `GET`) `/zipcodevalidation/validate/pincode` with `pincode`, `country_id` (defaults to `IN` when empty) and optional `region_id` (numeric Magento region id or a region code, up to 20 letters, digits, spaces, `_` or `-`; any other value is ignored). The response is JSON and is sent with `Cache-Control: max-age=0, must-revalidate, no-cache, no-store`:

- `{"valid": true, "message": ""}` when `pincode` is empty or "Enable Zipcode Validation" is No.
- `{"valid": false, "message": "Please enter a valid postal/ZIP code."}` when `pincode` is longer than 20 characters.
- `{"valid": false, "message": "Please select a valid country."}` when `country_id` is not a two-letter code.
- `{"valid": false, "message": "Indian PIN codes must be 6 digits (e.g. 110001)."}` for country `IN` when the code does not match `^[1-9][0-9]{5}$`.
- `{"valid": true, "message": "", "state": ""}` when the country has no active ranges.
- `{"valid": false, "message": "<Error Message setting>"}` when no active range contains the code (default text: "We couldn't verify this postal code. Please double-check and try again.").
- `{"valid": true, "rate_limited": true, "message": "Too many requests. Please try again later."}` with HTTP 429 when the client IP exceeded the rate limit.
- `{"valid": false, "message": "This postal code is associated with <state>. Please check your state/region selection."}` when a region was sent and none of the matching ranges has that region's code in `state_code`.
- `{"valid": true, "message": "", "state": "<state_name of the first matching range>"}` on success.

### How codes are matched

Non-alphanumeric characters (including spaces and hyphens) are stripped from the code and from both bounds, and the code is upper-cased. Only rows with `is_active = 1` for the requested `country_id` are loaded (once per request and cached in memory). If the code is longer than the longer of the two bounds, only that many leading characters are compared. If the code, `zip_start` and `zip_end` are all digits, the comparison is `start <= code <= end` as integers; otherwise it is a case-insensitive string comparison (`strcasecmp`) against both bounds. Ranges are global: they have no website or store view scope. A code may fall in several ranges; the first match supplies the state name, and the region check passes if any matching range has the selected region's code.

### Pre-loaded data

`Setup/Patch/Data/AddGlobalZipcodeRanges.php` inserts 83 rows into `panth_zipcode_range` during `setup:upgrade`, but only if the table is empty at that moment. All rows are active and can be edited or deleted in the grid. A second patch, `AddIndianPincodeRanges.php`, writes 37 Indian ranges to the config path `zipcode_validation/pincode_ranges/custom_ranges` in `core_config_data`; this value is not used by the validator and no admin screen reads it since 1.1.0.

### Templates and assets that can be overridden

- `view/frontend/templates/zipcode-validator.phtml` (passes the settings from `ViewModel\FrontendConfig` to the page script; the block `zipcode.validator` is only rendered when "Enable Zipcode Validation" is Yes and the page's "Validate on ..." setting allows it, and is removed on Hyva themes).
- `view/frontend/web/js/zipcode-validator.js` (generic form binding, messages and colours).
- `view/frontend/web/js/form/element/post-code-mixin.js` (checkout postcode component mixin, registered in `view/frontend/requirejs-config.js`; reads `window.checkoutConfig.panthZipcodeValidation` supplied by `Model\Checkout\ConfigProvider`).
- `view/adminhtml/ui_component/panth_zipcode_range_listing.xml` and `panth_zipcode_range_form.xml` (admin grid and form).

## Developer Notes

- Module name: `Panth_ZipcodeValidation`; Composer package: `mage2kishan/module-zipcode-validation`; PHP namespace: `Panth\ZipcodeValidation`.
- Validation logic: `Panth\ZipcodeValidation\Model\PincodeValidator` with `validate(string $pincode, ?string $regionId = null, string $countryId = 'IN'): array` and `getStateByPincode(string $pincode, string $countryId = 'IN'): ?string`. `Panth\ZipcodeValidation\Helper\Data` wraps the same calls as `validateZipcode()`, `validateIndianPincode()` and `getStateByPincode()`.
- Frontend controller: `Panth\ZipcodeValidation\Controller\Validate\Pincode` (route `zipcodevalidation`, standard router), rate limited by `Model\RateLimiter` (cache keys prefixed `panth_zipcode_rl_`).
- Enforcement: `Model\OrderAddressValidator` with the plugins `Plugin\ShippingInformationPlugin` (on `Magento\Checkout\Model\ShippingInformationManagement`) and `Plugin\QuoteSubmitPlugin` (on `Magento\Quote\Model\QuoteManagement`), declared in `etc/di.xml` for all areas.
- Admin controllers under `Panth\ZipcodeValidation\Controller\Adminhtml\Range`: `Index`, `NewAction`, `Edit`, `Save`, `Delete`, `MassDelete`, `Export`, `Import` (admin route `zipcodevalidation`). All use `ADMIN_RESOURCE = 'Panth_ZipcodeValidation::config'`.
- Model layer: `Model\ZipcodeRange`, `Model\ResourceModel\ZipcodeRange`, `Model\ResourceModel\ZipcodeRange\Collection`; the grid data source is the virtual type `Panth\ZipcodeValidation\Model\ResourceModel\ZipcodeRange\Grid\Collection` (a `SearchResult` over `panth_zipcode_range`) registered in `etc/di.xml` as `panth_zipcode_range_listing_data_source`.
- UI: `Ui\DataProvider\RangeDataProvider` (form), `Ui\Component\Listing\Column\RangeActions` (grid actions), `Block\Adminhtml\Range\Edit\BackButton` and `SaveButton`.
- `etc/frontend/di.xml` adds `Panth_ZipcodeValidation` to the `registeredModules` argument of `Panth\Core\ViewModel\ThemeConfig`; `etc/theme-config.json` declares `zipcode-success-color`, `zipcode-error-color` and `zipcode-font-size` for that mechanism.
- ACL resource: `Panth_ZipcodeValidation::config` ("Zipcode Validation Configuration") under `Magento_Config::config`. It guards the configuration section, both menu entries, the grid data source and all admin controllers.
- Admin menu: `Panth_ZipcodeValidation::group` ("Zipcode Validation") under `Panth_Core::panth_extensions`, with children `Panth_ZipcodeValidation::manage_ranges` ("Manage Ranges") and `Panth_ZipcodeValidation::config` ("Configuration").
- Database table (`etc/db_schema.xml`): `panth_zipcode_range` with columns `range_id` (PK, auto increment), `country_id` (varchar 2), `state_code` (varchar 10, nullable), `state_name` (varchar 255), `zip_start` (varchar 20), `zip_end` (varchar 20), `is_active` (smallint, default 1) and `created_at` (timestamp); indexes on `country_id` and `is_active`.
- The legacy config-based import/export classes and the `zipcodevalidation/import`, `zipcodevalidation/export` and `zipcodevalidation/sample` admin routes were removed in 1.1.0.
- No observers, cron jobs, web API routes, widgets or console commands are declared.

## Uninstallation

```bash
bin/magento module:disable Panth_ZipcodeValidation
composer remove mage2kishan/module-zipcode-validation
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

The module has no uninstall script. The `panth_zipcode_range` table and its rows, the `zipcode_validation/*` values in `core_config_data` (including `zipcode_validation/pincode_ranges/custom_ranges`) and the two data patch entries in `patch_list` remain after removal and have to be dropped manually if no longer wanted. Remove `Panth_Core` only if no other Panth module depends on it.

## Support

- Product page: [kishansavaliya.com/magento-2-zipcode-validation.html](https://kishansavaliya.com/magento-2-zipcode-validation.html)
- Contact form: [kishansavaliya.com/contact](https://kishansavaliya.com/contact)
- Email: kishansavaliyakb@gmail.com
- Bug reports: [GitHub issues](https://github.com/mage2sk/module-zipcode-validation/issues)

## Documentation

[USER_GUIDE.md](USER_GUIDE.md) is the administrator guide. It covers installation, checking that the module is active, configuration, adding, editing and deleting ranges in the admin grid, JSON import and export, how validation behaves on the storefront, and troubleshooting.

## License

Commercial software license. See [LICENSE.txt](LICENSE.txt) in this repository.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Links

- Website: [kishansavaliya.com](https://kishansavaliya.com)
- All extensions: [kishansavaliya.com/magento-extensions.html](https://kishansavaliya.com/magento-extensions.html)
- GitHub: [github.com/mage2sk/module-zipcode-validation](https://github.com/mage2sk/module-zipcode-validation)
- Packagist: [packagist.org/packages/mage2kishan/module-zipcode-validation](https://packagist.org/packages/mage2kishan/module-zipcode-validation)
