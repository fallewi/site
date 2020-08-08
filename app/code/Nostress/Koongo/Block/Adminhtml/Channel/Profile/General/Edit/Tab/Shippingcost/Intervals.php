<?php
/**
 * Magento Module developed by NoStress Commerce
 *
 * NOTICE OF LICENSE
 *
 * This program is licensed under the Koongo software licence (by NoStress Commerce). 
 * With the purchase, download of the software or the installation of the software 
 * in your application you accept the licence agreement. The allowed usage is outlined in the
 * Koongo software licence which can be found under https://docs.koongo.com/display/koongo/License+Conditions
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at https://store.koongo.com/.
 *
 * See the Koongo software licence agreement for more details.
 * @copyright Copyright (c) 2017 NoStress Commerce (http://www.nostresscommerce.cz, http://www.koongo.com/)
 *
 */

/**
 * Shipping cost intervals add/edit form options tab
 *
 * @category Nostress
 * @package Nostress_Koongo
 *
 */
namespace Nostress\Koongo\Block\Adminhtml\Channel\Profile\General\Edit\Tab\Shippingcost;

use Nostress\Koongo\Model\Channel\Profile;

class Intervals extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var string
     */
    protected $_template = 'Nostress_Koongo::koongo/channel/profile/general/shipping/intervals.phtml';

    /**
     * @var \Magento\Framework\Validator\UniversalFactory $universalFactory
     */
    protected $_universalFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->_universalFactory = $universalFactory;
    }

    
    public function getShippingConfigPath()
    {
    	return Profile::CONFIG_FEED.'['.Profile::CONFIG_COMMON.']'.'['.Profile::CONFIG_SHIPPING.']';
    }
    
	public function getCostIntervals()
	{
		$model = $this->_registry->registry('koongo_channel_profile');
		$config = $model->getConfigItem(Profile::CONFIG_FEED,true,Profile::CONFIG_COMMON);
		$intervals = [];
		
		if(isset($config['shipping']['cost_setup']))
			$intervals = $config['shipping']['cost_setup'];
		
		$values = [];
		$index = 0;
		foreach($intervals as $key => $value)
		{
			$value["id"] = "option_".$index;
			$value["sort_order"] = $index;
			$values[] = $value;
			$index++;
		}
		return $values;
	}
	
}
