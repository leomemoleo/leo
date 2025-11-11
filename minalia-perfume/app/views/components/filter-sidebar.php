<!-- Filter Sidebar Component -->
<aside class="filter-sidebar">
    <div class="filter-section">
        <button class="filter-toggle mobile-only">
            <i class="fas fa-filter"></i> Filtreler
        </button>
    </div>

    <div class="filter-content">
        <div class="filter-header">
            <h3>Filtrele</h3>
            <button class="filter-reset" id="resetFilters">
                <i class="fas fa-redo"></i> Temizle
            </button>
        </div>

        <!-- Price Filter -->
        <div class="filter-group">
            <h4 class="filter-title">Fiyat Aralığı</h4>
            <div class="price-range-slider">
                <input type="range" id="minPrice" min="0" max="20000" value="0" step="100">
                <input type="range" id="maxPrice" min="0" max="20000" value="20000" step="100">
            </div>
            <div class="price-inputs">
                <input type="number" id="minPriceInput" placeholder="Min" value="0">
                <span>-</span>
                <input type="number" id="maxPriceInput" placeholder="Max" value="20000">
            </div>
        </div>

        <!-- Brand Filter -->
        <div class="filter-group">
            <h4 class="filter-title">Markalar</h4>
            <div class="filter-search">
                <input type="text" placeholder="Marka ara..." id="brandSearch">
            </div>
            <div class="filter-options" id="brandFilters">
                <!-- Brands will be loaded dynamically -->
                <label class="filter-checkbox">
                    <input type="checkbox" name="brand" value="1">
                    <span>Chanel <small>(12)</small></span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="brand" value="2">
                    <span>Dior <small>(8)</small></span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="brand" value="3">
                    <span>Tom Ford <small>(15)</small></span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="brand" value="4">
                    <span>Creed <small>(6)</small></span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="brand" value="5">
                    <span>Jo Malone <small>(10)</small></span>
                </label>
            </div>
        </div>

        <!-- Gender Filter -->
        <div class="filter-group">
            <h4 class="filter-title">Cinsiyet</h4>
            <div class="filter-options">
                <label class="filter-checkbox">
                    <input type="checkbox" name="gender" value="men">
                    <span>Erkek</span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="gender" value="women">
                    <span>Kadın</span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="gender" value="unisex">
                    <span>Unisex</span>
                </label>
            </div>
        </div>

        <!-- Fragrance Notes Filter -->
        <div class="filter-group">
            <h4 class="filter-title">Koku Notaları</h4>
            <div class="filter-options">
                <label class="filter-checkbox">
                    <input type="checkbox" name="note" value="odunsu">
                    <span>Odunsu</span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="note" value="ciceksi">
                    <span>Çiçeksi</span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="note" value="meyvemsi">
                    <span>Meyvemsi</span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="note" value="baharatli">
                    <span>Baharatlı</span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="note" value="taze">
                    <span>Taze</span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="note" value="dogu">
                    <span>Doğu</span>
                </label>
            </div>
        </div>

        <!-- Rating Filter -->
        <div class="filter-group">
            <h4 class="filter-title">Değerlendirme</h4>
            <div class="filter-options">
                <label class="filter-checkbox">
                    <input type="radio" name="rating" value="5">
                    <span>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        ve üzeri
                    </span>
                </label>
                <label class="filter-checkbox">
                    <input type="radio" name="rating" value="4">
                    <span>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        ve üzeri
                    </span>
                </label>
                <label class="filter-checkbox">
                    <input type="radio" name="rating" value="3">
                    <span>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <i class="far fa-star"></i>
                        ve üzeri
                    </span>
                </label>
            </div>
        </div>

        <!-- Stock Status -->
        <div class="filter-group">
            <h4 class="filter-title">Stok Durumu</h4>
            <div class="filter-options">
                <label class="filter-checkbox">
                    <input type="checkbox" name="stock" value="in_stock">
                    <span>Stokta Var</span>
                </label>
                <label class="filter-checkbox">
                    <input type="checkbox" name="stock" value="on_sale">
                    <span>İndirimde</span>
                </label>
            </div>
        </div>

        <!-- Apply Button -->
        <button class="btn btn-primary btn-block" id="applyFilters">
            Filtreleri Uygula
        </button>
    </div>
</aside>

<style>
.filter-sidebar {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-sm);
}

.filter-toggle {
    display: none;
    width: 100%;
    padding: var(--spacing-md);
    background: var(--color-primary);
    color: var(--color-white);
    border-radius: var(--radius-md);
    font-weight: 500;
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-lg);
    padding-bottom: var(--spacing-md);
    border-bottom: 2px solid var(--color-border);
}

.filter-header h3 {
    margin: 0;
    color: var(--color-primary);
}

.filter-reset {
    color: var(--color-text-secondary);
    font-size: 0.875rem;
    transition: var(--transition-fast);
}

.filter-reset:hover {
    color: var(--color-primary);
}

.filter-group {
    margin-bottom: var(--spacing-lg);
    padding-bottom: var(--spacing-lg);
    border-bottom: 1px solid var(--color-border);
}

.filter-group:last-of-type {
    border-bottom: none;
}

.filter-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: var(--spacing-md);
    color: var(--color-text-primary);
}

.filter-search input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-sm);
}

.filter-options {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
    max-height: 200px;
    overflow-y: auto;
}

.filter-checkbox {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: var(--radius-sm);
    transition: var(--transition-fast);
}

.filter-checkbox:hover {
    background-color: var(--color-cream);
}

.filter-checkbox input {
    cursor: pointer;
}

.filter-checkbox span {
    flex: 1;
    font-size: 0.9rem;
}

.filter-checkbox small {
    color: var(--color-text-secondary);
}

/* Price Range Slider */
.price-range-slider {
    position: relative;
    height: 40px;
    margin-bottom: var(--spacing-sm);
}

.price-range-slider input[type="range"] {
    position: absolute;
    width: 100%;
    pointer-events: none;
    appearance: none;
    background: transparent;
}

.price-range-slider input[type="range"]::-webkit-slider-thumb {
    appearance: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--color-primary);
    cursor: pointer;
    pointer-events: all;
}

.price-range-slider input[type="range"]::-webkit-slider-runnable-track {
    width: 100%;
    height: 4px;
    background: var(--color-border);
    border-radius: 2px;
}

.price-inputs {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.price-inputs input {
    flex: 1;
    padding: 0.5rem;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    text-align: center;
}

.price-inputs span {
    color: var(--color-text-secondary);
}

@media (max-width: 768px) {
    .filter-toggle {
        display: block;
    }

    .filter-content {
        display: none;
        margin-top: var(--spacing-md);
    }

    .filter-content.active {
        display: block;
    }
}
</style>
