<?php
/**
 * Contains the AbstractTranslator class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-04
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model;

/**
 * Translates array data to remote model instances.
 *
 */
abstract class AbstractTranslator
{

    /** @var RemoteFactories */
    protected $remoteFactories;

    /** @var  string */
    protected $locale;

    /**
     * Creates a new instance of the class.
     *
     * @param   RemoteFactories   $remoteFactories   Factories used to create remote mode instances.
     * @param   string            $locale            The locale of the translation into which to copy the translations.
     *
     * @return  static
     */
    public static function create(RemoteFactories $remoteFactories, $locale)
    {
        $instance = new static();
        $instance->remoteFactories = $remoteFactories;
        $instance->locale = $locale;

        return $instance;
    }

    /**
     * Translates array data to a remote model instance.
     *
     * @param   array   $data
     *
     * @return  mixed
     */
    abstract public function translate(array $data);

}
