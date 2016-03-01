<?php
/**
 * InputFilesTest.php
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     Proprietary
 * @version     2016-03-01
 * @since       2016-03-01
 */


namespace Konekt\SerpaSyncBundle\Tests\Model;

use Konekt\SerpaSyncBundle\Model\InputFiles;

class InputFilesTest extends \PHPUnit_Framework_TestCase
{

    public function testCreation()
    {
        $instance = InputFiles::create([
            '/path/to/file1.txt',
            '/path/to/file2.txt',
            '/path/to/file3.txt',
        ]);

        $this->assertInstanceOf('Konekt\SerpaSyncBundle\Model\InputFiles', $instance);
    }

    public function testFileExists()
    {
        $instance = InputFiles::create([
            '/path/to/file1.txt',
            '/path/to/file2.txt',
            '/path/to/file3.txt',
        ]);

        $this->assertTrue($instance->fileExists('file2.txt'));
    }

    public function testFileNotExists()
    {
        $instance = InputFiles::create([
            '/path/to/file1.txt',
            '/path/to/file2.txt',
            '/path/to/file3.txt',
        ]);


        $this->assertTrue($instance->fileExists('File2.txt'));
    }

    public function testGetFile()
    {
        $instance = InputFiles::create([
            '/path/to/file1.txt',
            '/path/to/file2.txt',
            '/path/to/file3.txt',
        ]);

        $this->assertEquals('/path/to/file2.txt', $instance->getFile('file2.txt'));
    }

    public function testFileWithoutExtension()
    {
        $instance = InputFiles::create([
            '/path/to/file1',
            '/path/to/file2',
            '/path/to/file3',
        ]);

        $this->assertEquals('/path/to/file2', $instance->getFile('file2'));
    }

    public function testInvalidFile()
    {
        $this->setExpectedException('Konekt\SerpaSyncBundle\Model\Exception\InvalidInputFile');

        InputFiles::create([
            '/path/to/'
        ]);

    }

    public function testDuplicateFile()
    {
        $this->setExpectedException('Konekt\SerpaSyncBundle\Model\Exception\DuplicateInputFileName');

        InputFiles::create([
            '/path/to/file1.txt',
            '/path/to/file2.txt',
            '/another/path/to/file1.txt'
        ]);

    }

}