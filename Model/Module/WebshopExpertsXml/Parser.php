<?php
/**
 * Contains the Parser class.
 *
 * @author      Sandor Teglas <sandor@artkonekt.com>
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     Proprietary
 * @version     2016-05-03
 * @since       2016-05-02
 */

namespace Konekt\SerpaSyncBundle\Model\Module\WebshopExpertsXml;

use Konekt\SerpaSyncBundle\Model\DataSource\XmlDataSource;
use Konekt\SerpaSyncBundle\Model\Parser as BaseParser;

/**
 * Parses and loads data from XML files exported by the WebshopExperts XML module of Serpa.
 *
 */
class Parser extends BaseParser
{

    private $cache = [];

    /**
     * Loads taxonomies from the ProductTree.xml file.
     *
     * @return array
     */
    public function getProductTree()
    {
        if (array_key_exists('productTree', $this->cache)) {
            return $this->cache['productTree'];
        }

        $res = [];
        $data = XmlDataSource::create($this->inputFiles->getFile('ProductTree.xml'))->getAsArray();
        if (array_key_exists('Item', $data)) {
            $res = array_key_exists('@ID', $data['Item']) ? [$data['Item']] : $data['Item'];
        }

        $this->cache['productTree'] = $res;

        return $res;
    }

    /**
     * Loads products from the Products.xml file.
     *
     * @return array
     */
    public function getProducts()
    {
        if (array_key_exists('products', $this->cache)) {
            return $this->cache['products'];
        }

        $res = [];
        $data = XmlDataSource::create($this->inputFiles->getFile('Products.xml'))->getAsArray();
        if (array_key_exists('Product', $data)) {
            $res = array_key_exists('@ID', $data['Product']) ? [$data['Product']] : $data['Product'];
        }

        $this->cache['products'] = $res;

        return $res;
    }

    /**
     * Loads promotional prices from the SpecialPrice.xml file.
     *
     * @return array
     */
    public function getSpecialPrices()
    {
        if (array_key_exists('specialPrices', $this->cache)) {
            return $this->cache['specialPrices'];
        }

        $res = [];
        $data = XmlDataSource::create($this->inputFiles->getFile('SpecialPrice.xml'))->getAsArray();
        if (array_key_exists('SpecialPrice', $data)) {
            $res = array_key_exists('Product', $data['SpecialPrice']) ? [$data['SpecialPrice']] : $data['SpecialPrice'];
        }

        $this->cache['specialPrices'] = $res;

        return $res;
    }

    /**
     * Loads product attribute values from the Categories.xml file.
     *
     * @return array
     */
    public function getCategories()
    {
        if (array_key_exists('categories', $this->cache)) {
            return $this->cache['categories'];
        }

        $res = [];
        $data = XmlDataSource::create($this->inputFiles->getFile('Categories.xml'))->getAsArray();
        if (array_key_exists('WebParameterProductCategories', $data)) {
            if (array_key_exists('ProductCategories', $data['WebParameterProductCategories']['WebParameterProductCategory'])) {
                $res = [$data['WebParameterProductCategories']['WebParameterProductCategory']];
            } else {
                $res = $data['WebParameterProductCategories']['WebParameterProductCategory'];
            }
        }

        $this->cache['categories'] = $res;

        return $res;
    }

    /**
     * Loads stocks from the Stock.xml file.
     *
     * @return array
     */
    public function getStocks()
    {
        if (array_key_exists('stocks', $this->cache)) {
            return $this->cache['stocks'];
        }

        $res = [];
        $data = XmlDataSource::create($this->inputFiles->getFile('Stock.xml'))->getAsArray();
        if (array_key_exists('Stock', $data)) {
            $res = array_key_exists('Product', $data['Stock']) ? [$data['Stock']] : $data['Stock'];
        }

        $this->cache['stocks'] = $res;

        return $res;
    }

}
