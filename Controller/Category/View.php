<?php

declare(strict_types=1);

namespace BMG\InfiniteScroll\Controller\Category;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Category\Attribute\LayoutUpdateManager;
use Magento\Catalog\Model\Design;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer;
use Magento\Catalog\Model\Session;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class View extends \Magento\Catalog\Controller\Category\View
{
    /**
     * @var \BMG\InfiniteScroll\Helper\ProductCollection\Filter
     */
    private $productCollectionFilter;

    /**
     * View constructor.
     * @param Context $context
     * @param Design $catalogDesign
     * @param Session $catalogSession
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ToolbarMemorizer|null $toolbarMemorizer
     * @param LayoutUpdateManager|null $layoutUpdateManager
     * @param CategoryHelper|null $categoryHelper
     * @param LoggerInterface|null $logger
     * @param \BMG\InfiniteScroll\Helper\ProductCollection\Filter $productCollectionFilter
     */
    public function __construct(
        Context $context,
        Design $catalogDesign,
        Session $catalogSession,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        CategoryUrlPathGenerator $categoryUrlPathGenerator,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        ToolbarMemorizer $toolbarMemorizer = null,
        ?LayoutUpdateManager $layoutUpdateManager = null,
        CategoryHelper $categoryHelper = null,
        LoggerInterface $logger = null,
        \BMG\InfiniteScroll\Helper\ProductCollection\Filter $productCollectionFilter
    ) {
        $this->productCollectionFilter = $productCollectionFilter;

        parent::__construct(
            $context,
            $catalogDesign,
            $catalogSession,
            $coreRegistry,
            $storeManager,
            $categoryUrlPathGenerator,
            $resultPageFactory,
            $resultForwardFactory,
            $layerResolver,
            $categoryRepository,
            $toolbarMemorizer,
            $layoutUpdateManager,
            $categoryHelper,
            $logger
        );
    }

    public function execute()
    {
        // @TODO change constructor annotation
        $request = $this->getRequest();

        if (!$request->isAjax()) {
            return parent::execute();
        }

        $page = $this->resultPageFactory->create();

        /**@var \Magento\Catalog\Block\Product\ListProduct $productsListingBlock */
        $productsListingBlock = $page->getLayout()->getBlock('infinite-scroll.category.products.list');

        //@TODO handle view mode and per page changed

        $productCollection = $this->productCollectionFilter->createFilteredProductCollection($request);
        $productsListingBlock->setData('product_collection', $productCollection);

        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $resultJson->setData([
            'products' => $productsListingBlock->toHtml()
        ]);

        return $resultJson;
    }
}
