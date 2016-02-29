<?php
/**
 * Contains class InputFiles.
 *
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @author      Sandor Teglas
 * @license     MIT
 * @since       2016-02-29
 * @version     2016-02-29
 */

namespace Konekt\SerpaSyncBundle\Model;

/**
 * Represents the files exported by sERPa.
 *
 */
class InputFiles
{

    /** @var  string */
    private $productsFile;

    /** @var  string */
    private $pricesFile;

    /** @var  string */
    private $taxonomiesFile;

    /** @var  string */
    private $attributesFile;

    /** @var  string */
    private $stocksFile;

    /**
     * Creates a new instance of the class.
     *
     * @param   string   $productsFile      The file containing the products exported by sERPa.
     * @param   string   $pricesFile        The file containing the product prices exported by sERPa.
     * @param   string   $taxonomiesFile    The file containing the taxonomy hierarchy exported by sERPa.
     * @param   string   $attributesFile    The file containing product attributes exported by sERPa.
     * @param   string   $stocksFile        The file containing product stocks exported by sERPa.
     *
     * @return  static
     */
    public static function create($productsFile, $pricesFile, $taxonomiesFile, $attributesFile, $stocksFile)
    {
        $instance = new static();
        $instance->productsFile = $productsFile;
        $instance->pricesFile = $pricesFile;
        $instance->taxonomiesFile = $taxonomiesFile;
        $instance->attributesFile = $attributesFile;
        $instance->stocksFile = $stocksFile;

        return $instance;
    }

    /**
     * Returns the file containing products exported by sERPa.
     *
     * @return string
     */
    public function getProductsFile()
    {
        return $this->productsFile;
    }

    /**
     * Returns the file containing product pricess exported by sERPa.
     *
     * @return string
     */
    public function getPricesFile()
    {
        return $this->pricesFile;
    }

    /**
     * Returns the file containing the taxonomy hierarchy exported by sERPa.
     *
     * @return string
     */
    public function getTaxonomiesFile()
    {
        return $this->taxonomiesFile;
    }

    /**
     * Returns the file containing product attributes exported by sERPa.
     *
     * @return string
     */
    public function getAttributesFile()
    {
        return $this->attributesFile;
    }

    /**
     * Returns the file containing product stocks exported by sERPa.
     *
     * @return string
     */
    public function getStocksFile()
    {
        return $this->stocksFile;
    }

}
