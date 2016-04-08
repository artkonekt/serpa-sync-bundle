<?php
/**
 * Contains the ProductTranslator class.
 *
 * @author      Sandor Teglas
 * @author      Hunor Kedves <hunor@artkonekt.com>
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-04-08
 * @since       2016-03-01
 */

namespace Konekt\SerpaSyncBundle\Model\Translator\WebshopExperts;

use AppBundle\Model\Serpa\Import\ImportException;
use Konekt\SerpaSyncBundle\Model\AbstractTranslator;
use Konekt\SerpaSyncBundle\Model\Exception\FileNotFoundException;
use Konekt\SerpaSyncBundle\Model\Exception\InvalidImagesFolder;
use Konekt\SyliusSyncBundle\Model\Remote\Image\RemoteImageInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductInterface;

class ProductTranslator extends AbstractTranslator
{

    /** @var  string */
    private $imagesFolder;

    /**
     * Sets the folder where product images are located.
     *
     * @param   string   $imagesFolder
     */
    public function setImagesFolder($imagesFolder)
    {
        $this->imagesFolder = $imagesFolder;
    }

    /**
     * Returns the folder where product images are located.
     *
     * @return string
     */
    public function getImagesFolder()
    {
        return $this->imagesFolder;
    }

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
        $this->translateImages($product, $data['product']);
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
     *
     * @throws  ImportException          If an invalid VAT percent is encountered.
     * @throws  ImportException          When the price or catalog price would result in zero after adding VAT and rounding.
     */
    private function translatePriceInfo(RemoteProductInterface $product, array $data)
    {
        $vatPercent = $data['Afakulcs'];

        if (0 > $vatPercent) {
            throw new ImportException("Invalid VAT percent detected for product {$product->getSku()}: {$vatPercent}.");
        }

        $price = ('0' == $data['AkciosAr'] ? $data['Ar'] : $data['AkciosAr']);
        $catalogPrice = $data['Ar'];

        $priceWithVat = $this->applyVatPercent($price, $vatPercent);
        $catalogPriceWithVat = $this->applyVatPercent($catalogPrice, $vatPercent);

        $roundedPrice = $this->roundPrice($priceWithVat);
        $roundedCatalogPrice = $this->roundPrice($catalogPriceWithVat);

        if (0 >= $roundedPrice) {
            throw new ImportException("Rounding the price {$price} plus VAT of {$vatPercent}% for product {$product->getSku()} would result in zero price.");
        }
        if (0 >= $roundedCatalogPrice) {
            throw new ImportException("Rounding the catalog price {$catalogPrice} plus VAT of {$vatPercent}% for product {$product->getSku()} would result in zero price.");
        }

        $product->setPrice($roundedPrice);
        $product->setCatalogPrice($roundedCatalogPrice);
        $product->setVatPercent($vatPercent);

        return $product;
    }

    /**
     * Rounds a price up or down to -1 decimals i.e it will be a multiply of 10.
     *
     * @param   $price
     *
     * @return  int
     */
    private function roundPrice($price)
    {
        // Rounding to -1 decimals rounds the last digit of the integer part to the nearest 10th
        return round($price, -1);
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

    /**
     * Translate product images.
     *
     * @param   RemoteProductInterface   $product
     * @param   array                    $data
     *
     * @return  RemoteProductInterface
     *
     * @throws  InvalidImagesFolder      When the images folder containing images exported by Serpa was not set.
     * @throws  FileNotFoundException    When an image file specified by Serpa is not found.
     */
    private function translateImages(RemoteProductInterface $product, array $data)
    {
        $imagesFolder = $this->getImagesFolder();
        if (empty($imagesFolder)) {
            throw new InvalidImagesFolder('The product images folder must be set before translating Serpa products to remote product instances.');
        }

        // Parsing images exported by Serpa
        $images = explode(',', $data['Kepnev']);
        foreach ($images as $image) {
            if (empty($image)) {
                continue;
            }
            /** @var RemoteImageInterface $remoteImage */
            $imagePath = $imagesFolder . '/' . $image;
            if (!file_exists($imagePath) || is_dir($imagePath)) {
                throw new FileNotFoundException("The image file '$imagePath' of product having SKU '{$product->getSku()}' was not found.");
            }
            $remoteImage = $this->remoteFactories->getImageFactory()->createFromFile($imagesFolder . '/' . $image);
            $product->addImage($remoteImage);
        }

        return $product;
    }
}
