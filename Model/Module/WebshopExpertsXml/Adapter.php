<?php
/**
* Contains the Adapter class.
*
* @author      Sandor Teglas
* @copyright   Copyright (c) 2016 Storm Storez Srl-d
* @license     Proprietary
* @version     2016-05-03
* @since       2016-04-29
*/

namespace Konekt\SerpaSyncBundle\Model\Module\WebshopExpertsXml;

use Konekt\SerpaSyncBundle\Model\AbstractAdapter;

/**
 * Translates products, taxonomies and stocks from files exported by the WebshopExperts XML module of sERPa into Sylius Sync Bundle remote models.
 *
 */
class Adapter extends AbstractAdapter
{

    /** @var  Translator */
    private $translator;

    /**
     * @inheritdoc
     */
    public function getRequiredFiles()
    {
        return ['Products.xml', 'SpecialPrice.xml', 'ProductTree.xml', 'Categories.xml', 'Stock.xml'];
    }

    public function getParserClass()
    {
        return Parser::class;
    }

}
