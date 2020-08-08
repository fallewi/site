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
 * @package   Ced_EbayMultiAccount
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\EbayMultiAccount\Block\Adminhtml\JobScheduler;

/**
 * Class MassSyncJob
 * @package Ced\EbayMultiAccount\Block\Adminhtml\JobScheduler
 */
class MassSyncJob extends \Magento\Backend\Block\Widget\Container
{
    /**
     * MassSyncJob constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->setTemplate('Ced_EbayMultiAccount::jobscheduler/masssyncjob.phtml');
    }

    /**
     * @return int
     */
    public function totalcount()
    {
        $totalChunk = $this->_backendSession->getFeedIdsForSync();
        return count($totalChunk);
    }
}
