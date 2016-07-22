<?php
/**
 * Contains the PriceNotFoundException class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @license     MIT
 * @since       2016-07-21
 * @version     2016-07-21
 */

namespace Konekt\SerpaSyncBundle\Model\Exception;

use Exception;

/**
 * Exception thrown when an expected price is not found inside a product data.
 *
 */
class PriceNotFoundException extends Exception
{

}
