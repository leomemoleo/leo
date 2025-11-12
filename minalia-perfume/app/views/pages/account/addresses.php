<div class="account-container">
    <div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem;">
            Adreslerim
        </h1>

        <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
            <!-- Sidebar Menu -->
            <div>
                <?php include __DIR__ . '/../../components/account-sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div>
                <!-- Add New Address Button -->
                <div style="margin-bottom: 2rem;">
                    <button onclick="showAddressForm()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Yeni Adres Ekle
                    </button>
                </div>

                <?php if (empty($addresses)): ?>
                    <!-- No Addresses -->
                    <div class="card" style="text-align: center; padding: 4rem 2rem;">
                        <i class="fas fa-map-marker-alt" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                        <h3 style="margin: 0 0 1rem;">Kayıtlı Adresiniz Yok</h3>
                        <p style="color: #666; margin-bottom: 2rem;">Hızlı teslimat için adresinizi ekleyin.</p>
                        <button onclick="showAddressForm()" class="btn btn-primary">
                            <i class="fas fa-plus"></i> İlk Adresimi Ekle
                        </button>
                    </div>
                <?php else: ?>
                    <!-- Addresses List -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                        <?php foreach ($addresses as $address): ?>
                            <div class="card address-card">
                                <div class="card-body" style="padding: 1.5rem;">
                                    <!-- Address Header -->
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                        <h4 style="margin: 0;">
                                            <?= htmlspecialchars($address['title']) ?>
                                        </h4>
                                        <?php if ($address['is_default'] == 1): ?>
                                            <span class="badge badge-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">
                                                <i class="fas fa-star"></i> Varsayılan
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Address Details -->
                                    <div style="color: #666; line-height: 1.6; margin-bottom: 1rem;">
                                        <div style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">
                                            <?= htmlspecialchars($address['full_name']) ?>
                                        </div>
                                        <?= nl2br(htmlspecialchars($address['address'])) ?><br>
                                        <?= htmlspecialchars($address['district']) ?> / <?= htmlspecialchars($address['city']) ?><br>
                                        <?= htmlspecialchars($address['zip_code']) ?><br>
                                        <?= htmlspecialchars($address['country'] ?? 'Türkiye') ?><br><br>
                                        <strong>Tel:</strong> <?= htmlspecialchars($address['phone']) ?>
                                    </div>

                                    <!-- Actions -->
                                    <div style="display: flex; gap: 0.5rem; padding-top: 1rem; border-top: 1px solid #eee;">
                                        <?php if ($address['is_default'] != 1): ?>
                                            <button onclick="setDefaultAddress(<?= $address['id'] ?>)"
                                                    class="btn btn-sm btn-outline" style="flex: 1; padding: 0.5rem;">
                                                <i class="fas fa-star"></i> Varsayılan Yap
                                            </button>
                                        <?php endif; ?>
                                        <button onclick="editAddress(<?= $address['id'] ?>)"
                                                class="btn btn-sm btn-outline" style="flex: 1; padding: 0.5rem;">
                                            <i class="fas fa-edit"></i> Düzenle
                                        </button>
                                        <button onclick="deleteAddress(<?= $address['id'] ?>)"
                                                class="btn btn-sm btn-outline" style="padding: 0.5rem; color: #f44336; border-color: #f44336;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Address Modal -->
<div id="addressModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeAddressModal()"></div>
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 id="modalTitle">Yeni Adres Ekle</h3>
            <button onclick="closeAddressModal()" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addressForm" method="POST" action="/account/addresses/add">
                <input type="hidden" name="address_id" id="addressId">

                <div class="form-group">
                    <label>Adres Başlığı *</label>
                    <input type="text" name="title" id="addressTitle" class="form-control"
                           placeholder="Örn: Ev, İş, Yazlık" required>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div class="form-group">
                        <label>Ad Soyad *</label>
                        <input type="text" name="full_name" id="addressFullName" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Telefon *</label>
                        <input type="tel" name="phone" id="addressPhone" class="form-control"
                               placeholder="05XX XXX XX XX" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Adres *</label>
                    <textarea name="address" id="addressAddress" class="form-control"
                              rows="3" required></textarea>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div class="form-group">
                        <label>İlçe *</label>
                        <input type="text" name="district" id="addressDistrict" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>İl *</label>
                        <input type="text" name="city" id="addressCity" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Posta Kodu</label>
                        <input type="text" name="zip_code" id="addressZipCode" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_default" id="addressIsDefault">
                        <span>Varsayılan adres olarak ayarla</span>
                    </label>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                    <button type="button" onclick="closeAddressModal()" class="btn btn-outline">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.address-card {
    transition: all 0.3s;
}

.address-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.btn-sm {
    font-size: 0.875rem;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    position: relative;
    background: white;
    border-radius: 12px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    z-index: 1;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    line-height: 1;
    cursor: pointer;
    color: #999;
}

.modal-close:hover {
    color: #333;
}

.modal-body {
    padding: 1.5rem;
}

.badge {
    display: inline-block;
    border-radius: 12px;
    font-weight: 500;
}
.badge-primary {
    background: var(--primary);
    color: white;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.9375rem;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(122, 139, 92, 0.1);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
}
</style>

<script>
function showAddressForm() {
    document.getElementById('addressModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = 'Yeni Adres Ekle';
    document.getElementById('addressForm').reset();
    document.getElementById('addressForm').action = '/account/addresses/add';
}

function closeAddressModal() {
    document.getElementById('addressModal').style.display = 'none';
}

function editAddress(id) {
    // Load address data and show modal
    document.getElementById('addressModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = 'Adresi Düzenle';
    document.getElementById('addressId').value = id;
    document.getElementById('addressForm').action = '/account/addresses/update/' + id;
    // TODO: Load address data via AJAX
}

function deleteAddress(id) {
    if (confirm('Bu adresi silmek istediğinizden emin misiniz?')) {
        // TODO: Delete via AJAX
        console.log('Delete address:', id);
    }
}

function setDefaultAddress(id) {
    // TODO: Set default via AJAX
    console.log('Set default:', id);
}
</script>
