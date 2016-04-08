<?php
/**
* Contains the WebshopExpertsAdapter class.
*
* @author      Sandor Teglas
* @author      Hunor Kedves <hunor@artkonekt.com>
* @copyright   Copyright (c) 2016 Storm Storez Srl-d
* @license     Proprietary
* @version     2016-04-07
* @since       2016-03-02
*/

namespace Konekt\SerpaSyncBundle\Model\Adapter;

use Konekt\SerpaSyncBundle\Model\AbstractAdapter;
use Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\ProductParser;
use Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\StockParser;
use Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\TaxonomyParser;
use Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts\ImageTranslator;
use Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts\ProductTranslator;
use Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts\StockTranslator;
use Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts\TaxonomyTranslator;

/**
 * Translates products, taxonomies and stocks from files exported by the WebshopExperts module of sERPa into Sylius Sync Bundle remote models.
 *
 */
class WebshopExpertsAdapter extends AbstractAdapter
{

    /**
     * @inheritdoc
     */
    public function getRequiredFiles()
    {
        return ['Termek.txt', 'TermekAR.txt', 'TermekFa.txt', 'TermekKategoria.txt', 'TermekKeszlet.txt', 'Kepek.txt'];
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
