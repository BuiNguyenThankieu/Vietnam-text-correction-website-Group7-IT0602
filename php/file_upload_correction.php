<?php
require '../vendor/autoload.php';

// Function to create the corrected file and return to user
function createWordFile($correctedText, $fileName) {
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $section = $phpWord->addSection();
    $section->addText($correctedText);

    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $filePath = '../uploads/' . $fileName;
    $objWriter->save($filePath);
    return $filePath;
}

// Function to handle file upload and correction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file is uploaded
    if (!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] === UPLOAD_ERR_NO_FILE) {
        echo json_encode(['error' => 'No file selected. Please choose a file to upload.']);
        exit;
    }

    $file = $_FILES['fileToUpload'];
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validate file type
    if ($fileType !== 'docx' && $fileType !== 'txt') {
        echo json_encode(['error' => 'Invalid file type. Only .docx and .txt are allowed.']);
        exit;
    }

    $filePath = '../uploads/' . basename($file['name']);
    move_uploaded_file($file['tmp_name'], $filePath);

    // Read file content
    if ($fileType === 'txt') {
        $text = file_get_contents($filePath);
    } elseif ($fileType === 'docx') {
        $text = readWordFile($filePath);
    }

    // Send text to OpenAI for correction
    $correctedText = correctTextUsingOpenAI($text);

    // Generate corrected file
    if ($fileType === 'txt') {
        $fileName = 'corrected_file.txt';
        file_put_contents('../uploads/' . $fileName, $correctedText);
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        readfile('../uploads/' . $fileName);
    } elseif ($fileType === 'docx') {
        $fileName = 'corrected_file.docx';
        $filePath = createWordFile($correctedText, $fileName);
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        readfile($filePath);
    }

    // Clean up: Remove the file after sending
    unlink($filePath);
    exit;
}

// Function to correct text using OpenAI API
function correctTextUsingOpenAI($text) {
    // Your OpenAI API key
    $api_key = 'your-api-key-here';  // Replace with your actual API key

    // Setup OpenAI API request
    $url = 'https://api.openai.com/v1/chat/completions';
    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are an assistant that corrects spelling and grammar mistakes in the given text.'],
            ['role' => 'user', 'content' => $text],
        ],
        'max_tokens' => 500,
        'temperature' => 0.7,
    ];

    $options = [
        'http' => [
            'header' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key
            ],
            'method' => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $result = json_decode($response, true);

    if (isset($result['choices'][0]['message']['content'])) {
        return trim($result['choices'][0]['message']['content'], '"');
    } else {
        return 'Error: Unable to get corrected text from API.';
    }
}

// Function to read the content of a Word file
function readWordFile($filePath) {
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
    $text = '';

    foreach ($phpWord->getSections() as $section) {
        $elements = $section->getElements();
        foreach ($elements as $element) {
            if (method_exists($element, 'getText')) {
                $text .= $element->getText() . "\n";
            }
        }
    }

    return $text;
}
?>
