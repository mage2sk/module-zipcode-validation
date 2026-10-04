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
    'ko',
    'domReady!'
], function ($, ko) {
    'use strict';

    var COUNTRY_SELECTOR = 'select[name*="country_id"], input[name*="country_id"]',
        REGION_SELECTOR = 'select[name*="region_id"], input[name*="region_id"]',
        ZIP_SELECTOR = [
            'input[name*="postcode"]',
            'input[name*="zip"]',
            '#zip',
            '#postcode'
        ].join(', ');

    return function (config) {
        var validationUrl = config.validationUrl || '',
            showSuccessMessage = config.showSuccess !== false,
            successFormat = config.successFormat || 'Valid postal code for {state}',
            successColor = config.successColor || '#007a33',
            errorColor = config.errorColor || '#e02b27';

        function messageId(element) {
            if (!element.id) {
                element.id = 'panth-zip-' + Math.random().toString(36).slice(2, 9);
            }
            return element.id + '-zipmsg';
        }

        function isHandledByCheckoutMixin(element) {
            var component;

            try {
                component = ko.dataFor(element);
            } catch (e) {
                component = null;
            }

            return !!(component && typeof component.validatePincode === 'function');
        }

        function fieldValue(form, selector) {
            var field = form.find(selector).filter(':input').first();

            return field.length ? $.trim(String(field.val() || '')) : '';
        }

        function removeMessage(element) {
            var $element = $(element),
                id = messageId(element);

            $('#' + id).remove();
            if ($element.data('panthZipError')) {
                $element.closest('.field').removeClass('_error');
                $element.removeClass('mage-error').removeData('panthZipError');
                if ($element.attr('data-bind')) {
                    $element.attr('aria-invalid', 'false');
                } else {
                    $element.removeAttr('aria-invalid');
                }
            }
            if ($element.attr('aria-describedby') === id) {
                if ($element.data('panthZipDescribedBy')) {
                    $element.attr('aria-describedby', $element.data('panthZipDescribedBy'));
                } else {
                    $element.removeAttr('aria-describedby');
                }
            }
            $element.removeData('panthZipDescribedBy');
        }

        function addMessage(element, text, isError) {
            var $element = $(element),
                id = messageId(element),
                message = $('<div></div>')
                    .attr('id', id)
                    .attr('role', isError ? 'alert' : 'status')
                    .addClass(isError ? 'field-error panth-zip-message' : 'field-success panth-zip-message')
                    .css({color: isError ? errorColor : successColor, 'font-size': '12px', 'margin-top': '5px'})
                    .text(isError ? text : '\u2713 ' + text);

            removeMessage(element);
            if ($element.attr('aria-describedby')) {
                $element.data('panthZipDescribedBy', $element.attr('aria-describedby'));
            }
            ($element.closest('.control').length ? $element.closest('.control') : $element.parent()).append(message);
            $element.attr('aria-describedby', id);
            if (isError) {
                $element.closest('.field').addClass('_error');
                $element.addClass('mage-error').attr('aria-invalid', 'true').data('panthZipError', true);
            }
        }

        function validateZipcode(element) {
            var $element = $(element),
                form = $element.closest('form'),
                zipcode = $.trim(String($element.val() || '')),
                countryId = fieldValue(form, COUNTRY_SELECTOR),
                regionId = fieldValue(form, REGION_SELECTOR);

            clearTimeout($element.data('panthZipTimer'));
            removeMessage(element);

            if (!validationUrl || !zipcode || !countryId || isHandledByCheckoutMixin(element)) {
                return;
            }

            $element.data('panthZipTimer', setTimeout(function () {
                $.ajax({
                    url: validationUrl,
                    type: 'POST',
                    data: {
                        pincode: zipcode,
                        'country_id': countryId,
                        'region_id': regionId
                    },
                    dataType: 'json',
                    showLoader: false
                }).done(function (response) {
                    if (!response || response.rate_limited
                        || $.trim(String($element.val() || '')) !== zipcode
                        || fieldValue(form, COUNTRY_SELECTOR) !== countryId
                        || fieldValue(form, REGION_SELECTOR) !== regionId
                    ) {
                        return;
                    }
                    if (!response.valid) {
                        addMessage(element, response.message, true);
                    } else if (showSuccessMessage && response.state) {
                        addMessage(element, successFormat.split('{state}').join(response.state), false);
                    } else {
                        removeMessage(element);
                    }
                });
            }, 800));
        }

        function revalidateForm() {
            $(this).closest('form').find(ZIP_SELECTOR).each(function () {
                validateZipcode(this);
            });
        }

        $(document).on('change blur', ZIP_SELECTOR, function () {
            validateZipcode(this);
        });
        $(document).on('change', COUNTRY_SELECTOR, revalidateForm);
        $(document).on('change', REGION_SELECTOR, revalidateForm);
    };
});
