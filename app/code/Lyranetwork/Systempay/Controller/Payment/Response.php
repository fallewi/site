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

use Lyranetwork\Systempay\Helper\Payment;

class Response extends \Magento\Framework\App\Action\Action implements \Lyranetwork\Systempay\Api\ResponseActionInterface
{

    /**
     *
     * @var \Lyranetwork\Systempay\Helper\Data
     */
    protected $dataHelper;

    /**
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     *
     * @var \Lyranetwork\Systempay\Controller\Processor\ResponseProcessor
     */
    protected $responseProcessor;

    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lyranetwork\Systempay\Helper\Data $dataHelper
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Lyranetwork\Systempay\Controller\Processor\ResponseProcessor $responseProcessor
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lyranetwork\Systempay\Helper\Data $dataHelper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Lyranetwork\Systempay\Controller\Processor\ResponseProcessor $responseProcessor,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->quoteRepository = $quoteRepository;
        $this->responseProcessor = $responseProcessor;
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        return $this->responseProcessor->execute($this);
    }

    /**
     * Redirect to error page (when technical error occurred).
     *
     * @param \Magento\Sales\Model\Order $order
     */
    public function redirectError($order)
    {
        // clear all messages in session
        $this->messageManager->getMessages(true);

        $this->dataHelper->getCheckout()
            ->setLastQuoteId($order->getQuoteId())
            ->setLastOrderId($order->getId());

        $this->dataHelper->log("Redirecting to one page checkout failure page for order #{$order->getId()}.");
        return $this->createResult('checkout/onepage/failure', ['_scope' => $order->getStore()->getId()]);
    }

    /**
     * Redirect to result page (according to payment status).
     *
     * @param \Magento\Sales\Model\Order $order
     * @param string $case
     * @param bool $checkUrlWarn
     */
    public function redirectResponse($order, $case, $checkUrlWarn = false)
    {
        /**
         *
         * @var Magento\Checkout\Model\Session $checkout
         */
        $checkout = $this->dataHelper->getCheckout();

        // clear all messages in session
        $this->messageManager->getMessages(true);

        $storeId = $order->getStore()->getId();
        $features = \Lyranetwork\Systempay\Helper\Data::$pluginFeatures;
        if ($features['prodfaq'] && ($this->dataHelper->getCommonConfigData('ctx_mode', $storeId) == 'TEST')) {
            // display going to production message
            $message = __('<u><p>GOING INTO PRODUCTION:</u></p> You want to know how to put your shop into production mode, please read chapters &laquo; Proceeding to test phase &raquo; and &laquo; Shifting the shop to production mode &raquo; in the documentation of the module.');
            $this->messageManager->addNotice($message);

            if ($checkUrlWarn) {
                // order not validated by notification URL, in TEST mode, user is webmaster
                // so display a warning about notification URL not working

                if ($this->dataHelper->isMaintenanceMode()) {
                    $message = __('The shop is in maintenance mode.The automatic notification cannot work.');
                } else {
                    $message = __('The automatic validation has not worked. Have you correctly set up the notification URL in your Systempay Back Office?');
                    $message .= '<br /><br />';
                    $message .= __('For understanding the problem, please read the documentation of the module:<br />&nbsp;&nbsp;&nbsp;- Chapter &laquo; To read carefully before going further &raquo;<br />&nbsp;&nbsp;&nbsp;- Chapter &laquo; Notification URL settings &raquo;');
                }
                $this->messageManager->addError($message);
            }
        }

        if ($case == Payment::SUCCESS) {
            $checkout->setLastQuoteId($order->getQuoteId())
                ->setLastSuccessQuoteId($order->getQuoteId())
                ->setLastOrderId($order->getId())
                ->setLastRealOrderId($order->getIncrementId())
                ->setLastOrderStatus($order->getStatus());

            $this->dataHelper->log("Redirecting to one page checkout success page for order #{$order->getId()}.");
            $resultRedirect = $this->createResult('checkout/onepage/success', ['_scope' => $storeId]);
        } else {

            if ($case == Payment::FAILURE) {
                $this->messageManager->addWarning(__('Your payment was not accepted. Please, try to re-order.'));
            }

            $this->dataHelper->log("Restore cart for order #{$order->getId()} to allow re-order quicker.");
            $quote = $this->quoteRepository->get($order->getQuoteId());
            if ($quote->getId()) {
                $quote->setIsActive(true)->setReservedOrderId(null);
                $this->quoteRepository->save($quote);

                $checkout->replaceQuote($quote);
            }

            $this->dataHelper->log("Redirecting to cart page for order #{$order->getId()}.");
            $resultRedirect = $this->createResult('checkout/cart', ['_scope' => $storeId]);
        }

        return $resultRedirect;
    }

    private function createResult($path, $params)
    {
        if ($this->getRequest()->getParam('iframe', false)) {
            $result = $this->resultPageFactory->create();

            $block = $result->getLayout()
                ->createBlock(\Lyranetwork\Systempay\Block\Payment\Iframe\Response::class)
                ->setTemplate('Lyranetwork_Systempay::payment/iframe/response.phtml')
                ->setForwardPath($path, $params);

            $this->getResponse()->setBody($block->toHtml());
            return null;
        } else {
            /**
             *
             * @var \Magento\Framework\Controller\Result\Redirect $result
             */
            $result = $this->resultRedirectFactory->create();
            $result->setPath($path, $params);
            return $result;
        }
    }
}
