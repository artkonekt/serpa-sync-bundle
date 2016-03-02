<?php
/**
 * Contains the AbstractDataSource class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-01
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model;

use Konekt\SerpaSyncBundle\Model\Exception\FileNotFoundException;

/**
 * Represents a data source that loads data from files as PHP arrays.
 *
 */
abstract class AbstractDataSource
{

    /** @var string */
    protected $file;

    private function __construct() {}

    /**
     * Creates a new instance of the class.
     *
     * @param   string       $file   The file containing the sERPa data.
     *
     * @return  static
     *
     * @throws                       FileNotFoundException When the file does not exist.
     */
    public static function create($file)
    {
        if (!is_file($file) || is_dir($file)) {
            throw new FileNotFoundException("Data source file '$file' does not exist.");
        }

        $instance = new static();
        $instance->file = $file;

        return $instance;
    }

    /**
     * Loads the content of the file and builds a PHP array out of it.
     *
     * @return array
     */
    abstract public function getAsArray();

}
