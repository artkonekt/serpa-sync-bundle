<?php
/**
 * Contains the TaxonomyTranslatorTest class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-04
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Tests\Model\Translator\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\InputFiles;
use Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\TaxonomyParser;
use Konekt\SerpaSyncBundle\Model\RemoteFactories;
use Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts\TaxonomyTranslator;
use Konekt\SyliusSyncBundle\Model\Remote\Image\ImageFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Product\ProductFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Stock\StockFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxonomy;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonomyFactory;

class TaxonomyTranslatorTest extends \PHPUnit_Framework_TestCase
{

    private $locale = 'hu_HU';

    public function testCreation()
    {
        $instance = TaxonomyTranslator::create($this->getRemoteFactories(), $this->locale);

        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts\TaxonomyTranslator', $instance);
    }

    public function testTranslation()
    {
        $translator = TaxonomyTranslator::create($this->getRemoteFactories(), $this->locale);
        $parser = TaxonomyParser::create($this->getInputFiles());

        $data = $parser->getAsArray();

        /** @var Taxonomy $product */
        $taxonomy0 = $translator->translate($data[0]);
        $taxonomy1 = $translator->translate($data[1]);

        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxonomy', $taxonomy0);
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxonomy', $taxonomy1);

        // There are taxonomies loaded
        $this->assertEquals(2, count($data));

        $this->assertEquals($taxonomy0->getId(), '234');
        $this->assertEquals($taxonomy0->getTranslation($this->locale)->getName(), 'Regiok');
        $this->assertEquals(2, count($taxonomy0->getTaxons()));

        $this->assertEquals($taxonomy1->getId(), '235');
        $this->assertEquals($taxonomy1->getTranslation($this->locale)->getName(), 'Whiskey');
        $this->assertEquals(2, count($taxonomy1->getTaxons()));

        $this->assertEquals($taxonomy0->getTaxons()[0]->getId(), '194');
        $this->assertEquals($taxonomy0->getTaxons()[1]->getId(), '195');
   }

    private function getRemoteFactories()
    {
        return RemoteFactories::create(
            new ProductFactory('Konekt\SyliusSyncBundle\Model\Remote\Product\Product'),
            new ImageFactory('Konekt\SyliusSyncBundle\Model\Remote\Image\Image'),
            new TaxonomyFactory('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxonomy'),
            new TaxonFactory('Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxon'),
            new StockFactory('Konekt\SyliusSyncBundle\Model\Remote\Stock\Stock')
        );
    }

    private function getInputFiles()
    {
        return InputFiles::create([
            __DIR__ . DIRECTORY_SEPARATOR . '../../../Fixtures/Parser/WebshopExperts/Termek.txt',
            __DIR__ . DIRECTORY_SEPARATOR . '../../../Fixtures/Parser/WebshopExperts/TermekAR.txt',
            __DIR__ . DIRECTORY_SEPARATOR . '../../../Fixtures/Parser/WebshopExperts/TermekFa.txt',
            __DIR__ . DIRECTORY_SEPARATOR . '../../../Fixtures/Parser/WebshopExperts/TermekKategoria.txt',
            __DIR__ . DIRECTORY_SEPARATOR . '../../../Fixtures/Parser/WebshopExperts/TermekKeszlet.txt'
        ]);
    }

}
