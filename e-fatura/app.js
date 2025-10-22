// e-Fatura Sistemi - JavaScript

const API_BASE = 'api/';

// Sayfa Yönetimi
function changePage(pageName) {
    // Tüm sayfaları gizle
    document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
    });

    // Nav linkleri güncelle
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });

    // Seçili sayfayı göster
    const page = document.getElementById(pageName);
    if (page) {
        page.classList.add('active');
    }

    // Seçili nav linkini işaretle
    const navLink = document.querySelector(`[data-page="${pageName}"]`);
    if (navLink) {
        navLink.classList.add('active');
    }

    // Sayfa değiştiğinde veri yükle
    loadPageData(pageName);
}

// Sayfa verilerini yükle
function loadPageData(pageName) {
    switch (pageName) {
        case 'dashboard':
            loadDashboard();
            break;
        case 'invoices':
            loadInvoices();
            break;
        case 'customers':
            loadCustomers();
            break;
        case 'products':
            loadProducts();
            break;
    }
}

// Dashboard Verilerini Yükle
async function loadDashboard() {
    try {
        // İstatistikleri yükle
        const invoices = await fetch(API_BASE + 'invoices.php').then(r => r.json());
        const customers = await fetch(API_BASE + 'customers.php').then(r => r.json());
        const products = await fetch(API_BASE + 'products.php').then(r => r.json());

        // İstatistikleri güncelle
        document.getElementById('totalInvoices').textContent = invoices.data?.total || 0;
        document.getElementById('totalCustomers').textContent = customers.data?.total || 0;
        document.getElementById('totalProducts').textContent = products.data?.length || 0;

        // Toplam ciroyu hesapla
        let totalRevenue = 0;
        if (invoices.data?.invoices) {
            totalRevenue = invoices.data.invoices
                .filter(inv => inv.invoice_status !== 'cancelled')
                .reduce((sum, inv) => sum + parseFloat(inv.total_amount || 0), 0);
        }
        document.getElementById('totalRevenue').textContent = formatMoney(totalRevenue);

        // Son faturaları göster
        if (invoices.data?.invoices) {
            renderRecentInvoices(invoices.data.invoices.slice(0, 5));
        }
    } catch (error) {
        console.error('Dashboard yüklenemedi:', error);
        showAlert('Dashboard yüklenirken hata oluştu', 'error');
    }
}

// Son Faturaları Göster
function renderRecentInvoices(invoices) {
    const tbody = document.querySelector('#recentInvoicesTable tbody');
    if (!invoices || invoices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Henüz fatura yok</td></tr>';
        return;
    }

    tbody.innerHTML = invoices.map(inv => `
        <tr>
            <td><strong>${inv.invoice_no}</strong></td>
            <td>${inv.customer_name || 'N/A'}</td>
            <td>${formatDate(inv.invoice_date)}</td>
            <td class="text-right">${formatMoney(inv.total_amount)}</td>
            <td>${getStatusBadge(inv.invoice_status)}</td>
        </tr>
    `).join('');
}

// Faturaları Yükle
async function loadInvoices() {
    try {
        const search = document.getElementById('invoiceSearch')?.value || '';
        const status = document.getElementById('invoiceStatusFilter')?.value || '';
        const dateFrom = document.getElementById('dateFrom')?.value || '';
        const dateTo = document.getElementById('dateTo')?.value || '';

        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);

        const response = await fetch(API_BASE + 'invoices.php?' + params.toString());
        const result = await response.json();

        if (result.success && result.data?.invoices) {
            renderInvoices(result.data.invoices);
        } else {
            showAlert('Faturalar yüklenemedi', 'error');
        }
    } catch (error) {
        console.error('Faturalar yüklenemedi:', error);
        showAlert('Faturalar yüklenirken hata oluştu', 'error');
    }
}

// Faturaları Göster
function renderInvoices(invoices) {
    const tbody = document.querySelector('#invoicesTable tbody');
    if (!invoices || invoices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Fatura bulunamadı</td></tr>';
        return;
    }

    tbody.innerHTML = invoices.map(inv => `
        <tr>
            <td><strong>${inv.invoice_no}</strong></td>
            <td>${inv.customer_name || 'N/A'}</td>
            <td>${formatDate(inv.invoice_date)}</td>
            <td class="text-right">${formatMoney(inv.subtotal)}</td>
            <td class="text-right">${formatMoney(inv.total_kdv)}</td>
            <td class="text-right"><strong>${formatMoney(inv.total_amount)}</strong></td>
            <td>${getStatusBadge(inv.invoice_status)}</td>
            <td>
                <button class="btn btn-small btn-primary" onclick="viewInvoice(${inv.id})">Görüntüle</button>
                ${inv.invoice_status === 'draft' ? `
                    <button class="btn btn-small btn-danger" onclick="deleteInvoice(${inv.id})">Sil</button>
                ` : ''}
            </td>
        </tr>
    `).join('');
}

