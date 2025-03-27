<?php

session_start();
header('Content-Type: application/json');


$api_key = "AIzaSyBLJPxXxF7QnQ6poEPXD8sl4jKtT0iXwFI";
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$api_key";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['message'])) {
	echo json_decode(['error' => 'Invalid input']);
	exit;
}

$user_message = strtolower(trim($input['message']));

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	echo json_encode(["error" => "Database connection failed"]);
	exit;
}

$product_data = [];
$result = $conn->query("SELECT description, price FROM product");

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		$product_data[] = "Product: " . $row['description'] . " | Price: £" . $row['price'];
	}
}

$conn->close();

$database_info = "Here is the product list from the shop:\n" . implode("\n", $product_data);

$gemini_prompt = "You are an AI assistant for an online shop. You have access to the shop's product database. When users ask about products, provide information based on the following list:

$database_info

User question: $user_message";

$data = [
	"contents" => [
		[
			"parts" => [
				['text' => $gemini_prompt]
			]
		]
	]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
	echo json_decode(['error' => 'Google Gemini API error']);
	exit;
}
$response_data = json_decode($response, true);

if (!isset($response_data['candidates'][0]['content']['parts'][0]['text'])) {
	echo json_decode(['error' => 'Unexpected API response Format']);
	exit;
}

$ai_response = trim($response_data['candidates'][0]['content']['parts'][0]['text']);
echo json_encode(['response' => $ai_response]);
