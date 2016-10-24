<?php
/**
 * Contains the AbstractTranslator class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-05-03
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model;

use Konekt\SerpaSyncBundle\Model\Exception\InvalidParserException;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Stock\RemoteStockInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonomyInterface;

/**
 * Translates array data to remote model instances.
 *
 */
abstract class AbstractTranslator
{

    /** @var RemoteFactories */
    protected $remoteFactories;

    /** @var Parser */
    protected $parser;

    /** @var  string */
    protected $imageFolder;

    /** @var  string */
    protected $locale;

    protected function __construct() {}

    /**
     * Creates a new instance of the class.
     *
     * @param   RemoteFactories   $remoteFactories   Factories used to create remote mode instances.
     * @param   Parser            $parser            Supplies data from files exposted by Serpa.
     * @param   array             $imageFolders      The folders containing the image files of products.
     * @param   string            $locale            The locale of the remote objects into which translatable values are imported.
     *
     * @return  static
     */
    public static function create(RemoteFactories $remoteFactories, Parser $parser, array $imageFolders, $locale)
    {
        $instance = new static();
        $instance->remoteFactories = $remoteFactories;
        $instance->parser = $parser;
        $instance->imageFolders = $imageFolders;
        $instance->locale = $locale;

        return $instance;
    }

    /**
     * Returns the factories used to build remote object instances.
     *
     * @return  RemoteFactories
     */
    public function getRemoteFactories()
    {
        return $this->remoteFactories;
    }

    /**
     * Returns the parser instance that loads data from files exposrted by Serpa.
     *
     * @return  Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Returns the folders that contains product images.
     *
     * @return array
     */
    public function getImageFolders()
    {
        return $this->imageFolders;
    }

    /**
     * Returns the locale of the remote objects into which translatable values are imported.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Translates data supplied by the parser into remote instances.
     *
     * @return  mixed[]
     */
    abstract public function translate();

}