// Müşterileri Yükle
async function loadCustomers() {
    try {
        const search = document.getElementById('customerSearch')?.value || '';
        const params = new URLSearchParams();
        if (search) params.append('search', search);

        const response = await fetch(API_BASE + 'customers.php?' + params.toString());
        const result = await response.json();

        if (result.success && result.data?.customers) {
            renderCustomers(result.data.customers);
        } else {
            showAlert('Müşteriler yüklenemedi', 'error');
        }
    } catch (error) {
        console.error('Müşteriler yüklenemedi:', error);
        showAlert('Müşteriler yüklenirken hata oluştu', 'error');
    }
}

// Müşterileri Göster
function renderCustomers(customers) {
    const tbody = document.querySelector('#customersTable tbody');
    if (!customers || customers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Müşteri bulunamadı</td></tr>';
        return;
    }

    tbody.innerHTML = customers.map(cust => `
        <tr>
            <td><strong>${cust.customer_name}</strong></td>
            <td>${cust.customer_type === 'corporate' ? 'Kurumsal' : 'Bireysel'}</td>
            <td>${cust.tax_number || cust.tc_no || '-'}</td>
            <td>${cust.tax_office || '-'}</td>
            <td>${cust.city}</td>
            <td>${cust.phone || '-'}</td>
            <td>
                <button class="btn btn-small btn-primary" onclick="editCustomer(${cust.id})">Düzenle</button>
                <button class="btn btn-small btn-danger" onclick="deleteCustomer(${cust.id})">Sil</button>
            </td>
        </tr>
    `).join('');
}

// Ürünleri Yükle
async function loadProducts() {
    try {
        const search = document.getElementById('productSearch')?.value || '';
        const type = document.getElementById('productTypeFilter')?.value || '';
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (type) params.append('type', type);

        const response = await fetch(API_BASE + 'products.php?' + params.toString());
        const result = await response.json();

        if (result.success && result.data) {
            renderProducts(result.data);
        } else {
            showAlert('Ürünler yüklenemedi', 'error');
        }
    } catch (error) {
        console.error('Ürünler yüklenemedi:', error);
        showAlert('Ürünler yüklenirken hata oluştu', 'error');
    }
}

// Ürünleri Göster
function renderProducts(products) {
    const tbody = document.querySelector('#productsTable tbody');
    if (!products || products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Ürün bulunamadı</td></tr>';
        return;
    }

    tbody.innerHTML = products.map(prod => `
        <tr>
            <td>${prod.product_code || '-'}</td>
            <td><strong>${prod.product_name}</strong></td>
            <td>${prod.product_type === 'service' ? 'Hizmet' : 'Ürün'}</td>
            <td>${prod.unit}</td>
            <td class="text-right">${formatMoney(prod.unit_price)}</td>
            <td class="text-right">%${prod.kdv_rate}</td>
            <td>${prod.is_active == 1 ? '<span class="badge badge-sent">Aktif</span>' : '<span class="badge badge-cancelled">Pasif</span>'}</td>
            <td>
                <button class="btn btn-small btn-primary" onclick="editProduct(${prod.id})">Düzenle</button>
                <button class="btn btn-small btn-danger" onclick="deleteProduct(${prod.id})">Sil</button>
            </td>
        </tr>
    `).join('');
}

