<?php
namespace Strativ\ProductTags\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Strativ\ProductTags\Api\Product\TagRepositoryInterface;

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
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * @param Template\Context $context
     * @param Registry $registry
     * @param TagRepositoryInterface $tagRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        TagRepositoryInterface $tagRepository,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->tagRepository = $tagRepository;
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

        return $this->tagRepository->getTagsByProductId($product->getId());
    }
} 