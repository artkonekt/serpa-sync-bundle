<?php
/**
 * Contains the StockParser class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @license     MIT
 * @since       2016-04-29
 * @version     2016-04-29
 */

namespace Konekt\SerpaSyncBundle\Model\Parser\WebshopExpertsXml;

use Konekt\SerpaSyncBundle\Model\AbstractParser;

/**
 * Loads stock data from the Stock.xml file into an array.
 *
 */
class StockParser extends AbstractParser
{

    /**
     * @inheritdoc
     *
     */
    public function getAsArray()
    {
        $stocks = XmlDataSource::create($this->inputFiles->getFile('Stock.xml'))->getAsArray();

        if (!array_key_exists('Stock', $stocks)) {
            return [];
        }

        return array_key_exists('Product', $stocks['Stock']) ? [$stocks['Stock']] : $stocks['Stock'];
    }

}
