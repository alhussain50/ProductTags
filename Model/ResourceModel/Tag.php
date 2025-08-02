<?php
namespace Strativ\ProductTags\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Tag extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('strativ_product_tags', 'id');
    }
} 