<?php
/**
 * Contains the ProductParser class.
 *
 * @author      Sandor Teglas
 * @author      Hunor Kedves <hunor@artkonekt.com>
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @license     MIT
 * @since       2016-04-08
 * @version     2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\AbstractParser;
use Konekt\SerpaSyncBundle\Model\DataSource\TxtDataSource;

/**
 * Loads product data from Termek.txt, TermekAR.txt and TermekKategoria.txt files.
 *
 * Every row will contain 3 keys:
 * - product: product data
 * - price: price info
 * - attributes: product attributes
 *
 */
class ProductParser extends AbstractParser
{

    /**
     * @inheritdoc
     *
     */
    public function getAsArray()
    {
        $products = TxtDataSource::create($this->inputFiles->getFile('Termek.txt'))->getAsArray();
        $prices = TxtDataSource::create($this->inputFiles->getFile('TermekAR.txt'))->getAsArray();
        $attributes = TxtDataSource::create($this->inputFiles->getFile('TermekKategoria.txt'))->getAsArray();

        $res = [];
        foreach ($products as $product) {
            $sku = $product['TermekKod'];
            $priceInfo = $this->lookupPriceBySku($sku, $prices);
            $attributeInfo = $this->lookupAttributesBySku($sku, $attributes);
            $res[] = ['product' => $product, 'price' => $priceInfo, 'attributes' => $attributeInfo];
        }

        return $res;
    }

    /**
     * Looks up a price information by a product SKU.
     *
     * @param   string       $sku
     * @param   array        $prices   The prices array to search in.
     *
     * @return  null|array
     */
    private function lookupPriceBySku($sku, array $prices)
    {
        foreach($prices as $price) {
            if ($sku == $price['TermekKod']) {

                return $price;
            }
        }

        return null;
    }

    /**
     * Looks up the attribute os a product by its SKU.
     *
     * @param   string       $sku
     * @param   array        $attributes   The prices array to search in.
     *
     * @return  null|array
     */
    private function lookupAttributesBySku($sku, array $attributes)
    {
        foreach($attributes as $attribute) {
            if ($sku == $attribute['TermekKod']) {

                return $attribute;
            }
        }

        return null;
    }

}
