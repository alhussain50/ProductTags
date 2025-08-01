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
        $collection->addAttributeToSelect('*');

        $collection->getSelect()->join(
            ['spt' => $collection->getTable('strativ_product_tags')],
            'e.entity_id = spt.product_id',
            []
        )->where('spt.tag = ?', $tag);

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
