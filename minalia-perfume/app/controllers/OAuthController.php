<?php
/**
 * OAuth Controller
 * Handles social login (Google, Facebook)
 */

require_once __DIR__ . '/../helpers/OAuthHelper.php';
require_once __DIR__ . '/../models/User.php';

class OAuthController {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    /**
     * Redirect to Google OAuth
     */
    public function googleLogin() {
        try {
            $provider = OAuthFactory::create('google');
            $authUrl = $provider->getAuthorizationUrl();

            header('Location: ' . $authUrl);
            exit;

        } catch (Exception $e) {
            $this->handleError('Google login başlatılamadı: ' . $e->getMessage());
        }
    }

    /**
     * Google OAuth callback
     */
    public function googleCallback() {
        try {
            // Check for errors
            if (isset($_GET['error'])) {
                throw new Exception('Google login iptal edildi.');
            }

            // Get authorization code
            $code = $_GET['code'] ?? null;
            $state = $_GET['state'] ?? null;

            if (!$code) {
                throw new Exception('Authorization code bulunamadı.');
            }

            // Create provider
            $provider = OAuthFactory::create('google');

            // Verify state to prevent CSRF
            if (!$provider->verifyState($state)) {
                throw new Exception('Invalid state parameter. Possible CSRF attack.');
            }

            // Exchange code for access token
            $tokenData = $provider->getAccessToken($code);

            // Get user information
            $userInfo = $provider->getUserInfo($tokenData['access_token']);

            $socialManager = new SocialAccountManager($this->db);

            // Check if user is already logged in (linking scenario)
            if (isLoggedIn()) {
                $currentUserId = getCurrentUserId();

                // Check if this Google account is already linked to another user
                $stmt = $this->db->prepare("SELECT user_id FROM social_accounts WHERE provider = 'google' AND provider_user_id = ?");
                $stmt->execute([$userInfo['id']]);
                $existingLink = $stmt->fetch();

                if ($existingLink && $existingLink['user_id'] != $currentUserId) {
                    throw new Exception('Bu Google hesabı başka bir kullanıcıya bağlı.');
                }

                if ($existingLink && $existingLink['user_id'] == $currentUserId) {
                    setFlashMessage('Bu Google hesabı zaten hesabınıza bağlı.', 'info');
                    redirect('/account/security');
                    return;
                }

                // Link to current user
                $socialManager->linkSocialAccountToUser($currentUserId, 'google', $userInfo, $tokenData);
                setFlashMessage('Google hesabınız başarıyla bağlandı!', 'success');
                redirect('/account/security');
                return;
            }

            // Not logged in - normal login flow
            $userId = $socialManager->findOrCreateUser('google', $userInfo, $tokenData);

            // Log user in
            $this->loginUser($userId);

            // Redirect to home
            setFlashMessage('Google ile giriş başarılı! Hoş geldiniz.', 'success');
            redirect('/');

        } catch (Exception $e) {
            $this->handleError('Google login hatası: ' . $e->getMessage());
        }
    }

    /**
     * Redirect to Facebook OAuth
     */
    public function facebookLogin() {
        try {
            $provider = OAuthFactory::create('facebook');
            $authUrl = $provider->getAuthorizationUrl();

            header('Location: ' . $authUrl);
            exit;

        } catch (Exception $e) {
            $this->handleError('Facebook login başlatılamadı: ' . $e->getMessage());
        }
    }

