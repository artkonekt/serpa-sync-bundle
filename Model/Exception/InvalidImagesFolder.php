<?php
/**
 * Contains the InvalidImagesFolder class.
 *
 * @author      Sandor Teglas <sandor@artkonekt.com>
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @license     MIT
 * @since       2016-03-01
 * @version     2016-04-08
 */

namespace Konekt\SerpaSyncBundle\Model\Exception;

use Exception;

/**
 * Exception thrown when the folder containing images to import does not exist or it is not a folder.
 *
 */
class InvalidImagesFolder extends Exception
{

}
