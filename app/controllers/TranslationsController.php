<?php

namespace controllers;

use models\Translations;
// Import the OpenAI SDK
use Orhanerday\OpenAi\OpenAi;

class TranslationsController
{
   public function validateTranslations($inputData) {
    $errors = [];
    $originalText = isset($inputData['originalText']) ? $inputData['originalText'] : null;
    $newText = isset($inputData['newText']) ? $inputData['newText'] : null;

    if ($originalText) {
        $originalText = htmlspecialchars($originalText, ENT_QUOTES|ENT_HTML5, 'UTF-8', true);
        if (strlen($originalText) < 2) {
            $errors['originalTextShort'] = 'Original message is too short';
        }
    } else {
        $errors['requiredOriginalText'] = 'Original message is required';
    }

    if ($newText) {
        $newText = htmlspecialchars($newText, ENT_QUOTES|ENT_HTML5, 'UTF-8', true);
        if (strlen($newText) < 2) {
            $errors['newTextShort'] = 'Corporate message is too short';
        }
    } else {
        $errors['requiredNewText'] = 'Corporate message is required';
    }

    if (count($errors)) {
        http_response_code(400);
        echo json_encode($errors);
        exit();
    }
    
    return [
        'originalText' => $originalText,
        'newText' => $newText,
    ];
}

   public function generateTranslation() {
    $originalText = isset($_POST['originalText']) ? $_POST['originalText'] : null;
    
    if (!$originalText) {
        http_response_code(400);
        echo json_encode(['error' => 'Original text is required']);
        exit();
    }
    
    $originalText = htmlspecialchars($originalText, ENT_QUOTES|ENT_HTML5, 'UTF-8', true);
    
    try {
        $apiKey = $_ENV['OPENAI_API_KEY'];
        $open_ai = new OpenAi($apiKey);
        
        // Use chat completion with current model
        $params = [
            'model' => 'gpt-3.5-turbo',  // Current model as of 2025
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Transform the following message into corporate business speak. Use euphemisms, jargon, and overly formal language to make simple ideas sound complex and important. Make it sound like something a mid-level manager would write in a corporate email. The tone should be professional but deliberately using corporate buzzwords and unnecessarily complex phrasing.'
                ],
                [
                    'role' => 'user',
                    'content' => $originalText
                ]
            ],
            'temperature' => 0.6,
            'max_tokens' => 200
        ];
        
        $complete = $open_ai->chat($params);
        $response = json_decode($complete, true);
        
        // Check for errors in response
        if (isset($response["error"])) {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to generate translation: ' . $response["error"]["message"]]);
            exit();
        }
        
        // Extract message from chat completion response format
        if (!isset($response["choices"][0]["message"]["content"])) {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to generate translation: Unexpected response format']);
            exit();
        }
        
        $corporateMessage = trim($response["choices"][0]["message"]["content"]);
        
        header("Content-Type: application/json");
        $responseData = ['corporateMessage' => $corporateMessage];
        echo json_encode($responseData);
        
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
    
    exit();
}

    public function getAllTranslations() {
        $translationsModel = new Translations();
        header("Content-Type: application/json");
        $translations = $translationsModel->getAllTranslations();
        echo json_encode($translations);
        exit();
    }

    public function getTranslationsByID($id) {
        $translationsModel = new Translations();
        header("Content-Type: application/json");
        $translations = $translationsModel->getTranslationsById($id);
        echo json_encode($translations);
        exit();
    }

    public function saveTranslations() {
    $inputData = [
        'originalText' => isset($_POST['originalText']) ? $_POST['originalText'] : null,
        'newText' => isset($_POST['newText']) ? $_POST['newText'] : null,
    ];
    
    $translationsData = $this->validateTranslations($inputData);
    
    $translations = new Translations();
    $result = $translations->saveTranslations([
        'originalText' => $translationsData['originalText'],
        'newText' => $translationsData['newText'],
    ]);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $translationsData
    ]);
    exit();
}

    public function deleteTranslations($id) {
        if (!$id) {
            http_response_code(404);
            exit();
        }
        
        $translations = new Translations();
        $result = $translations->deleteTranslations(['id' => $id]);

        http_response_code(200);
        echo json_encode([
            'success' => true
        ]);
        exit();
    }

    public function translationsView() {
        include '../public/assets/views/translations/translations-view.html';
        exit();
    }

    public function translationsDeleteView() {
        include '../public/assets/views/translations/translations-delete.html';
        exit();
    }

    public function translationsHomepageView() {
        include '../public/assets/views/translations/translations-homepage.html';
        exit();
    }
}