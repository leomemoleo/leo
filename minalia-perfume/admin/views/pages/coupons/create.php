<div class="page-header">
    <div>
        <h1 class="page-title">Yeni Kupon Ekle</h1>
        <div class="page-breadcrumb">
            <span>Pazarlama</span>
            <span>›</span>
            <span>Kuponlar</span>
            <span>›</span>
            <span>Yeni Ekle</span>
        </div>
    </div>
    <div>
        <a href="<?= ADMIN_URL ?>/coupons" class="btn">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>
</div>

<form method="POST" action="<?= ADMIN_URL ?>/coupons/create">
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
                               placeholder="Örn: YILBASI2024"
                               style="font-family: monospace; text-transform: uppercase;">
                        <small class="form-text">Kupon kodu otomatik olarak büyük harfe dönüştürülecektir.</small>
                    </div>

                    <!-- Type -->
                    <div class="form-group">
                        <label class="form-label required">İndirim Tipi</label>
                        <select name="type" class="form-control" required onchange="updateValueLabel(this.value)">
                            <option value="percentage">Yüzde (%)</option>
                            <option value="fixed">Sabit Tutar (₺)</option>
                        </select>
                    </div>

                    <!-- Value -->
                    <div class="form-group">
                        <label class="form-label required">İndirim Değeri <span id="valueLabel">(%)</span></label>
                        <input type="number" name="value" class="form-control" step="0.01" min="0" required
                               placeholder="Örn: 20">
                    </div>

                    <!-- Min Purchase -->
                    <div class="form-group">
                        <label class="form-label">Minimum Sepet Tutarı (₺)</label>
                        <input type="number" name="min_purchase" class="form-control" step="0.01" min="0"
                               placeholder="Boş bırakılırsa limit yok">
                    </div>

                    <!-- Max Uses -->
                    <div class="form-group">
                        <label class="form-label">Maksimum Kullanım Sayısı</label>
                        <input type="number" name="max_uses" class="form-control" min="1"
                               placeholder="Boş bırakılırsa sınırsız">
                    </div>

                    <!-- Date Range -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Başlangıç Tarihi</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Bitiş Tarihi</label>
                                <input type="date" name="end_date" class="form-control">
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
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span>Kuponu aktif et</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> Kuponu Kaydet
                    </button>
                </div>
            </div>

            <!-- Preview -->
            <div class="card" style="margin-top: 1rem;">
                <div class="card-header">
                    <h3 class="card-title">Önizleme</h3>
                </div>
                <div class="card-body" style="text-align: center;">
                    <div style="padding: 1.5rem; background: linear-gradient(135deg, #D4AF37 0%, #C5A028 100%); border-radius: 12px; color: white;">
                        <div style="font-size: 2rem; font-weight: 700; font-family: monospace; margin-bottom: 0.5rem;">
                            KUPONKODU
                        </div>
                        <div style="font-size: 1.2rem; opacity: 0.95;">
                            % İndirim
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function updateValueLabel(type) {
    const label = document.getElementById('valueLabel');
    label.textContent = type === 'percentage' ? '(%)' : '(₺)';
}
</script>
