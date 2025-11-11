<?php
/**
 * AI Recommendation System
 * Support for OpenAI and DeepSeek
 * MINALIA Parfüm E-Ticaret Platformu
 */

interface AIProviderInterface {
    public function generateRecommendation($userPreferences, $products);
    public function analyzeFragranceProfile($description);
    public function generateProductDescription($product);
}

/**
 * OpenAI Provider
 */
class OpenAIProvider implements AIProviderInterface {
    private $apiKey;
    private $model;
    private $baseUrl = 'https://api.openai.com/v1';

    public function __construct() {
        $this->apiKey = $_ENV['OPENAI_API_KEY'] ?? '';
        $this->model = $_ENV['OPENAI_MODEL'] ?? 'gpt-4';
    }

    public function generateRecommendation($userPreferences, $products) {
        $systemPrompt = "Sen bir parfüm uzmanısın. Kullanıcının tercihlerine göre en uygun parfümleri öner.";

        $userPrompt = $this->buildRecommendationPrompt($userPreferences, $products);

        $response = $this->callAPI('chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000
        ]);

        if (isset($response['choices'][0]['message']['content'])) {
            return $this->parseRecommendations($response['choices'][0]['message']['content'], $products);
        }

        return [];
    }

    public function analyzeFragranceProfile($description) {
        $prompt = "Bu parfüm açıklamasını analiz et ve koku profilini çıkar (odunsu, çiçeksi, meyvemsi, vs.):\n\n$description";

        $response = $this->callAPI('chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.5,
            'max_tokens' => 200
        ]);

        if (isset($response['choices'][0]['message']['content'])) {
            return $response['choices'][0]['message']['content'];
        }

        return null;
    }

    public function generateProductDescription($product) {
        $prompt = "Bu parfüm için çekici ve satış odaklı bir açıklama yaz:\n\n";
        $prompt .= "İsim: {$product['name']}\n";
        $prompt .= "Marka: {$product['brand']}\n";
        $prompt .= "Notalar: " . json_encode($product['notes'], JSON_UNESCAPED_UNICODE) . "\n";
        $prompt .= "Cinsiyet: {$product['gender']}\n";

        $response = $this->callAPI('chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.8,
            'max_tokens' => 300
        ]);

        if (isset($response['choices'][0]['message']['content'])) {
            return $response['choices'][0]['message']['content'];
        }

        return null;
    }

    private function callAPI($endpoint, $data) {
        $url = $this->baseUrl . '/' . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            logError("OpenAI API Error: HTTP $httpCode - $response");
            return [];
        }

        return json_decode($response, true);
    }

    private function buildRecommendationPrompt($preferences, $products) {
        $prompt = "Kullanıcı Tercihleri:\n";
        $prompt .= "- Cinsiyet: " . ($preferences['gender'] ?? 'unisex') . "\n";
        $prompt .= "- Tercih edilen notalar: " . implode(', ', $preferences['notes'] ?? []) . "\n";
        $prompt .= "- Fiyat aralığı: " . ($preferences['budget'] ?? 'esnek') . "\n";
        $prompt .= "- Kullanım: " . ($preferences['occasion'] ?? 'günlük') . "\n\n";

        $prompt .= "Mevcut Ürünler:\n";
        foreach ($products as $i => $product) {
            $prompt .= ($i + 1) . ". {$product['name']} - {$product['brand']}\n";
            $prompt .= "   Fiyat: {$product['price']} TL\n";
            $prompt .= "   Notalar: " . json_encode($product['fragrance_notes'], JSON_UNESCAPED_UNICODE) . "\n\n";
        }

        $prompt .= "Bu ürünlerden kullanıcıya en uygun 3 parfümü öner ve neden önerdiğini açıkla.";

        return $prompt;
    }

    private function parseRecommendations($aiResponse, $products) {
        // Extract product names from AI response and match with products
        $recommendations = [];

        foreach ($products as $product) {
            if (stripos($aiResponse, $product['name']) !== false) {
                $recommendations[] = [
                    'product' => $product,
                    'reason' => $this->extractReason($aiResponse, $product['name'])
                ];

                if (count($recommendations) >= 3) break;
            }
        }

        return $recommendations;
    }

    private function extractReason($text, $productName) {
        // Simple reason extraction
        $sentences = preg_split('/[.!?]/', $text);
        foreach ($sentences as $sentence) {
            if (stripos($sentence, $productName) !== false) {
                return trim($sentence) . '.';
            }
        }
        return 'Size uygun bir seçenek.';
    }
}

