<?php
namespace Strativ\ProductTags\Plugin;

use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider;
use Magento\Framework\App\ResourceConnection;

class ProductDataProviderPlugin
{
    protected $resource;

    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    public function afterGetData($subject, $result)
    {
        foreach ($result as $productId => &$data) {
            if (isset($data['product'])) {
                $connection = $this->resource->getConnection();
                $table = $this->resource->getTableName('strativ_product_tags');
                $select = $connection->select()
                    ->from($table, ['tag'])
                    ->where('product_id = ?', $productId);
                $tags = $connection->fetchCol($select);
                $data['product']['strativ_tags'] = implode(',', $tags);
            }
        }
        return $result;
    }
}