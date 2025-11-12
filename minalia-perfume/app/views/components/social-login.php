<!-- Social Login Buttons Component -->
<div class="social-login">
    <div class="divider">
        <span>veya</span>
    </div>

    <div class="social-buttons">
        <a href="<?= BASE_URL ?>/auth/google" class="social-btn google-btn">
            <svg width="18" height="18" viewBox="0 0 18 18">
                <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/>
                <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                <path fill="#FBBC05" d="M3.964 10.71c-.18-.54-.282-1.117-.282-1.71s.102-1.17.282-1.71V4.958H.957C.347 6.173 0 7.548 0 9s.348 2.827.957 4.042l3.007-2.332z"/>
                <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z"/>
            </svg>
            <span>Google ile Giriş Yap</span>
        </a>

        <a href="<?= BASE_URL ?>/auth/facebook" class="social-btn facebook-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span>Facebook ile Giriş Yap</span>
        </a>
    </div>
</div>

<style>
.social-login {
    margin: 2rem 0;
}

.social-login .divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 1.5rem 0;
}

.social-login .divider::before,
.social-login .divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e0e0e0;
}

.social-login .divider span {
    padding: 0 1rem;
    color: #666;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.social-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.social-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 0.875rem 1.5rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    color: #333;
    font-weight: 500;
    font-size: 0.9375rem;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.social-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.social-btn svg {
    flex-shrink: 0;
}

.google-btn {
    border-color: #4285F4;
}

.google-btn:hover {
    background: #4285F4;
    color: white;
    border-color: #4285F4;
}

.google-btn:hover svg path {
    fill: white;
}

.facebook-btn {
    border-color: #1877F2;
}

.facebook-btn:hover {
    background: #1877F2;
    color: white;
    border-color: #1877F2;
}

.facebook-btn:hover svg {
    fill: white;
}

/* Mobile responsive */
@media (max-width: 480px) {
    .social-btn {
        font-size: 0.875rem;
        padding: 0.75rem 1rem;
    }

    .social-btn span {
        font-size: 0.875rem;
    }
}
</style>
