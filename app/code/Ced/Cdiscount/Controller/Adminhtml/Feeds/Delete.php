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
 * @package   Ced_Cdiscount
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Cdiscount\Controller\Adminhtml\Feeds;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Delete
 *
 * @package Ced\Cdiscount\Controller\Adminhtml\Feeds
 */
class Delete extends Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \Ced\Cdiscount\Model\Feeds
     */
    public $cdiscountFeeds;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    public $fileIo;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    public $filter;

    /**
     * Delete constructor.
     *
     * @param Action\Context                        $context
     * @param PageFactory                           $resultPageFactory
     * @param \Magento\Framework\Filesystem\Io\File $fileIo
     * @param \Ced\Cdiscount\Model\Feeds               $cdiscountFeeds
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem\Io\File $fileIo,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Ced\Cdiscount\Model\Feeds $cdiscountFeeds
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->fileIo = $fileIo;
        $this->filter = $filter;
        $this->cdiscountFeeds = $cdiscountFeeds;
    }

    public function execute()
    {
        $isFilter = $this->getRequest()->getParam('filters');
        if (isset($isFilter)) {
            $collection = $this->filter->getCollection($this->cdiscountFeeds->getCollection());
        } else {
            $id = $this->getRequest()->getParam('id');
            if (isset($id) and !empty($id)) {
                $collection = $this->cdiscountFeeds->getCollection()->addFieldToFilter('id', ['eq' => $id]);
            }
        }

        $feedStatus = false;
        if (isset($collection) and $collection->getSize() > 0) {
            $feedStatus = true;
            foreach ($collection as $feed) {
                $feedFile = $feed->getFeedFile();
                if ($this->fileIo->fileExists($feedFile)) {
                    $this->fileIo->rm($feedFile);
                }

                $responseFile = $feed->getResponseFile();
                if ($this->fileIo->fileExists($responseFile)) {
                    $this->fileIo->rm($responseFile);
                }
                $feedStatus = $feed->delete();
            }
        }

        if ($feedStatus) {
            $this->messageManager->addSuccessMessage('Feed deleted successfully.');
        } else {
            $this->messageManager->addErrorMessage('Feed delete failed.');
        }
        $this->_redirect('cdiscount/feeds');
    }
}
