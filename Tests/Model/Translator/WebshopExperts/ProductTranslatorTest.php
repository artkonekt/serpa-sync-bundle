<?php
/**
 * Contains the ProductTranslatorTest class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-04
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Tests\Model\Translator\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\InputFiles;
use Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\ProductParser;
use Konekt\SerpaSyncBundle\Model\RemoteFactories;
use Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts\ProductTranslator;
use Konekt\SyliusSyncBundle\Model\Remote\Product\Product;
use Konekt\SyliusSyncBundle\Model\Remote\Product\ProductFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Image\ImageFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Stock\StockFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonomyFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonFactory;

class ProductTranslatorTest extends \PHPUnit_Framework_TestCase
{
    
    private $locale = 'hu_HU';

    public function testCreation()
    {
        $instance = ProductTranslator::create($this->getRemoteFactories(), $this->locale);

        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts\ProductTranslator', $instance);
   }

    public function testTranslation()
    {
        $translator = ProductTranslator::create($this->getRemoteFactories(), $this->locale);
        $parser = ProductParser::create($this->getInputFiles());

        $data = $parser->getAsArray();

        /** @var Product $product */
        $product0 = $translator->translate($data[0]);
        $product1 = $translator->translate($data[1]);

        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Product\Product', $product0);
        $this->assertInstanceOf('Konekt\SyliusSyncBundle\Model\Remote\Product\Product', $product1);

        // There are 3 products loaded
        $this->assertEquals(3, count($data));
        $this->assertEquals($product0->getSku(), 'WBL-0004');
        $this->assertEquals($product0->getTranslation($this->locale)->getName(), 'Arran The Burns Blend (0,7 l, 40%)');
        $this->assertEquals($product0->getTranslation($this->locale)->getShortDescription(), 'Egy elragadóan sima és elegáns blend, melynek fõ alkotóeleme a nagyhírû Arran Malt.');
        $this->assertEquals($product0->getTranslation($this->locale)->getDescription(), '<p><p align=""/>Egy elragadóan sima blend, melynek fõ alkotóeleme a nagyhírû Arran Malt.</p><p><p align=""/>Ez a nagyszerû kevert whisky a híres Skót költõnek, Robert Burns-nek (1759-1796) állít emléket, úgy hogy tökéletesen visszaadja ennek a tiszta hegyi patakokkal és könnyû tengeri fuvallattal bíró gyönyörû szigetnek a karakterét.</p><p><p align=""/> </p><p><p align=""/>Illat: Málnás-mandulás lepény, nyomokban pörkölt tölggyel és tejkaramellával.</p><p><p align=""/> </p><p><p align=""/>Íz: Száraz, könnyû, édes alma és likõrös jegyekkel. Gyengéd tõzegfüst tûnik fel a háttérben.</p><p><p align=""/> </p><p><p align=""/>Lecsengés: Friss és melengetõ, hosszan tartó vaníliás édességgel.<br/></p>');

        // Price information
        $this->assertEquals($product0->getPrice(), '4244.1');
        $this->assertEquals($product0->getCatalogPrice(), '4244.1');
        $this->assertEquals($product1->getPrice(), '3165.4');
        $this->assertEquals($product1->getCatalogPrice(), '4165.4');

        // Attributes
        $this->assertEquals($product0->getAttributes()[0]->getId(), 'TermekKod');
        $this->assertEquals($product0->getAttributes()[0]->getTranslation($this->locale)->getName(), 'WBL-0004');
        $this->assertEquals($product0->getAttributes()[1]->getId(), 'Egyéb jellemzõ');
        $this->assertEquals($product0->getAttributes()[1]->getTranslation($this->locale)->getName(), 'Színében a csillogó rezet idézi.');
        $this->assertEquals($product0->getAttributes()[2]->getId(), 'Palackozás éve');
        $this->assertEquals($product0->getAttributes()[2]->getTranslation($this->locale)->getName(), 'n.a.');
        $this->assertEquals($product0->getAttributes()[3]->getId(), 'Hordós erõsség');
        $this->assertEquals($product0->getAttributes()[3]->getTranslation($this->locale)->getName(), 'NEM');
        $this->assertEquals($product0->getAttributes()[4]->getId(), 'Lepárlás éve');
        $this->assertEquals($product0->getAttributes()[4]->getTranslation($this->locale)->getName(), 'n.a.');
        $this->assertEquals($product0->getAttributes()[5]->getId(), 'Illat');
        $this->assertEquals($product0->getAttributes()[5]->getTranslation($this->locale)->getName(), 'Málnás-mandulás lepény, nyomokban pörkölt tölggyel és tejkaramellával.');
        $this->assertEquals($product0->getAttributes()[6]->getId(), 'Hordó típus');
        $this->assertEquals($product0->getAttributes()[6]->getTranslation($this->locale)->getName(), 'n.a.');
        $this->assertEquals($product0->getAttributes()[7]->getId(), 'Íz');
        $this->assertEquals($product0->getAttributes()[7]->getTranslation($this->locale)->getName(), 'Száraz, könnyû, édes alma és likõrös jegyekkel. Gyengéd tõzegfüst tûnik fel a háttérben.');
        $this->assertEquals($product0->getAttributes()[8]->getId(), 'Egyensúly');
        $this->assertEquals($product0->getAttributes()[8]->getTranslation($this->locale)->getName(), 'Egy csodálatos kevert whisky - nagyszerû példája a keverõmesterek mûvészetének!');
        $this->assertEquals($product0->getAttributes()[9]->getId(), 'Hordó szám');
        $this->assertEquals($product0->getAttributes()[9]->getTranslation($this->locale)->getName(), 'n.a.');
        $this->assertEquals($product0->getAttributes()[10]->getId(), 'Érlelési idõ');
        $this->assertEquals($product0->getAttributes()[10]->getTranslation($this->locale)->getName(), 'n.a.');
        $this->assertEquals($product0->getAttributes()[11]->getId(), 'Palackozó');
        $this->assertEquals($product0->getAttributes()[11]->getTranslation($this->locale)->getName(), 'ARRAN');
        $this->assertEquals($product0->getAttributes()[12]->getId(), 'Gyártó');
        $this->assertEquals($product0->getAttributes()[12]->getTranslation($this->locale)->getName(), 'Arran');
        $this->assertEquals($product0->getAttributes()[13]->getId(), 'Kep');
        $this->assertEquals($product0->getAttributes()[13]->getTranslation($this->locale)->getName(), 'e42cb6acabe092fa3e79fffccf540959.jpg');
        $this->assertEquals($product0->getAttributes()[14]->getId(), 'Alkoholtartalom');
        $this->assertEquals($product0->getAttributes()[14]->getTranslation($this->locale)->getName(), '40');
        $this->assertEquals($product0->getAttributes()[15]->getId(), 'Ürtartalom');
        $this->assertEquals($product0->getAttributes()[15]->getTranslation($this->locale)->getName(), '0.7');
        $this->assertEquals($product0->getAttributes()[16]->getId(), 'Whisky Shop');
        $this->assertEquals($product0->getAttributes()[16]->getTranslation($this->locale)->getName(), '4480.31');
        $this->assertEquals($product0->getAttributes()[17]->getId(), 'KLASSZ');
        $this->assertEquals($product0->getAttributes()[17]->getTranslation($this->locale)->getName(), 'IGEN');
        $this->assertEquals($product0->getAttributes()[18]->getId(), 'Lecsengés');
        $this->assertEquals($product0->getAttributes()[18]->getTranslation($this->locale)->getName(), 'Friss és melengetõ, hosszan tartó vaníliás édességgel.');
        $this->assertEquals($product0->getAttributes()[19]->getId(), 'Nettó Súly');
        $this->assertEquals($product0->getAttributes()[19]->getTranslation($this->locale)->getName(), '');
        $this->assertEquals($product0->getAttributes()[20]->getId(), 'Shopline');
        $this->assertEquals($product0->getAttributes()[20]->getTranslation($this->locale)->getName(), 'IGEN');
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
