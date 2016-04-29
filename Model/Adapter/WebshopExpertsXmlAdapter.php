<?php
/**
* Contains the WebshopExpertsXmlAdapter class.
*
* @author      Sandor Teglas
* @copyright   Copyright (c) 2016 Storm Storez Srl-d
* @license     Proprietary
* @version     2016-04-29
* @since       2016-04-29
*/

namespace Konekt\SerpaSyncBundle\Model\Adapter;

use Konekt\SerpaSyncBundle\Model\AbstractAdapter;
use Konekt\SerpaSyncBundle\Model\Parser\WebshopExpertsXml\ProductParser;
use Konekt\SerpaSyncBundle\Model\Parser\WebshopExpertsXml\StockParser;
use Konekt\SerpaSyncBundle\Model\Parser\WebshopExpertsXml\TaxonomyParser;
use Konekt\SerpaSyncBundle\Model\Translator\WebshopExpertsXml\ProductTranslator;
use Konekt\SerpaSyncBundle\Model\Translator\WebshopExpertsXml\StockTranslator;
use Konekt\SerpaSyncBundle\Model\Translator\WebshopExpertsXml\TaxonomyTranslator;

/**
 * Translates products, taxonomies and stocks from files exported by the WebshopExperts XML module of sERPa into Sylius Sync Bundle remote models.
 *
 */
class WebshopExpertsXmlAdapter extends AbstractAdapter
{

    /**
     * @inheritdoc
     */
    public function getRequiredFiles()
    {
        return ['Products.xml', 'SpecialPrice.xml', 'ProductTree.xml', 'Categories.xml', 'Stock.xml'];
    }

    /**
     * @inheritdoc
     */
    public function getProductParser()
    {
        return ProductParser::create($this->getInputFiles());
    }

    /**
     * @inheritdoc
     */
    public function getProductTranslator()
    {
        return ProductTranslator::create($this->getRemoteFactories(), 'hu_HU');
    }

    /**
     * @inheritdoc
     */
    public function getTaxonomyParser()
    {
        return TaxonomyParser::create($this->getInputFiles());
    }

    /**
     * @inheritdoc
     */
    public function getTaxonomyTranslator()
    {
        return TaxonomyTranslator::create($this->getRemoteFactories(), 'hu_HU');
    }

    /**
     * @inheritdoc
     */
    public function getStockParser()
    {
        return StockParser::create($this->getInputFiles());
    }

    /**
     * @inheritdoc
     */
    public function getStockTranslator()
    {
        return StockTranslator::create($this->getRemoteFactories(), 'hu_HU');
    }
}
