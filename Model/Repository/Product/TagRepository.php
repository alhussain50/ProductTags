<?php
namespace Strativ\ProductTags\Model\Repository\Product;

use Strativ\ProductTags\Api\Product\TagRepositoryInterface;
use Magento\Framework\App\ResourceConnection;

class TagRepository implements TagRepositoryInterface
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

    
    public function getTagsByProductId($productId)
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('strativ_product_tags');

        $select = $connection->select()
            ->from($tableName, 'tag')
            ->where('product_id = ?', $productId);

        return $connection->fetchCol($select);
    }
} 