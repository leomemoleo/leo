<!-- 404 Error Page -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="display-1 fw-bold text-primary">404</h1>
                <h2 class="mb-4">Sayfa Bulunamadı</h2>
                <p class="lead mb-4">
                    <?= htmlspecialchars($message ?? 'Aradığınız sayfa bulunamadı veya kaldırılmış olabilir.') ?>
                </p>
                <div class="mb-4">
                    <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="100" cy="100" r="80" stroke="#7A8B5C" stroke-width="4" fill="none"/>
                        <path d="M70 90 Q70 70 90 70 L110 70 Q130 70 130 90" stroke="#7A8B5C" stroke-width="4" stroke-linecap="round" fill="none"/>
                        <circle cx="75" cy="95" r="5" fill="#7A8B5C"/>
                        <circle cx="125" cy="95" r="5" fill="#7A8B5C"/>
                        <path d="M75 130 Q100 145 125 130" stroke="#7A8B5C" stroke-width="4" stroke-linecap="round" fill="none"/>
                    </svg>
                </div>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="<?= BASE_URL ?>/" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Geri Dön
                    </a>
                </div>

                <!-- Popular Categories -->
                <div class="mt-5">
                    <h5 class="mb-3">Popüler Kategoriler</h5>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="<?= BASE_URL ?>/products?gender=kadın" class="btn btn-sm btn-outline-primary">Kadın Parfümleri</a>
                        <a href="<?= BASE_URL ?>/products?gender=erkek" class="btn btn-sm btn-outline-primary">Erkek Parfümleri</a>
                        <a href="<?= BASE_URL ?>/products?is_featured=1" class="btn btn-sm btn-outline-primary">Öne Çıkanlar</a>
                        <a href="<?= BASE_URL ?>/products?is_new=1" class="btn btn-sm btn-outline-primary">Yeni Ürünler</a>
                    </div>
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
    color: #7A8B5C;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.error-page svg {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}
</style>
