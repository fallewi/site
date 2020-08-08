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
 * @package     Ced_2.3
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright © 2019 CedCommerce. All rights reserved.
 * @license     EULA http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Amazon\Plugin\Order;

use Ced\Amazon\Registry\Order as AmazonOrderRegistry;

class BackOrderCondition
{
    public $amazonOrderRegistry;

    public function __construct(
        AmazonOrderRegistry $amazonOrderRegistry
    ) {
        $this->amazonOrderRegistry = $amazonOrderRegistry;
    }

    public function afterExecute(
        \Magento\InventorySales\Model\IsProductSalableCondition\BackOrderCondition $subject,
        $result,
        $sku
    ) {
        // Injecting the Amazon Order Country
        if ($this->amazonOrderRegistry->hasOrder() && $this->amazonOrderRegistry->hasItem($sku)) {
            $result = true;
        }

        return $result;
    }
}
