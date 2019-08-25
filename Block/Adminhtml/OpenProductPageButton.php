<?php

namespace TheCactus117\ProductPageOpen\Block\Adminhtml;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\App\Area;

/**
 * Block Class OpenProductPageButton.
 * @package TheCactus117\ProductPageOpen\Block\Adminhtml
 */
class OpenProductPageButton extends Container
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Emulation
     */
    protected $emulation;

    /**
     * OpenProductPageButton constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Emulation $emulation
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Emulation $emulation,
        array $data = [])
    {
        $this->registry = $registry;
        $this->emulation = $emulation;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve currently edited product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Sub constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addOpenProductPageButton();
    }

    /**
     * Add open product page button to product page edition.
     */
    protected function addOpenProductPageButton()
    {
        $product = $this->getProduct();
        if ($product &&
            $product->isVisibleInCatalog() &&
            $product->isVisibleInSiteVisibility()) {
            $this->addButton(
                'open_product_page',
                [
                    'label' => __('Open product page'),
                    'on_click' => 'window.open("' . $this->getProductUrl($product) . '")',
                    'class' => 'view open_product_page'
                ]
            );
        }
    }

    /**
     * Get frontend product url.
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function getProductUrl($product)
    {
        $store = $this->_request->getParam('store');
        if (!$store) {
            $this->emulation->startEnvironmentEmulation(null, Area::AREA_FRONTEND, true);
            $productUrl = $product->loadByAttribute('entity_id', $product->getId())->getProductUrl();
            $this->emulation->stopEnvironmentEmulation();
            return $productUrl;
        }
        return $product->loadByAttribute('entity_id', $product->getId())
            ->setStoreId($store)
            ->getUrlInStore();
    }
}