<?php
namespace Strativ\ProductTags\Api\Product;

interface TagRepositoryInterface
{
    /**
     * Get tags by product ID
     *
     * @param int $productId
     * @return array
     */
    public function getTagsByProductId($productId);
} 