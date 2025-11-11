<?php
/**
 * Admin Panel Entry Point
 * MINALIA Parfüm E-Ticaret Platformu
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../app/helpers/functions.php';

// Admin-specific constants
define('ADMIN_URL', BASE_URL . '/admin');
define('ADMIN_VIEWS_PATH', __DIR__ . '/views');

// Check if admin is logged in
function requireAdminAuth() {
    if (!isset($_SESSION[SESSION_ADMIN_ID])) {
        redirect(ADMIN_URL . '/login');
        exit;
    }
}

// Simple admin router
$request = trim($_SERVER['REQUEST_URI'], '/');
$request = str_replace('admin/', '', $request);
$request = str_replace('admin', '', $request);
$parts = explode('?', $request);
$path = trim($parts[0], '/');

// Route mapping
$routes = [
    '' => 'AdminDashboardController@index',
    'login' => 'AdminAuthController@login',
    'logout' => 'AdminAuthController@logout',
    'dashboard' => 'AdminDashboardController@index',

    // Products
    'products' => 'AdminProductController@index',
    'products/create' => 'AdminProductController@create',
    'products/edit' => 'AdminProductController@edit',
    'products/delete' => 'AdminProductController@delete',
    'products/upload-image' => 'AdminProductController@uploadImage',

    // Categories
    'categories' => 'AdminCategoryController@index',
    'categories/create' => 'AdminCategoryController@create',
    'categories/edit' => 'AdminCategoryController@edit',
    'categories/delete' => 'AdminCategoryController@delete',

    // Brands
    'brands' => 'AdminBrandController@index',
    'brands/create' => 'AdminBrandController@create',
    'brands/edit' => 'AdminBrandController@edit',
    'brands/delete' => 'AdminBrandController@delete',

    // Orders
    'orders' => 'AdminOrderController@index',
    'orders/view' => 'AdminOrderController@view',
    'orders/update-status' => 'AdminOrderController@updateStatus',

    // Customers
    'customers' => 'AdminCustomerController@index',
    'customers/view' => 'AdminCustomerController@view',

    // Reviews
    'reviews' => 'AdminReviewController@index',
    'reviews/approve' => 'AdminReviewController@approve',
    'reviews/delete' => 'AdminReviewController@delete',

    // Coupons
    'coupons' => 'AdminCouponController@index',
    'coupons/create' => 'AdminCouponController@create',
    'coupons/edit' => 'AdminCouponController@edit',
    'coupons/delete' => 'AdminCouponController@delete',

    // Loyalty
    'loyalty' => 'AdminLoyaltyController@index',
    'loyalty/adjust-points' => 'AdminLoyaltyController@adjustPoints',

    // Newsletter
    'newsletter' => 'AdminNewsletterController@index',
    'newsletter/send' => 'AdminNewsletterController@send',
    'newsletter/toggle-status' => 'AdminNewsletterController@toggleStatus',
    'newsletter/delete' => 'AdminNewsletterController@delete',
    'newsletter/export' => 'AdminNewsletterController@export',

    // Pages
    'pages' => 'AdminPageController@index',
    'pages/create' => 'AdminPageController@create',
    'pages/edit' => 'AdminPageController@edit',
    'pages/delete' => 'AdminPageController@delete',

    // Settings
    'settings' => 'AdminSettingsController@index',
    'settings/update-general' => 'AdminSettingsController@updateGeneral',
    'settings/update-email' => 'AdminSettingsController@updateEmail',
    'settings/update-payment' => 'AdminSettingsController@updatePayment',
    'settings/update-shipping' => 'AdminSettingsController@updateShipping',
    'settings/update-seo' => 'AdminSettingsController@updateSeo',
    'settings/update-social' => 'AdminSettingsController@updateSocial',
    'settings/update-analytics' => 'AdminSettingsController@updateAnalytics',
    'settings/update-advanced' => 'AdminSettingsController@updateAdvanced',
    'settings/test-email' => 'AdminSettingsController@testEmail',
    'settings/clear-cache' => 'AdminSettingsController@clearCache',

    // Reports
    'reports' => 'AdminReportController@index',
    'ai-reports' => 'AdminReportController@aiReports',
];

// Find matching route
$handler = $routes[$path] ?? null;

if (!$handler) {
    http_response_code(404);
    echo "404 - Admin page not found";
    exit;
}

// Parse controller and method
list($controllerName, $method) = explode('@', $handler);

// Load controller
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(500);
    echo "Controller not found: $controllerName";
    exit;
}

require_once $controllerFile;

// Instantiate and call
$controller = new $controllerName();
$controller->$method();
