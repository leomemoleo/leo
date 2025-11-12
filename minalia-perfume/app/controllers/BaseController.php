<?php
/**
 * Base Controller Class
 * MINALIA Parfüm E-Ticaret Platformu
 */

class BaseController {
    protected $data = [];

    /**
     * Load view
     */
    protected function view($viewPath, $data = []) {
        $this->data = array_merge($this->data, $data);
        extract($this->data);

        // Start output buffering
        ob_start();

        // Check if view file exists
        $viewFile = APP_PATH . '/views/' . $viewPath . '.php';

        if (file_exists($viewFile)) {
            require $viewFile;
            $content = ob_get_clean();

            // Load layout if exists
            $layoutFile = APP_PATH . '/views/layouts/main.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            ob_end_clean();
            $this->error404("View not found: {$viewPath}");
        }
    }

    /**
     * Load admin view
     */
    protected function adminView($viewPath, $data = []) {
        $this->data = array_merge($this->data, $data);
        extract($this->data);

        ob_start();

        $viewFile = ROOT_PATH . '/admin/views/' . $viewPath . '.php';

        if (file_exists($viewFile)) {
            require $viewFile;
            $content = ob_get_clean();

            $layoutFile = ROOT_PATH . '/admin/views/layouts/main.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            ob_end_clean();
            $this->error404("Admin view not found: {$viewPath}");
        }
    }

    /**
     * Load component
     */
    protected function component($componentPath, $data = []) {
        extract($data);
        $componentFile = APP_PATH . '/views/components/' . $componentPath . '.php';

        if (file_exists($componentFile)) {
            require $componentFile;
        }
    }

    /**
     * Redirect
     */
    protected function redirect($url, $statusCode = 302) {
        redirect($url, $statusCode);
    }

    /**
     * JSON response
     */
    protected function json($data, $statusCode = 200) {
        jsonResponse($data, $statusCode);
    }

    /**
     * Validate request
     */
    protected function validate($data, $rules) {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $data[$field] ?? '';

            foreach ($rules as $rule) {
                // Required validation
                if ($rule === 'required' && empty($value)) {
                    $errors[$field] = ERROR_REQUIRED_FIELD;
                    break;
                }

                // Email validation
                if ($rule === 'email' && !empty($value) && !isValidEmail($value)) {
                    $errors[$field] = ERROR_INVALID_EMAIL;
                    break;
                }

                // Phone validation
                if ($rule === 'phone' && !empty($value) && !isValidPhone($value)) {
                    $errors[$field] = ERROR_INVALID_PHONE;
                    break;
                }

                // Min length validation
                if (strpos($rule, 'min:') === 0) {
                    $min = (int)substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = "Bu alan en az {$min} karakter olmalıdır.";
                        break;
                    }
                }

                // Max length validation
                if (strpos($rule, 'max:') === 0) {
                    $max = (int)substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = "Bu alan en fazla {$max} karakter olabilir.";
                        break;
                    }
                }

                // Numeric validation
                if ($rule === 'numeric' && !empty($value) && !is_numeric($value)) {
                    $errors[$field] = "Bu alan sayısal olmalıdır.";
                    break;
                }

                // Match validation
                if (strpos($rule, 'match:') === 0) {
                    $matchField = substr($rule, 6);
                    if ($value !== ($data[$matchField] ?? '')) {
                        $errors[$field] = "Bu alan {$matchField} ile eşleşmelidir.";
                        break;
                    }
                }
            }
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Check if user is authenticated
     */
    protected function requireAuth() {
        if (!isLoggedIn()) {
            setFlashMessage('Bu sayfaya erişmek için giriş yapmalısınız.', 'error');
            $this->redirect(BASE_URL . '/login');
        }
    }

    /**
     * Check if admin is authenticated
     */
    protected function requireAdminAuth() {
        if (!isAdminLoggedIn()) {
            setFlashMessage('Bu sayfaya erişmek için yönetici girişi yapmalısınız.', 'error');
            $this->redirect(ADMIN_URL . '/login');
        }
    }

    /**
     * Verify CSRF token
     */
    protected function verifyCsrf($token) {
        if (!verifyCSRFToken($token)) {
            $this->json(['success' => false, 'message' => 'Geçersiz istek.'], 403);
        }
    }

    /**
     * Get POST data
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Get request data
     */
    protected function input($key = null, $default = null) {
        $data = array_merge($_GET, $_POST);
        if ($key === null) {
            return $data;
        }
        return $data[$key] ?? $default;
    }

    /**
     * Check if request is POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request is GET
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Check if request is AJAX
     */
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * 404 Error page
     */
    protected function error404($message = 'Sayfa bulunamadı.') {
        http_response_code(404);
        $this->view('errors/404', ['message' => $message]);
        exit();
    }

    /**
     * 500 Error page
     */
    protected function error500($message = 'Sunucu hatası oluştu.') {
        http_response_code(500);
        $this->view('errors/500', ['message' => $message]);
        exit();
    }
}
