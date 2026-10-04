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
    'uiRegistry'
], function ($, urlBuilder, registry) {
    'use strict';

    return function (Component) {
        return Component.extend({
            defaults: {
                debounceTimer: null,
                zipRequest: null,
                zipMessage: '',
                imports: {
                    update: '${ $.parentName }.country_id:value',
                    onZipCountryChange: '${ $.parentName }.country_id:value',
                    onZipRegionChange: '${ $.parentName }.region_id:value'
                }
            },

            initialize: function () {
                this._super();
                this.on('value', this.onValueChange.bind(this));

                return this;
            },

            getValidationUrl: function () {
                return urlBuilder.build('zipcodevalidation/validate/pincode');
            },

            onValueChange: function () {
                this.validatePincode();
            },

            onZipCountryChange: function () {
                this.revalidateZip();
            },

            onZipRegionChange: function () {
                this.revalidateZip();
            },

            revalidateZip: function () {
                if (this.zipMessage && this.error() === this.zipMessage) {
                    this.error(false);
                }
                this.zipMessage = '';
                this.validatePincode();
            },

            getSettings: function () {
                return window.checkoutConfig && window.checkoutConfig.panthZipcodeValidation
                    ? window.checkoutConfig.panthZipcodeValidation
                    : null;
            },

            clearZipFeedback: function () {
                clearTimeout(this.debounceTimer);
                if (this.zipRequest) {
                    this.zipRequest.abort();
                    this.zipRequest = null;
                }
                if (this.notice) {
                    this.notice(false);
                }
                if (this.zipMessage && this.error() === this.zipMessage) {
                    this.error(false);
                }
                this.zipMessage = '';
            },

            validatePincode: function () {
                var self = this,
                    settings = this.getSettings(),
                    value = $.trim(String(this.value() || '')),
                    countryId,
                    regionId;

                if (settings && !settings.enabled) {
                    return this;
                }

                this.clearZipFeedback();
                countryId = this.getCountryId();

                if (!value || !countryId) {
                    return this;
                }

                regionId = this.getRegionId();

                this.debounceTimer = setTimeout(function () {
                    self.zipRequest = $.ajax({
                        url: self.getValidationUrl(),
                        type: 'POST',
                        data: {
                            pincode: value,
                            'country_id': countryId,
                            'region_id': regionId
                        },
                        dataType: 'json',
                        showLoader: false
                    }).done(function (response) {
                        var format;

                        if (!response || response.rate_limited
                            || $.trim(String(self.value() || '')) !== value
                            || self.getCountryId() !== countryId
                            || self.getRegionId() !== regionId
                        ) {
                            return;
                        }
                        if (!response.valid) {
                            self.zipMessage = response.message;
                            self.error(response.message);
                            self.bubble('error', response.message);
                            return;
                        }
                        if (self.notice && response.state && !(settings && settings.showSuccess === false)) {
                            format = settings && settings.successFormat
                                ? settings.successFormat
                                : 'Valid postal code for {state}';
                            self.notice('\u2713 ' + format.split('{state}').join(response.state));
                        }
                    }).always(function () {
                        self.zipRequest = null;
                    });
                }, 800);

                return this;
            },

            getSiblingValue: function (index) {
                var value = '',
                    sibling,
                    scope;

                try {
                    sibling = this.parentName ? registry.get(this.parentName + '.' + index) : null;
                    if (sibling && typeof sibling.value === 'function') {
                        value = sibling.value();
                    } else if (this.source && this.dataScope) {
                        scope = this.dataScope.replace(/\.postcode$/, '.' + index);
                        value = this.source.get(scope);
                    }
                } catch (e) {
                    value = '';
                }

                return value ? String(value) : '';
            },

            getCountryId: function () {
                return this.getSiblingValue('country_id');
            },

            getRegionId: function () {
                return this.getSiblingValue('region_id');
            }
        });
    };
});
