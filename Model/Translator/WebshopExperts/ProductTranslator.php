<?php
/**
 * Contains the ProductTranslator class.
 *
 * @author      Sandor Teglas
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-03-31
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts;

use Konekt\SerpaSyncBundle\Model\AbstractTranslator;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductInterface;

class ProductTranslator extends AbstractTranslator
{

    /**
     * Translates array data to a remote product instance.
     *
     * @param   array $data
     *
     * @return  RemoteProductInterface
     */
    public function translate(array $data)
    {
        /** @var RemoteProductInterface $product */
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
     * @param   RemoteProductInterface   $product
     * @param   array                    $data
     *
     * @return  RemoteProductInterface
     */
    private function translateProperties(RemoteProductInterface $product, array $data)
    {
        $product->setSku($data['TermekKod']);
        $taxonIds = [];
        foreach (explode(',', $data['TermekFaID']) as $taxonId) {
            $trimmed = trim($taxonId);
            if (0 < strlen($trimmed)) {
                $taxonIds[] = trim($taxonId);
            }
        }
        $product->setTaxonIds($taxonIds);
        /** @var RemoteProductTranslationInterface $translation */
        $translation = $product->getTranslation($this->locale, true);
        $translation->setName($data['TermekNev']);
        $translation->setShortDescription($data['RovidLeiras']);
        $translation->setDescription($data['Leiras']);

        return $product;
    }

    /**
     * Translates price information.
     *
     * @param   RemoteProductInterface   $product
     * @param   array                    $data
     *
     * @return  RemoteProductInterface
     */
    private function translatePriceInfo(RemoteProductInterface $product, array $data)
    {
        $vatPercent = $data['Afakulcs'];

        $catalogPrice = $data['Ar'];
        $price = ('0' == $data['AkciosAr'] ? $data['Ar'] : $data['AkciosAr']);

        $product->setPrice($this->applyVatPercent($price, $vatPercent));
        $product->setCatalogPrice($this->applyVatPercent($catalogPrice, $vatPercent));

        return $product;
    }

    /**
     * Applies a VAT percent to a price.
     *
     * @param   $price        The price for which to apply the VAT percent.
     * @param   $vatPercent   The percent to apply
     *
     * @return  float         The new price, having the VAT percent applied.
     */
    private function applyVatPercent($price, $vatPercent)
    {
        return $price + ($price * $vatPercent / 100);
    }

    /**
     * Translates product attributes.
     *
     * @param   RemoteProductInterface   $product
     * @param   array                    $data
     *
     * @return  RemoteProductInterface
     */
    private function translateAttributes(RemoteProductInterface $product, array $data)
    {
        foreach ($data as $attributeName => $attributeValue) {
            /** @var RemoteAttributeInterface $attribute */
            $attribute = $product->getAttribute($attributeName, true);  // create on if not exists
            /** @var RemoteAttributeTranslationInterface $translation */
            $translation = $attribute->getTranslation($this->locale, true);  // Create and return one if not exist
            $translation->setValue($attributeValue);
        }

        return $product;
    }

}
