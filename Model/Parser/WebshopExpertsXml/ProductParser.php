<?php
/**
 * Contains the ProductParser class.
 *
 * @author      Sandor Teglas
 * @author      Hunor Kedves <hunor@artkonekt.com>
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @license     MIT
 * @since       2016-04-29
 * @version     2016-04-29
 */

namespace Konekt\SerpaSyncBundle\Model\Parser\WebshopExpertsXml;

use Konekt\SerpaSyncBundle\Model\AbstractParser;
use Konekt\SerpaSyncBundle\Model\DataSource\XmlDataSource;
use Konekt\SerpaSyncBundle\Model\Exception\FileNotFoundException;

/**
 * Loads product data from Products.xml, SpecialPrice.xml and Categories.xml files.
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
        return [
            'products' => $this->getProductsAsArray(),
            'specialPrices' => $this->getSpecialPricesAsArray(),
            'categories' => $this->getCategoriesAsArray()
        ];
    }

    /**
     * Parses the Products.xml file and returns its content as an array.
     *
     * @return array
     *
     * @throws FileNotFoundException
     */
    private function getProductsAsArray()
    {
        $products = XmlDataSource::create($this->inputFiles->getFile('Products.xml'))->getAsArray();
        if (!array_key_exists('Product', $products)) {
            return [];
        }

        return array_key_exists('@ID', $products['Product']) ? [$products['Product']] : $products['Product'];
    }

    /**
     * Parses the SpecialPrice.xml file and returns its content as an array.
     *
     * @return array
     *
     * @throws FileNotFoundException
     */
    private function getSpecialPricesAsArray()
    {
            $prices = XmlDataSource::create($this->inputFiles->getFile('SpecialPrice.xml'))->getAsArray();
        if (!array_key_exists('SpecialPrice', $prices)) {
            return [];
        }

        return array_key_exists('Product', $prices['SpecialPrice']) ? [$prices['SpecialPrice']] : $prices['SpecialPrice'];
    }

    /**
     * Parses the Categories.xml file and returns its content as an array.
     *
     * @return array
     *
     * @throws FileNotFoundException
     */
    private function getCategoriesAsArray()
    {

        $categories = XmlDataSource::create($this->inputFiles->getFile('Categories.xml'))->getAsArray();

        if (!array_key_exists('WebParameterProductCategories', $categories)) {
            return [];
        }

        return array_key_exists('ProductCategories', $categories['WebParameterProductCategories']['WebParameterProductCategory']) ?
            [$categories['WebParameterProductCategories']['WebParameterProductCategory']] : $categories['WebParameterProductCategories']['WebParameterProductCategory'];
    }

}
