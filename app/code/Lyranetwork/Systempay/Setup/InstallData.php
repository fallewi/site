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
namespace Lyranetwork\Systempay\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     *
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     *
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        // prepare database for install
        $setup->startSetup();

        /**
         * Add Systempay identifier attribute to the customer entity.
         */
        $customerInstaller = $this->customerSetupFactory->create(
            [
                'resourceName' => 'customer_setup',
                'setup' => $setup
            ]
        );

        $customerInstaller->addAttribute(
            'customer',
            'systempay_identifier',
            [
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Systempay identifier',

                'global' => 1,
                'visible' => 0,
                'searchable' => 0,
                'filterable' => 0,
                'comparable' => 0,
                'visible_on_front' => 0,
                'required' => 0,
                'user_defined' => 0,
                'default' => '',
                'source' => null
            ]
        );

        $entityTypeId = $customerInstaller->getEntityTypeId('customer');
        $attributeSetId = $customerInstaller->getDefaultAttributeSetId($entityTypeId);
        $attributeGroupId = $customerInstaller->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
        $customerInstaller->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            'systempay_identifier',
            '999' /* sort_order */
        );

        $connection = $setup->getConnection();

        $select = $connection->select()
            ->from($setup->getTable('sales_order_status'), 'COUNT(*)')
            ->where('status = ?', 'systempay_to_validate');
        $count = (int) $connection->fetchOne($select);

        if ($count == 0) {
            /**
             * Add new Systempay statuses.
             */
            $connection->insert(
                $setup->getTable('sales_order_status'),
                [
                    'status' => 'systempay_to_validate',
                    'label' => 'To validate payment'
                ]
            );

            $connection->insert(
                $setup->getTable('sales_order_status_state'),
                [
                    'status' => 'systempay_to_validate',
                    'state' => 'payment_review',
                    'is_default' => 0
                ]
            );
        }

        /**
         * Empty log file.
         */
        $logFileName = BP . '/var/log/systempay.log';
        if (file_exists($logFileName)) {
            $f = fopen($logFileName, 'w'); // just for emptying module log file
            fclose($f);
        }

        // prepare database after install
        $setup->endSetup();
    }
}
