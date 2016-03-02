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

        /** @var RemoteTaxonomyInterface[] $taxonomy */
        $taxonomies = $adapter->fetchTaxonomies();

        ...

        }

    }

Adapter methods related to stocks still have to be implemented.

.. _sERPa: https://www.progen.hu
.. _Sylius Sync Bundle: https://github.com/artkonekt/sylius-sync-bundle
.. _modules: http://www.progen.hu/serpa/help/wk.htm
.. _module: http://www.progen.hu/serpa/help/wk.htm
.. _WebshopExperts: http://www.progen.hu/serpa/help/wk_webxhopexpertsinformacio.htm
