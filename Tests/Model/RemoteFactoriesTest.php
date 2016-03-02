<?php
/**
 * Contains the RemoteFactoriesTest class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-01
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Tests\Model;

use Konekt\SerpaSyncBundle\Model\RemoteFactories;
use Konekt\SyliusSyncBundle\Model\Remote\Product\ProductFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Image\ImageFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonomyFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonFactory;

class RemoteFactoriesTest extends \PHPUnit_Framework_TestCase
{

    public function testCreation()
    {
        $instance = RemoteFactories::create(
            new ProductFactory(''),
            new ImageFactory(''),
            new TaxonomyFactory(''),
            new TaxonFactory('')
        );

        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\RemoteFactories', $instance);
    }

    public function testGetters()
    {
        $instance = RemoteFactories::create(
            new ProductFactory('Konekt\SyliusSyncBundle\Model\Remote\Product\Product'),
            new ImageFactory('Konekt\SyliusSyncBundle\Model\Remote\Image\Image'),
            new TaxonomyFactory('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxonomy'),
            new TaxonFactory('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxon')
        );

        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Product\ProductFactory', $instance->getProductFactory());
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Image\ImageFactory', $instance->getImageFactoryFactory());
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonomyFactory', $instance->getTaxonomyFactory());
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonFactory', $instance->getTaxonFactory());
    }

}
