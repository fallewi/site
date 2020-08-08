/**
 * Systempay V2-Payment Module version 2.3.2 for Magento 2.x. Support contact : supportvad@lyra-network.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is licensed under the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category  Payment
 * @package   Systempay
 * @author    Lyra Network (http://www.lyra-network.com/)
 * @copyright 2014-2018 Lyra Network and contributors
 * @license   https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                redirectAfterPlaceOrder: false
            },

            initObservable: function () {
                this._super().observe('systempayCcType');
                return this;
            },

            getModuleLogoUrl: function () {
                return window.checkoutConfig.payment[this.item.method].moduleLogoUrl;
            },

            getAvailableCcTypes: function () {
                return window.checkoutConfig.payment[this.item.method].availableCcTypes;
            },

            getEntryMode: function () {
                return window.checkoutConfig.payment[this.item.method].entryMode;
            },

            getData: function () {
                var data = this._super(), additionalData = {};
                additionalData[this.item.method + '_cc_type'] = this.systempayCcType();

                return $.extend(true, data, {
                    'additional_data': additionalData
                });
            },

            showLabel: function () {
                return false;
            },

            getIframeLoaderUrl: function () {
                return window.checkoutConfig.payment[this.item.method].iframeLoaderUrl;
            },

            getCheckoutRedirectUrl: function () {
                return window.checkoutConfig.payment[this.item.method].checkoutRedirectUrl;
            },

            afterPlaceOrder: function () {
                // order placed with payment_pending status, redirect to gateway
                $.mage.redirect(this.getCheckoutRedirectUrl());
            }
        });
    }
);