    /**
     * Facebook OAuth callback
     */
    public function facebookCallback() {
        try {
            // Check for errors
            if (isset($_GET['error'])) {
                throw new Exception('Facebook login iptal edildi.');
            }

            // Get authorization code
            $code = $_GET['code'] ?? null;
            $state = $_GET['state'] ?? null;

            if (!$code) {
                throw new Exception('Authorization code bulunamadı.');
            }

            // Create provider
            $provider = OAuthFactory::create('facebook');

            // Verify state to prevent CSRF
            if (!$provider->verifyState($state)) {
                throw new Exception('Invalid state parameter. Possible CSRF attack.');
            }

            // Exchange code for access token
            $tokenData = $provider->getAccessToken($code);

            // Get user information
            $userInfo = $provider->getUserInfo($tokenData['access_token']);

            // Verify email exists
            if (empty($userInfo['email'])) {
                throw new Exception('Facebook hesabınızdan email alınamadı. Lütfen email iznini verin.');
            }

            $socialManager = new SocialAccountManager($this->db);

            // Check if user is already logged in (linking scenario)
            if (isLoggedIn()) {
                $currentUserId = getCurrentUserId();

                // Check if this Facebook account is already linked to another user
                $stmt = $this->db->prepare("SELECT user_id FROM social_accounts WHERE provider = 'facebook' AND provider_user_id = ?");
                $stmt->execute([$userInfo['id']]);
                $existingLink = $stmt->fetch();

                if ($existingLink && $existingLink['user_id'] != $currentUserId) {
                    throw new Exception('Bu Facebook hesabı başka bir kullanıcıya bağlı.');
                }

                if ($existingLink && $existingLink['user_id'] == $currentUserId) {
                    setFlashMessage('Bu Facebook hesabı zaten hesabınıza bağlı.', 'info');
                    redirect('/account/security');
                    return;
                }

                // Link to current user
                $socialManager->linkSocialAccountToUser($currentUserId, 'facebook', $userInfo, $tokenData);
                setFlashMessage('Facebook hesabınız başarıyla bağlandı!', 'success');
                redirect('/account/security');
                return;
            }

            // Not logged in - normal login flow
            $userId = $socialManager->findOrCreateUser('facebook', $userInfo, $tokenData);

            // Log user in
            $this->loginUser($userId);

            // Redirect to home
            setFlashMessage('Facebook ile giriş başarılı! Hoş geldiniz.', 'success');
            redirect('/');

        } catch (Exception $e) {
            $this->handleError('Facebook login hatası: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect social account
     */
    public function disconnect() {
        if (!isLoggedIn()) {
            redirect('/login');
            return;
        }

        // CSRF validation
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
            redirect('/account/security');
            return;
        }

        $provider = $_POST['provider'] ?? '';
        $userId = getCurrentUserId();

        if (!in_array($provider, ['google', 'facebook'])) {
            setFlashMessage('Geçersiz sosyal medya hesabı.', 'error');
            redirect('/account/security');
            return;
        }

        try {
            // Check if user has a password set
            // Prevent disconnecting if it's the only login method
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            // Check how many social accounts are connected
            $countStmt = $this->db->prepare("SELECT COUNT(*) as count FROM social_accounts WHERE user_id = ?");
            $countStmt->execute([$userId]);
            $socialCount = $countStmt->fetch()['count'];

            // If no password and this is the last social account, prevent disconnection
            if (empty($user['password']) && $socialCount <= 1) {
                setFlashMessage('Bu hesabı kaldıramazsınız. Hesabınıza erişim sağlamanız için en az bir giriş yöntemi gereklidir. Önce bir şifre belirleyin.', 'error');
                redirect('/account/security');
                return;
            }

            // Disconnect the account
            $socialManager = new SocialAccountManager($this->db);
            $socialManager->disconnectSocialAccount($userId, $provider);

            $providerName = $provider === 'google' ? 'Google' : 'Facebook';
            setFlashMessage($providerName . ' hesabı başarıyla kaldırıldı.', 'success');

        } catch (Exception $e) {
            error_log('OAuth disconnect error: ' . $e->getMessage());
            setFlashMessage('Hesap kaldırılırken bir hata oluştu: ' . $e->getMessage(), 'error');
        }

        redirect('/account/security');
    }

    /**
     * Log user in (create session)
     */
    private function loginUser($userId) {
        // Get user data
        $stmt = $this->db->prepare("
            SELECT id, email, first_name, last_name, avatar
            FROM users
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new Exception('User not found');
        }

        // Create session
        $_SESSION[SESSION_USER_ID] = $user['id'];
        $_SESSION[SESSION_USER_EMAIL] = $user['email'];
        $_SESSION[SESSION_USER_NAME] = $user['first_name'] . ' ' . $user['last_name'];

        // Update last login
        $this->db->prepare("
            UPDATE users
            SET last_login = NOW()
            WHERE id = ?
        ")->execute([$userId]);

        // Log activity
        $this->logActivity($userId, 'social_login');
    }

    /**
     * Handle OAuth error
     */
    private function handleError($message) {
        error_log('OAuth Error: ' . $message);
        setFlashMessage($message, 'error');
        redirect('/login');
    }

    /**
     * Log user activity
     */
    private function logActivity($userId, $action) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs
                (user_id, action, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $userId,
                $action,
                getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]);

        } catch (Exception $e) {
            // Silently fail - logging shouldn't break the flow
            error_log('Activity log error: ' . $e->getMessage());
        }
    }
}
