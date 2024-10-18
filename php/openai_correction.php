<?php
// API key cho OpenAI
$api_key = ''; 

// Lấy dữ liệu văn bản từ yêu cầu POST  
$text = $_POST['text'] ?? '';

// Kiểm tra xem văn bản có rỗng hay không
if (empty($text)) {
    echo json_encode(['error' => 'No text provided']);
    exit;
}

// Tạo yêu cầu tới API của OpenAI
$url = 'https://api.openai.com/v1/chat/completions';

$data = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        ['role' => 'system', 'content' => 'You are an assistant that corrects spelling and grammar mistakes in the given text. Respond only with the corrected text.'],
        ['role' => 'user', 'content' => $text], // Gửi trực tiếp văn bản cần sửa lỗi
    ],
    'max_tokens' => 500,
    'temperature' => 0.7,
];

// Cấu hình cURL để gửi yêu cầu
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

// Thực hiện yêu cầu và lấy phản hồi
$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo json_encode(['error' => curl_error($curl)]);
    curl_close($curl);
    exit;
}

curl_close($curl);

// Chuyển đổi phản hồi JSON thành mảng PHP
$result = json_decode($response, true);

// Kiểm tra phản hồi từ API
if (isset($result['choices'][0]['message']['content'])) {
    // Loại bỏ dấu ngoặc kép nếu có
    $corrected_text = trim($result['choices'][0]['message']['content'], '"');
    echo json_encode(['choices' => $corrected_text]);
} else {
    echo json_encode(['error' => 'API did not return a valid response']);
}
?>
