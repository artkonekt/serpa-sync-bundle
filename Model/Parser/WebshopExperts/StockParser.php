<?php
/**
 * Contains the StockParser class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @license     MIT
 * @since       2016-03-02
 * @version     2016-03-02
 */

namespace Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\AbstractParser;
use Konekt\SerpaSyncBundle\Model\DataSource\TxtDataSource;

/**
 * Loads stock data from the TermekKeszlet.txt file into an array with each element being another array having the keys
 * TermekKod (SKU) and Keszlet (quantity).
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
        // No processing or mergin is required, the stock list is a flat one.
        return TxtDataSource::create($this->inputFiles->getFile('TermekKeszlet.txt'))->getAsArray();
    }

}
