<?php
/**
 * Contains class DuplicateInputFileName.
 *
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @author      Sandor Teglas
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
