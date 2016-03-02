<?php
/**
 * Contains the WebshopExpertsAdapterTest class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-01
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Tests\Model\Adapter;

use Konekt\SerpaSyncBundle\Model\Adapter\WebshopExpertsAdapter;
use Konekt\SyliusSyncBundle\Model\Remote\Image\ImageFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Product\ProductFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonomyFactory;


class WebshopExpertsAdapterTest extends \PHPUnit_Framework_TestCase
{

    public function testCreation()
    {
        $instance = $this->getValidInstance();
        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\Adapter\WebshopExpertsAdapter', $instance);
    }

    public function testCreationMissingRequiredInputFile()
    {
        $this->setExpectedException('Konekt\SerpaSyncBundle\Model\Exception\MissingInputFile');

        WebshopExpertsAdapter::create(
            new ProductFactory('Konekt\SyliusSyncBundle\Model\Remote\Product\Product'),
            new ImageFactory('Konekt\SyliusSyncBundle\Model\Remote\Image\Image'),
            new TaxonomyFactory('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxonomy'),
            new TaxonFactory('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxon'),
            [
                __DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/Parser/WebshopExperts/TermeK.txt',   // this one should have lowercase "k"
                __DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/Parser/WebshopExperts/TermekAR.txt',
                __DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/Parser/WebshopExperts/TermekFa.txt',
                __DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/Parser/WebshopExperts/TermekKategoria.txt',
                __DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/Parser/WebshopExperts/TermekKeszlet.txt'
            ]
        );
    }

    public function testRequiredFiles()
    {
        $files = $this->getValidInstance()->getRequiredFiles();
        $this->assertContains('Termek.txt', $files);
        $this->assertContains('TermekAR.txt', $files);
        $this->assertContains('TermekFa.txt', $files);
        $this->assertContains('TermekKategoria.txt', $files);
        $this->assertContains('TermekKeszlet.txt', $files);
    }

    public function testProductParser()
    {
        $instance = $this->getValidInstance();
        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\ProductParser', $instance->getProductParser());
    }

    public function testProductTranslator()
    {
        $instance = $this->getValidInstance();
        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts\ProductTranslator', $instance->getProductTranslator());
    }

    public function testTaxonomyParser()
    {
        $instance = $this->getValidInstance();
        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\TaxonomyParser', $instance->getTaxonomyParser());
    }

    public function testTaxonomyTranslator()
    {
        $instance = $this->getValidInstance();
        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts\TaxonomyTranslator', $instance->getTaxonomyTranslator());
    }

    public function testFetchProducts()
    {
        $instance = $this->getValidInstance();
        $products = $instance->fetchProducts();

        $this->assertEquals(3, count($products));
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Product\Product', $products[0]);
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Product\Product', $products[1]);
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Product\Product', $products[2]);
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductInterface', $products[0]);
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductInterface', $products[1]);
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductInterface', $products[2]);

    }

    public function testFetchTaxonomies()
    {
        $instance = $this->getValidInstance();
        $taxonomies = $instance->fetchTaxonomies();

        $this->assertEquals(2, count($taxonomies));
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxonomy', $taxonomies[0]);
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxonomy', $taxonomies[1]);
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonomyInterface', $taxonomies[0]);
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonomyInterface', $taxonomies[1]);

    }

    /**
     * Returns a valid WebshopExpertsAdapter instance.
     *
     * @return WebshopExpertsAdapter
     */
    private function getValidInstance()
    {
        return WebshopExpertsAdapter::create(
            new ProductFactory('Konekt\SyliusSyncBundle\Model\Remote\Product\Product'),
            new ImageFactory('Konekt\SyliusSyncBundle\Model\Remote\Image\Image'),
            new TaxonomyFactory('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxonomy'),
            new TaxonFactory('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxon'),
            [
                __DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/Parser/WebshopExperts/Termek.txt',
                __DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/Parser/WebshopExperts/TermekAR.txt',
                __DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/Parser/WebshopExperts/TermekFa.txt',
                __DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/Parser/WebshopExperts/TermekKategoria.txt',
                __DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/Parser/WebshopExperts/TermekKeszlet.txt'
            ]
        );
    }

}
