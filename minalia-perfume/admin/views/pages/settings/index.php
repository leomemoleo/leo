<?php
// Helper function to get setting value
function getSetting($settings, $category, $key, $default = '') {
    return $settings[$category][$key]['value'] ?? $default;
}

function isChecked($settings, $category, $key) {
    $value = getSetting($settings, $category, $key, '0');
    return $value == '1' ? 'checked' : '';
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-cog"></i> Site Ayarları</h1>
        <div class="page-breadcrumb">
            <span>Admin Panel</span>
            <span>›</span>
            <span>Site Ayarları</span>
        </div>
    </div>
</div>

<!-- Settings Tabs -->
<div class="settings-container">
    <div class="settings-tabs">
        <a href="?tab=general" class="settings-tab <?= $active_tab === 'general' ? 'active' : '' ?>">
            <i class="fas fa-info-circle"></i>
            <span>Genel</span>
        </a>
        <a href="?tab=email" class="settings-tab <?= $active_tab === 'email' ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i>
            <span>Email</span>
        </a>
        <a href="?tab=payment" class="settings-tab <?= $active_tab === 'payment' ? 'active' : '' ?>">
            <i class="fas fa-credit-card"></i>
            <span>Ödeme</span>
        </a>
        <a href="?tab=shipping" class="settings-tab <?= $active_tab === 'shipping' ? 'active' : '' ?>">
            <i class="fas fa-truck"></i>
            <span>Kargo</span>
        </a>
        <a href="?tab=seo" class="settings-tab <?= $active_tab === 'seo' ? 'active' : '' ?>">
            <i class="fas fa-search"></i>
            <span>SEO</span>
        </a>
        <a href="?tab=social" class="settings-tab <?= $active_tab === 'social' ? 'active' : '' ?>">
            <i class="fas fa-share-alt"></i>
            <span>Sosyal Medya</span>
        </a>
        <a href="?tab=analytics" class="settings-tab <?= $active_tab === 'analytics' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Analytics</span>
        </a>
        <a href="?tab=advanced" class="settings-tab <?= $active_tab === 'advanced' ? 'active' : '' ?>">
            <i class="fas fa-cogs"></i>
            <span>Gelişmiş</span>
        </a>
    </div>

    <div class="settings-content">
        <?php
        // Include the appropriate tab content
        $tabFile = __DIR__ . '/tabs/' . $active_tab . '.php';
        if (file_exists($tabFile)) {
            include $tabFile;
        } else {
            echo '<div class="card"><div class="card-body">Sekme bulunamadı.</div></div>';
        }
        ?>
    </div>
</div>

<style>
.settings-container {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
    margin-top: 2rem;
}

.settings-tabs {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.settings-tab {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    background: white;
    border: 1px solid var(--color-border);
    border-radius: 8px;
    color: var(--color-text);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.settings-tab:hover {
    background: var(--color-cream);
    border-color: var(--admin-primary);
    transform: translateX(4px);
}

.settings-tab.active {
    background: linear-gradient(135deg, var(--admin-primary), var(--admin-success));
    color: white;
    border-color: var(--admin-primary);
    box-shadow: 0 4px 12px rgba(122, 139, 92, 0.3);
}

.settings-tab i {
    font-size: 1.125rem;
    width: 20px;
    text-align: center;
}

.settings-content {
    min-height: 500px;
}

.settings-group {
    margin-bottom: 2rem;
}

.settings-group-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--admin-primary);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--color-border);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--color-text);
}

.form-label .text-muted {
    font-weight: 400;
    font-size: 0.875rem;
    color: #999;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--color-border);
    border-radius: 6px;
    font-size: 0.9375rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px rgba(122, 139, 92, 0.1);
}

.form-switch {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.form-switch input[type="checkbox"] {
    width: 50px;
    height: 26px;
    position: relative;
    appearance: none;
    background: #ccc;
    border-radius: 13px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.form-switch input[type="checkbox"]:checked {
    background: var(--admin-success);
}

.form-switch input[type="checkbox"]::before {
    content: '';
    position: absolute;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: white;
    top: 2px;
    left: 2px;
    transition: transform 0.3s ease;
}

.form-switch input[type="checkbox"]:checked::before {
    transform: translateX(24px);
}

.form-switch-label {
    flex: 1;
}

.form-switch-label strong {
    display: block;
    margin-bottom: 0.25rem;
}

.form-switch-label small {
    color: #666;
}

.btn-save {
    background: linear-gradient(135deg, var(--admin-success), #45a049);
    color: white;
    padding: 0.875rem 2rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.btn-secondary {
    background: #6c757d;
    color: white;
    padding: 0.875rem 2rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #5a6268;
}

.image-preview {
    margin-top: 1rem;
    max-width: 200px;
}

.image-preview img {
    width: 100%;
    border-radius: 8px;
    border: 2px solid var(--color-border);
}

@media (max-width: 1024px) {
    .settings-container {
        grid-template-columns: 1fr;
    }

    .settings-tabs {
        flex-direction: row;
        overflow-x: auto;
        padding-bottom: 1rem;
    }

    .settings-tab {
        flex-shrink: 0;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>
