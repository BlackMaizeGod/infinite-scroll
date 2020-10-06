<?php

declare(strict_types=1);

namespace BMG\InfiniteScroll\Controller\Category;

class View extends \Magento\Catalog\Controller\Category\View
{
    public function execute()
    {
        $request = $this->getRequest();
        $params = $request->getParams();
        $page = parent::execute();

        if (count($params) <= 1) {
            return $page;
        }

        $layout = $page->getLayout();
        $productsListingBlock = $layout->getBlock('category.products.list');
        $productCollection = $productsListingBlock->getLoadedProductCollection();
        $shortProductsListingBlock = $layout->getBlock('infinite-scroll.category.products.list');
        $shortProductsListingBlock->setIsAjax(true);
        $shortProductsListingBlock->setLoadedProductCollection($productCollection);

        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $resultJson->setData([
            'products' => $shortProductsListingBlock->toHtml()
        ]);

        return $resultJson;
    }
}
