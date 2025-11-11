<?php
/**
 * Home Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/../models/Product.php';

class HomeController extends BaseController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    /**
     * Home page
     */
    public function index() {
        // Get featured products
        $featuredProducts = $this->productModel->getFeatured(8);

        // Get new products
        $newProducts = $this->productModel->getNew(8);

        // Get bestsellers
        $bestsellers = $this->productModel->getBestsellers(4);

        $this->view('home/index', [
            'title' => 'Ana Sayfa',
            'featured_products' => $featuredProducts,
            'new_products' => $newProducts,
            'bestsellers' => $bestsellers,
            'meta_description' => 'MINALIA - Lüks parfüm deneyimi. En seçkin markaların parfümleri, kampanyalı fiyatlarla.',
            'meta_keywords' => 'parfüm, lüks parfüm, erkek parfüm, kadın parfüm, niş parfüm'
        ]);
    }
}
