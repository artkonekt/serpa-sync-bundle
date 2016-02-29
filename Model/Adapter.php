<?php
/**
 * Contains class Adapter.
 *
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @author      Sandor Teglas
 * @license     MIT
 * @since       2016-02-29
 * @version     2016-02-29
 */

namespace Konekt\SerpaSyncBundle\Model;

use Konekt\SyliusSyncBundle\Model\Remote\Adapter\Konekt;
use Konekt\SyliusSyncBundle\Model\Remote\Adapter\RemoteAdapterInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Image\ImageFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Product\ProductFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonomyFactory;

/**
 * Represents the sERPa interface adapter implementation defined in Sylius Sync Bundle..
 *
 * @package AppBundle\Model
 */
class Adapter implements RemoteAdapterInterface
{

    /** @var ProductFactory */
    private $productFactory;

    /** @var  ImageFactory */
    private $imagefactory;

    /** @var  TaxonomyFactory */
    private $taxonomyFactory;

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
     * @param   ProductFactory    $productFactory    Factory service used to create products required by the sync bundle.
     * @param   ImageFactory      $imageFactory      Factory service used to create images required by the sync bundle.
     * @param   TaxonomyFactory   $taxonomyFactory   Factory service used to create taxonomies required by the sync bundle.
     * @param   string            $productsFile      The file containing the products exported by sERPa.
     * @param   string            $pricesFile        The file containing the product prices exported by sERPa.
     * @param   string            $taxonomiesFile    The file containing the taxonomy hierarchy exported by sERPa.
     * @param   string            $attributesFile    The file containing product attributes exported by sERPa.
     * @param   string            $stocksFile        The file containing product stocks exported by sERPa.
     */
    public function __construct(ProductFactory $productFactory, ImageFactory $imageFactory, TaxonomyFactory $taxonomyFactory,
        $productsFile, $pricesFile, $taxonomiesFile, $attributesFile, $stocksFile)
    {
        $this->productFactory = $productFactory;
        $this->imagefactory = $imageFactory;
        $this->taxonomyFactory = $taxonomyFactory;
        $this->productsFile = $productsFile;
        $this->pricesFile = $pricesFile;
        $this->taxonomiesFile = $taxonomiesFile;
        $this->attributesFile = $attributesFile;
        $this->stocksFile = $stocksFile;
    }

    /**
     * @inheritdoc
     *
     */
    public function fetchProducts()
    {
        $res = [];

        $productsData = Parser::create()->parseProducts($this->productsFile, $this->pricesFile, $this->attributesFile);
        $mapper = Mapper::create();

        foreach ($productsData as $data) {
            $res[] = $mapper->mapProduct($this->productFactory->create(), $data);
        }

        return $res;
    }

    /**
     * @inheritdoc
     *
     */
    public function fetchTaxonomies()
    {
        // TODO: Implement fetchTaxonomies() method.
    }

    /**
     * @inheritdoc
     *
     */
    public function fetchTaxonomy($id)
    {
        // TODO: Implement fetchTaxonomy() method.
    }

}
