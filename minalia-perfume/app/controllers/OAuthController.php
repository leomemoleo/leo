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

            // Create or update user
            $socialManager = new SocialAccountManager($this->db);
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

            // Create or update user
            $socialManager = new SocialAccountManager($this->db);
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

        $provider = $_POST['provider'] ?? '';

        if (!in_array($provider, ['google', 'facebook'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Geçersiz provider'
            ]);
            return;
        }

        try {
            $socialManager = new SocialAccountManager($this->db);
            $socialManager->disconnectSocialAccount($_SESSION[SESSION_USER_ID], $provider);

            echo json_encode([
                'success' => true,
                'message' => ucfirst($provider) . ' bağlantısı kesildi.'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
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
