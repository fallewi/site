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
namespace Lyranetwork\Systempay\Block\Adminhtml\System\Config\Form\Field;

/**
 * Custom renderer for the Systempay sub-module logo.
 */
class Logo extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     *
     * @var \Lyranetwork\Systempay\Helper\Data
     */
    protected $dataHelper;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Lyranetwork\Systempay\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Lyranetwork\Systempay\Helper\Data $dataHelper,
        $data = []
    ) {
        $this->dataHelper = $dataHelper;

        parent::__construct($context, $data);
    }

    /**
     * Set default image URL.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::render($element);

        $fileName = $element->getValue();
        if ($fileName && ! $this->dataHelper->isUploadFileImageExists($fileName)) {
            // default logo defined in the module
            $imgSrc = $this->getViewFileUrl('Lyranetwork_Systempay::images/' . $fileName);

            $html = preg_replace('#src="https?://[^"]+"#', 'src="' . $imgSrc . '"', $html);
        }

        return $html;
    }
}
