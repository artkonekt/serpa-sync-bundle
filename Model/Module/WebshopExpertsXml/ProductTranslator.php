<?php
/**
 * Contains the ProductTranslator class.
 *
 * @author      Sandor Teglas
 * @author      Hunor Kedves <hunor@artkonekt.com>
 * @copyright   Copyright (c) 2016 Storm Storez Srl-d
 * @license     MIT
 * @version     2016-05-03
 * @since       2016-04-29
 */

namespace Konekt\SerpaSyncBundle\Model\Module\WebshopExpertsXml;

use AppBundle\Model\Serpa\Import\ImportException;
use Konekt\SerpaSyncBundle\Model\AbstractTranslator;
use Konekt\SerpaSyncBundle\Model\Exception\MultiplePriceException;
use Konekt\SerpaSyncBundle\Model\Exception\PriceNotFoundException;
use Konekt\SyliusSyncBundle\Model\Remote\Image\RemoteImageInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteAttributeInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteAttributeTranslationInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductInterface;
use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductTranslationInterface;

class ProductTranslator extends AbstractTranslator
{

    /** @var  array */
    private $specialPrices;

    /** @var  array */
    private $categories;

    /** @var  string */
    public $internetPriceKey;

    /** @var  string */
    public $storePriceKey;

    /**
     * Translates product from XML files to remote product instances.
     *
     * @return  RemoteProductInterface[]
     */
    public function translate()
    {
        $data = $this->collectProductData();
        
        return $this->createRemoteProducts($data);
    }

    /**
     * Creates remote product instances out of data collected from XML files.
     *
     * @param    array   $data
     *
     * @return   RemoteProductInterface
     */
    private function createRemoteProducts($data)
    {
        $res = [];

        foreach ($data as $item) {
            $product = $this->getRemoteFactories()->getProductFactory()->create();
            $this->assignBasicProperties($product, $item);
            $this->assignPrice($product, $item);
            $this->assignTaxons($product, $item);
            $this->assignImages($product, $item);
            $this->assignCategories($product, $item);
            $res[] = $product;
        }

        return $res;
    }

    /**
     * Assign basic properties to a remote object.
     *
     * @param    RemoteProductInterface   $product
     * @param    array                    $data
     *
     * @return   RemoteProductInterface
     */
    private function assignBasicProperties(RemoteProductInterface $product, $data)
    {
        $product->setSku($data['sku']);
        /** @var RemoteProductTranslationInterface $translation */
        $translation = $product->getTranslation($this->locale, true);
        $translation->setName($data['name']);
        $translation->setShortDescription($data['shortDescription']);
        $translation->setDescription($data['description']);

        return $product;
    }

    /**
     * Assign taxons to a remote object.
     *
     * @param    RemoteProductInterface   $product
     * @param    array                    $data
     *
     * @return   RemoteProductInterface
     */
    private function assignTaxons(RemoteProductInterface $product, $data)
    {
        $product->setTaxonIds($data['taxonIds']);
        
        return $product;
    }

    /**
     * Calculates, rounds and assigns price to a remote object.
     *
     * @param    RemoteProductInterface   $product
     * @param    array                    $data
     *
     * @return   RemoteProductInterface
     *
     * @throws   ImportException   If a negative VAT percent is detected.
     * @throws   ImportException   If a rounded price would get zero or negative.
     * @throws   ImportException   If a rounded catalog price would get zero or negative.
     */
    private function assignPrice(RemoteProductInterface $product, $data)
    {
        $vatPercent = $data['vatPercent'];

        if (0 > $vatPercent) {
            throw new ImportException("Invalid VAT percent detected for product {$data['sku']}: {$vatPercent}.");
        }

        $price = $data['internetPrice'];
        $catalogPrice = $data['storePrice'];
        $specialPrice = $data['specialPrice'];

        $priceWithVat = $this->applyVatPercent($price, $vatPercent);
        $catalogPriceWithVat = $this->applyVatPercent($catalogPrice, $vatPercent);
        $specialPriceWithVat = $specialPrice ? $this->applyVatPercent($specialPrice, $vatPercent) : null;

        $roundedPrice = $this->roundPrice($priceWithVat);
        $roundedCatalogPrice = $this->roundPrice($catalogPriceWithVat);
        $roundedSpecialPrice = $specialPriceWithVat ? $this->roundPrice($specialPriceWithVat) : null;

        if (0 >= $roundedPrice) {
            throw new ImportException("Rounding the internet price {$price} plus VAT of {$vatPercent}% for product {$data['sku']} would result in zero price.");
        }
        if (0 >= $roundedCatalogPrice) {
            throw new ImportException("Rounding the store price {$catalogPrice} plus VAT of {$vatPercent}% for product {$data['sku']} would result in zero price.");
        }
        if ($roundedSpecialPrice && 0 >= $roundedSpecialPrice) {
            throw new ImportException("Rounding the special price {$specialPrice} plus VAT of {$vatPercent}% for product {$data['sku']} would result in zero price.");
        }

        $product->setPrice($roundedPrice);
        $product->setCatalogPrice($roundedCatalogPrice);
        $product->setSpecialPrice($roundedSpecialPrice);
        $product->setVatPercent($vatPercent);

        return $product;
    }

