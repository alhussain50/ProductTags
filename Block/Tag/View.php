<?php
namespace Strativ\ProductTags\Block\Tag;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Request\Http;

class View extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var Http
     */
    protected $_request;

    /**
     * @param Template\Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        Http $request,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_request = $request;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProducts()
    {
        $tag = $this->getTag();

        $collection = $this->_productCollectionFactory->create();

        // First, get the product IDs that have the specified tag.
        $connection = $collection->getConnection();
        $select = $connection->select()
            ->from($collection->getTable('strativ_product_tags'), 'product_id')
            ->where('tag = ?', $tag);
        $productIds = $connection->fetchCol($select);

        // If no products have the tag, we need to return an empty collection.
        // Filtering by an empty 'in' array is handled correctly by Magento.
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('entity_id', ['in' => $productIds]);

        return $collection;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->_request->getParam('tag');
    }
}