/**
 * DeepSeek Provider
 */
class DeepSeekProvider implements AIProviderInterface {
    private $apiKey;
    private $model;
    private $baseUrl = 'https://api.deepseek.com/v1';

    public function __construct() {
        $this->apiKey = $_ENV['DEEPSEEK_API_KEY'] ?? '';
        $this->model = $_ENV['DEEPSEEK_MODEL'] ?? 'deepseek-chat';
    }

    public function generateRecommendation($userPreferences, $products) {
        $systemPrompt = "You are a perfume expert. Recommend the best perfumes based on user preferences. Respond in Turkish.";

        $userPrompt = $this->buildRecommendationPrompt($userPreferences, $products);

        $response = $this->callAPI('chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000
        ]);

        if (isset($response['choices'][0]['message']['content'])) {
            return $this->parseRecommendations($response['choices'][0]['message']['content'], $products);
        }

        return [];
    }

    public function analyzeFragranceProfile($description) {
        $prompt = "Analyze this perfume description and extract the fragrance profile (woody, floral, fruity, etc.):\n\n$description\n\nRespond in Turkish.";

        $response = $this->callAPI('chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.5,
            'max_tokens' => 200
        ]);

        if (isset($response['choices'][0]['message']['content'])) {
            return $response['choices'][0]['message']['content'];
        }

        return null;
    }

    public function generateProductDescription($product) {
        $prompt = "Write an attractive and sales-focused description for this perfume in Turkish:\n\n";
        $prompt .= "Name: {$product['name']}\n";
        $prompt .= "Brand: {$product['brand']}\n";
        $prompt .= "Notes: " . json_encode($product['notes'], JSON_UNESCAPED_UNICODE) . "\n";
        $prompt .= "Gender: {$product['gender']}\n";

        $response = $this->callAPI('chat/completions', [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.8,
            'max_tokens' => 300
        ]);

        if (isset($response['choices'][0]['message']['content'])) {
            return $response['choices'][0]['message']['content'];
        }

        return null;
    }

    private function callAPI($endpoint, $data) {
        $url = $this->baseUrl . '/' . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            logError("DeepSeek API Error: HTTP $httpCode - $response");
            return [];
        }

        return json_decode($response, true);
    }

    private function buildRecommendationPrompt($preferences, $products) {
        $prompt = "User Preferences:\n";
        $prompt .= "- Gender: " . ($preferences['gender'] ?? 'unisex') . "\n";
        $prompt .= "- Preferred notes: " . implode(', ', $preferences['notes'] ?? []) . "\n";
        $prompt .= "- Budget: " . ($preferences['budget'] ?? 'flexible') . "\n";
        $prompt .= "- Occasion: " . ($preferences['occasion'] ?? 'daily') . "\n\n";

        $prompt .= "Available Products:\n";
        foreach ($products as $i => $product) {
            $prompt .= ($i + 1) . ". {$product['name']} - {$product['brand']}\n";
            $prompt .= "   Price: {$product['price']} TL\n";
            $prompt .= "   Notes: " . json_encode($product['fragrance_notes'], JSON_UNESCAPED_UNICODE) . "\n\n";
        }

        $prompt .= "Recommend the top 3 perfumes for this user and explain why. Respond in Turkish.";

        return $prompt;
    }

    private function parseRecommendations($aiResponse, $products) {
        $recommendations = [];

        foreach ($products as $product) {
            if (stripos($aiResponse, $product['name']) !== false) {
                $recommendations[] = [
                    'product' => $product,
                    'reason' => $this->extractReason($aiResponse, $product['name'])
                ];

                if (count($recommendations) >= 3) break;
            }
        }

        return $recommendations;
    }

    private function extractReason($text, $productName) {
        $sentences = preg_split('/[.!?]/', $text);
        foreach ($sentences as $sentence) {
            if (stripos($sentence, $productName) !== false) {
                return trim($sentence) . '.';
            }
        }
        return 'Size uygun bir seçenek.';
    }
}

/**
 * AI Recommendation Manager
 */
class AIRecommendationManager {
    private $provider;
    private $db;

    public function __construct($providerName = null) {
        if ($providerName === null) {
            $providerName = $_ENV['AI_PROVIDER'] ?? 'openai';
        }

        $this->provider = $this->createProvider($providerName);
        $this->db = Database::getInstance()->getConnection();
    }

