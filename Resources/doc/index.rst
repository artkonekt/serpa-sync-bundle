Konekt Serpa Sync Bundle
========================

`sERPa`_ ERP has the ability to export product, taxonomy, prices and stock related information and to make them available
for other systems, typically web shops. Exporting a set of files is done by a web shop module that is configured inside
`sERPa`_. There are different type of web shop `modules`_, each one exporting files in specific format and structure,
suitable for different web shops.

This bundle makes possible parsing files exported by `sERPa`_'s different web shop `modules`_ and building up a standardized
set of models as defined in `Sylius Sync Bundle`_.

Usage In Client Code
--------------------

Each `module`_'s implementation exposes an adapter class that makes the translation. Currently only the `WebshopExperts`_
module is implemented.

The client code must instantiate the appropriate module's implementation and fetch products, taxonomies and stocks.

Using the `WebshopExperts`_'s implementation should be made as follows:

.. code-block:: php

    use Konekt\SerpaSyncBundle\Model\Adapter\WebshopExpertsAdapter;
    use Konekt\SyliusSyncBundle\Model\Remote\Product\RemoteProductInterface;
    use Konekt\SyliusSyncBundle\Model\Remote\Taxonomy\RemoteTaxonomyInterface;
    use Konekt\SyliusSyncBundle\Model\Remote\Stock\RemoteStockInterface;

    ...

    class YourController extends OtherController
    {

        public function yourAction()
        {

            // The order of sERPa files does not matter but all of them should be specified

            $adapter = WebshopExpertsAdapter::create(
                $this->get('konekt_sylius_sync.remote_product.factory'),
                $this->get('konekt_sylius_sync.remote_image.factory'),
                $this->get('konekt_sylius_sync.remote_taxonomy.factory'),
                $this->get('konekt_sylius_sync.remote_taxon.factory'),
                $this->get('konekt_sylius_sync.remote_stock.factory'),
                [
                    '/path/to/serpa/webshop_experts_data/Termek.txt',
                    '/path/to/serpa/webshop_experts_data/TermekAR.txt',
                    '/path/to/serpa/webshop_experts_data/TermekFa.txt',
                    '/path/to/serpa/webshop_experts_data/TermekKategoria.txt',
                    '/path/to/serpa/webshop_experts_data/TermekKeszlet.txt'
                ]
            );

        /** @var RemoteProductInterface[] $product */
        $products = $adapter->fetchProducts();

        /** @var RemoteTaxonomyInterface[] $taxonomies */
        $taxonomies = $adapter->fetchTaxonomies();

        /** @var RemoteTaxonomyInterface $taxonomy */
        $taxonomy = $adapter->fetchTaxonomy(123);

        /** @var RemoteStockInterface[] $stocks */
        $stocks = $adapter->fetchStocks();

        ...

        }

    }

Adapter methods related to stocks still have to be implemented.

Implementing modules
--------------------

Let's stick to some naming conventions and use them:

- ``ModuleProvider`` represents the name of a module you implement (like ``WebshopExpertsXml``)
- an input file is a file exported by `sERPa`_ using the module you just implement

Implementing a new adapter for a module must be done by the following steps:

- Depending on the input file's format, check if there is a suitable parser implemented in Model/DataSource folder.
  If required, implement yours: all you have to do is to return a PHP array (hierarchical if required) containing the
  data source file content.

- Implement a product parser in ``Model/Parser/[ModuleProvider]/ProductParser.php`` that extends ``Model/AbstractParser.php``
  and has knowledge about which are the files containing products, prices and product attributes and which type of
  data source to use to load them. Important is to load the product, price and attributes in a format of your
  preference, you can use PHP arrays.

- Implement a taxonomy parser in ``Model/Parser/[ModuleProvider]/TaxonomyParser.php`` that extends ``Model/AbstractTranslator.php``
  and has knowledge about which are the files containing taxonomies and taxons and which type of data source to use to
  load them. Important is to load the taxonomies and their taxons in a hierarchical format of your preference, you can
  use PHP arrays.

- Implement a stock parser in ``Model/Parser/[ModuleProvider]/StockParser.php`` that extends ``Model/AbstractParser.php``
  and has knowledge about which are the files containing stocks and which type of data source to use to load them.
  Important is to load the stocks in a format of your preference, you can use PHP arrays.

- Implement a product translator in ``Model/Translator/[ModuleProvider]/ProductTranslator.php`` that extends
  ``Model/AbstractTranslator.php`` and has knowledge about the format of your product parser's output and creates remote
  product models defined in `Sylius Sync Bundle`_ using factories exposed by `Sylius Sync Bundle`_. The translator
  takes a single item from the parser's output and translates it.

- Implement a taxonomy translator in ``Model/Translator/[ModuleProvider]/TaxonomyTranslator.php`` that extends
  ``Model/AbstractTranslator.php`` and has knowledge about the format of your taxonomy parser's output and creates remote
  taxonomy models defined in `Sylius Sync Bundle`_ using factories exposed by `Sylius Sync Bundle`_. The translator
  takes a single item from the parser's output and translates it.

- Implement a stock translator in ``Model/Translator/[ModuleProvider]/StockTranslator.php`` that extends
  ``Model/AbstractTranslator.php`` and has knowledge about the format of your stock parser's output and creates remote
  stock models defined in `Sylius Sync Bundle`_ using factories exposed by `Sylius Sync Bundle`_. The translator
  takes a single item from the parser's output and translates it.

- Implement your ``Model/Adapter/[ModuleProvider]Adapter.php`` that extends ``Model/AbstractAdapter.php`` and has knowledge about
  the input file names generated by ModuleProvider you implement and about product, taxonomy and stock parsers and translators
  implemented in the previous steps.

Once you finished, you can use your new adapter by creating a new instance of ``Model/Adapter/[ModuleProvider]Adapter.php``
and fetching products, taxonomies and stocks out of it.

.. _sERPa: https://www.progen.hu
.. _Sylius Sync Bundle: https://github.com/artkonekt/sylius-sync-bundle
.. _modules: http://www.progen.hu/serpa/help/wk.htm
.. _module: http://www.progen.hu/serpa/help/wk.htm
.. _WebshopExperts: http://www.progen.hu/serpa/help/wk_webxhopexpertsinformacio.htm
