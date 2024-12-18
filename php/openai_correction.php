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
    'model' => 'ft:gpt-3.5-turbo-1106:personal::ATJerxqE', 
    'messages' => [
        ['role' => 'system', 'content' => 'You are an assistant that corrects spelling and grammar mistakes in Vietnamese. Respond with the corrected text.'],
        ['role' => 'user', 'content' => $text],
    ],
    'max_tokens' => 2000,
    'temperature' => 0.3,
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
    $corrected_text = trim($result['choices'][0]['message']['content'], '"');

    // Tạo danh sách các từ sai (sử dụng mảng giả định hoặc API trả về danh sách lỗi)
    $errors = []; // Cập nhật với danh sách từ sai chính tả từ API nếu có
    
    // Trả về kết quả bao gồm văn bản đã chỉnh sửa và các lỗi
    echo json_encode(['choices' => $corrected_text, 'errors' => $errors]);
} else {
    echo json_encode(['error' => 'API did not return a valid response']);
}
?>
