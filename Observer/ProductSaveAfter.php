<?php
namespace Strativ\ProductTags\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\ResourceConnection;

class ProductSaveAfter implements ObserverInterface
{
    protected $resource;

    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $tags = $product->getData('strativ_tags'); // Field name from the form

        if ($tags !== null) {
            $connection = $this->resource->getConnection();
            $table = $this->resource->getTableName('strativ_product_tags');

            // Remove old tags for this product
            $connection->delete($table, ['product_id = ?' => $product->getId()]);

            // Process and validate tags
            $validTags = $this->validateAndSanitizeTags($tags);
            
            // Insert valid tags
            foreach ($validTags as $tag) {
                $connection->insert($table, [
                    'product_id' => $product->getId(),
                    'tag' => $tag
                ]);
            }
        }
    }

    /**
     * Basic tag validation
     *
     * @param string $tags
     * @return array
     */
    private function validateAndSanitizeTags($tags)
    {
        $tagsArray = explode(',', $tags);
        $validTags = [];

        foreach ($tagsArray as $tag) {
            $tag = trim(strip_tags($tag)); // Trim and remove HTML
            
            if ($tag !== '' && strlen($tag) <= 50) {
                $validTags[] = $tag;
            }
        }

        return array_unique($validTags);
    }
}