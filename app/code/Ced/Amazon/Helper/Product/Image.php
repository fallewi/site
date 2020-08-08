<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Amazon
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright © 2018 CedCommerce. All rights reserved.
 * @license     EULA http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Amazon\Helper\Product;

class Image
{
    /** @var \Magento\Framework\Api\Search\SearchCriteriaBuilderFactory $search  */
    public $search;

    /** @var \Magento\Framework\Api\FilterFactory  */
    public $filter;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  */
    public $products;

    /** @var \Ced\Amazon\Api\ProfileRepositoryInterface  */
    public $profile;

    /**
     * @var \Ced\Amazon\Api\QueueRepositoryInterface
     */
    public $queue;

    /** @var \Ced\Amazon\Api\Data\Queue\DataInterfaceFactory  */
    public $queueDataFactory;

    /** @var \Ced\Amazon\Api\AccountRepositoryInterface  */
    public $account;

    /** @var \Ced\Amazon\Api\FeedRepositoryInterface  */
    public $feed;

    /** @var \Ced\Amazon\Helper\Config  */
    public $config;

    /** @var \Amazon\Sdk\EnvelopeFactory  */
    public $envelope;

    /** @var \Amazon\Sdk\Product\ImageFactory  */
    public $image;

    public function __construct(
        \Magento\Framework\Api\Search\SearchCriteriaBuilderFactory $search,
        \Magento\Framework\Api\FilterFactory $filter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsFactory,
        \Ced\Amazon\Api\AccountRepositoryInterface $account,
        \Ced\Amazon\Api\ProfileRepositoryInterface $profile,
        \Ced\Amazon\Api\QueueRepositoryInterface $queue,
        \Ced\Amazon\Api\FeedRepositoryInterface $feed,
        \Ced\Amazon\Api\Data\Queue\DataInterfaceFactory $queueDataFactory,
        \Ced\Amazon\Helper\Config $config,
        \Amazon\Sdk\EnvelopeFactory $envelopeFactory,
        \Amazon\Sdk\Product\ImageFactory $image
    ) {
        $this->search = $search;
        $this->filter = $filter;

        $this->products = $productsFactory;

        $this->account = $account;
        $this->profile = $profile;
        $this->feed = $feed;
        $this->queue = $queue;
        $this->queueDataFactory = $queueDataFactory;
        $this->config = $config;

        $this->envelope = $envelopeFactory;
        $this->image = $image;
    }

