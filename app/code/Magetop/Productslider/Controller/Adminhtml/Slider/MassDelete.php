<?php
/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_Productslider
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

namespace Magetop\Productslider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magetop\Productslider\Model\ResourceModel\Slider\CollectionFactory;

/**
 * Class MassDelete
 * @package Magetop\Productslider\Controller\Adminhtml\Slider
 */
class MassDelete extends Action
{
    /**
     * Mass Action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * Collection Factory
     *
     * @var \Magetop\Productslider\Model\ResourceModel\Slider\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    )
    {
        $this->_filter            = $filter;
        $this->_collectionFactory = $collectionFactory;

        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());

        $delete = 0;
        foreach ($collection as $item) {
            /** @var \Magetop\Productslider\Model\Slider $item */
            $item->delete();
            $delete++;
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $delete));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
