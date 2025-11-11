/**
 * Advanced Filtering and Search JavaScript
 * MINALIA Parfüm E-Ticaret Platformu
 */

(function() {
    'use strict';

    // Filter Manager
    const FilterManager = {
        filters: {
            minPrice: 0,
            maxPrice: 20000,
            brands: [],
            genders: [],
            notes: [],
            rating: null,
            stock: [],
            sort: 'newest'
        },

        init() {
            this.bindEvents();
            this.loadFiltersFromURL();
        },

        bindEvents() {
            // Price range
            const minPrice = document.getElementById('minPrice');
            const maxPrice = document.getElementById('maxPrice');
            const minPriceInput = document.getElementById('minPriceInput');
            const maxPriceInput = document.getElementById('maxPriceInput');

            if (minPrice && maxPrice) {
                minPrice.addEventListener('input', (e) => {
                    this.filters.minPrice = parseInt(e.target.value);
                    minPriceInput.value = e.target.value;
                });

                maxPrice.addEventListener('input', (e) => {
                    this.filters.maxPrice = parseInt(e.target.value);
                    maxPriceInput.value = e.target.value;
                });

                minPriceInput.addEventListener('change', (e) => {
                    minPrice.value = e.target.value;
                    this.filters.minPrice = parseInt(e.target.value);
                });

                maxPriceInput.addEventListener('change', (e) => {
                    maxPrice.value = e.target.value;
                    this.filters.maxPrice = parseInt(e.target.value);
                });
            }

            // Brand checkboxes
            document.querySelectorAll('input[name="brand"]').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    const value = e.target.value;
                    if (e.target.checked) {
                        this.filters.brands.push(value);
                    } else {
                        this.filters.brands = this.filters.brands.filter(b => b !== value);
                    }
                });
            });

            // Gender checkboxes
            document.querySelectorAll('input[name="gender"]').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    const value = e.target.value;
                    if (e.target.checked) {
                        this.filters.genders.push(value);
                    } else {
                        this.filters.genders = this.filters.genders.filter(g => g !== value);
                    }
                });
            });

            // Notes checkboxes
            document.querySelectorAll('input[name="note"]').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    const value = e.target.value;
                    if (e.target.checked) {
                        this.filters.notes.push(value);
                    } else {
                        this.filters.notes = this.filters.notes.filter(n => n !== value);
                    }
                });
            });

            // Rating radio
            document.querySelectorAll('input[name="rating"]').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    this.filters.rating = e.target.value;
                });
            });

            // Stock checkboxes
            document.querySelectorAll('input[name="stock"]').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    const value = e.target.value;
                    if (e.target.checked) {
                        this.filters.stock.push(value);
                    } else {
                        this.filters.stock = this.filters.stock.filter(s => s !== value);
                    }
                });
            });

            // Apply filters button
            const applyBtn = document.getElementById('applyFilters');
            if (applyBtn) {
                applyBtn.addEventListener('click', () => this.applyFilters());
            }

            // Reset filters button
            const resetBtn = document.getElementById('resetFilters');
            if (resetBtn) {
                resetBtn.addEventListener('click', () => this.resetFilters());
            }

            // Mobile filter toggle
            const filterToggle = document.querySelector('.filter-toggle');
            const filterContent = document.querySelector('.filter-content');
            if (filterToggle && filterContent) {
                filterToggle.addEventListener('click', () => {
                    filterContent.classList.toggle('active');
                });
            }

            // Brand search
            const brandSearch = document.getElementById('brandSearch');
            if (brandSearch) {
                brandSearch.addEventListener('input', (e) => {
                    this.searchBrands(e.target.value);
                });
            }

            // Sort dropdown
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.addEventListener('change', (e) => {
                    this.filters.sort = e.target.value;
                    this.applyFilters();
                });
            }
        },

        applyFilters() {
            // Show loading
            this.showLoading();

            // Build query string
            const params = new URLSearchParams();

            if (this.filters.minPrice > 0) {
                params.append('min_price', this.filters.minPrice);
            }
            if (this.filters.maxPrice < 20000) {
                params.append('max_price', this.filters.maxPrice);
            }
            if (this.filters.brands.length > 0) {
                params.append('brands', this.filters.brands.join(','));
            }
            if (this.filters.genders.length > 0) {
                params.append('genders', this.filters.genders.join(','));
            }
            if (this.filters.notes.length > 0) {
                params.append('notes', this.filters.notes.join(','));
            }
            if (this.filters.rating) {
                params.append('rating', this.filters.rating);
            }
            if (this.filters.stock.length > 0) {
                params.append('stock', this.filters.stock.join(','));
            }
            params.append('sort', this.filters.sort);

            // Update URL
            const newURL = window.location.pathname + '?' + params.toString();
            window.history.pushState({}, '', newURL);

            // Fetch filtered products
            fetch('/api/products/filter?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.updateProductGrid(data.products);
                this.updateResultCount(data.total);
                this.hideLoading();
            })
            .catch(error => {
                console.error('Filter error:', error);
                this.hideLoading();
            });
        },

        resetFilters() {
            // Reset all filters
            this.filters = {
                minPrice: 0,
                maxPrice: 20000,
                brands: [],
                genders: [],
                notes: [],
                rating: null,
                stock: [],
                sort: 'newest'
            };

            // Reset UI
            document.getElementById('minPrice').value = 0;
            document.getElementById('maxPrice').value = 20000;
            document.getElementById('minPriceInput').value = 0;
            document.getElementById('maxPriceInput').value = 20000;

            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[type="radio"]').forEach(rb => rb.checked = false);

            // Apply reset
            this.applyFilters();
        },

        loadFiltersFromURL() {
            const params = new URLSearchParams(window.location.search);

            if (params.has('min_price')) {
                this.filters.minPrice = parseInt(params.get('min_price'));
                document.getElementById('minPrice').value = this.filters.minPrice;
                document.getElementById('minPriceInput').value = this.filters.minPrice;
            }

            if (params.has('max_price')) {
                this.filters.maxPrice = parseInt(params.get('max_price'));
                document.getElementById('maxPrice').value = this.filters.maxPrice;
                document.getElementById('maxPriceInput').value = this.filters.maxPrice;
            }

            // Load other filters from URL...
        },

        searchBrands(query) {
            const brandCheckboxes = document.querySelectorAll('#brandFilters .filter-checkbox');
            query = query.toLowerCase();

            brandCheckboxes.forEach(checkbox => {
                const label = checkbox.querySelector('span').textContent.toLowerCase();
                if (label.includes(query)) {
                    checkbox.style.display = 'flex';
                } else {
                    checkbox.style.display = 'none';
                }
            });
        },

        updateProductGrid(products) {
            const grid = document.querySelector('.products-grid');
            if (!grid) return;

            if (products.length === 0) {
                grid.innerHTML = '<div class="no-products"><p>Filtrelere uygun ürün bulunamadı.</p></div>';
                return;
            }

            grid.innerHTML = products.map(product => this.renderProductCard(product)).join('');
        },

        renderProductCard(product) {
            return `
                <div class="product-card">
                    ${product.is_new ? '<span class="product-badge badge-new">Yeni</span>' : ''}
                    ${product.sale_price ? '<span class="product-badge badge-sale">İndirim</span>' : ''}
                    <div class="product-image">
                        <a href="/products/${product.slug}">
                            <img src="/images/products/${product.slug}.jpg" alt="${product.name}" loading="lazy">
                        </a>
                        <div class="product-actions">
                            <button class="action-btn wishlist-btn" data-product-id="${product.id}">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="action-btn quick-view-btn" data-product-id="${product.id}">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-info">
                        <p class="product-brand">${product.brand_name}</p>
                        <h3 class="product-name">
                            <a href="/products/${product.slug}">${product.name}</a>
                        </h3>
                        <div class="product-rating">
                            ${this.renderStars(product.rating)}
                            <span class="rating-count">(${product.review_count})</span>
                        </div>
                        <div class="product-price">
                            ${product.sale_price ? `
                                <span class="price-old">₺${this.formatPrice(product.price)}</span>
                                <span class="price-current">₺${this.formatPrice(product.sale_price)}</span>
                            ` : `
                                <span class="price-current">₺${this.formatPrice(product.price)}</span>
                            `}
                        </div>
                        <button class="btn btn-primary btn-block add-to-cart-btn" data-product-id="${product.id}">
                            <i class="fas fa-shopping-bag"></i> Sepete Ekle
                        </button>
                    </div>
                </div>
            `;
        },

        renderStars(rating) {
            const fullStars = Math.floor(rating);
            const halfStar = (rating % 1) >= 0.5;
            const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);

            let html = '';
            for (let i = 0; i < fullStars; i++) html += '<i class="fas fa-star"></i>';
            if (halfStar) html += '<i class="fas fa-star-half-alt"></i>';
            for (let i = 0; i < emptyStars; i++) html += '<i class="far fa-star"></i>';

            return html;
        },

        formatPrice(price) {
            return parseFloat(price).toLocaleString('tr-TR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        updateResultCount(count) {
            const resultCount = document.querySelector('.result-count');
            if (resultCount) {
                resultCount.textContent = `${count} ürün bulundu`;
            }
        },

        showLoading() {
            const grid = document.querySelector('.products-grid');
            if (grid) {
                grid.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>';
            }
        },

        hideLoading() {
            // Loading is replaced by content
        }
    };

    // Advanced Search with Autocomplete
    const SearchManager = {
        searchTimeout: null,

        init() {
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
            }
        },

        handleSearch(query) {
            clearTimeout(this.searchTimeout);

            if (query.length < 2) {
                this.hideSuggestions();
                return;
            }

            this.searchTimeout = setTimeout(() => {
                this.fetchSuggestions(query);
            }, 300);
        },

        fetchSuggestions(query) {
            fetch(`/api/products/search-suggestions?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.results) {
                        this.displaySuggestions(data.results);
                    }
                })
                .catch(error => console.error('Search error:', error));
        },

        displaySuggestions(results) {
            const container = document.querySelector('.search-suggestions');
            if (!container) return;

            if (results.length === 0) {
                container.innerHTML = '<div class="no-results">Sonuç bulunamadı</div>';
                container.style.display = 'block';
                return;
            }

            let html = '';

            // Products
            if (results.products && results.products.length > 0) {
                html += '<div class="suggestion-section"><h4>Ürünler</h4>';
                results.products.forEach(product => {
                    html += `
                        <a href="/products/${product.slug}" class="suggestion-item">
                            <img src="/images/products/${product.slug}.jpg" alt="${product.name}">
                            <div class="suggestion-info">
                                <strong>${product.name}</strong>
                                <span class="suggestion-brand">${product.brand_name}</span>
                                <span class="suggestion-price">₺${this.formatPrice(product.price)}</span>
                            </div>
                        </a>
                    `;
                });
                html += '</div>';
            }

            // Brands
            if (results.brands && results.brands.length > 0) {
                html += '<div class="suggestion-section"><h4>Markalar</h4>';
                results.brands.forEach(brand => {
                    html += `
                        <a href="/brand/${brand.slug}" class="suggestion-item">
                            <i class="fas fa-tag"></i>
                            <span>${brand.name}</span>
                        </a>
                    `;
                });
                html += '</div>';
            }

            container.innerHTML = html;
            container.style.display = 'block';
        },

        hideSuggestions() {
            const container = document.querySelector('.search-suggestions');
            if (container) {
                container.style.display = 'none';
            }
        },

        formatPrice(price) {
            return parseFloat(price).toLocaleString('tr-TR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };

    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            FilterManager.init();
            SearchManager.init();
        });
    } else {
        FilterManager.init();
        SearchManager.init();
    }

})();
