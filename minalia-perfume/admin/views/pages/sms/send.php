<?php $content = ob_start(); ?>

<div class="content-header">
    <h1><i class="fas fa-paper-plane"></i> SMS Gönder</h1>
    <div class="header-actions">
        <a href="/admin/sms" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3>Yeni SMS</h3>
            </div>
            <div class="card-body">
                <form id="send-sms-form">
                    <div class="form-group">
                        <label>Telefon Numarası *</label>
                        <input type="tel" name="phone" class="form-control"
                               placeholder="05XXXXXXXXX" required>
                        <small class="form-text">Örnek: 05321234567</small>
                    </div>

                    <div class="form-group">
                        <label>Mesaj *</label>
                        <textarea name="message" class="form-control" rows="5"
                                  maxlength="160" required></textarea>
                        <small class="form-text">
                            <span id="char-count">0</span> / 160 karakter
                        </small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Gönder
                        </button>
                        <button type="button" class="btn btn-outline" onclick="testMessage()">
                            <i class="fas fa-flask"></i> Test Et
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3>SMS Şablonları</h3>
            </div>
            <div class="card-body">
                <div class="template-list">
                    <button class="template-item" onclick="useTemplate('Siparişiniz #{order_number} alındı. Teşekkür ederiz!')">
                        <i class="fas fa-shopping-bag"></i>
                        Sipariş Alındı
                    </button>
                    <button class="template-item" onclick="useTemplate('Siparişiniz #{order_number} kargoya verildi.')">
                        <i class="fas fa-truck"></i>
                        Kargoya Verildi
                    </button>
                    <button class="template-item" onclick="useTemplate('Doğrulama kodunuz: {code}')">
                        <i class="fas fa-shield-alt"></i>
                        Doğrulama Kodu
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Bilgilendirme</h3>
            </div>
            <div class="card-body">
                <ul class="info-list">
                    <li><i class="fas fa-check text-success"></i> Maksimum 160 karakter</li>
                    <li><i class="fas fa-check text-success"></i> Türkçe karakter desteği</li>
                    <li><i class="fas fa-check text-success"></i> Türkiye için 05XX formatı</li>
                    <li><i class="fas fa-info-circle text-info"></i> Teslimat süresi: 5-30 saniye</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.template-item {
    display: block;
    width: 100%;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    text-align: left;
    transition: all 0.3s;
}

.template-item:hover {
    background: var(--admin-primary);
    color: white;
    transform: translateX(5px);
}

.template-item i {
    margin-right: 0.5rem;
}

.info-list {
    list-style: none;
    padding: 0;
}

.info-list li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.info-list li:last-child {
    border-bottom: none;
}

.info-list i {
    margin-right: 0.5rem;
}
</style>

<script>
const form = document.getElementById('send-sms-form');
const messageInput = form.querySelector('[name="message"]');
const charCount = document.getElementById('char-count');

// Character counter
messageInput.addEventListener('input', () => {
    charCount.textContent = messageInput.value.length;
});

// Use template
function useTemplate(message) {
    messageInput.value = message;
    messageInput.dispatchEvent(new Event('input'));
}

// Test message
function testMessage() {
    const message = messageInput.value;
    if (!message) {
        alert('Lütfen mesaj girin');
        return;
    }

    alert(`Test Mesajı:\n\n${message}\n\nKarakter: ${message.length}/160`);
}

// Send SMS
form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...';

    try {
        const response = await fetch('/admin/sms/send', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showToast('SMS başarıyla gönderildi!', 'success');
            form.reset();
            charCount.textContent = '0';
        } else {
            showToast(result.message || 'SMS gönderilemedi', 'error');
        }

    } catch (error) {
        showToast('Bir hata oluştu', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Gönder';
    }
});

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
    toast.textContent = message;
    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; animation: slideIn 0.3s;';

    document.body.appendChild(toast);

    setTimeout(() => toast.remove(), 3000);
}
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../../layouts/admin.php'; ?>