    /**
     * Asssign product images to a remote product.
     *
     * @param   RemoteProductInterface   $product
     * @param   array                    $data
     *
     * @return  RemoteProductInterface
     */
    private function assignImages(RemoteProductInterface $product, array $data)
    {
        foreach ($data['images'] as $image) {
            /** @var RemoteImageInterface $remoteImage */
            $imagePath = $this->getImageFolder() . DIRECTORY_SEPARATOR . $image;
            $remoteImage = $this->getRemoteFactories()->getImageFactory()->createFromFile($imagePath);
            $product->addImage($remoteImage);
        }

        return $product;
    }

    /**
     * Assign attributes to a remote product.
     *
     * @param   RemoteProductInterface   $product
     * @param   array                    $data
     *
     * @return  RemoteProductInterface
     */
    private function assignCategories(RemoteProductInterface $product, array $data)
    {
        foreach ($data['categories'] as $attributeName => $attributeValue) {
            /** @var RemoteAttributeInterface $attribute */
            $attribute = $product->getAttribute($attributeName, true);  // create on if not exists
            /** @var RemoteAttributeTranslationInterface $translation */
            $translation = $attribute->getTranslation($this->locale, true);  // Create and return one if not exist
            $translation->setValue($attributeValue);
        }

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
     * Collects required data from products, special proces and categories XML arrays into a single array.
     *
     * @throws   MultiplePriceException   If there is at least one product that has multiple prices specified.
     */
    private function collectProductData()
    {
        $res = [];

        $specialPrices = $this->collectSpecialPrices();

        foreach ($this->parser->getProducts() as $data) {
            $id = $data['@ID'];
            $sku = $data['Code'];
            $specialPrice = $this->collectSpecialPriceOfProduct($id, $specialPrices);

            $internetPrice = $this->extractPriceFromPriceList($data['Prices']['Price'], $this->internetPriceKey);
            if (is_null($internetPrice)) {
                throw new PriceNotFoundException("Price of type `{$this->internetPriceKey}` was not found.");
            }
            $storePrice = $this->extractPriceFromPriceList($data['Prices']['Price'], $this->storePriceKey);
            if (is_null($storePrice)) {
                throw new PriceNotFoundException("Price of type `{$this->storePriceKey}` was not found.");
            }

            $res[$sku] = [
                'id' => $id,
                'sku' => $sku,
                'name' => $data['Name'],
                'shortDescription' => isset($data['ShortDescription']) ? $data['ShortDescription'] : null,
                'description' => isset($data['Description']) ? $data['Description'] : null,
                'vatPercent' => $data['VATPercent'],
                'internetPrice' => $internetPrice,
                'storePrice' => $storePrice,
                'specialPrice' => $specialPrice ? $specialPrice : null,
                'taxonIds' => $this->collectTaxonIdsOfProduct($data),
                'images' => $this->collectImagesOfProduct($data),
                'categories' => $this->collectCategoriesOfProduct($data)
            ];
        }

        return $res;
    }

    private function extractPriceFromPriceList(array $list, $type)
    {
        foreach ($list as $priceInfo) {
            if ($type == $priceInfo['@Type']) {
                return $priceInfo['Value'];
            }
        }

        return null;
    }

    /**
     * Collecte the special prices from XML array into a plain array having product ID as key and
     * promo price as value.
     *
     * @return array
     */
    private function collectSpecialPrices()
    {
        $res = [];

        foreach ($this->parser->getSpecialPrices() as $item) {
            $id = $item['Product']['@ID'];
            $res[$id] = $item['Price'];
        }

        return $res;
    }


    /**
     * Returns the list of image file names associated with a product.
     *
     * @param    $data   The array parsed from XML data.
     *
     * @return   array
     */
    private function collectImagesOfProduct($data)
    {
        $res = [];

        if (isset($data['Images'])) {
            $imagesArray = isset($data['Images']['Image']['@Path']) ? [$data['Images']['Image']] : $data['Images']['Image'];
            foreach ($imagesArray as $imageData) {
                $res[] = basename($imageData['@Path']);
            }
        }

        return $res;
    }

    /**
     * Returns the special price of a product or null if it is not defined.
     *
     * @param    $id             The array parsed from XML data.
     * @param    $specialPrices   The list of special prices having product ids as keys.
     *
     * @return   float|null
     */
    private function collectSpecialPriceOfProduct($id, array $specialPrices)
    {
        if (!$this->specialPrices) {
            $this->specialPrices = $this->collectSpecialPrices();
        }

        return isset($specialPrices[$id]) ? $specialPrices[$id] : null;
    }

    /**
     * Returns the list of taxosn ids the product belongs to,.
     *
     * @param    $data   The product data parsed from XML.
     *
     * @return   array
     */
    private function collectTaxonIdsOfProduct($data)
    {
        $res = [];

        if (isset($data['ProductTrees'])) {
            $array = isset($data['ProductTrees']['Item']['@ID']) ? [$data['ProductTrees']['Item']] : $data['ProductTrees']['Item'];;
            foreach ($array as $values) {
                $res[] = $values['@ID'];
            }
        }

        return $res;
    }

    /**
     * Collects attributes and their existing values from Categories.xml into a plain array.
     *
     * The key is the id of the attribute (ex: 2 is Palackozo)
     * Values are arrays with the following keys:
     * - name: name of attribute (Palackozo)
     * - values: id value pairs, value is attribute value, id is ItemId from products
     *
     * @return array
     */
    private function collectCategories()
    {
        $res = [];

        foreach ($this->parser->getCategories() as $item) {
            $container = $item['ProductCategories']['ProductCategory'];
            $id = $container['@ID'];
            $name = $container['Name'];
            $valueList = isset($container['Items']['Item']['@ID']) ? [$container['Items']['Item']] : $container['Items']['Item'];
            $values = [];
            foreach ($valueList as $valueDetails) {
                $values[$valueDetails['@ID']] = $valueDetails['Name'];
            }
            $res[$id] = ['name' => $name, 'values' => $values];
        }

        return $res;
    }

    /**
     * Returns the category name and value pairs for a product.
     * A product has all categories enaumerated in Product.xml but only a subset of values are exported to Categries.xml
     * as specified in Serpa. Only those will be collected that have values exported in Categories.xml.
     *
     * @param    $data    XLM data of product
     *
     * @return   array
     */
    private function collectCategoriesOfProduct($data)
    {
        if (!$this->categories) {
            $this->categories = $this->collectCategories();
        }

        if (!isset($data['Categories']['Category'])) {
            return [];
        }

        $res = [];

        // Getting the categories into an array
        $productCategories = isset($data['Categories']['Category']['@ID']) ? [$data['Categories']['Category']] : $data['Categories']['Category'];

        // Extracting the category name and value from the possible category values parsed from Categories.xml
        foreach ($productCategories as $values) {
            $categoryId = $values['@ID'];  // this is the ID of the attribute (2 for Palackozo)
            $valueId = $values['@ItemID']; // this is the ID of value of category
            if (isset($this->categories[$categoryId]['values'][$valueId])) {
                $categoryName = $this->categories[$categoryId]['name'];
                $categoryValue = $this->categories[$categoryId]['values'][$valueId];
                $res[$categoryName] = $categoryValue;
            }
        }

        return $res;
    }

}
