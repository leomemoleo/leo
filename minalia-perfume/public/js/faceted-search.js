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

        // Clear previous results
        this.resultsContainer.innerHTML = '';

        // Products
        if (products && products.length > 0) {
            const section = this.createSection('Ürünler');
            products.forEach(product => {
                const item = this.createProductItem(product);
                section.appendChild(item);
            });
            this.resultsContainer.appendChild(section);
        }

        // Brands
        if (brands && brands.length > 0) {
            const section = this.createSection('Markalar');
            brands.forEach(brand => {
                const item = this.createBrandItem(brand);
                section.appendChild(item);
            });
            this.resultsContainer.appendChild(section);
        }

        // Categories
        if (categories && categories.length > 0) {
            const section = this.createSection('Kategoriler');
            categories.forEach(category => {
                const item = this.createCategoryItem(category);
                section.appendChild(item);
            });
            this.resultsContainer.appendChild(section);
        }

        // Trending
        if (trending && trending.length > 0) {
            const section = this.createSection('🔥 Trend Aramalar');
            trending.forEach(term => {
                const item = this.createTrendingItem(term);
                section.appendChild(item);
            });
            this.resultsContainer.appendChild(section);
        }

        // Empty state
        if (this.resultsContainer.children.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'autocomplete-empty';
            empty.textContent = 'Sonuç bulunamadı';
            this.resultsContainer.appendChild(empty);
        }
    }

    createSection(title) {
        const section = document.createElement('div');
        section.className = 'autocomplete-section';

        const titleEl = document.createElement('div');
        titleEl.className = 'autocomplete-section-title';
        titleEl.textContent = title;

        section.appendChild(titleEl);
        return section;
    }

    createProductItem(product) {
        const link = document.createElement('a');
        link.href = '/products/' + escapeHtml(product.slug);
        link.className = 'autocomplete-item';

        const img = document.createElement('img');
        img.src = escapeHtml(product.main_image);
        img.alt = escapeHtml(product.name);
        img.className = 'autocomplete-image';

        const content = document.createElement('div');
        content.className = 'autocomplete-content';

        const name = document.createElement('div');
        name.className = 'autocomplete-name';
        name.textContent = product.name;

        const meta = document.createElement('div');
        meta.className = 'autocomplete-meta';

        const brand = document.createElement('span');
        brand.className = 'brand';
        brand.textContent = product.brand_name;

        const price = document.createElement('span');
        price.className = 'price';
        price.textContent = formatPrice(product.price);

        meta.appendChild(brand);
        meta.appendChild(price);
        content.appendChild(name);
        content.appendChild(meta);
        link.appendChild(img);
        link.appendChild(content);

        return link;
    }

    createBrandItem(brand) {
        const link = document.createElement('a');
        link.href = '/brand/' + escapeHtml(brand.slug);
        link.className = 'autocomplete-item';

        const icon = document.createElement('i');
        icon.className = 'fas fa-tag';

        const content = document.createElement('div');
        content.className = 'autocomplete-content';

        const name = document.createElement('div');
        name.className = 'autocomplete-name';
        name.textContent = brand.name;

        const meta = document.createElement('div');
        meta.className = 'autocomplete-meta';
        meta.textContent = brand.product_count + ' ürün';

        content.appendChild(name);
        content.appendChild(meta);
        link.appendChild(icon);
        link.appendChild(content);

        return link;
    }

    createCategoryItem(category) {
        const link = document.createElement('a');
        link.href = '/category/' + escapeHtml(category.slug);
        link.className = 'autocomplete-item';

        const icon = document.createElement('i');
        icon.className = 'fas fa-folder';

        const content = document.createElement('div');
        content.className = 'autocomplete-content';

        const name = document.createElement('div');
        name.className = 'autocomplete-name';
        name.textContent = category.name;

        const meta = document.createElement('div');
        meta.className = 'autocomplete-meta';
        meta.textContent = category.product_count + ' ürün';

        content.appendChild(name);
        content.appendChild(meta);
        link.appendChild(icon);
        link.appendChild(content);

        return link;
    }

    createTrendingItem(term) {
        const link = document.createElement('a');
        link.href = '/search?q=' + encodeURIComponent(term.search_term);
        link.className = 'autocomplete-item';

        const icon = document.createElement('i');
        icon.className = 'fas fa-fire';

        const content = document.createElement('div');
        content.className = 'autocomplete-content';

        const name = document.createElement('div');
        name.className = 'autocomplete-name';
        name.textContent = term.search_term;

        content.appendChild(name);
        link.appendChild(icon);
        link.appendChild(content);

        return link;
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
                this.clearAllFilters();
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

/**
 * Escape HTML to prevent XSS attacks
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }

    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
        '/': '&#x2F;'
    };

    return String(text).replace(/[&<>"'/]/g, (char) => map[char]);
}

/**
 * Format price in Turkish Lira
 * @param {number} price - Price value
 * @returns {string} Formatted price
 */
function formatPrice(price) {
    if (isNaN(price) || price === null || price === undefined) {
        return '0,00 ₺';
    }

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
