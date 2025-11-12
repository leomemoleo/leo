<?php
/**
 * MINALIA Parfüm E-Ticaret Platformu
 * Front-end Entry Point
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

// Load helpers
require_once __DIR__ . '/../app/helpers/functions.php';

// Load Router
require_once __DIR__ . '/../app/Router.php';

// Load Base Classes
require_once __DIR__ . '/../app/models/BaseModel.php';
require_once __DIR__ . '/../app/controllers/BaseController.php';

// Initialize Router
$router = new Router();

// ============================================
// PUBLIC ROUTES
// ============================================

// Home Page
$router->get('/', 'HomeController@index', 'home');

// Products
$router->get('/products', 'ProductController@index', 'products.index');
$router->get('/products/{slug}', 'ProductController@show', 'products.show');
$router->get('/category/{slug}', 'ProductController@category', 'products.category');
$router->get('/brand/{slug}', 'ProductController@brand', 'products.brand');

// Search
$router->get('/search', 'ProductController@search', 'search');

// Cart
$router->get('/cart', 'CartController@index', 'cart.index');
$router->post('/cart/add', 'CartController@add', 'cart.add');
$router->post('/cart/update', 'CartController@update', 'cart.update');
$router->post('/cart/remove', 'CartController@remove', 'cart.remove');
$router->post('/cart/clear', 'CartController@clear', 'cart.clear');

// Wishlist
$router->get('/wishlist', 'WishlistController@index', 'wishlist.index');
$router->post('/wishlist/add', 'WishlistController@add', 'wishlist.add');
$router->post('/wishlist/remove', 'WishlistController@remove', 'wishlist.remove');

// Checkout
$router->get('/checkout', 'CheckoutController@index', 'checkout.index');
$router->post('/checkout/process', 'CheckoutController@process', 'checkout.process');
$router->get('/checkout/success/{orderNumber}', 'CheckoutController@success', 'checkout.success');

// Authentication
$router->get('/login', 'AuthController@loginForm', 'auth.login');
$router->post('/login', 'AuthController@login', 'auth.login.post');
$router->get('/register', 'AuthController@registerForm', 'auth.register');
$router->post('/register', 'AuthController@register', 'auth.register.post');
$router->get('/logout', 'AuthController@logout', 'auth.logout');
$router->get('/forgot-password', 'AuthController@forgotPasswordForm', 'auth.forgot');
$router->post('/forgot-password', 'AuthController@forgotPassword', 'auth.forgot.post');
$router->get('/reset-password/{token}', 'AuthController@resetPasswordForm', 'auth.reset');
$router->post('/reset-password', 'AuthController@resetPassword', 'auth.reset.post');

// Social Login (OAuth)
$router->get('/auth/google', 'OAuthController@googleLogin', 'oauth.google');
$router->get('/auth/google/callback', 'OAuthController@googleCallback', 'oauth.google.callback');
$router->get('/auth/facebook', 'OAuthController@facebookLogin', 'oauth.facebook');
$router->get('/auth/facebook/callback', 'OAuthController@facebookCallback', 'oauth.facebook.callback');
$router->post('/auth/disconnect', 'OAuthController@disconnect', 'oauth.disconnect');

// User Account
$router->group('/account', function($router) {
    $router->get('/', 'AccountController@index', 'account.dashboard');
    $router->get('/profile', 'AccountController@profile', 'account.profile');
    $router->post('/profile', 'AccountController@updateProfile', 'account.profile.update');
    $router->get('/orders', 'AccountController@orders', 'account.orders');
    $router->get('/orders/{orderNumber}', 'AccountController@orderDetail', 'account.order.detail');
    $router->get('/addresses', 'AccountController@addresses', 'account.addresses');
    $router->post('/addresses/add', 'AccountController@addAddress', 'account.addresses.add');
    $router->post('/addresses/update/{id}', 'AccountController@updateAddress', 'account.addresses.update');
    $router->post('/addresses/delete/{id}', 'AccountController@deleteAddress', 'account.addresses.delete');
    $router->post('/addresses/set-default/{id}', 'AccountController@setDefaultAddress', 'account.addresses.default');
    $router->post('/change-password', 'AccountController@changePassword', 'account.password.change');
    $router->get('/loyalty', 'AccountController@loyalty', 'account.loyalty');
    $router->get('/payment-methods', 'AccountController@paymentMethods', 'account.payment.methods');
    $router->post('/payment-methods/add', 'AccountController@addPaymentMethod', 'account.payment.add');
    $router->post('/payment-methods/delete/{id}', 'AccountController@deletePaymentMethod', 'account.payment.delete');
    $router->post('/payment-methods/set-default/{id}', 'AccountController@setDefaultPaymentMethod', 'account.payment.default');
});

// Reviews
$router->post('/reviews/add', 'ReviewController@add', 'reviews.add');

// Newsletter
$router->post('/newsletter/subscribe', 'NewsletterController@subscribe', 'newsletter.subscribe');

// Static Pages
$router->get('/about', 'PageController@about', 'pages.about');
$router->get('/contact', 'PageController@contact', 'pages.contact');
$router->post('/contact', 'PageController@contactSubmit', 'pages.contact.submit');
$router->get('/privacy-policy', 'PageController@privacy', 'pages.privacy');
$router->get('/terms-conditions', 'PageController@terms', 'pages.terms');

// ============================================
// API ROUTES
// ============================================

$router->group('/api', function($router) {
    $router->post('/cart/count', 'Api\CartApiController@count');
    $router->post('/wishlist/count', 'Api\WishlistApiController@count');
    $router->get('/products/featured', 'Api\ProductApiController@featured');
    $router->get('/products/search-suggestions', 'Api\ProductApiController@searchSuggestions');
});

// Dispatch the request
$router->dispatch();
