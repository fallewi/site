<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Iksanika\Productmanage\Model\ResourceModel\Product;
use Magento\Framework\DB\Select;
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Get SQL for get record count
     *
     * @param null $select
     * @param bool $resetLeftJoins
     * @return \Magento\Framework\DB\Select
     */
    /*public function _getSelectCountSql($select = null, $resetLeftJoins = true)
    {
        $this->_renderFilters();
        $countSelect = is_null($select) ? $this->_getClearSelect() : $this->_buildClearSelect($select);
        if(count($this->getSelect()->getPart(\Zend_Db_Select::GROUP)) > 0)
        {
            $countSelect->reset(\Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(\Zend_Db_Select::GROUP);
            $countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
        } else {
            $countSelect->columns('COUNT(*)');
        }
        return $countSelect;
    }*/
	
	
	public function _getSelectCountSql(?Select $select = null, $resetLeftJoins = true)
{
    $this->_renderFilters();
    $countSelect = is_null($select) ? $this->_getClearSelect() : $this->_buildClearSelect($select);
    if(count($this->getSelect()->getPart(\Zend_Db_Select::GROUP)) > 0)
    {
        $countSelect->reset(\Zend_Db_Select::GROUP);
        $countSelect->distinct(true);
        $group = $this->getSelect()->getPart(\Zend_Db_Select::GROUP);
        $countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
    } else {
        $countSelect->columns('COUNT(*)');
    }
    return $countSelect;
}
}
