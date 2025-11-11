/**
 * MINALIA Parfüm E-Ticaret Platformu
 * Main JavaScript File - Modernized with AJAX Integration
 */

(function() {
    'use strict';

    // ===================================
    // UTILITY FUNCTIONS
    // ===================================

    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);

    // AJAX Helper with improved error handling
    function ajax(url, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const config = { ...defaults, ...options };

        // Handle FormData vs JSON
        if (config.data) {
            if (config.method === 'GET') {
                url += '?' + new URLSearchParams(config.data);
            } else if (config.data instanceof FormData) {
                config.body = config.data;
            } else {
                config.headers['Content-Type'] = 'application/x-www-form-urlencoded';
                config.body = new URLSearchParams(config.data).toString();
            }
        }

        return fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                return { success: false, message: 'Bağlantı hatası oluştu.' };
            });
    }

    // Show Toast Notification with modern design
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const icon = type === 'success' ? '✓' : '✕';
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <span class="toast-icon">${icon}</span>
            <span class="toast-message">${message}</span>
        `;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: ${type === 'success' ? 'linear-gradient(135deg, #4CAF50, #45a049)' : 'linear-gradient(135deg, #f44336, #e53935)'};
            color: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            z-index: 10000;
            animation: slideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            max-width: 400px;
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // Loading Spinner
    function showLoading(element) {
        const originalText = element.innerHTML;
        element.dataset.originalText = originalText;
        element.disabled = true;
        element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yükleniyor...';
    }

    function hideLoading(element) {
        element.disabled = false;
        element.innerHTML = element.dataset.originalText || element.innerHTML;
        delete element.dataset.originalText;
    }

    // ===================================
    // CART FUNCTIONS - AJAX INTEGRATED
    // ===================================

    function initAddToCart() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart-btn') ||
                e.target.closest('.add-to-cart-btn')) {

                e.preventDefault();
                const btn = e.target.classList.contains('add-to-cart-btn') ?
                            e.target : e.target.closest('.add-to-cart-btn');

                const productId = btn.dataset.productId;
                const quantity = parseInt(btn.dataset.quantity || '1');

                addToCart(productId, quantity, btn);
            }
        });
    }

    function addToCart(productId, quantity = 1, button = null) {
        if (button) showLoading(button);

        ajax('/cart/add', {
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity
            }
        }).then(data => {
            if (button) hideLoading(button);

            if (data.success) {
                showToast(data.message || 'Ürün sepete eklendi!', 'success');
                updateCartCount();

                // Add visual feedback
                if (button) {
                    button.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        button.style.transform = 'scale(1)';
                    }, 200);
                }
            } else {
                showToast(data.message || 'Ürün sepete eklenemedi.', 'error');
            }
        });
    }

    function updateCartCount() {
        ajax('/cart/items', {
            method: 'POST'
        }).then(data => {
            if (data.success && data.items) {
                const count = data.items.length;
                const cartCount = $('#cart-count');
                if (cartCount) {
                    cartCount.textContent = count;

                    // Animate count
                    cartCount.style.transform = 'scale(1.3)';
                    setTimeout(() => {
                        cartCount.style.transform = 'scale(1)';
                    }, 200);
                }
            }
        });
    }

    // ===================================
    // WISHLIST FUNCTIONS - AJAX INTEGRATED
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
        const icon = btn.querySelector('i');
        const isFilled = icon.classList.contains('fas');

        const endpoint = isFilled ? '/wishlist/remove' : '/wishlist/add';

        // Optimistic UI update
        if (isFilled) {
            icon.classList.remove('fas');
            icon.classList.add('far');
        } else {
            icon.classList.remove('far');
            icon.classList.add('fas');
        }

        ajax(endpoint, {
            method: 'POST',
            data: { product_id: productId }
        }).then(data => {
            if (data.success) {
                showToast(data.message || (isFilled ? 'Favorilerden çıkarıldı' : 'Favorilere eklendi'), 'success');
                updateWishlistCount();

                // Heart animation
                icon.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    icon.style.transform = 'scale(1)';
                }, 300);
            } else {
                // Revert on error
                if (isFilled) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                }
                showToast(data.message || 'İşlem başarısız oldu.', 'error');
            }
        });
    }

    function updateWishlistCount() {
        ajax('/wishlist/items', {
            method: 'POST'
        }).then(data => {
            if (data.success && data.items) {
                const count = data.items.length;
                const wishlistCount = $('#wishlist-count');
                if (wishlistCount) {
                    wishlistCount.textContent = count;
                }
            }
        });
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
                const isVisible = miniCart.style.display === 'block';
                miniCart.style.display = isVisible ? 'none' : 'block';

                if (!isVisible) {
                    loadMiniCartItems();
                }
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

    function loadMiniCartItems() {
        ajax('/cart/items', {
            method: 'POST'
        }).then(data => {
            if (data.success && data.items) {
                renderMiniCart(data.items);
            }
        });
    }

    function renderMiniCart(items) {
        const miniCartItems = $('.mini-cart-items');
        if (!miniCartItems) return;

        if (items.length === 0) {
            miniCartItems.innerHTML = '<p style="text-align: center; padding: 2rem; color: #999;">Sepetiniz boş</p>';
            return;
        }

        const html = items.map(item => `
            <div class="mini-cart-item">
                <img src="${item.main_image}" alt="${item.name}">
                <div class="item-info">
                    <h4>${item.name}</h4>
                    <p>${item.quantity}x ${formatPrice(item.price)}</p>
                </div>
                <button onclick="removeFromCart(${item.product_id})" class="remove-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');

        miniCartItems.innerHTML = html;
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('tr-TR', {
            style: 'currency',
            currency: 'TRY'
        }).format(price);
    }

    // ===================================
    // HERO SLIDER
    // ===================================

    function initHeroSlider() {
        const slides = $$('.slide');
        const indicators = $$('.indicator');
        const prevBtn = $('.slider-prev');
        const nextBtn = $('.slider-next');

        if (slides.length === 0) return;

        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(ind => ind.classList.remove('active'));

            if (index >= slides.length) currentSlide = 0;
            if (index < 0) currentSlide = slides.length - 1;

            slides[currentSlide].classList.add('active');
            if (indicators[currentSlide]) {
                indicators[currentSlide].classList.add('active');
            }
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
        ajax(`/api/search/suggestions?q=${encodeURIComponent(query)}`)
            .then(data => {
                if (data.success && data.products && data.products.length > 0) {
                    displaySuggestions(data.products, suggestionsEl);
                } else {
                    suggestionsEl.style.display = 'none';
                }
            });
    }

    function displaySuggestions(products, suggestionsEl) {
        const html = products.map(product => `
            <a href="/products/${product.slug}" class="suggestion-item">
                <img src="${product.image || '/images/placeholder.jpg'}" alt="${product.name}">
                <div>
                    <strong>${product.name}</strong>
                    <span>${formatPrice(product.price)}</span>
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
    // FLASH MESSAGE
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

                const submitBtn = form.querySelector('button[type="submit"]');
                const email = form.querySelector('input[name="email"]').value;

                if (submitBtn) showLoading(submitBtn);

                ajax('/newsletter/subscribe', {
                    method: 'POST',
                    data: { email }
                }).then(data => {
                    if (submitBtn) hideLoading(submitBtn);

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
                nav.classList.toggle('active');
            });
        }
    }

    // ===================================
    // LAZY LOADING IMAGES
    // ===================================

    function initLazyLoading() {
        const images = $$('img[data-src]');

        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback for older browsers
            images.forEach(img => {
                img.src = img.dataset.src;
            });
        }
    }

    // ===================================
    // PRODUCT QUICK VIEW
    // ===================================

    function initQuickView() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quick-view-btn')) {
                e.preventDefault();
                const productId = e.target.dataset.productId;
                showQuickView(productId);
            }
        });
    }

    function showQuickView(productId) {
        // Implementation for quick view modal
        console.log('Quick view for product:', productId);
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
        initQuickView();

        // Load initial counts
        updateCartCount();
        updateWishlistCount();

        console.log('✓ MINALIA initialized with AJAX integration');
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Make functions globally accessible
    window.MINALIA = {
        addToCart,
        toggleWishlist,
        showToast,
        updateCartCount,
        updateWishlistCount
    };

})();

// ===================================
// CSS ANIMATIONS
// ===================================

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(-100%);
            opacity: 0;
        }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .toast-icon {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .toast-message {
        flex: 1;
    }

    img[data-src] {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    img[data-src].loaded {
        opacity: 1;
    }

    /* Smooth transitions */
    button, .btn {
        transition: all 0.3s ease;
    }

    button:hover, .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    button:active, .btn:active {
        transform: translateY(0);
    }

    /* Loading spinner */
    .fa-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