    /**
     * @TODO: Update images for 'uploaded' products only,
     * @TODO: Swatches
     * Update the values for provided ids
     * @param array $ids
     * @param bool $throttle
     * @return boolean
     * @throws \Exception
     */
    public function update(array $ids = [], $throttle = true)
    {
        $status = false;
        if (isset($ids) && !empty($ids)) {
            $profileIds = $this->profile->getProfileIdsByProductIds($ids);
            if (!empty($profileIds)) {
                /** @var \Magento\Framework\Api\Filter $idsFilter */
                $idsFilter = $this->filter->create();
                $idsFilter->setField(\Ced\Amazon\Model\Profile::COLUMN_ID)
                    ->setConditionType('in')
                    ->setValue($profileIds);

                /** @var \Magento\Framework\Api\Filter $statusFilter */
                $statusFilter = $this->filter->create();
                $statusFilter->setField(\Ced\Amazon\Model\Profile::COLUMN_STATUS)
                    ->setConditionType('eq')
                    ->setValue(\Ced\Amazon\Model\Source\Profile\Status::ENABLED);

                /** @var \Magento\Framework\Api\Search\SearchCriteriaBuilder $criteria */
                $criteria = $this->search->create();
                $criteria->addFilter($statusFilter);
                $criteria->addFilter($idsFilter);
                /** @var \Ced\Amazon\Api\Data\ProfileSearchResultsInterface $profiles */
                $profiles = $this->profile->getList($criteria->create());
                /** @var \Ced\Amazon\Api\Data\AccountSearchResultsInterface $accounts */
                $accounts = $profiles->getAccounts();

                /** @var array $stores */
                $stores = $profiles->getProfileByStoreIdWise();

                /** @var \Ced\Amazon\Api\Data\AccountInterface $account */
                foreach ($accounts->getItems() as $accountId => $account) {
                    foreach ($stores as $storeId => $profiles) {
                        $envelope = null;
                        /** @var \Ced\Amazon\Api\Data\ProfileInterface $profile */
                        foreach ($profiles as $profileId => $profile) {
                            /** @var array $productIds */
                            $productIds = $this->profile->getAssociatedProductIds($profileId, $storeId, $ids);
                            $specifics = [
                                'ids' => $productIds,
                                'account_id' => $accountId,
                                'marketplace' => $profile->getMarketplace(),
                                'profile_id' => $profileId,
                                'store_id' => $storeId,
                                'type' => \Amazon\Sdk\Api\Feed::PRODUCT_IMAGE,
                            ];

                            if (!empty($productIds)) {
                                if ($throttle == true) {
                                    // queue
                                    /** @var \Ced\Amazon\Api\Data\Queue\DataInterface $queueData */
                                    $queueData = $this->queueDataFactory->create();
                                    $queueData->setAccountId($accountId);
                                    $queueData->setMarketplace($profile->getMarketplace());
                                    $queueData->setSpecifics($specifics);
                                    $queueData->setType(\Amazon\Sdk\Api\Feed::PRODUCT_IMAGE);
                                    $status = $this->queue->push($queueData);
                                } else {
                                    //TODO: add all data to uniqueid in session & process via multiple ajax requests.

                                    // prepare & send: divide in chunks and process in multiple requests
                                    $envelope = $this->prepare($specifics, $envelope);
                                    $status = $this->feed->send($envelope, $specifics);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $status;
    }

    /**
     * Prepare Price for Amazon
     * @param array $specifics
     * @param array|null $envelope
     * @return \Amazon\Sdk\Envelope|null
     * @throws \Exception
     */
    public function prepare(array $specifics = [], $envelope = null)
    {
        if (isset($specifics) && !empty($specifics)) {
            $imageTypes = [
                0 => 'Main',
                1 => 'PT1',
                2 => 'PT2',
                3 => 'PT3',
                4 => 'PT4',
                5 => 'PT5',
                6 => 'PT6',
                7 => 'PT7',
                8 => 'PT8'
            ];

            $ids = $specifics['ids'];
            /** @var \Ced\Amazon\Api\Data\ProfileInterface $profile */
            $profile = $this->profile->getById($specifics['profile_id']);

            /** @var \Ced\Amazon\Api\Data\AccountInterface $account */
            $account = $this->account->getById($specifics['account_id']);

            if (!isset($envelope)) {
                /** @var \Amazon\Sdk\Envelope $envelope */
                $envelope = $this->envelope->create(
                    [
                        'merchantIdentifier' => $account->getConfig($profile->getMarketplaceIds())->getSellerId(),
                        'messageType' => \Amazon\Sdk\Base::MESSAGE_TYPE_IMAGE
                    ]
                );
            }

            $storeId = $profile->getStore()->getId();

            /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $products */
            $products = $this->products->create()
                ->setStoreId($storeId)
                ->addAttributeToSelect(['sku', 'entity_id', 'type_id'])
                ->addAttributeToFilter('entity_id', ['in' => $ids])
                ->addMediaGalleryData();
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($products as $product) {
                // case 1 : for configurable products
                if ($product->getTypeId() == 'configurable') {
                    // Adding images for parent, useful if child have no image.
                    $images = $product->getMediaGalleryImages();
                    if (isset($images) && $images->getSize() > 0) {
                        $counter = 0;
                        $tmp = [];
                        foreach ($images as $key => $image) {
                            $tmp[$image->getData("position")] = $image->getUrl();
                        }
                        ksort($tmp);
                        $tmp = array_unique($tmp);
                        foreach ($tmp as $position => $url) {
                            // Maximum 8 images allowed
                            if ($counter > 8) {
                                break;
                            }

                            /**
                             * @var \Amazon\Sdk\Product\Image $i
                             */
                            $i = $this->image->create();
                            $i->setId($profile->getId() . $product->getId() . $position);
                            $i->setData($product->getSku(), $imageTypes[$counter++], $url);
                            $envelope->addImage($i);
                        }
                    }

                    $parentId = $product->getId();
                    $productType = $product->getTypeInstance();

                    /** @codingStandardsIgnoreStart */
                    $childIds = $productType->getChildrenIds($parentId);
                    /** @codingStandardsIgnoreEnd */
                    /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $products */
                    $children = $this->products->create()
                        ->setStoreId($storeId)
                        ->addAttributeToSelect(['sku', 'entity_id', 'type_id'])
                        ->addAttributeToFilter('entity_id', ['in' => $childIds[0]])
                        ->addMediaGalleryData();
                    /** @var \Magento\Catalog\Model\Product $child */
                    foreach ($children as $child) {
                        $images = $child->getMediaGalleryImages();
                        $tmp = [];
                        foreach ($images as $key => $image) {
                            $tmp[$image->getData("position")] = $image->getUrl();
                        }
                        ksort($tmp);
                        $tmp = array_unique($tmp);

                        if (isset($tmp) && count($tmp) > 0) {
                            $counter = 0;
                            foreach ($tmp as $position => $url) {
                                // Maximum 8 images allowed
                                if ($counter > 8) {
                                    break;
                                }

                                /**
                                 * @var \Amazon\Sdk\Product\Image $i
                                 */
                                $i = $this->image->create();
                                $i->setId($profile->getId() . $child->getId() . $position);
                                $i->setData($child->getSku(), $imageTypes[$counter++], $url);
                                $envelope->addImage($i);
                            }
                        }
                    }
                } elseif ($product->getTypeId() == 'simple') {
                    $images = $product->getMediaGalleryImages();
                    // case 2 : for simple products
                    if (isset($images) && $images->getSize() > 0) {
                        $counter = 0;
                        $tmp = [];
                        foreach ($images as $key => $image) {
                            $tmp[$image->getData("position")] = $image->getUrl();
                        }
                        ksort($tmp);
                        $tmp = array_unique($tmp);
                        foreach ($tmp as $position => $url) {
                            // Maximum 8 images allowed
                            if ($counter > 8) {
                                break;
                            }

                            /**
                             * @var \Amazon\Sdk\Product\Image $i
                             */
                            $i = $this->image->create();
                            $i->setId($profile->getId() . $product->getId() . $position);
                            $i->setData($product->getSku(), $imageTypes[$counter++], $url);
                            $envelope->addImage($i);
                        }
                    }
                }
            }
        }

        return $envelope;
    }
}
