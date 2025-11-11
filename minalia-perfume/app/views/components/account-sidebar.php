<div class="account-sidebar" style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <nav>
        <a href="<?= BASE_URL ?>/account" class="sidebar-item <?= $active_menu === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <a href="<?= BASE_URL ?>/account/orders" class="sidebar-item <?= $active_menu === 'orders' ? 'active' : '' ?>">
            <i class="fas fa-shopping-bag"></i>
            <span>Siparişlerim</span>
        </a>

        <a href="<?= BASE_URL ?>/account/addresses" class="sidebar-item <?= $active_menu === 'addresses' ? 'active' : '' ?>">
            <i class="fas fa-map-marker-alt"></i>
            <span>Adreslerim</span>
        </a>

        <a href="<?= BASE_URL ?>/account/wishlist" class="sidebar-item <?= $active_menu === 'wishlist' ? 'active' : '' ?>">
            <i class="fas fa-heart"></i>
            <span>Favorilerim</span>
        </a>

        <a href="<?= BASE_URL ?>/account/loyalty" class="sidebar-item <?= $active_menu === 'loyalty' ? 'active' : '' ?>">
            <i class="fas fa-star"></i>
            <span>Sadakat Puanları</span>
        </a>

        <a href="<?= BASE_URL ?>/account/profile" class="sidebar-item <?= $active_menu === 'profile' ? 'active' : '' ?>">
            <i class="fas fa-user"></i>
            <span>Profil Bilgilerim</span>
        </a>

        <a href="<?= BASE_URL ?>/auth/logout" class="sidebar-item" style="color: #f44336;">
            <i class="fas fa-sign-out-alt"></i>
            <span>Çıkış Yap</span>
        </a>
    </nav>
</div>

<style>
.sidebar-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1rem;
    color: #666;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s;
    margin-bottom: 0.5rem;
}

.sidebar-item:hover {
    background: #f8f5f0;
    color: var(--primary);
}

.sidebar-item.active {
    background: var(--primary);
    color: white;
}

.sidebar-item i {
    width: 20px;
    text-align: center;
}
</style>
