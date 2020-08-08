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
 * @package   Ced_m2.2.EE
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Cdiscount\Model;


class ProductChange extends \Magento\Framework\Model\AbstractModel
{
    const PRODUCT_CHANGE_TYPE_INVENTORY_PRICE = 'price_inventory';

    const PRODUCT_CHANGE_ACTION_UPDATE = 'update';

    public function _construct()
    {
        $this->_init('Ced\Cdiscount\Model\ResourceModel\ProductChange');
    }
}