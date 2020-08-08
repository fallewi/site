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

class BackendPaymentRedirectObserver implements ObserverInterface
{

    /**
     *
     * @var \Lyranetwork\Systempay\Helper\Data
     */
    protected $dataHelper;

    /**
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *
     * @param \Lyranetwork\Systempay\Helper\Data $dataHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Lyranetwork\Systempay\Helper\Data $dataHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->dataHelper = $dataHelper;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
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
        $order = $observer->getEvent()->getOrder();
        if (! $order || ! $order->getId()) {
            // order creation failed
            return $this;
        }

        if (! $this->dataHelper->isBackend()) {
            // placed on frontend
            return $this;
        }

        $method = $order->getPayment()->getMethodInstance();
        if ($method instanceof \Lyranetwork\Systempay\Model\Method\Systempay) {
            $flag = false;
            if ($data = $this->request->getPost('order')) {
                $flag = isset($data['send_confirmation']) ? (bool) $data['send_confirmation'] : false;
            }

            if (! $flag) {
                $order->setSendEmail($flag);
                $order->save();
            }

            $redirectUrl = $this->urlBuilder->getUrl(
                'systempay/payment/redirect',
                [
                    'order_id' => $order->getId(),
                    '_secure' => true
                ]
            );
            $this->coreRegistry->register(Constants::REDIRECT_URL, $redirectUrl);
            $this->coreRegistry->register(Constants::LAST_SUCCESS_QUOTE_ID, $order->getQuoteId());
        }

        return $this;
    }
}
