<?php
/**
 * Contains the ProductParserTest class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-01
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Tests\Model\Parser\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\InputFiles;
use Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\ProductParser;

class ProductParserTest extends \PHPUnit_Framework_TestCase
{

    public function testCreation()
    {
        $instance = ProductParser::create($this->getInputFiles());

        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\ProductParser', $instance);

        $data = $instance->getAsArray();

        // There are 3 products to load
        $this->assertEquals(3, count($data));

        // Checking the keys of every row
        $keys = array_keys($data[0]);
        $this->assertEquals('product', $keys[0]);
        $this->assertEquals('price', $keys[1]);
        $this->assertEquals('attributes', $keys[2]);

        // Checking the second product's data
        $productData = $data[1]['product'];
        $this->assertEquals($productData['TermekKod'], 'WBL-0010');
        $this->assertEquals($productData['TermekNev'], 'Ballantine\'s Pdd (0,7 l, 40%)');
        $this->assertEquals($productData['MennyisegiEgyszeg'], 'db');
        $this->assertEquals($productData['TermekLastModify'], '1427198792');
        $this->assertEquals($productData['TermekKepLastModify'], '');
        $this->assertEquals($productData['Kepnev'], '');
        $this->assertEquals($productData['Vonalkod'], '5010106113127');
        $this->assertEquals($productData['VamtarifaKod'], '22083091F');
        $this->assertEquals($productData['RovidLeiras'], 'A világ egyik legnépszerubb kevert skót whiskyje. Ennyi ember biztosan nem tud tévedni.');
        $this->assertEquals($productData['TermekFaID'], '236, 194');
        $this->assertEquals($productData['Leiras'], '<p align="justify"><span style="font-size: 10pt; font-family: verdana,geneva">A világ egyik legnépszerubb kevert skót whiskyje. Ennyi ember biztosan nem tud tévedni.</span></p><p align="justify"><span style="font-size: 10pt; font-family: verdana,geneva">A Ballantine\'s-t Dumbartonban keverik és palackozzák 57 féle maláta whiskybol és saját lepárlójában fozött kukoricawhiskybol.</span></p><p align="justify"><span style="font-size: 10pt; font-family: verdana,geneva">A márkát kifejleszto céget 1827-ben Edingburgban alapította egy 18 éves fiatalember, George Ballantine. A cég ekkor még elsosorban szeszkereskedelemmel foglalkozott. Egyik fia és unokája vitte tovább a céget az alapító 1891-es halálát követoen és hamarosan udvari beszállítók is lettek. A cimkén ma is olvasható, hogy Viktória királyno és VII. Edward király számára is szállítottak. A cégnek ezt követoen több tulajdonosa is volt, viszont ez nem befolyásolta a márka növekvo népszeruségét, amit csak fokozott az amerikai szesztilalom, mivel a Ballantine\'s a csempészek egyik legkedveltebb itala volt.</span></p>');

        // Checking the first product's price information
        $this->assertEquals($data[0]['price']['TermekKod'], 'WBL-0004');
        $this->assertEquals($data[0]['price']['Afakulcs'], '27');
        $this->assertEquals($data[0]['price']['Ar'], '4244.1');
        $this->assertEquals($data[0]['price']['AkciosAr'], '0');

        // Checking the second product's price information
        $this->assertEquals($data[1]['price']['TermekKod'], 'WBL-0010');
        $this->assertEquals($data[1]['price']['Afakulcs'], '27');
        $this->assertEquals($data[1]['price']['Ar'], '4165.4');
        $this->assertEquals($data[1]['price']['AkciosAr'], '3165.4');

        // The third product has no price information
        $this->assertNull($data[2]['price']);

        // Checking the first product's attributes
        $this->assertEquals($data[0]['attributes']['TermekKod'], 'WBL-0004');
        $this->assertEquals($data[0]['attributes']['Egyéb jellemzõ'], 'Színében a csillogó rezet idézi.');
        $this->assertEquals($data[0]['attributes']['Palackozás éve'], 'n.a.');
        $this->assertEquals($data[0]['attributes']['Hordós erõsség'], 'NEM');
        $this->assertEquals($data[0]['attributes']['Lepárlás éve'], 'n.a.');
        $this->assertEquals($data[0]['attributes']['Illat'], 'Málnás-mandulás lepény, nyomokban pörkölt tölggyel és tejkaramellával.');
        $this->assertEquals($data[0]['attributes']['Hordó típus'], 'n.a.');
        $this->assertEquals($data[0]['attributes']['Íz'], 'Száraz, könnyû, édes alma és likõrös jegyekkel. Gyengéd tõzegfüst tûnik fel a háttérben.');
        $this->assertEquals($data[0]['attributes']['Egyensúly'], 'Egy csodálatos kevert whisky - nagyszerû példája a keverõmesterek mûvészetének!');
        $this->assertEquals($data[0]['attributes']['Hordó szám'], 'n.a.');
        $this->assertEquals($data[0]['attributes']['Érlelési idõ'], 'n.a.');
        $this->assertEquals($data[0]['attributes']['Palackozó'], 'ARRAN');
        $this->assertEquals($data[0]['attributes']['Gyártó'], 'Arran');
        $this->assertEquals($data[0]['attributes']['Kep'], 'e42cb6acabe092fa3e79fffccf540959.jpg');
        $this->assertEquals($data[0]['attributes']['Alkoholtartalom'], '40');
        $this->assertEquals($data[0]['attributes']['Ürtartalom'], '0.7');
        $this->assertEquals($data[0]['attributes']['Whisky Shop'], '4480.31');
        $this->assertEquals($data[0]['attributes']['KLASSZ'], 'IGEN');
        $this->assertEquals($data[0]['attributes']['Lecsengés'], 'Friss és melengetõ, hosszan tartó vaníliás édességgel.');
        $this->assertEquals($data[0]['attributes']['Nettó Súly'], '');
        $this->assertEquals($data[0]['attributes']['Shopline'], 'IGEN');

        // The second product does not have attributes
        $this->assertNull($data[1]['attributes']);

        // Checking the third product's attributes
        $this->assertEquals($data[2]['attributes']['TermekKod'], 'WBL-0015');
        $this->assertEquals($data[2]['attributes']['Egyéb jellemzõ'], 'Mindig legyen a polcon!');
        $this->assertEquals($data[2]['attributes']['Palackozás éve'], 'n.a.');
        $this->assertEquals($data[2]['attributes']['Hordós erõsség'], 'NEM');
        $this->assertEquals($data[2]['attributes']['Lepárlás éve'], 'n.a.');
        $this->assertEquals($data[2]['attributes']['Illat'], 'Finoman füstös, tõzeges.');
        $this->assertEquals($data[2]['attributes']['Hordó típus'], 'n.a.');
        $this->assertEquals($data[2]['attributes']['Íz'], 'A finom gabona együtt van jelen a malátával, sõt még némi vanilia is érezhetõ. ');
        $this->assertEquals($data[2]['attributes']['Egyensúly'], 'Nagyon összetett, imádni való!');
        $this->assertEquals($data[2]['attributes']['Hordó szám'], 'n.a.');
        $this->assertEquals($data[2]['attributes']['Érlelési idõ'], '12');
        $this->assertEquals($data[2]['attributes']['Palackozó'], 'Pernod Ricard');
        $this->assertEquals($data[2]['attributes']['Gyártó'], 'Pernod Ricard');
        $this->assertEquals($data[2]['attributes']['Kep'], 'e38fb39e6727ac662e1836002bf4fa7c.jpg, 6d4a4f5ccd43019e68a1676acf097b10.jpg');
        $this->assertEquals($data[2]['attributes']['Alkoholtartalom'], '40');
        $this->assertEquals($data[2]['attributes']['Ürtartalom'], '0.7');
        $this->assertEquals($data[2]['attributes']['Whisky Shop'], '6212.60');
        $this->assertEquals($data[2]['attributes']['KLASSZ'], 'NEM');
        $this->assertEquals($data[2]['attributes']['Lecsengés'], 'Égetett cukor, kakaó.');
        $this->assertEquals($data[2]['attributes']['Nettó Súly'], '');
        $this->assertEquals($data[2]['attributes']['Shopline'], '');
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
