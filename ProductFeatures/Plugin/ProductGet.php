<?php namespace Bdcrops\ProductFeatures\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;

class ProductGet
{
    protected $productExtensionFactory;
    protected $productFactory;

    public function __construct(
        \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->productFactory = $productFactory;
        $this->productExtensionFactory = $productExtensionFactory;
    }

    public function aroundGetById(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Closure $proceed,
        $customerId
    )
    {
        /** @var ProductInterface $product */
        $product = $proceed($customerId);

	// If extension attribute is already set, return early.
        if ($product->getExtensionAttributes() && $product->getExtensionAttributes()->getFeatures()) {
            return $product;
        }

        // In the event that extension attribute class has not be instansiated yet.
        // in this event, we create it ourselves.
        if (!$product->getExtensionAttributes()) {
            $productExtension = $this->productExtensionFactory->create();
            $product->setExtensionAttributes($productExtension);
        }

        // Fetch the raw product model (I have not found a better way), and set the data onto our attribute.
        $productModel = $this->productFactory->create()->load($product->getId());
        $product->getExtensionAttributes()
            ->setFeatures($productModel->getData('features'));

        return $product;
    }
}
