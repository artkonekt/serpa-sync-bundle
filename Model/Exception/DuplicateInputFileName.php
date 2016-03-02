<?php
/**
 * Contains the DuplicateInputFileName class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @license     MIT
 * @since       2016-03-01
 * @version     2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model\Exception;

use Exception;

/**
 * Exception thrown when an input file name is duplicate (ignoring the path portion).
 *
 */
class DuplicateInputFileName extends Exception
{

}
