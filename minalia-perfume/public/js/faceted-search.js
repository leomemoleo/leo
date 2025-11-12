/**
 * Faceted Search & Autocomplete
 * MINALIA E-Commerce Platform
 * Enterprise-level filtering and search experience
 */

// ==========================================
// AUTOCOMPLETE SEARCH
// ==========================================

class AutocompleteSearch {
    constructor(inputSelector, resultsSelector) {
        this.input = document.querySelector(inputSelector);
        this.resultsContainer = document.querySelector(resultsSelector);
        this.debounceTimer = null;
        this.minChars = 2;
        this.currentRequest = null;

        if (this.input) {
            this.init();
        }
    }

    init() {
        this.input.addEventListener('input', (e) => this.handleInput(e));
        this.input.addEventListener('focus', (e) => this.handleFocus(e));
        document.addEventListener('click', (e) => this.handleOutsideClick(e));
    }

    handleInput(e) {
        const query = e.target.value.trim();

        // Clear previous timer
        clearTimeout(this.debounceTimer);

        // Hide results if query too short
        if (query.length < this.minChars) {
            this.hideResults();
            return;
        }

        // Debounce API call (300ms)
        this.debounceTimer = setTimeout(() => {
            this.fetchSuggestions(query);
        }, 300);
    }

    handleFocus(e) {
        const query = e.target.value.trim();
        if (query.length >= this.minChars) {
            this.showResults();
        }
    }

    handleOutsideClick(e) {
        if (!this.input.contains(e.target) && !this.resultsContainer.contains(e.target)) {
            this.hideResults();
        }
    }

    async fetchSuggestions(query) {
        try {
            // Cancel previous request
            if (this.currentRequest) {
                this.currentRequest.abort();
            }

            // Create new request
            this.currentRequest = new AbortController();

            const response = await fetch(`/api/search/autocomplete?q=${encodeURIComponent(query)}`, {
                signal: this.currentRequest.signal
            });

            const data = await response.json();

            if (data.success) {
                this.renderResults(data.results);
                this.showResults();
            }

        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Autocomplete error:', error);
            }
        }
    }

    renderResults(results) {
        const { products, brands, categories, trending } = results;

        let html = '';

        // Products
        if (products && products.length > 0) {
            html += '<div class="autocomplete-section">';
            html += '<div class="autocomplete-section-title">Ürünler</div>';
            products.forEach(product => {
                html += `
                    <a href="/products/${product.slug}" class="autocomplete-item">
                        <img src="${product.main_image}" alt="${product.name}" class="autocomplete-image">
                        <div class="autocomplete-content">
                            <div class="autocomplete-name">${product.name}</div>
                            <div class="autocomplete-meta">
                                <span class="brand">${product.brand_name}</span>
                                <span class="price">${formatPrice(product.price)}</span>
                            </div>
                        </div>
                    </a>
                `;
            });
            html += '</div>';
        }

        // Brands
        if (brands && brands.length > 0) {
            html += '<div class="autocomplete-section">';
            html += '<div class="autocomplete-section-title">Markalar</div>';
            brands.forEach(brand => {
                html += `
                    <a href="/brand/${brand.slug}" class="autocomplete-item">
                        <i class="fas fa-tag"></i>
                        <div class="autocomplete-content">
                            <div class="autocomplete-name">${brand.name}</div>
                            <div class="autocomplete-meta">${brand.product_count} ürün</div>
                        </div>
                    </a>
                `;
            });
            html += '</div>';
        }

        // Categories
        if (categories && categories.length > 0) {
            html += '<div class="autocomplete-section">';
            html += '<div class="autocomplete-section-title">Kategoriler</div>';
            categories.forEach(category => {
                html += `
                    <a href="/category/${category.slug}" class="autocomplete-item">
                        <i class="fas fa-folder"></i>
                        <div class="autocomplete-content">
                            <div class="autocomplete-name">${category.name}</div>
                            <div class="autocomplete-meta">${category.product_count} ürün</div>
                        </div>
                    </a>
                `;
            });
            html += '</div>';
        }

        // Trending
        if (trending && trending.length > 0) {
            html += '<div class="autocomplete-section">';
            html += '<div class="autocomplete-section-title">🔥 Trend Aramalar</div>';
            trending.forEach(term => {
                html += `
                    <a href="/search?q=${encodeURIComponent(term.search_term)}" class="autocomplete-item">
                        <i class="fas fa-fire"></i>
                        <div class="autocomplete-content">
                            <div class="autocomplete-name">${term.search_term}</div>
                        </div>
                    </a>
                `;
            });
            html += '</div>';
        }

        // Empty state
        if (html === '') {
            html = '<div class="autocomplete-empty">Sonuç bulunamadı</div>';
        }

        this.resultsContainer.innerHTML = html;
    }

    showResults() {
        this.resultsContainer.style.display = 'block';
    }

    hideResults() {
        this.resultsContainer.style.display = 'none';
    }
}

