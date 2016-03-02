<?php
/**
 * Contains the TaxonomyParserTest class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-01
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Tests\Model\Parser\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\InputFiles;
use Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\TaxonomyParser;

class TaxonomyParserTest extends \PHPUnit_Framework_TestCase
{

    public function testCreation()
    {
        $instance = TaxonomyParser::create($this->getInputFiles());

        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts\TaxonomyParser', $instance);

        $data = $instance->getAsArray();

        // There are taxonomies loaded
        $this->assertEquals(2, count($data));

        // Checking the first taxonomy: Regiok
        $this->assertEquals($data[0]['ID'], '234');
        $this->assertEquals($data[0]['SzuloID'], '0');
        $this->assertEquals($data[0]['LevelNev'], 'Regiok');
        $this->assertEquals($data[0]['LevelLeiras'], 'Ezek a regiok...');

        // Checking the second taxonomy: Whiskey
        $this->assertEquals($data[1]['ID'], '235');
        $this->assertEquals($data[1]['SzuloID'], '0');
        $this->assertEquals($data[1]['LevelNev'], 'Whiskey');
        $this->assertEquals($data[1]['LevelLeiras'], '');

        // Regiok taxonomy has 2 children taxons: Skocia, Kanada
        $this->assertEquals(count($data[0]['children']), 2);
        $this->assertEquals($data[0]['children'][0]['LevelNev'], 'Skocia');
        $this->assertEquals($data[0]['children'][1]['LevelNev'], 'Kanada');

        // Kanada's children: Quebec, Ontario
        $this->assertEquals($data[0]['children'][1]['children'][0]['LevelNev'], 'Quebec');
        $this->assertEquals($data[0]['children'][1]['children'][1]['LevelNev'], 'Ontario');

        // Whiskey taxonomy has 2 children taxons: Blended, Bourbon
        $this->assertEquals(count($data[1]['children']), 2);
        $this->assertEquals($data[1]['children'][0]['LevelNev'], 'Blended');
        $this->assertEquals($data[1]['children'][1]['LevelNev'], 'Bourbon');

        // Skocia's children: Islay, Felfold, Melyfold
        $this->assertEquals($data[0]['children'][0]['children'][0]['LevelNev'], 'Islay');
        $this->assertEquals($data[0]['children'][0]['children'][1]['LevelNev'], 'Felfold');
        $this->assertEquals($data[0]['children'][0]['children'][2]['LevelNev'], 'Melyfold');

        // Felfold's children: Nyugat Felfold, Észak Felfold, Közép Felfold, Kelet Felfold
        $this->assertEquals($data[0]['children'][0]['children'][1]['children'][0]['LevelNev'], 'Nyugat Felfold');
        $this->assertEquals($data[0]['children'][0]['children'][1]['children'][1]['LevelNev'], 'Észak Felfold');
        $this->assertEquals($data[0]['children'][0]['children'][1]['children'][2]['LevelNev'], 'Közép Felfold');
        $this->assertEquals($data[0]['children'][0]['children'][1]['children'][3]['LevelNev'], 'Kelet Felfold');

        // Kelet Felfold has no children
        $this->assertEquals(0, count($data[0]['children'][0]['children'][1]['children'][0]['children']));
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
