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
 * @copyright   Copyright (c) 2018 Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

namespace Magetop\Productslider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magetop\Productslider\Model\ResourceModel\Slider\CollectionFactory;

/**
 * Class MassStatus
 * @package Magetop\Productslider\Controller\Adminhtml\Slider
 */
class MassStatus extends Action
{
    /**
     * Mass Action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    public $filter;

    /**
     * Collection Factory
     *
     * @var \Magetop\Productslider\Model\ResourceModel\Slider\CollectionFactory
     */
    public $collectionFactory;

    /**
     * MassStatus constructor.
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
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection    = $this->filter->getCollection($this->collectionFactory->create());
        $status        = (int)$this->getRequest()->getParam('status');
        $sliderUpdated = 0;
        foreach ($collection as $slider) {
            try {
                $slider->setStatus($status)
                    ->save();

                $sliderUpdated++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while updating status for %1.', $slider->getName()));
            }
        }

        if ($sliderUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $sliderUpdated));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
