<?php
/**
 * Page Controller
 * Display static pages (About, Contact, Terms, Privacy)
 * MINALIA Parfüm E-Ticaret Platformu
 */

class PageController extends BaseController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Show page by slug
     */
    public function show($slug) {
        // Get page from database
        $sql = "SELECT * FROM pages WHERE slug = ? AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$page) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => 'Sayfa Bulunamadı',
                'message' => 'Aradığınız sayfa bulunamadı.'
            ]);
            return;
        }

        // Display page
        $this->view('pages/page-detail', [
            'title' => $page['meta_title'] ?: $page['title'],
            'meta_description' => $page['meta_description'],
            'page' => $page
        ]);
    }

    /**
     * Contact page with form
     */
    public function contact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processContactForm();
            return;
        }

        // Get contact page content
        $sql = "SELECT * FROM pages WHERE slug = 'iletisim' AND is_active = 1";
        $stmt = $this->db->query($sql);
        $page = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->view('pages/contact', [
            'title' => 'İletişim',
            'meta_description' => 'MINALIA ile iletişime geçin',
            'page' => $page
        ]);
    }

    /**
     * Process contact form
     */
    private function processContactForm() {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');

        // Validate
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            setFlashMessage('Lütfen tüm alanları doldurun.', 'error');
            redirect('/iletisim');
            return;
        }

        if (!isValidEmail($email)) {
            setFlashMessage('Geçerli bir email adresi girin.', 'error');
            redirect('/iletisim');
            return;
        }

        // Save to database (optional - create contact_messages table)
        try {
            $sql = "INSERT INTO contact_messages (name, email, subject, message, created_at)
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name, $email, $subject, $message]);

            // Send email notification (optional)
            // EmailService::sendContactNotification($name, $email, $subject, $message);

            setFlashMessage('Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.', 'success');
        } catch (Exception $e) {
            setFlashMessage('Bir hata oluştu. Lütfen daha sonra tekrar deneyin.', 'error');
        }

        redirect('/iletisim');
    }
}
