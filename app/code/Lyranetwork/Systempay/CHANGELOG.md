2.3.2, 2018-12-24:
- Bug fix: get the correct means of payment when selection on site is enabled.
- [paypal]Bug fix: error when refunding a PayPal payment.
- Fix new signature algorithm name (HMAC-SHA-256).
- Send Magento phone number as vads\_cell\_phone (required for some payment means).
- [choozeo]Make Choozeo payment available for DOM.
- Update payment means logos.
- Improve iframe mode interface.
- Save transaction UUID in order payment details.
- Added Spanish translation.
- [prodfaq]Fix notice about shifting the shop to production mode.
- Improve error message after a failed payment.

2.3.1, 2018-07-06:
- [shatwo]Enable HMAC-SHA-256 signature algorithm by default.
- Ignore spaces at the beginning and the end of certificates on return signature processing.

2.3.0, 2018-05-23:
- Enable signature algorithm selection (SHA-1 or HMAC-SHA-256).
- Improve backend configuration screen.

2.2.0, 2018-03-19:
- Display card brand user choice if any in backend order details.
- [choozeo]Added Choozeo sub-module.

2.1.4, 2018-03-08:
- Bug fix: Check value type in logo uploader to avoid a PHP warning during import data from configuration files.
- Bug fix: Added the component load order in etc/module.xml to avoid an exception during Magento installation using comand-line interface.
- [technical]Manage enabled/disabled features by plugin variant.

2.1.3, 2017-10-16:
- Compatibility with Magento 2.2 version (method signature, namespace use, JSON unserialize and layout init).

2.1.2, 2017-08-14:
- Bug fix: compilation problem relative to class constructor parameters.
- Possibility to view payment page within pop-in for standard payment (iframe mode).
- Possibility to select payment card type on merchant website (for both standard payment and payment in installments).
- Save both converted and original paid amounts in Magento transaction details.
- Use of protected variables (instead of private) to facilitate module code extension.
- [multi]Register details about all payment installments.

2.1.1, 2017-01-13:
- Bug fix: correction of an error when returning to store using browser backward button.
- [multi]Bug fix: selected payment in installments option is not considered since Magento 2.1.3.
- Update module structure and code to fulfill Magento marketplace requirements.
- Using "lyranetwork" as vendor name instead of "lyra" (already used in Magento Marketplace).

2.1.0, 2016-11-05:
- Bug fix: error relative to CMS version detection since Magento V 2.1.
- Bug fix: notify URL is not displayed correctly in module backend since Magento V 2.1.
- [multi]Bug fix: multiple payment sub-module dos not work since Magento V 2.1 (selected payment option is not considered in frontend).
- Remove control over certificate format modified on the gateway.
- Possibility to refund payments from Magento backend via WS.
- Backend payments in MOTO mode.
- Upgrade supported card types list.

2.0.1, 2016-06-02:
- Bug fix: correction of an error in backend order creation page when module is enabled.
- Bug fix: use of _scope parameter (instead of _store) in store URLs to redirect to the correct store (in multistore mode).
- Bug fix: error occures when saving module settings without resfreshing cache (if asked by Magento).
- Bug fix: capture delay in sub-modules not considered if equals 0.
- [multi]Bug fix: payment in installments did not work (always processed as single).
- Dispatch event order_cancel_after after order cancellation.
- Check if current quote currency is supported before to check base currency.
- Improve logging system and log request format errors.
- Update german language file.
- Improve of label fields display on admin panel.
- [multi]Do not delete virtual multi payment methods (systempay_multi_Nx) to avoid errors when viewing orders paid with these methods.

2.0.0, 2016-03-10:
- New Systempay payment module for magento 2.
