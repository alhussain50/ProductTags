<?php
namespace Strativ\ProductTags\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\ResourceConnection;

class Tags extends Template
{
    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * @param Template\Context $context
     * @param Registry $registry
     * @param ResourceConnection $resource
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        ResourceConnection $resource,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_resource = $resource;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_registry->registry('product');
        }
        return $this->_product;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        $product = $this->getProduct();
        if (!$product) {
            return [];
        }

        $connection = $this->_resource->getConnection();
        $tableName = $this->_resource->getTableName('strativ_product_tags');

        $select = $connection->select()
            ->from($tableName, 'tag')
            ->where('product_id = ?', $product->getId());

        return $connection->fetchCol($select);
    }
} 