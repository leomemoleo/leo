<div class="page-container">
    <div class="container" style="max-width: 900px; margin: 3rem auto; padding: 0 1.5rem;">
        <div class="page-content" style="background: white; padding: 3rem; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; margin-bottom: 2rem; color: var(--text-dark); border-bottom: 2px solid var(--primary); padding-bottom: 1rem;">
                <?= htmlspecialchars($page['title']) ?>
            </h1>

            <div class="page-body" style="line-height: 1.8; color: #444; font-size: 1.05rem;">
                <?= $page['content'] ?>
            </div>

            <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #eee; text-align: center;">
                <a href="<?= BASE_URL ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Ana Sayfaya Dön
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.page-body h2 {
    font-family: 'Playfair Display', serif;
    font-size: 1.75rem;
    margin: 2rem 0 1rem;
    color: var(--text-dark);
}

.page-body h3 {
    font-size: 1.25rem;
    margin: 1.5rem 0 0.75rem;
    color: var(--text-dark);
    font-weight: 600;
}

.page-body p {
    margin-bottom: 1.25rem;
}

.page-body ul, .page-body ol {
    margin: 1rem 0 1.5rem 2rem;
}

.page-body li {
    margin-bottom: 0.5rem;
}

.page-body strong {
    color: var(--primary);
    font-weight: 600;
}

.page-body a {
    color: var(--primary);
    text-decoration: underline;
}

.page-body a:hover {
    color: var(--primary-dark);
}

.page-body code {
    background: #f5f5f5;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.9em;
}
</style>
