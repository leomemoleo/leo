<!-- Language Switcher Component -->
<?php
$currentLang = currentLang();
$languages = getLanguages();
?>

<div class="language-switcher">
    <button class="lang-trigger" onclick="toggleLangMenu()">
        <span class="flag"><?= $languages[$currentLang]['flag'] ?></span>
        <span class="lang-code"><?= strtoupper($currentLang) ?></span>
        <i class="fas fa-chevron-down"></i>
    </button>

    <div class="lang-menu" id="lang-menu">
        <?php foreach ($languages as $code => $lang): ?>
            <a href="?lang=<?= $code ?>"
               class="lang-option <?= $code === $currentLang ? 'active' : '' ?>"
               onclick="switchLanguage('<?= $code ?>', event)">
                <span class="flag"><?= $lang['flag'] ?></span>
                <span class="lang-name"><?= $lang['native_name'] ?></span>
                <?php if ($code === $currentLang): ?>
                    <i class="fas fa-check"></i>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<style>
.language-switcher {
    position: relative;
    display: inline-block;
}

.lang-trigger {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9375rem;
}

.lang-trigger:hover {
    border-color: var(--color-primary);
    background: #f8f9fa;
}

.lang-trigger .flag {
    font-size: 1.25rem;
}

.lang-trigger .lang-code {
    font-weight: 600;
    color: #333;
}

.lang-trigger i {
    font-size: 0.75rem;
    color: #666;
    transition: transform 0.3s;
}

.lang-trigger.active i {
    transform: rotate(180deg);
}

.lang-menu {
    position: absolute;
    top: calc(100% + 0.5rem);
    right: 0;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    min-width: 200px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
}

.lang-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.lang-option {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
    border-bottom: 1px solid #f0f0f0;
}

.lang-option:last-child {
    border-bottom: none;
}

.lang-option:hover {
    background: #f8f9fa;
}

.lang-option.active {
    background: var(--color-primary);
    color: white;
}

.lang-option .flag {
    font-size: 1.5rem;
}

.lang-option .lang-name {
    flex: 1;
    font-weight: 500;
}

.lang-option i.fa-check {
    font-size: 0.875rem;
    color: white;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .lang-trigger {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .lang-trigger .lang-code {
        display: none;
    }

    .lang-menu {
        right: auto;
        left: 0;
    }
}
</style>

<script>
function toggleLangMenu() {
    const menu = document.getElementById('lang-menu');
    const trigger = document.querySelector('.lang-trigger');

    menu.classList.toggle('show');
    trigger.classList.toggle('active');
}

function switchLanguage(lang, event) {
    event.preventDefault();

    // Show loading state
    const trigger = document.querySelector('.lang-trigger');
    trigger.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    // Set language via AJAX
    fetch('<?= BASE_URL ?>/api/set-language', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ language: lang })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to apply new language
            window.location.reload();
        } else {
            // Fallback: redirect with lang parameter
            window.location.href = '?lang=' + lang;
        }
    })
    .catch(() => {
        // Fallback: redirect with lang parameter
        window.location.href = '?lang=' + lang;
    });
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const switcher = document.querySelector('.language-switcher');
    const menu = document.getElementById('lang-menu');

    if (switcher && !switcher.contains(event.target)) {
        menu.classList.remove('show');
        document.querySelector('.lang-trigger').classList.remove('active');
    }
});
</script>
