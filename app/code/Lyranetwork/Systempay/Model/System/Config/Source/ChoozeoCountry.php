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
namespace Lyranetwork\Systempay\Model\System\Config\Source;

class ChoozeoCountry implements \Magento\Framework\Option\ArrayInterface
{
    // France and DOM-TOM
    public static $availableCountries = ['FR', 'GP', 'MQ', 'GF', 'RE', 'YT'];

    /**
     * Locale model
     *
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $localeLists;

    /**
     *
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     */
    public function __construct(\Magento\Framework\Locale\ListsInterface $localeLists)
    {
        $this->localeLists = $localeLists;
    }

    public function toOptionArray()
    {
        $result = [];

        foreach (self::$availableCountries as $code) {
            $name = (string) $this->localeLists->getCountryTranslation($code);
            if (empty($name)) {
                $name = $code;
            }

            $result[] = [
                'value' => $code,
                'label' => $name
            ];
        }

        return $result;
    }
}
