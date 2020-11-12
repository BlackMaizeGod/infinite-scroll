<?php

declare(strict_types=1);

namespace BMG\InfiniteScroll\Helper\ProductCollection;

class Filter extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
     */
    private $toolbar;

    /**
     * Filter constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->toolbar = $toolbar;

        parent::__construct($context);
    }

    public function createFilteredProductCollection(
        \Magento\Framework\App\RequestInterface $request
    ) {
        //@TODO check why the product got by ajax way has 28 values in the data array, but not ajax way product has 30 values
        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->addCategoriesFilter(['eq' => $request->getParam('categoryId')])
            ->addAttributeToSelect('*')
            ->setOrder($request->getParam('order') ?? $this->toolbar->getCurrentOrder())
            ->setCurPage($request->getParam('p') ?? $this->toolbar->getCurrentPage())
            ->setPageSize($request->getParam('perPage') ?? $this->toolbar->getDefaultPerPageValue())
        ;

        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $productCollection]
        );

        return $productCollection;
    }
}
