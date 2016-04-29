<?php
/**
 * Contains the TaxonomyParser class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @license     MIT
 * @since       2016-04-29
 * @version     2016-04-29
 */

namespace Konekt\SerpaSyncBundle\Model\Parser\WebshopExpertsXml;

use Konekt\SerpaSyncBundle\Model\AbstractParser;
use Konekt\SerpaSyncBundle\Model\DataSource\XmlDataSource;

/**
 * Loads taxonomy data from the ProductTree.xml file.
 *
 */
class TaxonomyParser extends AbstractParser
{

    /**
     * @inheritdoc
     *
     */
    public function getAsArray()
    {
        $taxonomies = XmlDataSource::create($this->inputFiles->getFile('ProductTree.xml'))->getAsArray();

        if (!array_key_exists('Item', $taxonomies)) {
            return [];
        }

        return array_key_exists('@ID', $taxonomies['Item']) ? [$taxonomies['Item']] : $taxonomies['Item'];
    }

}
