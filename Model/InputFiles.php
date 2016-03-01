<?php
/**
 * Contains class InputFiles.
 *
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @author      Sandor Teglas
 * @license     MIT
 * @since       2016-02-29
 * @version     2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model;

use Konekt\SerpaSyncBundle\Model\Exception\DuplicateInputFileName;
use Konekt\SerpaSyncBundle\Model\Exception\InvalidInputFile;

/**
 * Represents a list of files exported by sERPa.
 *
 */
class InputFiles
{

    /** @var  string  Holds file paths by file names as keys. */
    private $files = [];

    /**
     * Creates a new instance of the class.
     *
     * @param   array   $files   The list of files exported by sERPa.
     *
     * @return  static
     */
    public static function create(array $files)
    {
        $instance = new static();

        foreach ($files as $file) {
            $instance->import($file);
        }

        return $instance;
    }

    /**
     * Returns true if a file exists, false otherwise.
     *
     * @param $fileName The name of the file whose existence to check.
     *
     * @return bool
     */
    public function fileExists($fileName)
    {
        return array_key_exists($fileName, $this->files);
    }

    /**
     * Gets the file path by its name or null if the file does not exist.
     *
     * @param $fileName The file name identifying the file.
     *
     * @return null|string
     */
    public function getFile($fileName)
    {
        return $this->fileExists($fileName) ? $this->files[$fileName] : null;
    }

    /**
     * Imports a file. The file name will be stored as the key, the parameter as the value.
     *
     * @param $file The file name with or without path information.
     *
     * @throws InvalidInputFile  When the file name could not be determined.
     * @throws DuplicateInputFileName  When a file with this name was already added.
     */
    private function import($file)
    {
        // Making sure we always have a string
        $stringFile = trim($file);

        $key = pathinfo($stringFile ,PATHINFO_BASENAME);

        if (0 == strlen(trim($key))) {
            throw new InvalidInputFile("File name could not be extracted from $stringFile.");
        }

        if ($this->fileExists($key)) {
            throw new DuplicateInputFileName("File $key was already added.");
        }

        $this->files[$key] = $file;
    }

}
