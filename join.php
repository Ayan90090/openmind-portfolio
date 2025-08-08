<?php
// Configuration
$baserow_api_token = '3M6dfJp4AeFCFtzRjwK7P80jsGG9VWFs';
$table_id = '636019';
$api_url = "https://api.baserow.io/api/database/rows/table/{$table_id}/";

// Field IDs from your Baserow table (replace with actual field IDs)
$fields = [
    'name' => 5189605,
    'email' => 5189606,
    'phone' => 5189772,
    'company_name' => 5189773,
    'company_category' => 5189774,
    'project_details' => 5189775,
    'type' => 5189875,
    'created_at' => 5189611 // DateTime field in Baserow
];

// Accept POST input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (empty($data['name']) || empty($data['email']) || empty($data['company_name']) || empty($data['company_category'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields.']);
    exit;
}

// Prepare payload
$payload = [
    "field_{$fields['name']}" => htmlspecialchars(trim($data['name'])),
    "field_{$fields['email']}" => htmlspecialchars(trim($data['email'])),
    "field_{$fields['company_name']}" => htmlspecialchars(trim($data['company_name'])),
    "field_{$fields['company_category']}" => htmlspecialchars(trim($data['company_category'])),
    "field_{$fields['type']}" => htmlspecialchars(trim($data['type'])),
    "field_{$fields['created_at']}" => date("Y-m-d H:i:s"),
];

// Optional fields
if (!empty($data['phone'])) {
    $payload["field_{$fields['phone']}"] = htmlspecialchars(trim($data['phone']));
}
if (!empty($data['project_details'])) {
    $payload["field_{$fields['project_details']}"] = htmlspecialchars(trim($data['project_details']));
}

// Send to Baserow
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Token {$baserow_api_token}",
    "Content-Type: application/json",
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Output result
if ($httpCode === 200 || $httpCode === 201) {
    echo json_encode(['success' => true, 'response' => json_decode($response, true)]);
} else {
    http_response_code($httpCode);
    echo json_encode(['error' => 'Baserow API request failed.', 'response' => json_decode($response, true)]);
}
?>