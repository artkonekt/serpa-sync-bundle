Konekt Serpa Sync Bundle
========================

Usage In Client Code
--------------------

A new instance of Model\Adapter class must be created than the fetchProducts() method must be called. It will return a
list of product model classes defined in `Sylius Sync Bundle`_.

The Sylius sync Bundle defines factory services that can be used to create the required model instances. Using them
from inside a controller is straightforward as shown below. The last 5 parameters represents the .txt files exported by
the `WebshopExperts`_' module configured in `sERPa`_.

.. code-block:: php

    class YourController extends OtherController
    {

        public function mainAction()
        {
            /** @var Konekt\SerpaSyncBundle\Model\Adapter $products */
            $serpaAdapter = new Konekt\SerpaSyncBundle\Model\Adapter(
                $this->get('konekt_sylius_sync.remote_product.factory'),
                $this->get('konekt_sylius_sync.remote_image.factory'),
                $this->get('konekt_sylius_sync.remote_taxonomy.factory'),
                '/location/Termek.txt',
                '/location/TermekAR.txt',
                '/location/TermekFa.txt',
                '/location/TermekKategoria.txt',
                '/location/TermekKeszlet.txt'
            );

            /** @var Konekt\SyliusSyncBundle\Model\Remote\Product\Product[] $products */
            $products = $serpaAdapter->fetchProducts();

            return $this->render('your_template.html.twig');
        }

    }

Adapter methods related to taxonomies and stocks still to be implemented.

.. _Sylius Sync Bundle: https://github.com/artkonekt/sylius-sync-bundle
.. _WebshopExperts: http://www.progen.hu/serpa/help/wk_webxhopexpertsinformacio.htm
.. _sERPa: https://www.progen.hu
