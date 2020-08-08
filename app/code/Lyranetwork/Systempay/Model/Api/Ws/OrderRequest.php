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

namespace Lyranetwork\Systempay\Model\Api\Ws;

class OrderRequest
{
    /**
     * @var string $orderId
     */
    private $orderId = null;

    /**
     * @var ExtInfo[] $extInfo
     */
    private $extInfo = null;

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return OrderRequest
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return ExtInfo[]
     */
    public function getExtInfo()
    {
        return $this->extInfo;
    }

    /**
     * @param ExtInfo[] $extInfo
     * @return OrderRequest
     */
    public function setExtInfo(array $extInfo = null)
    {
        $this->extInfo = $extInfo;
        return $this;
    }
}
