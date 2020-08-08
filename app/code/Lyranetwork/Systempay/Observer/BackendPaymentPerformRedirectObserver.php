<?php
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
namespace Lyranetwork\Systempay\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Lyranetwork\Systempay\Block\Constants;

class BackendPaymentPerformRedirectObserver implements ObserverInterface
{

    /**
     *
     * @var \Lyranetwork\Systempay\Helper\Data
     */
    protected $dataHelper;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *
     * @param \Lyranetwork\Systempay\Helper\Data $dataHelper
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Lyranetwork\Systempay\Helper\Data $dataHelper, \Magento\Framework\Registry $coreRegistry)
    {
        $this->dataHelper = $dataHelper;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Redirect to payment gateway after backend order creation.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $response = $observer->getEvent()->getResponse();
        $request = $observer->getEvent()->getRequest();

        $moduleName = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if ($moduleName !== 'sales' || $controller !== 'order_create' || $action !== 'save') {
            // not interested in this action
            return $this;
        }

        if (! $this->dataHelper->isBackend()) {
            // order placed on frontend
            return $this;
        }

        $url = $this->coreRegistry->registry(Constants::REDIRECT_URL);
        if ($url) {
            $this->dataHelper->getCheckout()->setLastSuccessQuoteId(
                $this->coreRegistry->registry(Constants::LAST_SUCCESS_QUOTE_ID)
            );

            $response->setRedirect($url)->sendResponse();
        }

        return $this;
    }
}