// Yeni Fatura Formu
function showNewInvoiceForm() {
    const modal = createModal('Yeni Fatura Oluştur', `
        <form id="newInvoiceForm">
            <div class="form-grid">
                <div class="form-group">
                    <label>Müşteri *</label>
                    <select name="customer_id" required id="customerSelect">
                        <option value="">Yükleniyor...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fatura Tarihi *</label>
                    <input type="date" name="invoice_date" required value="${new Date().toISOString().split('T')[0]}">
                </div>
                <div class="form-group">
                    <label>Para Birimi</label>
                    <select name="currency_code">
                        <option value="TRY">TRY - Türk Lirası</option>
                        <option value="USD">USD - Dolar</option>
                        <option value="EUR">EUR - Euro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Senaryo</label>
                    <select name="invoice_scenario">
                        <option value="basic">Temel Fatura</option>
                        <option value="commercial">Ticari Fatura</option>
                    </select>
                </div>
            </div>

            <h4>Fatura Kalemleri</h4>
            <table class="invoice-items-table" id="invoiceItemsTable">
                <thead>
                    <tr>
                        <th style="width: 30%">Ürün/Hizmet</th>
                        <th style="width: 10%">Miktar</th>
                        <th style="width: 10%">Birim</th>
                        <th style="width: 15%">Birim Fiyat</th>
                        <th style="width: 10%">KDV %</th>
                        <th style="width: 15%">Toplam</th>
                        <th style="width: 10%">İşlem</th>
                    </tr>
                </thead>
                <tbody id="invoiceItemsBody">
                    <tr>
                        <td colspan="7" class="text-center">
                            <button type="button" class="btn btn-secondary" onclick="addInvoiceItem()">➕ Kalem Ekle</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="form-grid" style="margin-top: 2rem;">
                <div class="form-group">
                    <label>Not</label>
                    <textarea name="notes" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <h4>Fatura Toplamı</h4>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 6px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Ara Toplam:</span>
                            <strong id="subtotalDisplay">0,00 ₺</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>KDV:</span>
                            <strong id="taxDisplay">0,00 ₺</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 1.2rem; color: #2563eb;">
                            <span>Genel Toplam:</span>
                            <strong id="totalDisplay">0,00 ₺</strong>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    `, [
        { text: 'İptal', class: 'btn-secondary', onclick: 'closeModal()' },
        { text: 'Kaydet', class: 'btn-primary', onclick: 'submitInvoice()' }
    ]);

    // Müşterileri yükle
    loadCustomersForSelect();
}

// Müşteri Formu
function showCustomerForm(customerId = null) {
    const modal = createModal('Yeni Müşteri Ekle', `
        <form id="customerForm">
            <div class="form-grid">
                <div class="form-group">
                    <label>Müşteri Türü *</label>
                    <select name="customer_type" onchange="toggleCustomerType(this.value)" required>
                        <option value="individual">Bireysel</option>
                        <option value="corporate">Kurumsal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Müşteri Adı *</label>
                    <input type="text" name="customer_name" required>
                </div>
                <div class="form-group corporate-field">
                    <label>Vergi Dairesi</label>
                    <input type="text" name="tax_office">
                </div>
                <div class="form-group corporate-field">
                    <label>Vergi Numarası</label>
                    <input type="text" name="tax_number" maxlength="10">
                </div>
                <div class="form-group individual-field" style="display:none">
                    <label>TC Kimlik No</label>
                    <input type="text" name="tc_no" maxlength="11">
                </div>
                <div class="form-group">
                    <label>Telefon</label>
                    <input type="tel" name="phone">
                </div>
                <div class="form-group">
                    <label>E-posta</label>
                    <input type="email" name="email">
                </div>
                <div class="form-group">
                    <label>Şehir *</label>
                    <input type="text" name="city" required>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Adres *</label>
                    <textarea name="address" required rows="3"></textarea>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Notlar</label>
                    <textarea name="notes" rows="2"></textarea>
                </div>
            </div>
        </form>
    `, [
        { text: 'İptal', class: 'btn-secondary', onclick: 'closeModal()' },
        { text: 'Kaydet', class: 'btn-primary', onclick: 'submitCustomer()' }
    ]);
}

// Ürün Formu
function showProductForm(productId = null) {
    const modal = createModal('Yeni Ürün Ekle', `
        <form id="productForm">
            <div class="form-grid">
                <div class="form-group">
                    <label>Ürün Kodu</label>
                    <input type="text" name="product_code">
                </div>
                <div class="form-group">
                    <label>Ürün Adı *</label>
                    <input type="text" name="product_name" required>
                </div>
                <div class="form-group">
                    <label>Tür</label>
                    <select name="product_type">
                        <option value="product">Ürün</option>
                        <option value="service">Hizmet</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Birim</label>
                    <input type="text" name="unit" value="Adet">
                </div>
                <div class="form-group">
                    <label>Birim Fiyat *</label>
                    <input type="number" name="unit_price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>KDV Oranı (%)</label>
                    <select name="kdv_rate">
                        <option value="0">0%</option>
                        <option value="1">1%</option>
                        <option value="10">10%</option>
                        <option value="20" selected>20%</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Açıklama</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
            </div>
        </form>
    `, [
        { text: 'İptal', class: 'btn-secondary', onclick: 'closeModal()' },
        { text: 'Kaydet', class: 'btn-primary', onclick: 'submitProduct()' }
    ]);
}

