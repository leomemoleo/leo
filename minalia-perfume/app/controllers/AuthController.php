<?php
/**
 * Authentication Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Show login form
     */
    public function loginForm() {
        if (isLoggedIn()) {
            $this->redirect(BASE_URL . '/account');
        }

        $this->view('auth/login', [
            'title' => 'Giriş Yap',
            'csrf_token' => generateCSRFToken()
        ]);
    }

    /**
     * Process login
     */
    public function login() {
        if ($this->isPost()) {
            $email = sanitize($this->post('email'));
            $password = $this->post('password');
            $csrfToken = $this->post('csrf_token');

            // Verify CSRF token
            if (!verifyCSRFToken($csrfToken)) {
                setFlashMessage('Geçersiz istek.', 'error');
                $this->redirect(BASE_URL . '/login');
            }

            // Rate limiting
            if (!checkRateLimit('login_' . getClientIP(), 5, 300)) {
                setFlashMessage('Çok fazla deneme yaptınız. Lütfen daha sonra tekrar deneyin.', 'error');
                $this->redirect(BASE_URL . '/login');
            }

            // Validate
            $errors = $this->validate($_POST, [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($errors !== true) {
                setFlashMessage('Lütfen tüm alanları doldurun.', 'error');
                $this->redirect(BASE_URL . '/login');
            }

            // Verify credentials
            $user = $this->userModel->verifyCredentials($email, $password);

            if (!$user) {
                setFlashMessage(ERROR_INVALID_CREDENTIALS, 'error');
                $this->redirect(BASE_URL . '/login');
            }

            if (!$user['is_active']) {
                setFlashMessage('Hesabınız aktif değil.', 'error');
                $this->redirect(BASE_URL . '/login');
            }

            // Update last login
            $this->userModel->updateLastLogin($user['id']);

            // Set session
            $_SESSION[SESSION_USER_ID] = $user['id'];
            $_SESSION[SESSION_USER_EMAIL] = $user['email'];
            $_SESSION[SESSION_USER_NAME] = $user['first_name'] . ' ' . $user['last_name'];

            setFlashMessage(SUCCESS_LOGIN, 'success');

            // Redirect to intended page or account
            $redirectTo = $_SESSION['redirect_to'] ?? BASE_URL . '/account';
            unset($_SESSION['redirect_to']);

            $this->redirect($redirectTo);
        }

        $this->redirect(BASE_URL . '/login');
    }

    /**
     * Show registration form
     */
    public function registerForm() {
        if (isLoggedIn()) {
            $this->redirect(BASE_URL . '/account');
        }

        $this->view('auth/register', [
            'title' => 'Üye Ol',
            'csrf_token' => generateCSRFToken()
        ]);
    }

    /**
     * Process registration
     */
    public function register() {
        if ($this->isPost()) {
            $csrfToken = $this->post('csrf_token');

            // Verify CSRF token
            if (!verifyCSRFToken($csrfToken)) {
                setFlashMessage('Geçersiz istek.', 'error');
                $this->redirect(BASE_URL . '/register');
            }

            // Validate
            $errors = $this->validate($_POST, [
                'first_name' => 'required|min:2',
                'last_name' => 'required|min:2',
                'email' => 'required|email',
                'password' => 'required|min:8',
                'password_confirm' => 'required|match:password'
            ]);

            if ($errors !== true) {
                setFlashMessage('Lütfen tüm alanları doğru şekilde doldurun.', 'error');
                $this->redirect(BASE_URL . '/register');
            }

            // Check if email exists
            if ($this->userModel->emailExists(sanitize($this->post('email')))) {
                setFlashMessage(ERROR_USER_EXISTS, 'error');
                $this->redirect(BASE_URL . '/register');
            }

            // Create user
            $userData = [
                'first_name' => sanitize($this->post('first_name')),
                'last_name' => sanitize($this->post('last_name')),
                'email' => sanitize($this->post('email')),
                'password' => $this->post('password'),
                'phone' => sanitize($this->post('phone')),
                'newsletter_subscribed' => $this->post('newsletter') ? 1 : 0
            ];

            $userId = $this->userModel->create($userData);

            if ($userId) {
                setFlashMessage(SUCCESS_REGISTERED, 'success');
                $this->redirect(BASE_URL . '/login');
            } else {
                setFlashMessage(ERROR_DATABASE, 'error');
                $this->redirect(BASE_URL . '/register');
            }
        }

        $this->redirect(BASE_URL . '/register');
    }

    /**
     * Logout
     */
    public function logout() {
        // Clear session
        unset($_SESSION[SESSION_USER_ID]);
        unset($_SESSION[SESSION_USER_EMAIL]);
        unset($_SESSION[SESSION_USER_NAME]);

        setFlashMessage(SUCCESS_LOGOUT, 'success');
        $this->redirect(BASE_URL);
    }

    /**
     * Show forgot password form
     */
    public function forgotPasswordForm() {
        $this->view('auth/forgot-password', [
            'title' => 'Şifremi Unuttum',
            'csrf_token' => generateCSRFToken()
        ]);
    }

    /**
     * Process forgot password
     */
    public function forgotPassword() {
        if ($this->isPost()) {
            $email = sanitize($this->post('email'));
            $csrfToken = $this->post('csrf_token');

            if (!verifyCSRFToken($csrfToken)) {
                setFlashMessage('Geçersiz istek.', 'error');
                $this->redirect(BASE_URL . '/forgot-password');
            }

            if (!isValidEmail($email)) {
                setFlashMessage(ERROR_INVALID_EMAIL, 'error');
                $this->redirect(BASE_URL . '/forgot-password');
            }

            $token = $this->userModel->generateResetToken($email);

            if ($token) {
                // In production, send email with reset link
                // For now, just show success message
                $resetLink = BASE_URL . '/reset-password/' . $token;

                setFlashMessage('Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.', 'success');
            } else {
                setFlashMessage('Bu e-posta adresi kayıtlı değil.', 'error');
            }

            $this->redirect(BASE_URL . '/forgot-password');
        }

        $this->redirect(BASE_URL . '/forgot-password');
    }

    /**
     * Show reset password form
     */
    public function resetPasswordForm($token) {
        $user = $this->userModel->verifyResetToken($token);

        if (!$user) {
            setFlashMessage('Geçersiz veya süresi dolmuş şifre sıfırlama bağlantısı.', 'error');
            $this->redirect(BASE_URL . '/forgot-password');
        }

        $this->view('auth/reset-password', [
            'title' => 'Şifre Sıfırla',
            'token' => $token,
            'csrf_token' => generateCSRFToken()
        ]);
    }

    /**
     * Process reset password
     */
    public function resetPassword() {
        if ($this->isPost()) {
            $token = $this->post('token');
            $password = $this->post('password');
            $passwordConfirm = $this->post('password_confirm');
            $csrfToken = $this->post('csrf_token');

            if (!verifyCSRFToken($csrfToken)) {
                setFlashMessage('Geçersiz istek.', 'error');
                $this->redirect(BASE_URL . '/forgot-password');
            }

            if ($password !== $passwordConfirm) {
                setFlashMessage(ERROR_PASSWORD_MISMATCH, 'error');
                $this->redirect(BASE_URL . '/reset-password/' . $token);
            }

            if (strlen($password) < PASSWORD_MIN_LENGTH) {
                setFlashMessage(ERROR_WEAK_PASSWORD, 'error');
                $this->redirect(BASE_URL . '/reset-password/' . $token);
            }

            if ($this->userModel->resetPassword($token, $password)) {
                setFlashMessage('Şifreniz başarıyla değiştirildi. Giriş yapabilirsiniz.', 'success');
                $this->redirect(BASE_URL . '/login');
            } else {
                setFlashMessage('Şifre sıfırlama başarısız. Lütfen tekrar deneyin.', 'error');
                $this->redirect(BASE_URL . '/forgot-password');
            }
        }

        $this->redirect(BASE_URL . '/forgot-password');
    }
}
