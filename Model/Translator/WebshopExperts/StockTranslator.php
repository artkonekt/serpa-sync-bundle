<?php
/**
 * Contains the StockTranslator class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-02
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\AbstractTranslator;
use Konekt\SyliusSyncBundle\Model\Remote\Stock\RemoteStockInterface;

class StockTranslator extends AbstractTranslator
{

    /**
     * Translates array data to a remote stock instance.
     *
     * @param   array $data
     *
     * @return  RemoteStockInterface
     */
    public function translate(array $data)
    {
        /** @var RemoteStockInterface $stock */
        $stock = $this->remoteFactories->getStockFactory()->create();
        $stock->setSku($data['TermekKod']);
        $stock->setQuantity($data['Keszlet']);

        return $stock;
    }

}
