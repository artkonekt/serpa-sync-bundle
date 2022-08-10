<?php
/**
 * Contains the Parser class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-05-03
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model;

/**
 * Parses files exported by sERPa and returns a data ready to be translated to remote model instances.
 *
 */
class Parser
{

    /** @var InputFiles */
    protected $inputFiles;

    private function __construct() {}

    /**
     * Creates a new instance of the class.
     *
     * @param   InputFiles   $inputFiles   File containing data to parse.
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
     * Returns the list of files that are parsed.
     *
     * @return InputFiles
     */
    public function getInputFiles()
    {
        return $this->inputFiles;
    }

}
