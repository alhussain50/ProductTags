<?php
namespace Strativ\ProductTags\Plugin;

use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider;
use Strativ\ProductTags\Api\Product\TagRepositoryInterface;

class ProductDataProviderPlugin
{
    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * @param TagRepositoryInterface $tagRepository
     */
    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function afterGetData($subject, $result)
    {
        foreach ($result as $productId => &$data) {
            if (isset($data['product'])) {
                $tags = $this->tagRepository->getTagsByProductId($productId);
                $data['product']['strativ_tags'] = implode(',', $tags);
            }
        }
        return $result;
    }
}