/**
 * Panth_ZipcodeValidation
 *
 * @category  Panth
 * @package   Panth_ZipcodeValidation
 * @author    Panth
 * @copyright Copyright (c) 2025 Panth
 */

define([
    'jquery',
    'mage/url',
    'mage/translate',
    'domReady!'
], function ($, urlBuilder, $t) {
    'use strict';

    return function (config) {
        var validationUrl = config.validationUrl || '';
        var debounceTimer;

        /**
         * Add error message
         */
        function showError(element, message) {
            removeError(element);

            var errorDiv = $('<div class="field-error" style="color: #e02b27; font-size: 12px; margin-top: 5px;"></div>').text(message);
            $(element).closest('.field').addClass('_error').append(errorDiv);
            $(element).addClass('mage-error');
        }

        /**
         * Remove error message
         */
        function removeError(element) {
            $(element).closest('.field').removeClass('_error').find('.field-error, .field-success').remove();
            $(element).removeClass('mage-error');
        }

        /**
         * Show success message
         */
        function showSuccess(element, message) {
            removeError(element);

            var successDiv = $('<div class="field-success" style="color: #007a33; font-size: 12px; margin-top: 5px;"></div>').html('✓ ' + message);
            $(element).closest('.field').append(successDiv);
        }

        /**
         * Validate zipcode via AJAX
         */
        function validateZipcode(element) {
            var zipcode = $(element).val();
            var form = $(element).closest('form');
            var countryField = form.find('[name*="country_id"]');
            var countryId = countryField.length ? countryField.val() : '';

            // Skip if no country selected
            if (!countryId) {
                removeError(element);
                return;
            }

            // Skip if empty
            if (!zipcode || zipcode.trim() === '') {
                removeError(element);
                return;
            }

            // Get region/state field
            var regionField = form.find('[name*="region_id"]');
            var regionId = regionField.length ? regionField.val() : '';

            // Clear previous timer
            clearTimeout(debounceTimer);

            // Debounce AJAX call
            debounceTimer = setTimeout(function () {
                $.ajax({
                    url: validationUrl,
                    type: 'POST',
                    data: {
                        pincode: zipcode,
                        country_id: countryId,
                        region_id: regionId
                    },
                    dataType: 'json',
                    showLoader: false,
                    success: function (response) {
                        if (response.valid) {
                            if (response.state) {
                                showSuccess(element, 'Valid PIN code for ' + response.state);
                            } else {
                                showSuccess(element, 'Valid PIN code');
                            }
                        } else {
                            showError(element, response.message);
                        }
                    },
                    error: function () {
                        // Silent fail
                    }
                });
            }, 800);
        }

        /**
         * Bind validation to zipcode fields
         */
        function bindValidation() {
            // Common selectors for zipcode/postcode fields
            var selectors = [
                'input[name*="postcode"]',
                'input[name*="zip"]',
                'input[name="postcode"]',
                'input[name="zip"]',
                '#zip',
                '#postcode'
            ].join(', ');

            $(document).on('change blur', selectors, function () {
                validateZipcode(this);
            });

            // Also validate on country change
            $(document).on('change', '[name*="country_id"]', function () {
                var form = $(this).closest('form');
                form.find(selectors).each(function () {
                    if ($(this).val()) {
                        validateZipcode(this);
                    }
                });
            });

            // Validate on region/state change
            $(document).on('change', '[name*="region_id"]', function () {
                var form = $(this).closest('form');
                form.find(selectors).each(function () {
                    if ($(this).val()) {
                        validateZipcode(this);
                    }
                });
            });
        }

        // Initialize validation
        bindValidation();

        // Re-bind after AJAX updates (for checkout and dynamic forms)
        $(document).ajaxComplete(function () {
            setTimeout(bindValidation, 100);
        });
    };
});
