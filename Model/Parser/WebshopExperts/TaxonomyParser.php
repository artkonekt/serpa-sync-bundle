<?php
/**
 * Contains the TaxonomyParser class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @license     MIT
 * @since       2016-03-01
 * @version     2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model\Parser\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\AbstractParser;
use Konekt\SerpaSyncBundle\Model\DataSource\TxtDataSource;

/**
 * Loads taxonomy data from the TermekFa.txt file.
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
        $taxonomies = TxtDataSource::create($this->inputFiles->getFile('TermekFa.txt'))->getAsArray();

        return $this->getTaxonChildrenByParentId(0, $taxonomies);
    }

    private function getTaxonChildrenByParentId($parentId, array $rawTaxonomies)
    {
        $res = [];
        foreach ($rawTaxonomies as $data) {
            if ($parentId == $data['SzuloID']) {
                $child = [
                    'ID' => $data['ID'],
                    'SzuloID' => $data['SzuloID'],
                    'LevelNev' => $data['LevelNev'],
                    'LevelLeiras' => $data['LevelLeiras']
                ];
                $child['children'] = $this->getTaxonChildrenByParentId($data['ID'], $rawTaxonomies);
                $res[] = $child;
            }
        }

        return $res;
    }

}
