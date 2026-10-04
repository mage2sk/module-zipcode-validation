# Changelog

All notable changes to this extension are documented here. The format
is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.1.4] - 2026-10-04

### Fixed
- The "Add New Range" form could not be saved: the Active field failed to load, so the form never submitted. Active now defaults to Yes and uses a Yes/No list.
- A failed range save keeps the entered values in the form instead of showing an empty form.
- The range grid keyword search returned every row. It now searches country, state code, state name and the ZIP/PIN bounds.
- The Active column of the range grid showed 1 and 0 instead of Yes and No.
- UK postcodes such as W2 1AA, W9 9ZZ and W1A 1AA were rejected by a W1 to W14 range. UK codes are now compared by their outward code, and letter and digit codes in natural order (W2 comes before W14).
- US ZIP codes with extra letters (10001ABC) were accepted. US, Canadian and UK codes are now checked for their format (5 or 9 digits, A1A 1A1, SW1A 1AA) like Indian PIN codes, on the storefront and in the server-side enforcement.
- A range without a state code rejected every customer who picked a region. Such ranges now accept any region.
- The Luma cart shipping estimate never validated the postcode because the country and region were read from the wrapping element instead of the select.
- Checkout: changing the country or the region did not re-check the postcode, so a stale confirmation or error stayed under the field; a postcode that was already filled in was not checked when the page loaded; a slow answer for an older value could overwrite the current one.
- Checkout billing address forms were checked against the shipping country and region instead of their own.
- Countries without ranges showed a "Valid PIN code" confirmation on Luma forms although nothing was checked; no message is shown now.
- Storefront validation messages are now translatable.

### Added
- "Delete" and "Save and Continue Edit" buttons on the range edit form, and help text for State/Region Code, ZIP/PIN Start, ZIP/PIN End and Active.

### Changed
- The default "Success Message Format" is now "Valid postal code for {state}", which fits ZIP, postcode and PIN codes alike.