// Form Submit Fonksiyonları
async function submitCustomer() {
    const form = document.getElementById('customerForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch(API_BASE + 'customers.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showAlert('Müşteri başarıyla eklendi', 'success');
            closeModal();
            loadCustomers();
        } else {
            showAlert(result.error || 'Hata oluştu', 'error');
        }
    } catch (error) {
        showAlert('Bağlantı hatası', 'error');
    }
}

async function submitProduct() {
    const form = document.getElementById('productForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch(API_BASE + 'products.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showAlert('Ürün başarıyla eklendi', 'success');
            closeModal();
            loadProducts();
        } else {
            showAlert(result.error || 'Hata oluştu', 'error');
        }
    } catch (error) {
        showAlert('Bağlantı hatası', 'error');
    }
}

async function submitInvoice() {
    const form = document.getElementById('newInvoiceForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    // Fatura kalemlerini topla
    const items = [];
    document.querySelectorAll('#invoiceItemsBody tr[data-item]').forEach(row => {
        const inputs = row.querySelectorAll('input, select');
        const item = {};
        inputs.forEach(input => {
            item[input.name] = input.value;
        });
        items.push(item);
    });

    if (items.length === 0) {
        showAlert('En az bir ürün eklemelisiniz', 'error');
        return;
    }

    data.items = items;

    try {
        const response = await fetch(API_BASE + 'invoices.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showAlert('Fatura başarıyla oluşturuldu', 'success');
            closeModal();
            loadInvoices();
        } else {
            showAlert(result.error || 'Hata oluştu', 'error');
        }
    } catch (error) {
        showAlert('Bağlantı hatası', 'error');
    }
}

// Yardımcı Fonksiyonlar
function formatMoney(amount) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY'
    }).format(amount);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR');
}

function getStatusBadge(status) {
    const badges = {
        draft: 'Taslak',
        issued: 'Kesildi',
        sent: 'Gönderildi',
        cancelled: 'İptal'
    };
    return `<span class="badge badge-${status}">${badges[status] || status}</span>`;
}

// Modal Yönetimi
function createModal(title, content, buttons) {
    const modalHTML = `
        <div class="modal active" id="mainModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>${title}</h3>
                    <button class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">${content}</div>
                <div class="modal-footer">
                    ${buttons.map(btn => `
                        <button class="btn ${btn.class}" onclick="${btn.onclick}">${btn.text}</button>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    document.getElementById('modalContainer').innerHTML = modalHTML;
}

function closeModal() {
    document.getElementById('modalContainer').innerHTML = '';
}

// Alert Göster
function showAlert(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.style.minWidth = '300px';
    document.body.appendChild(alert);

    setTimeout(() => alert.remove(), 3000);
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Nav linklerini dinle
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = link.getAttribute('data-page');
            changePage(page);
        });
    });

    // İlk yükleme
    loadDashboard();
});

// Müşterileri select için yükle
async function loadCustomersForSelect() {
    try {
        const response = await fetch(API_BASE + 'customers.php');
        const result = await response.json();

        const select = document.getElementById('customerSelect');
        if (result.success && result.data?.customers) {
            select.innerHTML = '<option value="">Müşteri Seçin</option>' +
                result.data.customers.map(c => `<option value="${c.id}">${c.customer_name}</option>`).join('');
        }
    } catch (error) {
        console.error('Müşteriler yüklenemedi:', error);
    }
}

// Fatura kalemi ekle
let itemCounter = 0;
function addInvoiceItem() {
    const tbody = document.getElementById('invoiceItemsBody');
    const emptyRow = tbody.querySelector('tr:not([data-item])');
    if (emptyRow) emptyRow.remove();

    itemCounter++;
    const row = document.createElement('tr');
    row.setAttribute('data-item', itemCounter);
    row.innerHTML = `
        <td><input type="text" name="product_name" required placeholder="Ürün/Hizmet adı"></td>
        <td><input type="number" name="quantity" value="1" min="0.001" step="0.001" onchange="calculateItemTotal(${itemCounter})"></td>
        <td><input type="text" name="unit" value="Adet"></td>
        <td><input type="number" name="unit_price" value="0" min="0" step="0.01" onchange="calculateItemTotal(${itemCounter})"></td>
        <td><input type="number" name="kdv_rate" value="20" min="0" max="100" onchange="calculateItemTotal(${itemCounter})"></td>
        <td class="text-right"><strong class="item-total">0,00 ₺</strong></td>
        <td><button type="button" class="btn btn-small btn-danger" onclick="removeInvoiceItem(${itemCounter})">Sil</button></td>
    `;
    tbody.appendChild(row);
}

