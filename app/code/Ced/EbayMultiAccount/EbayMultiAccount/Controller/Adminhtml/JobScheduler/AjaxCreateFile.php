<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_EbayMultiAccount
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\EbayMultiAccount\Controller\Adminhtml\JobScheduler;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Ced\EbayMultiAccount\Helper\Logger;

/**
 * Class AjaxCreateFile
 * @package Ced\EbayMultiAccount\Controller\Adminhtml\JobScheduler
 */
class AjaxCreateFile extends Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;
    /**
     * @var JsonFactory
     */
    public $resultJsonFactory;

    /**
     * @var \Ced\EbayMultiAccount\Model\JobScheduler
     */
    public $schedulerCollection;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var \Ced\EbayMultiAccount\Helper\FileUpload
     */
    public $fileUploadHelper;

    /**
     * AjaxCreateFile constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param Logger $logger
     * @param \Ced\EbayMultiAccount\Helper\FileUpload $fileUploadHelper
     * @param \Ced\EbayMultiAccount\Model\JobScheduler $schedulerResource
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        Logger $logger,
        \Ced\EbayMultiAccount\Helper\FileUpload $fileUploadHelper,
        \Ced\EbayMultiAccount\Model\JobScheduler $schedulerResource
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->fileUploadHelper = $fileUploadHelper;
        $this->logger = $logger;
        $this->schedulerCollection = $schedulerResource;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $createdFile = false;
        $message = [];
        $message['error'] = "";
        $message['success'] = "";

        $key = $this->getRequest()->getParam('index');
        $totalChunk = $this->_session->getSchedulerIds();
        $index = $key + 1;
        if (count($totalChunk) <= $index) {
            $this->_session->unsSchedulerIds();
        }
        try {
            if (isset($totalChunk[$key])) {
                $ebaymultiaccountSchedulerIds = $totalChunk[$key];
                foreach ($ebaymultiaccountSchedulerIds as $schedulerId) {
                    /** @var \Ced\EbayMultiAccount\Model\JobScheduler $schedulerData */
                    $schedulerData = $this->schedulerCollection->load($schedulerId);
                    try {
                        if ($schedulerData ) {
                            $productIds = $schedulerData->getProductIds();
                            $productIds = is_string($productIds) ? explode(",", $productIds) : array();
                            if ($schedulerData->getSchedulerType() == $schedulerData::REVISEINVENTORYSTATUS) {
                                $createdFile = $this->fileUploadHelper->prepareInventoryUpdateFile($productIds, $schedulerData->getSchedulerType(), $schedulerData->getAccountId());
                            } else {
                                $createdFile = $this->fileUploadHelper->prepareUploadFile($productIds, $schedulerData->getSchedulerType(), $schedulerData->getAccountId());
                            }
                            if (isset($createdFile['feed_file'])) {
                                $schedulerData->setFeedFilePath($createdFile['feed_file'])
                                    ->setCronStatus('file_created')
                                    ->save();
                            }
                        }
                    } catch (\Exception $e) {
                        $message['error'] = $message['error'] . 'Exception : ' . $e->getMessage();
                    }
                }
                if ($createdFile) {
                    $message['success'] = $message['success'] . "Batch: " . $index . ' ' . $schedulerData->getSchedulerType() . " : products file created for upload.";
                } else {
                    $message['error'] = $message['error'] . 'Batch: ' . $index . ' ' . $schedulerData->getSchedulerType() . ' : file not created for upload.';
                }
            }
        } catch (\Exception $e) {
            $message['error'] = $e->getMessage();
            $this->logger->addError($message['error'], ['path' => __METHOD__]);
        }
        return $resultJson->setData($message);
    }
}
