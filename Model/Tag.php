<?php
namespace Strativ\ProductTags\Model;

use Magento\Framework\Model\AbstractModel;

class Tag extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Strativ\ProductTags\Model\ResourceModel\Tag::class);
    }
} 