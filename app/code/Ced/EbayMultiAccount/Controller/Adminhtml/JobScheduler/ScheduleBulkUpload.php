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

/**
 * Class ScheduleBulkUpload
 * @package Ced\EbayMultiAccount\Controller\Adminhtml\JobScheduler
 */
class ScheduleBulkUpload extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /** @var \Ced\EbayMultiAccount\Helper\FileUpload */
    public $fileUploadHelper;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_EbayMultiAccount::EbayMultiAccount';

    /**
     * ScheduleBulkUpload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Ced\EbayMultiAccount\Helper\FileUpload $fileUploadHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ced\EbayMultiAccount\Helper\FileUpload $fileUploadHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->fileUploadHelper = $fileUploadHelper;
    }

    /**
     * Product list page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $hasError = false;
        $prodCollection = $this->fileUploadHelper->getAllAssignedProductCollection();
        if (count($prodCollection) > 0) {
            if(count($prodCollection) > 100) {
                $this->_session->setBulkUploadIds($prodCollection);
                $resultPage = $this->resultPageFactory->create();
                $resultPage->setActiveMenu('Ced_EbayMultiAccount::product');
                $resultPage->getConfig()->getTitle()->prepend(__('Schedule Bulk Upload Ids'));
                return $resultPage;
            }

            $accountIndexes = $this->_session->getAccountIndexes();
            foreach ($prodCollection as $chunkIndex => $collectionIds) {
                $accountIdToSchedule = '';
                foreach ($accountIndexes as $accountId => $accountIndex) {
                    if($chunkIndex <= $accountIndex['end_index'] && $chunkIndex >= $accountIndex['start_index']) {
                        $accountIdToSchedule = $accountId;
                    }
                }
                $scheduled = $this->fileUploadHelper->createSchedulerForIds($collectionIds, $accountIdToSchedule);
                if(!$scheduled && !$hasError) {
                    $hasError = true;
                }
            }

            if (!$hasError) {
                $this->messageManager->addSuccessMessage(__('Product Ids Scheduled for Upload.'));
            } else {
                $this->messageManager->addErrorMessage('Something Went Wrong while scheduling Product Ids Scheduled for Upload');
            }
        } else {
            $this->messageManager->addErrorMessage(__('No Product Assigned in Active Profiles.'));
        }
        $result = $this->resultRedirectFactory->create();
        $result->setPath('ebaymultiaccount/jobScheduler/index');
        return $result;
    }
}
