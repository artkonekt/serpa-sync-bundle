<?php
/**
 * Contains the AbstractAdapter class
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-02
 * @since       2016-03-02
 */

namespace Konekt\SerpaSyncBundle\Model;

use Konekt\SerpaSyncBundle\Model\Exception\MissingInputFile;
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

    /** @var  InputFiles */
    private $inputFiles;

    /** @var  RemoteFactories */
    private $remoteFactories;

    /** @var  RemoteTaxonomyInterface */
    private $taxonomies = null;

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
     *
     * @return  static
     *
     * @throws MissingInputFile                      When at least one required input file is missing.
     */
    public static function create(ProductFactory $productFactory, ImageFactory $imageFactory, TaxonomyFactory $taxonomyFactory,
                                  TaxonFactory $taxonFactory, StockFactory $stockFactory, array $inputFiles)
    {
        $instance = new static();

        // All required input file must be present
        $inputFiles = InputFiles::create($inputFiles);
        foreach ($instance->getRequiredFiles() as $file) {
            if (!$inputFiles->fileExists($file)) {
                throw new MissingInputFile("Input file '$file'' is required but it is missing.");
            }
        }

        $instance->inputFiles = $inputFiles;
        $instance->remoteFactories = RemoteFactories::create($productFactory, $imageFactory, $taxonomyFactory, $taxonFactory, $stockFactory);

        return $instance;
    }

    /**
     * Returns the list of files exported by sERPa.
     *
     * @return InputFiles
     */
    public function getInputFiles()
    {
        return $this->inputFiles;
    }

    /**
     * Returns the remote factories to be used to create Sylius Sync Bundle model instances.
     *
     * @return RemoteFactories
     */
    public function getRemoteFactories()
    {
        return $this->remoteFactories;
    }

    /**
     * Loads, parses and translate a list of items (products, taxonomies or stocks) from files exported by sERPa
     * and build up a list of Sylius Sync Bundle remote model instances.
     *
     * @param   AbstractParser       $parser       The parser used to load data from sERPa files.
     * @param   AbstractTranslator   $translator   The translator used to translate sERPa data to Sylius Remote Bundle model instances.
     *
     * @return  array
     */
    protected function translate(AbstractParser $parser, AbstractTranslator $translator)
    {
        $remoteModels = [];
        foreach ($parser->getAsArray() as $data) {
            $remoteModels[] = $translator->translate($data);
        }

        return $remoteModels;
    }

    /**
     * Returns the list of files (only file names) exported by sERPa and required by the adapter.
     *
     * @return array
     */
    abstract public function getRequiredFiles();

    /**
     * Returns the parser that loads product data from files exported by sERPa.
     *
     * @return AbstractParser
     */
    abstract public function getProductParser();

    /**
     * Returns the translator that translates product data loaded by the parser into Sylius Sync Bundle remote product instances.
     *
     * @return AbstractTranslator
     */
    abstract public function getProductTranslator();

    /**
     * Returns the parser that loads taxonomies data from files exported by sERPa.
     *
     * @return AbstractParser
     */
    abstract public function getTaxonomyParser();

    /**
     * Returns the translator that translates taxonomy data loaded by the parser into Sylius Sync Bundle remote taxonomy instances.
     *
     * @return AbstractTranslator
     */
    abstract public function getTaxonomyTranslator();

    /**
     * Returns the parser that loads stock data from files exported by sERPa.
     *
     * @return AbstractParser
     */
    abstract public function getStockParser();

    /**
     * Returns the translator that translates stock data loaded by the parser into Sylius Sync Bundle remote stock instances.
     *
     * @return AbstractTranslator
     */
    abstract public function getStockTranslator();

    /**
     * Loads the products from sERPa exported files as Sylius Sync Bundle remote model product instances.
     *
     * @return RemoteProductInterface[]
     */
    public function fetchProducts()
    {
        return $this->translate($this->getProductParser(), $this->getProductTranslator());
    }

    /**
     * Loads the taxonomies from sERPa exported files as Sylius Sync Bundle remote model taxonomy instances.
     *
     * @return RemoteTaxonomyInterface[]
     */
    public function fetchTaxonomies()
    {
        if (null === $this->taxonomies) {
            $this->taxonomies = $this->translate($this->getTaxonomyParser(), $this->getTaxonomyTranslator());
        }

        return $this->taxonomies;
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
        return $this->translate($this->getStockParser(), $this->getStockTranslator());
    }

}
