<?php
/**
 * Contains the TaxonomyTranslator class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-01
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\AbstractTranslator;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxon;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\Taxonomy;
use Sylius\Component\Taxonomy\Model\TaxonomyTranslation;

class TaxonomyTranslator extends AbstractTranslator
{

    /**
     * Translates array data to a remote taxonomy instance.
     *
     * @param   array $data
     *
     * @return  Taxonomy
     */
    public function translate(array $data)
    {
        $taxonomy = $this->remoteFactories->getTaxonomyFactory()->create();

        $taxonomy->setId($data['ID']);
        /** @var TaxonomyTranslation $translation */
        $translation = $taxonomy->getTranslation('hu', true);
        $translation->setName($data['LevelNev']);

        $taxons = $this->translateTaxonsRecursively($data['children'], $taxonomy);
        foreach ($taxons as $taxon) {
            $taxon->setTaxonomy($taxonomy);
            $taxonomy->addTaxon($taxon);
        }

        return $taxonomy;
    }

    /**
     * Translates array data to a remote taxon instance.
     *
     * @param   array $data
     *
     * @return  Taxon
     */
    private function translateTaxon(array $data)
    {
        $taxon = $this->remoteFactories->getTaxonFactory()->create();

        $taxon->setId($data['ID']);
        /** @var Taxon $translation */
        $translation = $taxon->getTranslation('hu', true);
        $translation->setName($data['LevelNev']);

        return $taxon;
    }

    /**
     * Recursively translates a list of taxon data arrays to taxon instances.
     *
     * @param   array      $data       The array of taxons data containing the taxons to map.
     * @param   Taxonomy   $taxonomy   The taxonomy to which the taxon belongs directly or indirectly.
     *
     * @return  Taxon[]
     */
    private function translateTaxonsRecursively(array $data, Taxonomy $taxonomy)
    {
        $taxons = [];

        foreach ($data as $taxonData) {
            $taxon = $this->translateTaxon($taxonData);
            /** @var Taxon $childTaxon */
            $childTaxons = $this->translateTaxonsRecursively($taxonData['children'], $taxonomy);
            foreach ($childTaxons as $childTaxon) {
                $childTaxon->setParent($taxon);
                $childTaxon->setTaxonomy($taxonomy);
                $taxon->addChild($childTaxon);
            }
            $taxons[] = $taxon;
        }

        return $taxons;
    }

}
