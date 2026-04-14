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
    'mage/translate',
    'mage/url'
], function ($, $t, urlBuilder) {
    'use strict';

    return function (Component) {
        return Component.extend({
            defaults: {
                debounceTimer: null,
                imports: {
                    update: '${ $.parentName }.country_id:value'
                }
            },

            /**
             * Initialize component
             */
            initialize: function () {
                this._super();

                // Listen for changes on this field
                this.on('value', this.onValueChange.bind(this));

                return this;
            },

            /**
             * Get validation URL
             */
            getValidationUrl: function () {
                var baseUrl = urlBuilder.build('');
                return baseUrl + 'zipcodevalidation/validate/pincode';
            },

            /**
             * Handle value change
             */
            onValueChange: function (value) {
                if (value && value.length > 0) {
                    this.validatePincode();
                }
            },

            /**
             * Validate pincode via AJAX
             */
            validatePincode: function () {
                var self = this;
                var value = this.value();
                var countryId = this.getCountryId();

                // Skip if no value or no country
                if (!value || !countryId) {
                    return this;
                }

                var regionId = this.getRegionId();

                // Clear previous timer
                clearTimeout(this.debounceTimer);

                // Debounce AJAX call
                this.debounceTimer = setTimeout(function () {
                    $.ajax({
                        url: self.getValidationUrl(),
                        type: 'POST',
                        data: {
                            pincode: value,
                            country_id: countryId,
                            region_id: regionId
                        },
                        dataType: 'json',
                        showLoader: false,
                        success: function (response) {
                            if (!response.valid) {
                                self.error(response.message);
                                self.bubble('error', response.message);
                                if (self.notice) {
                                    self.notice(false);
                                }
                            } else {
                                self.error(false);
                                if (self.notice && response.state) {
                                    self.notice('✓ Valid postal code for ' + response.state);
                                }
                            }
                        },
                        error: function () {
                            // Silent fail on error
                        }
                    });
                }, 800);

                return this;
            },

            /**
             * Get country ID from form
             */
            getCountryId: function () {
                var countryId = '';

                try {
                    if (this.source && this.parentName) {
                        var addressPath = this.parentName.replace('.postcode', '');

                        // Try to get country_id from the source
                        countryId = this.source.get(addressPath + '.country_id');

                        // If not found, try without address path
                        if (!countryId && this.source.get('shippingAddress.country_id')) {
                            countryId = this.source.get('shippingAddress.country_id');
                        }
                    }
                } catch (e) {
                    countryId = '';
                }

                return countryId || '';
            },

            /**
             * Get region ID from form
             */
            getRegionId: function () {
                var regionId = '';

                try {
                    if (this.source && this.parentName) {
                        var addressPath = this.parentName.replace('.postcode', '');

                        // Try to get region_id from the source
                        regionId = this.source.get(addressPath + '.region_id');

                        // If not found, try without address path
                        if (!regionId && this.source.get('shippingAddress.region_id')) {
                            regionId = this.source.get('shippingAddress.region_id');
                        }
                    }
                } catch (e) {
                    regionId = '';
                }

                return regionId || '';
            }
        });
    };
});
