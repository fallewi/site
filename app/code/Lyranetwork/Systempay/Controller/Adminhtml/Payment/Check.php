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
namespace Lyranetwork\Systempay\Controller\Adminhtml\Payment;

class Check extends \Magento\Backend\App\Action implements \Lyranetwork\Systempay\Api\CheckActionInterface
{
    /**
     * @var \Lyranetwork\Systempay\Controller\Processor\CheckProcessor
     */
    protected $checkProcessor;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $rawResultFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Lyranetwork\Systempay\Controller\Processor\CheckProcessor $checkProcessor
     * @param \Magento\Framework\Controller\Result\RawFactory $rawResultFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Lyranetwork\Systempay\Controller\Processor\CheckProcessor $checkProcessor,
        \Magento\Framework\Controller\Result\RawFactory $rawResultFactory
    ) {
        $this->checkProcessor = $checkProcessor;
        $this->rawResultFactory = $rawResultFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        return $this->checkProcessor->execute($this);
    }

    public function renderResponse($text)
    {
        $rawResult = $this->rawResultFactory->create();
        $rawResult->setContents($text);

        return $rawResult;
    }
}
