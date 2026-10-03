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
        var showSuccessMessage = config.showSuccess !== false;
        var successFormat = config.successFormat || 'Valid PIN code for {state}';
        var successColor = config.successColor || '#007a33';
        var errorColor = config.errorColor || '#e02b27';
        var debounceTimer;

        /**
         * Add error message
         */
        function showError(element, message) {
            removeError(element);

            var msgId = messageId(element);
            var errorDiv = $('<div class="field-error" role="alert"></div>')
                .attr('id', msgId)
                .css({color: errorColor, 'font-size': '12px', 'margin-top': '5px'})
                .text(message);
            $(element).closest('.field').addClass('_error').append(errorDiv);
            $(element).addClass('mage-error').attr('aria-invalid', 'true').attr('aria-describedby', msgId);
        }

        /**
         * Remove error message
         */
        function removeError(element) {
            $(element).closest('.field').removeClass('_error').find('.field-error, .field-success').remove();
            $(element).removeClass('mage-error').removeAttr('aria-invalid');
            if ($(element).attr('aria-describedby') === messageId(element)) {
                $(element).removeAttr('aria-describedby');
            }
        }

        /**
         * Show success message
         */
        function showSuccess(element, message) {
            removeError(element);

            var successDiv = $('<div class="field-success" role="status"></div>')
                .attr('id', messageId(element))
                .css({color: successColor, 'font-size': '12px', 'margin-top': '5px'})
                .text('\u2713 ' + message);
            $(element).closest('.field').append(successDiv);
            $(element).attr('aria-describedby', messageId(element));
        }

        /**
         * Validate zipcode via AJAX
         */
        function messageId(element) {
            if (!element.id) {
                element.id = 'panth-zip-' + Math.random().toString(36).slice(2, 9);
            }
            return element.id + '-zipmsg';
        }

        function validateZipcode(element) {
            clearTimeout(debounceTimer);
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
                        var currentCountry = countryField.length ? countryField.val() : '';
                        if ($(element).val() !== zipcode || currentCountry !== countryId) {
                            return;
                        }
                        if (response.valid) {
                            if (!showSuccessMessage) {
                                removeError(element);
                            } else if (response.state) {
                                showSuccess(element, successFormat.split('{state}').join(response.state));
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
    };
});