function removeInvoiceItem(itemId) {
    document.querySelector(`tr[data-item="${itemId}"]`)?.remove();
    calculateInvoiceTotal();
}

function calculateItemTotal(itemId) {
    const row = document.querySelector(`tr[data-item="${itemId}"]`);
    const quantity = parseFloat(row.querySelector('[name="quantity"]').value) || 0;
    const unitPrice = parseFloat(row.querySelector('[name="unit_price"]').value) || 0;
    const kdvRate = parseFloat(row.querySelector('[name="kdv_rate"]').value) || 0;

    const subtotal = quantity * unitPrice;
    const tax = subtotal * (kdvRate / 100);
    const total = subtotal + tax;

    row.querySelector('.item-total').textContent = formatMoney(total);
    calculateInvoiceTotal();
}

function calculateInvoiceTotal() {
    let subtotal = 0;
    let totalTax = 0;

    document.querySelectorAll('#invoiceItemsBody tr[data-item]').forEach(row => {
        const quantity = parseFloat(row.querySelector('[name="quantity"]').value) || 0;
        const unitPrice = parseFloat(row.querySelector('[name="unit_price"]').value) || 0;
        const kdvRate = parseFloat(row.querySelector('[name="kdv_rate"]').value) || 0;

        const lineSubtotal = quantity * unitPrice;
        const lineTax = lineSubtotal * (kdvRate / 100);

        subtotal += lineSubtotal;
        totalTax += lineTax;
    });

    const total = subtotal + totalTax;

    document.getElementById('subtotalDisplay').textContent = formatMoney(subtotal);
    document.getElementById('taxDisplay').textContent = formatMoney(totalTax);
    document.getElementById('totalDisplay').textContent = formatMoney(total);
}

function toggleCustomerType(type) {
    const corporateFields = document.querySelectorAll('.corporate-field');
    const individualFields = document.querySelectorAll('.individual-field');

    if (type === 'corporate') {
        corporateFields.forEach(f => f.style.display = 'flex');
        individualFields.forEach(f => f.style.display = 'none');
    } else {
        corporateFields.forEach(f => f.style.display = 'none');
        individualFields.forEach(f => f.style.display = 'flex');
    }
}

// Fatura görüntüleme ve silme fonksiyonları
async function viewInvoice(id) {
    showAlert('Fatura detayı hazırlanıyor...', 'info');
    // TODO: Fatura detay modalı
}

async function deleteInvoice(id) {
    if (!confirm('Bu faturayı silmek istediğinizden emin misiniz?')) return;

    try {
        const response = await fetch(API_BASE + 'invoices.php?id=' + id, { method: 'DELETE' });
        const result = await response.json();

        if (result.success) {
            showAlert('Fatura silindi', 'success');
            loadInvoices();
        } else {
            showAlert(result.error || 'Hata oluştu', 'error');
        }
    } catch (error) {
        showAlert('Bağlantı hatası', 'error');
    }
}

async function deleteCustomer(id) {
    if (!confirm('Bu müşteriyi silmek istediğinizden emin misiniz?')) return;

    try {
        const response = await fetch(API_BASE + 'customers.php?id=' + id, { method: 'DELETE' });
        const result = await response.json();

        if (result.success) {
            showAlert('Müşteri silindi', 'success');
            loadCustomers();
        } else {
            showAlert(result.error || 'Hata oluştu', 'error');
        }
    } catch (error) {
        showAlert('Bağlantı hatası', 'error');
    }
}

async function deleteProduct(id) {
    if (!confirm('Bu ürünü silmek istediğinizden emin misiniz?')) return;

    try {
        const response = await fetch(API_BASE + 'products.php?id=' + id, { method: 'DELETE' });
        const result = await response.json();

        if (result.success) {
            showAlert('Ürün silindi', 'success');
            loadProducts();
        } else {
            showAlert(result.error || 'Hata oluştu', 'error');
        }
    } catch (error) {
        showAlert('Bağlantı hatası', 'error');
    }
}

function filterInvoices() {
    loadInvoices();
}

function editCustomer(id) {
    showAlert('Düzenleme özelliği hazırlanıyor...', 'info');
    // TODO: Müşteri düzenleme
}

function editProduct(id) {
    showAlert('Düzenleme özelliği hazırlanıyor...', 'info');
    // TODO: Ürün düzenleme
}
