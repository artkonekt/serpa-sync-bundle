<?php
/**
 * Contains class Parser.
 *
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @author      Sandor Teglas
 * @license     MIT
 * @since       2016-02-29
 * @version     2016-02-29
 */

namespace Konekt\SerpaSyncBundle\Model;

/**
 * Loads data from sERPa files and merges them together wherever necessary.
 *
 * @package AppBundle\Model\Serpa
 */
class Parser
{

    private function __construct() {}

    /**
     * Creates a new instance of the class.
     *
     * @return  static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Returns sERPa products as arrays containing product data, price information and product attributes.
     *
     * @param   string   $productsFile     File containing products in sERPa format.
     * @param   string   $pricesFile       File containing prices in sERPa format.
     * @param   string   $attributesFile   File containing product attributes in sERPa format.
     *
     * @return  array
     */
    public function parseProducts($productsFile, $pricesFile, $attributesFile)
    {
        $products = DataSource::create($productsFile)->getDataRows();
        $prices = DataSource::create($pricesFile)->getDataRows();
        $attributes = DataSource::create($attributesFile)->getDataRows();

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