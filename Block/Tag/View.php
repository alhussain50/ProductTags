<?php
namespace Strativ\ProductTags\Block\Tag;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Request\Http;
use Strativ\ProductTags\Api\Tag\ProductRepositoryInterface;

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
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param Template\Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Http $request
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        Http $request,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_request = $request;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProducts()
    {
        $tag = $this->getTag();
        $collection = $this->_productCollectionFactory->create();

        // Get the product IDs that have the specified tag using repository
        $productIds = $this->productRepository->getProductIdsByTag($tag);

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
