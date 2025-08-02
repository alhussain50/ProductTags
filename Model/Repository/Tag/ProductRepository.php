<?php
namespace Strativ\ProductTags\Model\Repository\Tag;

use Strativ\ProductTags\Api\Tag\ProductRepositoryInterface;
use Magento\Framework\App\ResourceConnection;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    public function getProductIdsByTag($tag)
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('strativ_product_tags');

        $select = $connection->select()
            ->from($tableName, 'product_id')
            ->where('tag = ?', $tag);

        return $connection->fetchCol($select);
    }
} 