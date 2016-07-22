<?php
/**
 * Contains the AbstractAdapter class
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-05-03
 * @since       2016-03-02
 */

namespace Konekt\SerpaSyncBundle\Model;

use Konekt\SerpaSyncBundle\Model\Exception\InvalidImagesFolder;
use Konekt\SerpaSyncBundle\Model\Exception\MissingInputFile;
use Konekt\SerpaSyncBundle\Model\Module\WebshopExpertsXml\ProductTranslator;
use Konekt\SerpaSyncBundle\Model\Module\WebshopExpertsXml\StockTranslator;
use Konekt\SerpaSyncBundle\Model\Module\WebshopExpertsXml\TaxonomyTranslator;
use Konekt\SyliusSyncBundle\Model\Remote\Adapter\Konekt;
use Konekt\SyliusSyncBundle\Model\Remote\Adapter\RemoteAdapterInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Image\ImageFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Product\ProductFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Stock\RemoteStockInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Stock\StockFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonomyInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonomyFactory;

/**
 * Translates products, taxonomies and stocks from files exported by sERPa into Sylius Sync Bundle remote models.
 *
 */
abstract class AbstractAdapter implements RemoteAdapterInterface
{

    /** @var  RemoteFactories */
    private $remoteFactories;

    /** @var  InputFiles */
    private $inputFiles;

    /** @var  string */
    private $imagesFolder;

    /** @var  string */
    private $locale;

    /** @var  string */
    private $internetPriceKey;

    /** @var  string */
    private $storePriceKey;

    /** @var  Parser */
    private $parser;

    /** @var array */
    private $cache = [];
    
    private function __construct() {}

    /**
     * Creates a new instance of the class.
     *
     * @param   ProductFactory    $productFactory    Factory service used to create remote Sylius Sync Bundle products.
     * @param   ImageFactory      $imageFactory      Factory service used to create remote Sylius Sync Bundle images.
     * @param   TaxonomyFactory   $taxonomyFactory   Factory service used to create remote Sylius Sync Bundle taxonomies.
     * @param   TaxonFactory      $taxonFactory      Factory service used to create remote Sylius Sync Bundle taxons.
     * @param   StockFactory      $stockFactory      Factory service used to create remote Sylius Sync Bundle stocks.
     * @param   array             $inputFiles        The list of files exported by sERPa.
     * @param   string            $imagesFolder      The folder containing product images to import.
     * @param   string            $locale            The locale of the translation into which to copy the translations.
     *
     * @return  static
     *
     * @throws MissingInputFile                      When at least one required input file is missing.
     * @throws InvalidImagesFolder                   When the folder containing the product images either does not exist or it is not a folder.
     */
    public static function create(ProductFactory $productFactory, ImageFactory $imageFactory, TaxonomyFactory $taxonomyFactory,
                                  TaxonFactory $taxonFactory, StockFactory $stockFactory, array $inputFiles, $imagesFolder, $locale,
                                  $internetPriceKey, $storePriceKey)
    {
        $instance = new static();

        // All required input file must be present
        $inputFiles = InputFiles::create($inputFiles);
        foreach ($instance->getRequiredFiles() as $file) {
            if (!$inputFiles->fileExists($file)) {
                throw new MissingInputFile("Input file '$file' is required but it is missing.");
            }
        }

        $instance->inputFiles = $inputFiles;

        if (!file_exists($imagesFolder) || !is_dir($imagesFolder)) {
            throw new InvalidImagesFolder("The images folder '$imagesFolder' either does not exist or it is not a folder.");
        }

        $instance->imagesFolder = $imagesFolder;

        $instance->remoteFactories = RemoteFactories::create($productFactory, $imageFactory, $taxonomyFactory, $taxonFactory, $stockFactory);
        $instance->locale = $locale;

        $instance->internetPriceKey = $internetPriceKey;
        $instance->storePriceKey = $storePriceKey;

        return $instance;
    }

    /**
     * Returns the list of files (only file names) exported by sERPa and required by the adapter.
     *
     * @return array
     */
    abstract public function getRequiredFiles();

    /**
     * Returns the class of the parser able to load data from Serpa files.
     *
     * @return string
     */
    abstract public function getParserClass();

    /**
     * Loads the products from sERPa exported files as Sylius Sync Bundle remote model product instances.
     *
     * @return RemoteProductInterface[]
     */
    public function fetchProducts()
    {
        if (!array_key_exists('products', $this->cache)) {
            $translator = $this->createTranslatorInstance(ProductTranslator::class);
            $translator->internetPriceKey = $this->internetPriceKey;
            $translator->storePriceKey = $this->storePriceKey;
            $res = $translator->translate();
            $this->cache['products'] = $res;
        }

        return $this->cache['products'];
    }

    /**
     * Loads the taxonomies from sERPa exported files as Sylius Sync Bundle remote model taxonomy instances.
     *
     * @return RemoteTaxonomyInterface[]
     */
    public function fetchTaxonomies()
    {
        if (!array_key_exists('taxonomies', $this->cache)) {
            $translator = $this->createTranslatorInstance(TaxonomyTranslator::class);
            $res = $translator->translate();
            $this->cache['taxonomies'] = $res;
        }

        return $this->cache['taxonomies'];
    }

    /**
     * Returns a taxonomy by its ID or null if not found.
     *
     * @param    string   $id
     *
     * @return   null|RemoteTaxonomyInterface
     */
    public function fetchTaxonomy($id)
    {
        /** @var RemoteTaxonomyInterface $taxonomy */
        foreach ($this->fetchTaxonomies() as $taxonomy) {
            if ($id == $taxonomy->getId()) {
                return $taxonomy;
            }
        }

        return null;
    }

    /**
     * Loads the stocks from sERPa exported files as Sylius Sync Bundle remote model stock instances.
     *
     * @return RemoteStockInterface[]
     */
    public function fetchStocks()
    {
        if (!array_key_exists('stocks', $this->cache)) {
            $translator = $this->createTranslatorInstance(StockTranslator::class);
            $res = $translator->translate();
            $this->cache['stocks'] = $res;
        }

        return $this->cache['stocks'];
    }

    /**
     * Creates a translator able to ranslate products, stocks or taxonomies from files exported by Serpa to remote instances.
     *
     * @return   AbstractTranslator
     */
    private function createTranslatorInstance($class)
    {
        if (!$this->parser) {
            $parserClass = $this->getParserClass();
            $this->parser = $parserClass::create($this->inputFiles);
        }

        return $class::create(
            $this->remoteFactories,
            $this->parser,
            $this->imagesFolder,
            $this->locale);
    }

}
