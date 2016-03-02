<?php
/**
 * Contains TxtDataSource class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-01
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Tests\Model\DataSource;

use Konekt\SerpaSyncBundle\Model\DataSource\TxtDataSource;

class TxtDataSourceTest extends \PHPUnit_Framework_TestCase
{

    public function testCreation()
    {
        $instance = TxtDataSource::create(__DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/DataSource/tab_delimited.txt');

        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\AbstractDataSource', $instance);
        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\DataSource\TxtDataSource', $instance);
    }

    public function testLoading()
    {
        $instance = TxtDataSource::create(__DIR__ . DIRECTORY_SEPARATOR . '../../Fixtures/DataSource/tab_delimited.txt');
        $rows = $instance->getAsArray();

        $this->assertTrue(is_array($rows));

        $this->assertEquals(3, count($rows));

        $header = array_keys($rows[0]);
        $this->assertEquals(3, count($header));
        $this->assertEquals('col1', $header[0]);
        $this->assertEquals('col2', $header[1]);
        $this->assertEquals('col3', $header[2]);

        $row = $rows[0];
        $this->assertEquals(3, count($row));
        $this->assertEquals('row1_col1', $row['col1']);
        $this->assertEquals('row1_col2', $row['col2']);
        $this->assertEquals('row1_col3', $row['col3']);

    }

}