    private function createProvider($name) {
        switch (strtolower($name)) {
            case 'openai':
                return new OpenAIProvider();
            case 'deepseek':
                return new DeepSeekProvider();
            default:
                throw new Exception("Unsupported AI provider: $name");
        }
    }

    /**
     * Get personalized recommendations for user
     */
    public function getPersonalizedRecommendations($userId) {
        // Get user preferences
        $preferences = $this->getUserPreferences($userId);

        // Get products
        $products = $this->getRelevantProducts($preferences);

        // Generate AI recommendations
        $recommendations = $this->provider->generateRecommendation($preferences, $products);

        // Log recommendations
        $this->logRecommendations($userId, $recommendations);

        return $recommendations;
    }

    /**
     * Get quiz-based recommendations
     */
    public function getQuizRecommendations($quizAnswers) {
        // Convert quiz answers to preferences
        $preferences = $this->quizToPreferences($quizAnswers);

        // Get products
        $products = $this->getRelevantProducts($preferences);

        // Generate recommendations
        return $this->provider->generateRecommendation($preferences, $products);
    }

    /**
     * Generate product description using AI
     */
    public function generateDescription($productId) {
        $sql = "SELECT p.*, b.name as brand FROM products p
                INNER JOIN brands b ON p.brand_id = b.id
                WHERE p.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        $product['notes'] = json_decode($product['fragrance_notes'], true);

        return $this->provider->generateProductDescription($product);
    }

    /**
     * Get user preferences from history
     */
    private function getUserPreferences($userId) {
        // Analyze past purchases
        $sql = "SELECT p.gender, p.fragrance_notes, p.price
                FROM order_items oi
                INNER JOIN orders o ON oi.order_id = o.id
                INNER JOIN products p ON oi.product_id = p.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC
                LIMIT 10";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $preferences = [
            'gender' => 'unisex',
            'notes' => [],
            'budget' => 'medium'
        ];

        if (!empty($purchases)) {
            // Determine most common gender
            $genders = array_column($purchases, 'gender');
            $preferences['gender'] = $this->mostCommon($genders);

            // Extract common notes
            $allNotes = [];
            foreach ($purchases as $purchase) {
                $notes = json_decode($purchase['fragrance_notes'], true);
                if ($notes) {
                    $allNotes = array_merge($allNotes, array_values($notes['ust'] ?? []),
                                           array_values($notes['orta'] ?? []),
                                           array_values($notes['alt'] ?? []));
                }
            }
            $preferences['notes'] = array_unique($allNotes);

            // Determine budget
            $avgPrice = array_sum(array_column($purchases, 'price')) / count($purchases);
            if ($avgPrice > 5000) {
                $preferences['budget'] = 'premium';
            } elseif ($avgPrice > 2000) {
                $preferences['budget'] = 'medium';
            } else {
                $preferences['budget'] = 'budget';
            }
        }

        return $preferences;
    }

    /**
     * Get relevant products based on preferences
     */
    private function getRelevantProducts($preferences) {
        $sql = "SELECT p.*, b.name as brand
                FROM products p
                INNER JOIN brands b ON p.brand_id = b.id
                WHERE p.stock_quantity > 0";

        $params = [];

        if ($preferences['gender'] !== 'unisex') {
            $sql .= " AND (p.gender = ? OR p.gender = 'unisex')";
            $params[] = $preferences['gender'];
        }

        $sql .= " ORDER BY p.is_featured DESC, p.rating DESC LIMIT 20";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Convert quiz answers to preferences
     */
    private function quizToPreferences($answers) {
        return [
            'gender' => $answers['gender'] ?? 'unisex',
            'notes' => $answers['preferred_notes'] ?? [],
            'budget' => $answers['budget'] ?? 'medium',
            'occasion' => $answers['occasion'] ?? 'daily'
        ];
    }

    /**
     * Log recommendations
     */
    private function logRecommendations($userId, $recommendations) {
        foreach ($recommendations as $rec) {
            $sql = "INSERT INTO ai_recommendations (user_id, product_id, reason, created_at)
                    VALUES (?, ?, ?, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $rec['product']['id'],
                $rec['reason']
            ]);
        }
    }

    /**
     * Helper: Find most common value in array
     */
    private function mostCommon($array) {
        $values = array_count_values($array);
        arsort($values);
        return key($values);
    }
}
