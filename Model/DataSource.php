<?php
/**
 * Contains class DataSource.
 *
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @author      Sandor Teglas
 * @license     MIT
 * @since       2016-02-29
 * @version     2016-02-29
 */

namespace Konekt\SerpaSyncBundle\Model;

use Exception;

/**
 * Represents a text file created by sERPa containing various data exported by the WebshopExperts module configured inside sERPa.
 *
 * The first line of the file is always the header, the following ones contains the data.
 * Both the header and the data rows use the tab character as column separator.
 * The last line is always a Ctrl-Z (ASCII 26) character that is ignored.
 *
 */
class DataSource
{

    /**
     * @var string The file containing sERPa data.
     */
    protected $file;

    /**
     * @var resource The handle of the open file.
     */
    private $handle;

    /**
     * @var array The column names from the file header.
     */
    private $header;

    /**
     * @var int The number of rows read from the data source file (including the header line).
     */
    private $rowsCount = 0;

    private function __construct() {}

    /**
     * Creates a new instance of the class.
     *
     * @param   string       $file   The file containing the sERPa data.
     *
     * @return  static
     *
     * @throws                       Exception When the file does not exist.
     */
    public static function create($file)
    {
        if (!is_file($file) || is_dir($file)) {
            throw new Exception("Data source file '$file' does not exist.");
        }

        $instance = new static();
        $instance->file = $file;

        return $instance;
    }

    /**
     * Returns an array of rows, each one representing an associative array with values mapped to header columns as array keys.
     *
     * @return   array
     */
    public function getDataRows()
    {
        $this->openFile();
        $this->loadHeader();

        $res = [];
        while (($row = $this->getNextLine()) !== null) {
            $values = $this->splitLine($row);
            $res[] = $this->mapValues($values);
        }

        $this->closeFile();

        return $res;
    }

    /**
     * Returns the number of data rows read from the data source file.
     *
     * @return  int
     */
    public function getDataRowsCount()
    {
        // Header row is not counted
        return 0 < $this->rowsCount ? $this->rowsCount - 1 : 0;
    }

    /**
     * Splits a text line by the TAB separator character.
     *
     * @param   $line   The text line to split.
     *
     * @return  array
     */
    private function splitLine($line)
    {
        return explode("\t", $line);
    }

    /**
     * Maps a series of values from a data row using the columns from the file header as keys.
     *
     * @param   array   $data     The values of a data row line.
     *
     * @return  array
     */
    private function mapValues(array $data)
    {
        $res = [];

        for ($i = 0; $i < count($this->header); $i++) {
            $res[$this->header[$i]] = $data[$i];
        }

        return $res;
    }

    /**
     * Opens the source file and sets the file handle.
     *
     * @throws   Exception   When the file could not be open.
     */
    private function openFile()
    {
        $this->handle = null;
        $this->rowsCount = 0;
        $this->header = [];

        $handle = fopen($this->file, "r");
        if (!$handle) {
            throw new Exception('Failed opening file for reading.');
        }

        $this->handle = $handle;
    }

    /**
     * Loads the next line from the data source file and interprets it as the header.
     *
     */
    private function loadHeader()
    {
        $header = $this->getNextLine();

        if (null === $header) {
            $this->header = [];

            return;
        }

        $this->header = $this->splitLine(trim($header));
    }

    /**
     * Closes the source file identified by its handle.
     *
     */
    private function closeFile()
    {
        fclose($this->handle);
    }

    /**
     * Returns the next line from the data source file or false if there are no more lines left
     * or Ctrl-Z is encountered.
     *
     * @return null|string
     */
    private function getNextLine()
    {
        $line = fgets($this->handle);

        if (false === $line) {

            return null;
        }

        // The last line contains Ctrl-Z, it must be ignored
        if (26 == ord($line)) {

            return null;
        }

        $this->rowsCount++;

        return $line;
    }

}
