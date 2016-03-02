<?php
/**
 * Contains the AbstractParser class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-01
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model;

/**
 * Parses files exported by sERPa and returns a PHP array containing data ready to be translated to remote model instances.
 *
 */
abstract class AbstractParser
{

    /** @var InputFiles */
    protected $inputFiles;

    private function __construct() {}

    /**
     * Creates a new instance of the class.
     *
     * @param   InputFiles   $inputFiles   File containing sERPa data.
     *
     * @return  static
     */
    public static function create(InputFiles $inputFiles)
    {
        $instance = new static();
        $instance->inputFiles = $inputFiles;

        return $instance;
    }

    /**
     * Parses the input files and returns data ready to be mapped to remote model instances.
     *
     * @return array
     */
    abstract public function getAsArray();

}
