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
namespace Lyranetwork\Systempay\Block\Payment\Form;

class Oney extends Systempay
{

    protected $_template = 'Lyranetwork_Systempay::payment/form/oney.phtml';

    public function getPaymentOptions()
    {
        if ($this->getMethod()->getConfigData('enable_payment_options') != 1) {
            // local payment options selection is not allowed
            return false;
        }

        $amount = $this->getMethod()
            ->getInfoInstance()
            ->getQuote()
            ->getBaseGrandTotal();
        return $this->getMethod()->getPaymentOptions($amount);
    }
}
