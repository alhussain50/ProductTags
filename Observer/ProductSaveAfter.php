<?php
namespace Strativ\ProductTags\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Message\ManagerInterface;

class ProductSaveAfter implements ObserverInterface
{
    protected $resource;
    protected $messageManager;

    public function __construct(
        ResourceConnection $resource,
        ManagerInterface $messageManager
    ) {
        $this->resource = $resource;
        $this->messageManager = $messageManager;
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $tags = $product->getData('strativ_tags');

        if ($tags !== null) {
            $connection = $this->resource->getConnection();
            $table = $this->resource->getTableName('strativ_product_tags');

            // Remove old tags for this product
            $connection->delete($table, ['product_id = ?' => $product->getId()]);

            // Process and validate tags
            $result = $this->validateAndSanitizeTags($tags);
            $validTags = $result['valid'];
            $rejectedTags = $result['rejected'];
            
            // Show messages for rejected tags
            if (!empty($rejectedTags)) {
                foreach ($rejectedTags as $rejectedTag => $reason) {
                    $this->messageManager->addWarningMessage(
                        "Tag '{$rejectedTag}' was rejected: {$reason}"
                    );
                }
            }
            
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
     * Basic tag validation with feedback
     *
     * @param string $tags
     * @return array
     */
    private function validateAndSanitizeTags($tags)
    {
        $tagsArray = explode(',', $tags);
        $validTags = [];
        $rejectedTags = [];

        foreach ($tagsArray as $originalTag) {
            $tag = trim(strip_tags($originalTag));
            
            if ($tag === '') {
                if (trim($originalTag) !== '') {
                    $rejectedTags[trim($originalTag)] = 'Empty after removing HTML tags';
                }
                continue;
            }
            
            if (strlen($tag) > 10) {
                $rejectedTags[$tag] = 'Too long (max 10 characters)';
                continue;
            }
            
            if (!preg_match('/^[a-zA-Z0-9\s\-_]+$/', $tag)) {
                $rejectedTags[$tag] = 'Contains invalid characters (only letters, numbers, spaces, hyphens, underscores allowed)';
                continue;
            }
            
            if (preg_match('/^[0-9\s\-_]+$/', $tag)) {
                $rejectedTags[$tag] = 'Must contain at least one letter';
                continue;
            }
            
            $validTags[] = $tag;
        }

        return [
            'valid' => array_unique($validTags),
            'rejected' => $rejectedTags
        ];
    }
}