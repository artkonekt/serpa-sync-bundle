<?php
/**
 * Contains the TaxonomyTranslator class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-05-03
 * @since       2016-04-29
 */

namespace Konekt\SerpaSyncBundle\Model\Module\WebshopExpertsXml;

use Konekt\SerpaSyncBundle\Model\AbstractTranslator;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonomyInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonTranslationInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxon;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonFactory;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\TaxonomyFactory;

class TaxonomyTranslator extends AbstractTranslator
{

    /**
     * Translates taxonomies and taxons from XML files to remote taxonomy instances having remote taxons as children.
     *
     * @return  RemoteTaxonomyInterface[]
     */
    public function translate()
    {
        $res = [];

        $collected = $this->collect();
        foreach ($collected as $item) {
            $taxonomy = $this->createRemoteTaxonomy($item['id'], $item['name'], $this->locale);
            if (isset($item['children'])) {
                foreach ($item['children'] as $taxonChild) {
                    $taxon = $this->createRemoteTaxon($taxonChild['id'], $taxonChild['name'], isset($taxonChild['children']) ? $taxonChild['children'] : null, $taxonomy);
                    $taxon->setTaxonomy($taxonomy);
                    $taxonomy->addTaxon($taxon);
                }
            }

            $res[] = $taxonomy;
        }

        return $res;
    }

    /**
     * Collects data out of XML array into a hierarchical array with taxonomies on the first level.
     *
     * @return  array
     */
    private function collect()
    {
        $res = [];

        foreach ($this->parser->getProductTree() as $item) {
            $taxonomy = ['id' => $item['@ID'], 'name' => $item['@Name']];
            if (isset($item['Item'])) {
                // this taxonomy has taxon children
                $childredArray = isset($item['Item']['@ID']) ? [$item['Item']] : $item['Item'];
                $taxonomy['children'] = $this->collectTaxons($childredArray);
            }
            $res[] = $taxonomy;
        }

        return $res;
    }

    /**
     * Recursively collects a list of taxons from an array
     *
     * @param   array   $data
     *
     * @return  array
     */
    private function collectTaxons(array $data)
    {
        $res = [];

        foreach ($data as $item) {
            $taxon = ['id' => $item['@ID'], 'name' => $item['@Name']];
            if (isset($item['Item'])) {
                // this taxon has other taxon children
                $childredArray = isset($item['Item']['@ID']) ? [$item['Item']] : $item['Item'];
                $taxon['children'] = $this->collectTaxons($childredArray);
            }
            $res[] = $taxon;
        }

        return $res;
    }

    /**
     * Creates a remote taxonomy instance.
     *
     * @param   $id                       The ID of the remote taxonomy.
     * @param   $name                     The name of the remote taxonomy.
     *
     * @return RemoteTaxonomyInterface
     */
    private function createRemoteTaxonomy($id, $name)
    {
        /** @var RemoteTaxonomyInterface $res */
        $res = $this->remoteFactories->getTaxonomyFactory()->create();
        $res->setId($id);
        /** @var TaxonomyTranslation $translation */
        $translation = $res->getTranslation($this->locale, true);
        $translation->setName($name);

        return $res;
    }

    /**
     * Creates a remote taxon instance.
     *
     * @param   $id                       The ID of the remote taxon.
     * @param   $name                     The name of the remote taxon.
     * @param   $children                 The children taxons of the taxon.
     * @param   RemoteTaxonomyInterface   The taxonomy to which ths taxon belongs.
     *
     * @return  RemoteTaxonInterface
     */
    private function createRemoteTaxon($id, $name, $children, RemoteTaxonomyInterface $taxonomy)
    {
        /** @var RemoteTaxonInterface $res */
        $res = $this->remoteFactories->getTaxonFactory()->create();
        $res->setId($id);
        /** @var TaxonTranslation $translation */
        $translation = $res->getTranslation($this->locale, true);
        $translation->setName($name);

        if ($children) {
            foreach ($children as $child) {
                $childTaxon = $this->createRemoteTaxon($child['id'], $child['name'], isset($child['children']) ? $child['children'] : null, $taxonomy);
                $childTaxon->setParent($res);
                $childTaxon->setTaxonomy($taxonomy);
                $res->addChild($childTaxon);
            }
        }

        return $res;
    }

}
