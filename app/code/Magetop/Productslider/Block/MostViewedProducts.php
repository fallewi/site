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

namespace Magetop\Productslider\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magetop\Productslider\Helper\Data;
use Magetop\Productslider\Model\ResourceModel\Report\Product\CollectionFactory as MostViewedCollectionFactory;

/**
 * Class MostViewedProducts
 * @package Magetop\Productslider\Block
 */
class MostViewedProducts extends AbstractSlider
{
    /**
     * @var \Magetop\Productslider\Model\ResourceModel\Report\Product\CollectionFactory
     */
    protected $_mostViewedProductsFactory;

    /**
     * MostViewedProducts constructor.
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param DateTime $dateTime
     * @param Data $helperData
     * @param HttpContext $httpContext
     * @param MostViewedCollectionFactory $mostViewedProductsFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        DateTime $dateTime,
        Data $helperData,
        HttpContext $httpContext,
        MostViewedCollectionFactory $mostViewedProductsFactory,
        array $data = []
    )
    {
        $this->_mostViewedProductsFactory = $mostViewedProductsFactory;

        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility, $dateTime, $helperData, $httpContext, $data);
    }

    /**
     * Get Product Collection of MostViewed Products
     * @return mixed
     */
    public function getProductCollection()
    {
        $collection = $this->_mostViewedProductsFactory->create()
            ->addAttributeToSelect('*')
            ->setStoreId($this->getStoreId())->addViewsCount()
            ->addStoreFilter($this->getStoreId())
            ->setPageSize($this->getProductsCount());

        return $collection;
    }
}