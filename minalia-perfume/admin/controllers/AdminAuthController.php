<?php
/**
 * Admin Authentication Controller
 * MINALIA Parfüm E-Ticaret Platformu
 */

class AdminAuthController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Show login page
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION[SESSION_ADMIN_ID])) {
            redirect(ADMIN_URL . '/dashboard');
            return;
        }

        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
            return;
        }

        // Show login form
        $this->showLoginForm();
    }

    /**
     * Process login
     */
    private function processLogin() {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate
        if (empty($email) || empty($password)) {
            $this->showLoginForm('Lütfen tüm alanları doldurun.');
            return;
        }

        // Check credentials
        $sql = "SELECT * FROM admin_users WHERE email = ? AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($password, $admin['password'])) {
            // Log failed attempt
            $this->logActivity(null, 'login_failed', "Failed login attempt for: $email");

            $this->showLoginForm('Email veya şifre hatalı.');
            return;
        }

        // Update last login
        $updateSql = "UPDATE admin_users SET last_login = NOW() WHERE id = ?";
        $updateStmt = $this->db->prepare($updateSql);
        $updateStmt->execute([$admin['id']]);

        // Set session
        $_SESSION[SESSION_ADMIN_ID] = $admin['id'];
        $_SESSION[SESSION_ADMIN_ROLE] = $admin['role'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_name'] = $admin['name'];

        // Log successful login
        $this->logActivity($admin['id'], 'login', 'Admin logged in');

        redirect(ADMIN_URL . '/dashboard');
    }

    /**
     * Show login form
     */
    private function showLoginForm($error = null) {
        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Girişi | MINALIA</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Inter', sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }

                .login-container {
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                    overflow: hidden;
                    max-width: 400px;
                    width: 100%;
                }

                .login-header {
                    background: #7A8B5C;
                    padding: 40px 30px;
                    text-align: center;
                    color: white;
                }

                .login-header h1 {
                    font-size: 2rem;
                    margin-bottom: 10px;
                }

                .login-header p {
                    opacity: 0.9;
                    font-size: 0.9rem;
                }

                .login-body {
                    padding: 40px 30px;
                }

                .alert {
                    background: #fee;
                    color: #c33;
                    padding: 12px 16px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                    font-size: 0.9rem;
                    border-left: 4px solid #c33;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                .form-label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: 500;
                    color: #333;
                    font-size: 0.9rem;
                }

                .form-control {
                    width: 100%;
                    padding: 12px 16px;
                    border: 2px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 0.95rem;
                    transition: all 0.3s;
                }

                .form-control:focus {
                    outline: none;
                    border-color: #7A8B5C;
                    box-shadow: 0 0 0 3px rgba(122, 139, 92, 0.1);
                }

                .btn-login {
                    width: 100%;
                    padding: 14px;
                    background: #7A8B5C;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-size: 1rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                }

                .btn-login:hover {
                    background: #6a7a4f;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(122, 139, 92, 0.3);
                }

                .login-footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 0.85rem;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <div class="login-header">
                    <h1>MINALIA</h1>
                    <p>Admin Panel Girişi</p>
                </div>

                <div class="login-body">
                    <?php if ($error): ?>
                        <div class="alert"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required autofocus>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Şifre</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i> Giriş Yap
                        </button>
                    </form>

                    <div class="login-footer">
                        <p>&copy; 2025 MINALIA. Tüm hakları saklıdır.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    /**
     * Logout
     */
    public function logout() {
        if (isset($_SESSION[SESSION_ADMIN_ID])) {
            $this->logActivity($_SESSION[SESSION_ADMIN_ID], 'logout', 'Admin logged out');
        }

        session_destroy();
        redirect(ADMIN_URL . '/login');
    }

    /**
     * Log admin activity
     */
    private function logActivity($adminId, $action, $description) {
        try {
            $sql = "INSERT INTO activity_logs (admin_id, action, description, ip_address, created_at)
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $adminId,
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            // Silently fail - don't break login if logging fails
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
}
