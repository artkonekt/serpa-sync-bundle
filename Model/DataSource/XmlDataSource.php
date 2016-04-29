<?php
/**
 * Contains the XmlDataSource class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     Proprietary
 * @version     2016-04-29
 * @since       2016-04-29
 */

namespace Konekt\SerpaSyncBundle\Model\DataSource;

use FOS\RestBundle\Decoder\XmlDecoder;
use Konekt\SerpaSyncBundle\Model\AbstractDataSource;

/**
 * Represents an XML file.
 *
 */
class XmlDataSource extends AbstractDataSource
{

    /**
     * @inheritdoc
     *
     */
    public function getAsArray()
    {
        $decoder = new XmlDecoder();
        $result = $decoder->decode(file_get_contents($this->file));
        
        return $result ? $result : [];
    }

}
