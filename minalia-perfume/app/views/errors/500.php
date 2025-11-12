<!-- 500 Error Page -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 fw-bold text-danger">500</h1>
                <h2 class="mb-4">Sunucu Hatası</h2>
                <p class="lead mb-4">
                    <?= htmlspecialchars($message ?? 'Üzgünüz, bir sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.') ?>
                </p>
                <div class="mb-4">
                    <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="100" cy="100" r="80" stroke="#dc3545" stroke-width="4" fill="none"/>
                        <circle cx="75" cy="90" r="5" fill="#dc3545"/>
                        <circle cx="125" cy="90" r="5" fill="#dc3545"/>
                        <path d="M75 135 Q100 120 125 135" stroke="#dc3545" stroke-width="4" stroke-linecap="round" fill="none"/>
                        <line x1="60" y1="60" x2="140" y2="140" stroke="#dc3545" stroke-width="4" stroke-linecap="round"/>
                        <line x1="140" y1="60" x2="60" y2="140" stroke="#dc3545" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="<?= BASE_URL ?>/" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                    </a>
                    <a href="javascript:location.reload()" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Tekrar Dene
                    </a>
                </div>

                <!-- Contact Support -->
                <div class="mt-5">
                    <p class="text-muted">Sorun devam ediyorsa lütfen bizimle iletişime geçin:</p>
                    <a href="<?= BASE_URL ?>/contact" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-envelope me-2"></i>İletişim
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-page {
    padding: 3rem 0;
}

.error-page h1 {
    font-size: 8rem;
    color: #dc3545;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.error-page svg {
    animation: shake 0.5s infinite;
}

@keyframes shake {
    0%, 100% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-10px);
    }
    75% {
        transform: translateX(10px);
    }
}
</style>
