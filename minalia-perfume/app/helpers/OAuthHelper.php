<?php
/**
 * OAuth Helper Class
 * Enterprise Social Login Integration
 * Supports: Google OAuth 2.0, Facebook Login
 */

/**
 * Abstract OAuth Provider
 */
abstract class OAuthProvider {
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $state;

    abstract public function getAuthorizationUrl();
    abstract public function getAccessToken($code);
    abstract public function getUserInfo($accessToken);
    abstract public function getProviderName();

    /**
     * Generate secure state token
     */
    protected function generateState() {
        return bin2hex(random_bytes(16));
    }

    /**
     * Verify state token
     */
    public function verifyState($receivedState) {
        $sessionState = $_SESSION['oauth_state'] ?? null;
        return $receivedState && $sessionState && hash_equals($sessionState, $receivedState);
    }

    /**
     * Make HTTP request
     */
    protected function makeRequest($url, $params = [], $method = 'GET', $headers = []) {
        $ch = curl_init();

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("HTTP Error: {$httpCode}");
        }

        return json_decode($response, true);
    }
}

/**
 * Google OAuth Provider
 * Official Google OAuth 2.0 implementation
 * Documentation: https://developers.google.com/identity/protocols/oauth2
 */
class GoogleOAuthProvider extends OAuthProvider {
    private $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth';
    private $tokenUrl = 'https://oauth2.googleapis.com/token';
    private $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';

    public function __construct() {
        $this->clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
        $this->clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
        $this->redirectUri = BASE_URL . '/auth/google/callback';
    }

    /**
     * Get Google authorization URL
     */
    public function getAuthorizationUrl() {
        $this->state = $this->generateState();
        $_SESSION['oauth_state'] = $this->state;

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'state' => $this->state,
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];

        return $this->authUrl . '?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken($code) {
        $params = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ];

        $result = $this->makeRequest($this->tokenUrl, $params, 'POST');

        if (!isset($result['access_token'])) {
            throw new Exception('Failed to get access token');
        }

        return [
            'access_token' => $result['access_token'],
            'refresh_token' => $result['refresh_token'] ?? null,
            'expires_in' => $result['expires_in'] ?? 3600,
            'token_type' => $result['token_type'] ?? 'Bearer'
        ];
    }

    /**
     * Get user information from Google
     */
    public function getUserInfo($accessToken) {
        $headers = [
            'Authorization: Bearer ' . $accessToken
        ];

        $userInfo = $this->makeRequest($this->userInfoUrl, [], 'GET', $headers);

        return [
            'id' => $userInfo['id'] ?? '',
            'email' => $userInfo['email'] ?? '',
            'name' => $userInfo['name'] ?? '',
            'first_name' => $userInfo['given_name'] ?? '',
            'last_name' => $userInfo['family_name'] ?? '',
            'avatar' => $userInfo['picture'] ?? '',
            'email_verified' => $userInfo['verified_email'] ?? false,
            'locale' => $userInfo['locale'] ?? 'tr'
        ];
    }

    public function getProviderName() {
        return 'google';
    }
}

/**
 * Facebook OAuth Provider
 * Official Facebook Login implementation
 * Documentation: https://developers.facebook.com/docs/facebook-login
 */
class FacebookOAuthProvider extends OAuthProvider {
    private $authUrl = 'https://www.facebook.com/v18.0/dialog/oauth';
    private $tokenUrl = 'https://graph.facebook.com/v18.0/oauth/access_token';
    private $userInfoUrl = 'https://graph.facebook.com/v18.0/me';

    public function __construct() {
        $this->clientId = $_ENV['FACEBOOK_APP_ID'] ?? '';
        $this->clientSecret = $_ENV['FACEBOOK_APP_SECRET'] ?? '';
        $this->redirectUri = BASE_URL . '/auth/facebook/callback';
    }

    /**
     * Get Facebook authorization URL
     */
    public function getAuthorizationUrl() {
        $this->state = $this->generateState();
        $_SESSION['oauth_state'] = $this->state;

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'email,public_profile',
            'state' => $this->state,
            'response_type' => 'code'
        ];

        return $this->authUrl . '?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken($code) {
        $params = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri
        ];

        $result = $this->makeRequest($this->tokenUrl, $params, 'GET');

        if (!isset($result['access_token'])) {
            throw new Exception('Failed to get access token');
        }

        return [
            'access_token' => $result['access_token'],
            'token_type' => $result['token_type'] ?? 'Bearer',
            'expires_in' => $result['expires_in'] ?? 5184000 // 60 days default
        ];
    }

    /**
     * Get user information from Facebook
     */
    public function getUserInfo($accessToken) {
        $params = [
            'fields' => 'id,name,email,first_name,last_name,picture.type(large)',
            'access_token' => $accessToken
        ];

        $userInfo = $this->makeRequest($this->userInfoUrl, $params, 'GET');

        return [
            'id' => $userInfo['id'] ?? '',
            'email' => $userInfo['email'] ?? '',
            'name' => $userInfo['name'] ?? '',
            'first_name' => $userInfo['first_name'] ?? '',
            'last_name' => $userInfo['last_name'] ?? '',
            'avatar' => $userInfo['picture']['data']['url'] ?? '',
            'email_verified' => !empty($userInfo['email']),
            'locale' => 'tr'
        ];
    }

    public function getProviderName() {
        return 'facebook';
    }
}

