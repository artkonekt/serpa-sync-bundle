<?php
/**
 * Contains the TxtDataSource class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     Proprietary
 * @version     2016-03-30
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model\DataSource;

use Konekt\SerpaSyncBundle\Model\AbstractDataSource;

/**
 * Represents a text file created by sERPa containing various data exported by the WebshopExperts module configured inside sERPa.
 *
 * The first line of the file is always the header, the following ones contains the data.
 * Both the header and the data rows use the tab character as column separator.
 * The last line is always a Ctrl-Z (ASCII 26) character that is ignored.
 *
 */
class TxtDataSource extends AbstractDataSource
{

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

    /**
     * @inheritdoc
     *
     */
    public function getAsArray()
    {
        $this->openFile();
        $this->loadHeader();

        $res = [];
        while (($row = $this->getNextLine()) !== null) {
            if (0 < strlen(trim($row))) {
                $values = $this->splitLine($row);
                $res[] = $this->mapValues($values);
            }
        }

        $this->closeFile();

        return $res;
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
            $res[$this->header[$i]] = isset($data[$i]) ? $data[$i] : null;
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

        $utf8String = $this->convertEncodingToUtf8($line);

        return trim($this->fixHungarianAccents($utf8String));  // fgets() gets the line with a newline at the end, trimming it
    }

    /**
     * Converts a string to UTF-8 encoding if it is encoded differently.
     *
     * @param   string   $str   The string to encode.
     *
     * @return  string
     */
    private function convertEncodingToUtf8($str)
    {
        return mb_convert_encoding($str, 'UTF-8', 'ASCII');
    }

    /**
     * Fixes incorrect hungarian accented characters (õ => ő, û => ű, Õ => Ő, Û => Ű).
     *
     * @param   string   $str   The string to encode.
     *
     * @return  string
     */
    private function fixHungarianAccents($str)
    {
        return strtr($str, ['õ' => 'ő', 'û' => 'ű', 'Õ' => 'Ő', 'Û' => 'Ű']);
    }

}
