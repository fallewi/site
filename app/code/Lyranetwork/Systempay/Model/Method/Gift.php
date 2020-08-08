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
namespace Lyranetwork\Systempay\Model\Method;

class Gift extends Systempay
{

    protected $_code = \Lyranetwork\Systempay\Helper\Data::METHOD_GIFT;

    protected $_formBlockType = \Lyranetwork\Systempay\Block\Payment\Form\Gift::class;

    protected $_canRefund = false;

    protected $_canRefundInvoicePartial = false;

    protected function setExtraFields($order)
    {
        $info = $this->getInfoInstance();

        // override payment_cards
        $this->systempayRequest->set('payment_cards', $info->getCcType());
    }

    /**
     * Assign data to info model instance.
     *
     * @param \Magento\Framework\DataObject $data
     * @return $this
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        // reset payment method specific data
        $this->resetData();

        parent::assignData($data);

        $info = $this->getInfoInstance();

        $systempayData = $this->extractSystempayData($data);
        $info->setCcType($systempayData->getData('systempay_gift_gc_type'));

        return $this;
    }

    /**
     * Return true if the method can be used at this time.
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if (! $this->getConfigData('gift_cards')) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * Get a complete list of available gift cards.
     *
     * @return array[string][string]
     */
    public function getAvailableGcTypes()
    {
        // get selected values from module configuration
        $cards = $this->getConfigData('gift_cards');
        if (empty($cards)) {
            return [];
        }

        $cards = explode(',', $cards);

        $availCards = [];
        foreach ($this->getSupportedGcTypes() as $code => $label) {
            if (in_array($code, $cards)) {
                $availCards[$code] = $label;
            }
        }
        return $availCards;
    }

    /**
     * Get a complete list of supported gift cards.
     *
     * @return array[string][string]
     */
    public function getSupportedGcTypes()
    {
        $options = $this->_getHelper()->getConfigArray('gift_cards'); // the default gift cards

        $addedCards = $this->dataHelper->unserialize($this->getConfigData('added_gift_cards')); // the user-added gift cards
        if (is_array($addedCards) && ! empty($addedCards)) {
            foreach ($addedCards as $value) {
                if (empty($value)) {
                    continue;
                }

                $options[$value['code']] = $value['name'];
            }
        }

        return $options;
    }
}
