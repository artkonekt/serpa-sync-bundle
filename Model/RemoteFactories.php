<?php
/**
 * Contains the RemoteFactories class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @license     MIT
 * @since       2016-03-01
 * @version     2016-03-02
 */

namespace Konekt\SerpaSyncBundle\Model;

use Konekt\SyliusSyncBundle\Model\Remote\Image\ImageFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Product\ProductFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Stock\StockFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonomyFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonFactory;

/**
 * Represents the remote factory services exposed by the sylius sync Bundle.
 *
 */
class RemoteFactories
{

    /** @var ProductFactory */
    private $productFactory;

    /** @var  ImageFactory */
    private $imageFactory;

    /** @var  TaxonomyFactory */
    private $taxonomyFactory;

    /** @var  TaxonFactory */
    private $taxonFactory;

    /** @var  StockFactory */
    private $stockFactory;

    /**
     * Creates a new instance of the class.
     *
     * @param   ProductFactory    $productFactory    Factory service used to create products required by the sync bundle.
     * @param   ImageFactory      $imageFactory      Factory service used to create images required by the sync bundle.
     * @param   TaxonomyFactory   $taxonomyFactory   Factory service used to create taxonomies required by the sync bundle.
     * @param   TaxonFactory      $taxonFactory      Factory service used to create taxons required by the sync bundle.
     * @param   StockFactory      $stockFactory      Factory service used to create stocks required by the sync bundle.
     *
     * @return  static
     */
    public static function create(ProductFactory $productFactory, ImageFactory $imageFactory,
                                  TaxonomyFactory $taxonomyFactory, TaxonFactory $taxonFactory, StockFactory $stockFactory)
    {
        $instance = new static();

        $instance->productFactory = $productFactory;
        $instance->imageFactory = $imageFactory;
        $instance->taxonomyFactory = $taxonomyFactory;
        $instance->taxonFactory = $taxonFactory;
        $instance->stockFactory = $stockFactory;

        return $instance;
    }

    /**
     * Returns the remote product factory service exposed by Sylius Sync Service to be used to create remote product model instances.
     *
     * @return ProductFactory
     */
    public function getProductFactory()
    {
        return $this->productFactory;
    }

    /**
     * Returns the remote image factory service exposed by Sylius Sync Service to be used to create remote image model instances.
     *
     * @return ProductFactory
     */
    public function getImageFactoryFactory()
    {
        return $this->imageFactory;
    }
    /**
     * Returns the remote taxonomy factory service exposed by Sylius Sync Service to be used to create remote taxonomy model instances.
     *
     * @return TaxonomyFactory
     */
    public function getTaxonomyFactory()
    {
        return $this->taxonomyFactory;
    }

    /**
     * Returns the remote product factory service exposed by Sylius Sync Service to be used to create remote taxon model instances.
     *
     * @return TaxonFactory
     */
    public function getTaxonFactory()
    {
        return $this->taxonFactory;
    }

    /**
     * Returns the remote stock factory service exposed by Sylius Sync Service to be used to create remote stock model instances.
     *
     * @return StockFactory
     */
    public function getStockFactory()
    {
        return $this->taxonFactory;
    }

}