/**
 * OAuth Factory
 */
class OAuthFactory {
    public static function create($provider) {
        switch (strtolower($provider)) {
            case 'google':
                return new GoogleOAuthProvider();
            case 'facebook':
                return new FacebookOAuthProvider();
            default:
                throw new Exception("Unsupported OAuth provider: {$provider}");
        }
    }
}

/**
 * Social Account Manager
 * Handles user creation and linking with social accounts
 */
class SocialAccountManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Find or create user from social account
     */
    public function findOrCreateUser($provider, $socialUserInfo, $tokenData) {
        // Check if social account exists
        $stmt = $this->db->prepare("
            SELECT user_id FROM social_accounts
            WHERE provider = ? AND provider_user_id = ?
        ");
        $stmt->execute([$provider, $socialUserInfo['id']]);
        $existingAccount = $stmt->fetch();

        if ($existingAccount) {
            // Update existing social account token
            $this->updateSocialAccount($existingAccount['user_id'], $provider, $socialUserInfo, $tokenData);
            return $existingAccount['user_id'];
        }

        // Check if user exists with this email
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$socialUserInfo['email']]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            // Link social account to existing user
            $userId = $existingUser['id'];
            $this->createSocialAccount($userId, $provider, $socialUserInfo, $tokenData);

            // Update user auth provider
            $this->db->prepare("
                UPDATE users
                SET auth_provider = ?, avatar = ?, email_verified = 1
                WHERE id = ?
            ")->execute([$provider, $socialUserInfo['avatar'], $userId]);

            return $userId;
        }

        // Create new user
        $userId = $this->createUserFromSocialAccount($provider, $socialUserInfo);

        // Create social account link
        $this->createSocialAccount($userId, $provider, $socialUserInfo, $tokenData);

        return $userId;
    }

    /**
     * Create new user from social account
     */
    private function createUserFromSocialAccount($provider, $socialUserInfo) {
        $stmt = $this->db->prepare("
            INSERT INTO users
            (email, first_name, last_name, avatar, auth_provider, email_verified, created_at)
            VALUES (?, ?, ?, ?, ?, 1, NOW())
        ");

        $stmt->execute([
            $socialUserInfo['email'],
            $socialUserInfo['first_name'],
            $socialUserInfo['last_name'],
            $socialUserInfo['avatar'],
            $provider
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Create social account record
     */
    private function createSocialAccount($userId, $provider, $socialUserInfo, $tokenData) {
        $expiresAt = null;
        if (isset($tokenData['expires_in'])) {
            $expiresAt = date('Y-m-d H:i:s', time() + $tokenData['expires_in']);
        }

        $stmt = $this->db->prepare("
            INSERT INTO social_accounts
            (user_id, provider, provider_user_id, provider_email, provider_name,
             provider_avatar, access_token, refresh_token, token_expires_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $userId,
            $provider,
            $socialUserInfo['id'],
            $socialUserInfo['email'],
            $socialUserInfo['name'],
            $socialUserInfo['avatar'],
            $tokenData['access_token'],
            $tokenData['refresh_token'] ?? null,
            $expiresAt
        ]);
    }

    /**
     * Update existing social account
     */
    private function updateSocialAccount($userId, $provider, $socialUserInfo, $tokenData) {
        $expiresAt = null;
        if (isset($tokenData['expires_in'])) {
            $expiresAt = date('Y-m-d H:i:s', time() + $tokenData['expires_in']);
        }

        $stmt = $this->db->prepare("
            UPDATE social_accounts
            SET provider_email = ?,
                provider_name = ?,
                provider_avatar = ?,
                access_token = ?,
                refresh_token = ?,
                token_expires_at = ?,
                updated_at = NOW()
            WHERE user_id = ? AND provider = ?
        ");

        $stmt->execute([
            $socialUserInfo['email'],
            $socialUserInfo['name'],
            $socialUserInfo['avatar'],
            $tokenData['access_token'],
            $tokenData['refresh_token'] ?? null,
            $expiresAt,
            $userId,
            $provider
        ]);

        // Update user avatar
        $this->db->prepare("
            UPDATE users
            SET avatar = ?, email_verified = 1
            WHERE id = ?
        ")->execute([$socialUserInfo['avatar'], $userId]);
    }

    /**
     * Disconnect social account
     */
    public function disconnectSocialAccount($userId, $provider) {
        $stmt = $this->db->prepare("
            DELETE FROM social_accounts
            WHERE user_id = ? AND provider = ?
        ");

        return $stmt->execute([$userId, $provider]);
    }

    /**
     * Get user's social accounts
     */
    public function getUserSocialAccounts($userId) {
        $stmt = $this->db->prepare("
            SELECT provider, provider_email, provider_name, created_at
            FROM social_accounts
            WHERE user_id = ?
        ");

        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
