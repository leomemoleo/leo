/**
 * MINALIA Parfüm E-Ticaret Platformu
 * Main JavaScript File
 */

(function() {
    'use strict';

    // ===================================
    // UTILITY FUNCTIONS
    // ===================================

    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);

    // AJAX Helper
    function ajax(url, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const config = { ...defaults, ...options };

        if (config.data) {
            if (config.method === 'GET') {
                url += '?' + new URLSearchParams(config.data);
            } else {
                config.body = JSON.stringify(config.data);
            }
        }

        return fetch(url, config)
            .then(response => response.json())
            .catch(error => {
                console.error('AJAX Error:', error);
                return { success: false, message: 'Bir hata oluştu.' };
            });
    }

    // Show Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: ${type === 'success' ? '#4CAF50' : '#f44336'};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ===================================
    // HERO SLIDER
    // ===================================

    function initHeroSlider() {
        const slides = $$('.slide');
        const indicators = $$('.indicator');
        const prevBtn = $('.slider-prev');
        const nextBtn = $('.slider-next');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(ind => ind.classList.remove('active'));

            if (index >= slides.length) currentSlide = 0;
            if (index < 0) currentSlide = slides.length - 1;

            slides[currentSlide].classList.add('active');
            indicators[currentSlide].classList.add('active');
        }

        function nextSlide() {
            currentSlide++;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide--;
            showSlide(currentSlide);
        }

        function startAutoSlide() {
            slideInterval = setInterval(nextSlide, 5000);
        }

        function stopAutoSlide() {
            clearInterval(slideInterval);
        }

        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => {
                stopAutoSlide();
                prevSlide();
                startAutoSlide();
            });

            nextBtn.addEventListener('click', () => {
                stopAutoSlide();
                nextSlide();
                startAutoSlide();
            });
        }

        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                stopAutoSlide();
                currentSlide = index;
                showSlide(currentSlide);
                startAutoSlide();
            });
        });

        startAutoSlide();
    }

    // ===================================
    // MINI CART
    // ===================================

    function initMiniCart() {
        const cartTrigger = $('.cart-trigger');
        const miniCart = $('.mini-cart');
        const miniCartClose = $('.mini-cart-close');

        if (cartTrigger && miniCart) {
            cartTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                miniCart.style.display = miniCart.style.display === 'none' ? 'block' : 'none';
                loadCartItems();
            });

            if (miniCartClose) {
                miniCartClose.addEventListener('click', () => {
                    miniCart.style.display = 'none';
                });
            }

            document.addEventListener('click', (e) => {
                if (!miniCart.contains(e.target) && !cartTrigger.contains(e.target)) {
                    miniCart.style.display = 'none';
                }
            });
        }
    }

    function loadCartItems() {
        // TODO: Load cart items via AJAX
        const cartItems = JSON.parse(localStorage.getItem('cart') || '[]');
        updateCartCount(cartItems.length);
    }

    function updateCartCount(count) {
        const cartCount = $('#cart-count');
        if (cartCount) {
            cartCount.textContent = count;
        }
    }

    // ===================================
    // ADD TO CART
    // ===================================

    function initAddToCart() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart-btn') ||
                e.target.closest('.add-to-cart-btn')) {

                e.preventDefault();
                const btn = e.target.classList.contains('add-to-cart-btn') ?
                            e.target : e.target.closest('.add-to-cart-btn');

                const productId = btn.dataset.productId;
                addToCart(productId);
            }
        });
    }

    function addToCart(productId, quantity = 1) {
        // Get cart from localStorage
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');

        // Check if product already in cart
        const existingItem = cart.find(item => item.id === productId);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({ id: productId, quantity });
        }

        // Save to localStorage
        localStorage.setItem('cart', JSON.stringify(cart));

        // Update UI
        updateCartCount(cart.length);
        showToast('Ürün sepete eklendi', 'success');

        // Sync with server if user is logged in
        syncCartWithServer(cart);
    }

    function syncCartWithServer(cart) {
        // TODO: Implement server sync
        ajax('/api/cart/sync', {
            method: 'POST',
            data: { cart }
        });
    }

    // ===================================
    // WISHLIST
    // ===================================

    function initWishlist() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('wishlist-btn') ||
                e.target.closest('.wishlist-btn')) {

                e.preventDefault();
                const btn = e.target.classList.contains('wishlist-btn') ?
                            e.target : e.target.closest('.wishlist-btn');

                const productId = btn.dataset.productId;
                toggleWishlist(productId, btn);
            }
        });
    }

    function toggleWishlist(productId, btn) {
        let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');

        const index = wishlist.indexOf(productId);

        if (index > -1) {
            wishlist.splice(index, 1);
            btn.querySelector('i').classList.remove('fas');
            btn.querySelector('i').classList.add('far');
            showToast('Favorilerden çıkarıldı', 'success');
        } else {
            wishlist.push(productId);
            btn.querySelector('i').classList.remove('far');
            btn.querySelector('i').classList.add('fas');
            showToast('Favorilere eklendi', 'success');
        }

        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        updateWishlistCount(wishlist.length);

        // Sync with server
        ajax('/api/wishlist/sync', {
            method: 'POST',
            data: { wishlist }
        });
    }

    function updateWishlistCount(count) {
        const wishlistCount = $('#wishlist-count');
        if (wishlistCount) {
            wishlistCount.textContent = count;
        }
    }

    // ===================================
    // SEARCH SUGGESTIONS
    // ===================================

    function initSearchSuggestions() {
        const searchInput = $('.search-input');
        const suggestions = $('.search-suggestions');

        if (searchInput && suggestions) {
            let debounceTimer;

            searchInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);

                const query = e.target.value.trim();

                if (query.length < 2) {
                    suggestions.style.display = 'none';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    searchProducts(query, suggestions);
                }, 300);
            });

            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target)) {
                    suggestions.style.display = 'none';
                }
            });
        }
    }

    function searchProducts(query, suggestionsEl) {
        ajax(`/api/products/search-suggestions?q=${encodeURIComponent(query)}`)
            .then(data => {
                if (data.success && data.products.length > 0) {
                    displaySuggestions(data.products, suggestionsEl);
                } else {
                    suggestionsEl.style.display = 'none';
                }
            });
    }

    function displaySuggestions(products, suggestionsEl) {
        const html = products.map(product => `
            <a href="/products/${product.slug}" class="suggestion-item">
                <img src="/images/products/${product.slug}.jpg" alt="${product.name}">
                <div>
                    <strong>${product.name}</strong>
                    <span>${product.price} TL</span>
                </div>
            </a>
        `).join('');

        suggestionsEl.innerHTML = html;
        suggestionsEl.style.display = 'block';
    }

    // ===================================
    // SCROLL TO TOP
    // ===================================

    function initScrollToTop() {
        const scrollBtn = $('#scrollToTop');

        if (scrollBtn) {
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    scrollBtn.classList.add('visible');
                } else {
                    scrollBtn.classList.remove('visible');
                }
            });

            scrollBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    }

    // ===================================
    // FLASH MESSAGE CLOSE
    // ===================================

    function initFlashMessage() {
        const flashClose = $('.flash-close');
        if (flashClose) {
            flashClose.addEventListener('click', () => {
                flashClose.closest('.flash-message').remove();
            });

            // Auto hide after 5 seconds
            setTimeout(() => {
                const flashMsg = $('.flash-message');
                if (flashMsg) {
                    flashMsg.style.animation = 'slideUp 0.3s ease';
                    setTimeout(() => flashMsg.remove(), 300);
                }
            }, 5000);
        }
    }

    // ===================================
    // NEWSLETTER SUBSCRIPTION
    // ===================================

    function initNewsletter() {
        const form = $('#newsletter-form');

        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();

                const email = form.querySelector('input[name="email"]').value;

                ajax('/newsletter/subscribe', {
                    method: 'POST',
                    data: { email }
                }).then(data => {
                    if (data.success) {
                        showToast('E-bültene başarıyla abone oldunuz!', 'success');
                        form.reset();
                    } else {
                        showToast(data.message || 'Bir hata oluştu.', 'error');
                    }
                });
            });
        }
    }

    // ===================================
    // MOBILE MENU
    // ===================================

    function initMobileMenu() {
        const toggle = $('.mobile-menu-toggle');
        const nav = $('.header-nav');

        if (toggle && nav) {
            toggle.addEventListener('click', () => {
                nav.style.display = nav.style.display === 'none' ? 'block' : 'none';
            });
        }
    }

    // ===================================
    // LAZY LOADING IMAGES
    // ===================================

    function initLazyLoading() {
        const images = $$('img[loading="lazy"]');

        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src;
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }
    }

    // ===================================
    // INITIALIZE ALL
    // ===================================

    function init() {
        initHeroSlider();
        initMiniCart();
        initAddToCart();
        initWishlist();
        initSearchSuggestions();
        initScrollToTop();
        initFlashMessage();
        initNewsletter();
        initMobileMenu();
        initLazyLoading();

        // Load initial counts
        loadCartItems();

        const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        updateWishlistCount(wishlist.length);

        console.log('MINALIA initialized');
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    @keyframes slideUp {
        from { transform: translateY(0); opacity: 1; }
        to { transform: translateY(-100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
