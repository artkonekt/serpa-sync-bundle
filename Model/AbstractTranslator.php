<?php
/**
 * Contains the AbstractTranslator class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-01
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

    /**
     * Creates a new instance of the class.
     *
     * @param   RemoteFactories   $remoteFactories   Factories used to create remote mode instances.
     *
     * @return  static
     */
    public static function create(RemoteFactories $remoteFactories)
    {
        $instance = new static();
        $instance->remoteFactories = $remoteFactories;

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
