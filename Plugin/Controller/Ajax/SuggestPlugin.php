<?php

namespace Magebiz\ElasticAutoComplete\Plugin\Controller\Ajax;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SuggestPlugin
{
    const XML_PATH_ENABLE = 'magebiz_search_auto_complete/general/enable';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var QueryFactory
     */
    private $_queryFactory;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    public function __construct(
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->_storeManager = $storeManager;
        $this->_queryFactory = $queryFactory;
        $this->layerResolver = $layerResolver;
        $this->scopeConfig   = $scopeConfig;
    }

    /**
     * @param Magento\Search\Controller\Ajax\Suggest $subject
     * @param Magento\Framework\Controller\Result\Json $result
     * @return Magento\Framework\Controller\Result\Json
     */
    public function afterExecute(
        \Magento\Search\Controller\Ajax\Suggest $subject,
        Json $result
    ): \Magento\Framework\Controller\Result\Json {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE)) {
            return $result;
        }

        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
        $layerResolver = $this->layerResolver->get();

        /* @var $query \Magento\Search\Model\Query */
        $query = $this->_queryFactory->get();

        $storeId = $this->_storeManager->getStore()->getId();
        $query->setStoreId($storeId);

        $queryText = $query->getQueryText();

        if ($queryText) {
            $collection = $layerResolver->getProductCollection();
            $collection->setPageSize(8);
            $newResult = [];
            $additional = [];
            $additional['_escape'] = true;
            foreach ($collection->getItems() as $item) {
                $newResult[] = [
                    'title' => $item->getName(),
                    'url' => $item->getUrlModel()->getUrl($item, $additional),
                    'num_results' => 0
                ];
            }
            if ($newResult > 0) {
                $result->setData($newResult);
            }
        }

        return $result;
    }

}
