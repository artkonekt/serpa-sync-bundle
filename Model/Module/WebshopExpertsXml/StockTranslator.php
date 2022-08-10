<?php
/**
 * Contains the StockTranslator class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-05-03
 * @since       2016-04-29
 */

namespace Konekt\SerpaSyncBundle\Model\Module\WebshopExpertsXml;

use Konekt\SerpaSyncBundle\Model\AbstractTranslator;
use Konekt\SyliusSyncBundle\Model\Remote\Stock\RemoteStockInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Stock\StockFactory;

class StockTranslator extends AbstractTranslator
{

    /**
     * Translates stocks from XML files to remote stock instances.
     *
     * @return  RemoteStockInterface[]
     */
    public function translate()
    {
        $res = [];
        
        $collected = $this->collect();
        foreach ($collected as $sku => $quantity) {
            $stock = $this->remoteFactories->getStockFactory()->create();
            $stock->setSku($sku);
            $stock->setQuantity($quantity);
            $res[] = $stock;
        }

        return $res;
    }

    /**
     * Collects and summarizes the available stocks for each product into an array with SKU's as keys.
     * A product might occure multiple times, once for each selling channel (shop, warehouse etc).
     *
     * @return   array
     */
    private function collect()
    {
        $res = [];
        foreach ($this->parser->getStocks() as $item) {
            $sku = $item['Product']['@Code'];
            $inTrade = isset($item['InTrade']) ? $item['InTrade'] : 0;
            $committed = isset($item['Committed']) ? $item['Committed'] : 0;
            if (isset($res[$sku])) {
                $res[$sku] += ($inTrade - $committed);
            } else {
                $res[$sku] = ($inTrade - $committed);
            }
        }

        return $res;
    }

}
