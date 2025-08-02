<?php
namespace Strativ\ProductTags\Api\Tag;

interface ProductRepositoryInterface
{
    /**
     * Get product IDs by tag
     *
     * @param string $tag
     * @return array
     */
    public function getProductIdsByTag($tag);
} 