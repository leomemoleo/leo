<div class="page-header">
    <div>
        <h1 class="page-title">Kupon Düzenle</h1>
        <div class="page-breadcrumb">
            <span>Pazarlama</span>
            <span>›</span>
            <span>Kuponlar</span>
            <span>›</span>
            <span>Düzenle</span>
        </div>
    </div>
    <div>
        <a href="<?= ADMIN_URL ?>/coupons" class="btn">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>
</div>

<form method="POST" action="<?= ADMIN_URL ?>/coupons/edit?id=<?= $coupon['id'] ?>">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kupon Bilgileri</h3>
                </div>
                <div class="card-body">
                    <!-- Code -->
                    <div class="form-group">
                        <label class="form-label required">Kupon Kodu</label>
                        <input type="text" name="code" class="form-control" required
                               value="<?= htmlspecialchars($coupon['code']) ?>"
                               style="font-family: monospace; text-transform: uppercase;">
                        <small class="form-text">Kupon kodu otomatik olarak büyük harfe dönüştürülecektir.</small>
                    </div>

                    <!-- Type -->
                    <div class="form-group">
                        <label class="form-label required">İndirim Tipi</label>
                        <select name="type" class="form-control" required onchange="updateValueLabel(this.value)">
                            <option value="percentage" <?= $coupon['type'] === 'percentage' ? 'selected' : '' ?>>Yüzde (%)</option>
                            <option value="fixed" <?= $coupon['type'] === 'fixed' ? 'selected' : '' ?>>Sabit Tutar (₺)</option>
                        </select>
                    </div>

                    <!-- Value -->
                    <div class="form-group">
                        <label class="form-label required">İndirim Değeri <span id="valueLabel">(<?= $coupon['type'] === 'percentage' ? '%' : '₺' ?>)</span></label>
                        <input type="number" name="value" class="form-control" step="0.01" min="0" required
                               value="<?= $coupon['value'] ?>">
                    </div>

                    <!-- Min Purchase -->
                    <div class="form-group">
                        <label class="form-label">Minimum Sepet Tutarı (₺)</label>
                        <input type="number" name="min_purchase" class="form-control" step="0.01" min="0"
                               value="<?= $coupon['min_purchase'] ?? '' ?>"
                               placeholder="Boş bırakılırsa limit yok">
                    </div>

                    <!-- Max Uses -->
                    <div class="form-group">
                        <label class="form-label">Maksimum Kullanım Sayısı</label>
                        <input type="number" name="max_uses" class="form-control" min="1"
                               value="<?= $coupon['max_uses'] ?? '' ?>"
                               placeholder="Boş bırakılırsa sınırsız">
                        <?php if (isset($coupon['used_count']) && $coupon['used_count'] > 0): ?>
                            <small class="form-text" style="color: var(--admin-info);">
                                <i class="fas fa-info-circle"></i> Bu kupon şu ana kadar <?= $coupon['used_count'] ?> kez kullanıldı.
                            </small>
                        <?php endif; ?>
                    </div>

                    <!-- Date Range -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Başlangıç Tarihi</label>
                                <input type="date" name="start_date" class="form-control"
                                       value="<?= $coupon['start_date'] ? date('Y-m-d', strtotime($coupon['start_date'])) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Bitiş Tarihi</label>
                                <input type="date" name="end_date" class="form-control"
                                       value="<?= $coupon['end_date'] ? date('Y-m-d', strtotime($coupon['end_date'])) : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Durum</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="is_active" value="1" <?= $coupon['is_active'] ? 'checked' : '' ?>>
                            <span>Kuponu aktif et</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> Değişiklikleri Kaydet
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <?php if (isset($coupon['used_count']) && $coupon['used_count'] > 0): ?>
                <div class="card" style="margin-top: 1rem;">
                    <div class="card-header">
                        <h3 class="card-title">İstatistikler</h3>
                    </div>
                    <div class="card-body">
                        <div style="text-align: center;">
                            <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem;">
                                <?= $coupon['used_count'] ?>
                            </div>
                            <div style="color: #666;">Toplam Kullanım</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<script>
function updateValueLabel(type) {
    const label = document.getElementById('valueLabel');
    label.textContent = type === 'percentage' ? '(%)' : '(₺)';
}
</script>