// ==========================================
// FACETED FILTERS
// ==========================================

class FacetedFilters {
    constructor() {
        this.filters = this.getCurrentFilters();
        this.init();
    }

    init() {
        // Price range slider
        this.initPriceSlider();

        // Filter checkboxes
        this.initCheckboxFilters();

        // Sort dropdown
        this.initSortDropdown();

        // Clear filters button
        this.initClearFilters();

        // Apply filters button (mobile)
        this.initApplyFilters();
    }

    getCurrentFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        return {
            category: urlParams.get('category') || '',
            brand: urlParams.getAll('brand[]') || [],
            gender: urlParams.getAll('gender[]') || [],
            min_price: urlParams.get('min_price') || '',
            max_price: urlParams.get('max_price') || '',
            min_rating: urlParams.get('min_rating') || '',
            sort: urlParams.get('sort') || 'newest',
            in_stock: urlParams.get('in_stock') !== 'false'
        };
    }

    initPriceSlider() {
        const minInput = document.getElementById('price-min');
        const maxInput = document.getElementById('price-max');
        const minSlider = document.getElementById('price-min-slider');
        const maxSlider = document.getElementById('price-max-slider');

        if (!minSlider || !maxSlider) return;

        // Update input when slider changes
        minSlider.addEventListener('input', () => {
            const min = parseInt(minSlider.value);
            const max = parseInt(maxSlider.value);

            if (min >= max) {
                minSlider.value = max - 10;
            }

            minInput.value = minSlider.value;
        });

        maxSlider.addEventListener('input', () => {
            const min = parseInt(minSlider.value);
            const max = parseInt(maxSlider.value);

            if (max <= min) {
                maxSlider.value = min + 10;
            }

            maxInput.value = maxSlider.value;
        });

        // Update slider when input changes
        minInput.addEventListener('change', () => {
            minSlider.value = minInput.value;
            this.applyFilters();
        });

        maxInput.addEventListener('change', () => {
            maxSlider.value = maxInput.value;
            this.applyFilters();
        });
    }

    initCheckboxFilters() {
        const checkboxes = document.querySelectorAll('.filter-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.applyFilters();
            });
        });
    }

    initSortDropdown() {
        const sortSelect = document.getElementById('sort-select');

        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                this.applyFilters();
            });
        }
    }

    initClearFilters() {
        const clearButton = document.querySelector('.clear-filters-btn');

        if (clearButton) {
            clearButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.clearAll Filters();
            });
        }
    }

    initApplyFilters() {
        const applyButton = document.querySelector('.apply-filters-btn');

        if (applyButton) {
            applyButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.applyFilters();
            });
        }
    }

    applyFilters() {
        const params = new URLSearchParams();

        // Get all active filters
        const brands = Array.from(document.querySelectorAll('input[name="brand[]"]:checked'))
            .map(cb => cb.value);

        const genders = Array.from(document.querySelectorAll('input[name="gender[]"]:checked'))
            .map(cb => cb.value);

        const minPrice = document.getElementById('price-min')?.value;
        const maxPrice = document.getElementById('price-max')?.value;

        const minRating = document.querySelector('input[name="min_rating"]:checked')?.value;

        const inStock = document.getElementById('in-stock')?.checked;

        const sort = document.getElementById('sort-select')?.value || 'newest';

        // Build URL parameters
        brands.forEach(brand => params.append('brand[]', brand));
        genders.forEach(gender => params.append('gender[]', gender));

        if (minPrice) params.set('min_price', minPrice);
        if (maxPrice) params.set('max_price', maxPrice);
        if (minRating) params.set('min_rating', minRating);
        if (!inStock) params.set('in_stock', 'false');
        if (sort) params.set('sort', sort);

        // Reload page with new filters
        window.location.search = params.toString();
    }

    clearAllFilters() {
        // Redirect to base products page
        window.location.href = window.location.pathname;
    }
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

function formatPrice(price) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY'
    }).format(price);
}

// ==========================================
// INITIALIZATION
// ==========================================

document.addEventListener('DOMContentLoaded', () => {
    // Initialize autocomplete
    new AutocompleteSearch('#search-input', '#search-results');

    // Initialize faceted filters
    if (document.querySelector('.faceted-filters')) {
        new FacetedFilters();
    }
});

// Export for external use
window.MinaliaSearch = {
    AutocompleteSearch,
    FacetedFilters
};
