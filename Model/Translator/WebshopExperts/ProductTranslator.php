<?php
/**
 * Contains the ProductTranslator class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-01
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\AbstractTranslator;
use Konekt\SyliusSyncBundle\Model\Remote\Product\Product;

class ProductTranslator extends AbstractTranslator
{

    /**
     * Translates array data to a remote product instance.
     *
     * @param   array $data
     *
     * @return  Product
     */
    public function translate(array $data)
    {
        $product = $this->remoteFactories->getProductFactory()->create();

        $this->translateProperties($product, $data['product']);
        if ($data['price']) {
            $this->translatePriceInfo($product, $data['price']);
        }
        if ($data['attributes']) {
            $this->translateAttributes($product, $data['attributes']);
        }

        return $product;
    }

    /**
     * Translates general product data.
     *
     * @param   Product   $product
     * @param   array     $data
     *
     * @return  Product
     */
    private function translateProperties(Product $product, array $data)
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
     * Translates price information.
     *
     * @param   Product   $product
     * @param   array     $data
     *
     * @return  Product
     */
    private function translatePriceInfo(Product $product, array $data)
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
     * Translates product attributes.
     *
     * @param   Product   $product
     * @param   array     $data
     *
     * @return  Product
     */
    private function translateAttributes(Product $product, array $data)
    {
        foreach ($data as $key => $value) {
            /** @var RemoteAttributeInterface $attribute */
            $attribute = $product->getAttribute($key, true);  // create on if not exists
            /** @var RemoteAttributeTranslationInterface $translation */
            $translation = $attribute->getTranslation('hu', true);  // Create and return one if not exist
            $translation->setName($value);
        }

        return $product;
    }

}
