<?php
/**
 * Contains class Mapper.
 *
 * @copyright   Copyright (c) 2016 Storm Storez Srl
 * @author      Sandor Teglas
 * @license     MIT
 * @since       2016-02-29
 * @version     2016-02-29
 */

namespace Konekt\SerpaSyncBundle\Model;

use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteAttributeInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteAttributeTranslationInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductTranslationInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonomyInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonomyTranslationInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonTranslationInterface;

/**
 * Maps data coming from sERPa to Sylius Sync Bundle models.
 *
 * @package AppBundle\Model\Serpa
 */
class Mapper
{

    private function __construct() {}

    /**
     * Creates a new instance of the class.
     *
     * @return  static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Maps product data coming from sERPa to a Sync Bundle product instance.
     *
     * @param   RemoteProductInterface   $product   The Sync Bundle product instance.
     * @param   array                    $data      The array containing the product data.
     *
     * @return RemoteProductInterface
     */
    public function mapProduct(RemoteProductInterface $product, array $data)
    {
        $this->mapProductProperties($product, $data['product']);

        if ($data['price']) {
            $this->mapProductPrice($product, $data['price']);
        }

        if ($data['attributes']) {
            $this->mapProductAttributes($product, $data['attributes']);
        }

        return $product;
    }

    /**
     * Maps taxonomy data coming from sERPa to a Sync Bundle product instance.
     *
     * @param   RemoteTaxonomyInterface   $taxonomy   The Sync Bundle taxonomy instance.
     * @param   array                     $data       The array containing the taxonomy data.
     *
     * @return RemoteProductInterface
     */
    public function mapTaxonomy(RemoteTaxonomyInterface $taxonomy, array $data)
    {
        $taxonomy->setId($data['ID']);
        /** @var RemoteTaxonomyTranslationInterface $translation */
        $translation = $taxonomy->getTranslation('hu', true);
        $translation->setName($data['LevelNev']);

        return $taxonomy;
    }

    /**
     * Maps taxon data coming from sERPa to a Sync Bundle taxon instance.
     *
     * @param   RemoteTaxonInterface   $taxon   The Sync Bundle taxon instance.
     * @param   array                  $data    The array containing the taxon data.
     *
     * @return RemoteProductInterface
     */
    public function mapTaxon(RemoteTaxonInterface $taxon, array $data)
    {
        $taxon->setId($data['ID']);
        /** @var RemoteTaxonTranslationInterface $translation */
        $translation = $taxon->getTranslation('hu', true);
        $translation->setName($data['LevelNev']);

        return $taxon;
    }

    /**
     * Maps general product data coming from sERPa to a Sync Bundle product instance.
     *
     * @param   RemoteProductInterface   $product
     * @param   array                    $data
     *
     * @return  RemoteProductInterface
     */
    private function mapProductProperties(RemoteProductInterface $product, array $data)
    {
        $product->setSku($data['TermekKod']);
        /** @var RemoteProductTranslationInterface $translation */
        $translation = $product->getTranslation('hu', true);
        $translation->setName($data['TermekNev']);
        $translation->setShortDescription($data['RovidLeiras']);
        $translation->setDescription($data['Leiras']);

        return $product;
    }

    /**
     * Maps prices coming from sERPa to a Sync Bundle product instance.
     *
     * @param   RemoteProductInterface   $product
     * @param   array                    $data
     *
     * @return  RemoteProductInterface
     */
    private function mapProductPrice(RemoteProductInterface $product, array $data)
    {
        if ('0' == $data['AkciosAr']) {
            $product->setPrice($data['Ar']);
            $product->setCatalogPrice($data['Ar']);
        } else {
            $product->setPrice($data['AkciosAr']);
            $product->setCatalogPrice($data['Ar']);
        }

        return $product;
    }

    /**
     * Maps product attributes coming from sERPa to a Sync Bundle product instance.
     *
     * @param   RemoteProductInterface   $product
     * @param   array                    $data
     *
     * @return  RemoteProductInterface
     */
    private function mapProductAttributes(RemoteProductInterface $product, array $data)
    {
        // The first column is the product code, the rest of them are all attributes
        $columnProcessed = 0;
        foreach ($data as $key => $value) {
            if (0 < $columnProcessed) {  // Ignoring the first column
                /** @var RemoteAttributeInterface $attribute */
                $attribute = $product->getAttribute($key, true);  // create on if not exists
                /** @var RemoteAttributeTranslationInterface $translation */
                $translation = $attribute->getTranslation('hu', true);  // Create and return one if not exist
                $translation->setName($value);
            }
            $columnProcessed++;
        }

        return $product;
    }

}