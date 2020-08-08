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
namespace Lyranetwork\Systempay\Controller\Payment;

use Lyranetwork\Systempay\Model\OrderException;

class Redirect extends \Magento\Framework\App\Action\Action implements \Lyranetwork\Systempay\Api\RedirectActionInterface
{

    /**
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     *
     * @var \Lyranetwork\Systempay\Helper\Data
     */
    protected $dataHelper;

    /**
     *
     * @var \Lyranetwork\Systempay\Controller\Processor\RedirectProcessor
     */
    protected $redirectProcessor;

    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Lyranetwork\Systempay\Helper\Data $dataHelper
     * @param \Lyranetwork\Systempay\Controller\Processor\RedirectProcessor $redirectProcessor
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Lyranetwork\Systempay\Helper\Data $dataHelper,
        \Lyranetwork\Systempay\Controller\Processor\RedirectProcessor $redirectProcessor,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->dataHelper = $dataHelper;
        $this->redirectProcessor = $redirectProcessor;
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        return $this->redirectProcessor->execute($this);
    }

    /**
     * Get order to pay from session and check it (amount, already processed, ...).
     */
    public function getAndCheckOrder()
    {
        /**
         *
         * @var Magento\Checkout\Model\Session $checkout
         */
        $checkout = $this->dataHelper->getCheckout();

        // load order
        $lastIncrementId = $checkout->getLastRealOrderId();

        // check that there is an order to pay
        if (empty($lastIncrementId)) {
            $this->dataHelper->log("No order to pay. It may be a direct access to redirection page." .
                     " [IP = {$this->dataHelper->getIpAddress()}].");
            throw new OrderException('Order not found in session.');
        }

        $order = $this->orderFactory->create();
        $order->loadByIncrementId($lastIncrementId);

        // check that there is products in cart
        if ($order->getTotalDue() == 0) {
            $this->dataHelper->log("Payment attempt with no amount. [Order = {$order->getId()}]" .
                     " [IP = {$this->dataHelper->getIpAddress()}].");
            throw new OrderException('Order total is empty.');
        }

        // check that order is not processed yet
        if (! $checkout->getLastSuccessQuoteId()) {
            $this->dataHelper->log("Payment attempt with a quote already processed. [Order = {$order->getId()}]" .
                     " [IP = {$this->dataHelper->getIpAddress()}].");
            throw new OrderException('Order payment already processed.');
        }

        // clear quote data
        $checkout->unsLastQuoteId()
            ->unsLastSuccessQuoteId()
            ->clearHelperData();

        return $order;
    }

    /**
     * Redirect to checkout initial page (when payment cannot be done).
     */
    public function back($msg)
    {
        // clear all messages in session
        $this->messageManager->getMessages(true);
        $this->dataHelper->log($msg . ' Redirecting to cart page.');

        if ($this->getRequest()->getParam('iframe', false)) {
            $result = $this->resultPageFactory->create();

            $block = $result->getLayout()
                ->createBlock(\Lyranetwork\Systempay\Block\Payment\Iframe\Response::class)
                ->setTemplate('Lyranetwork_Systempay::payment/iframe/response.phtml')
                ->setForwardPath('checkout/cart');

            $this->getResponse()->setBody($block->toHtml());
            return null;
        } else {
            $result = $this->resultRedirectFactory->create();
            $result->setPath('checkout/cart');
            return $result;
        }
    }

    /**
     * Display redirection page.
     */
    public function forward()
    {
        $resultPage = $this->resultPageFactory->create();

        if ($this->getRequest()->getParam('iframe', false)) {
            $resultPage->addHandle('systempay_payment_iframe_redirect');
        } else {
            $resultPage->addHandle('systempay_payment_form_redirect');
            $resultPage->getConfig()->getTitle()->set(__('Payment gateway redirection'));
        }

        return $resultPage;
    }
}
