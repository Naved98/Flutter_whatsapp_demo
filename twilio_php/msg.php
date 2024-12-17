<?php
// CORS headers to allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// For preflight requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require_once 'vendor/autoload.php';
use Twilio\Rest\Client;

$account_sid = 'AC2ddc6b95d216f4c5d6999827f47d3c51'; // Your Twilio Account SID
$auth_token = '58ea9629191cf750c40ab7556bae883c'; // Your Twilio Auth Token
$twilio_number = 'whatsapp:+919315605199'; // Your Twilio Sandbox WhatsApp number

// Create Twilio client
$twilio = new Client($account_sid, $auth_token);

// Get POST data from the Flutter app
$data = json_decode(file_get_contents('php://input'), true);

$recipient = $data['to'] ?? ''; // WhatsApp recipient number
$customer_name = $data['customer_name'] ?? '';
$request_id = $data['request_id'] ?? '';
$service = $data['service'] ?? '';
$date = $data['date'] ?? '';
$time = $data['time'] ?? '';
$cost = $data['cost'] ?? '';
$salon = $data['salon'] ?? '';
$phone_number = $data['phone_number'] ?? '';

// Validate input data
if ($recipient && $customer_name && $request_id && $service && $date && $time && $cost && $salon && $phone_number) {
    try {
        // Send the WhatsApp message using Twilio API and template
        $response = $twilio->messages->create(
            $recipient, // recipient's WhatsApp number
            [
                'from' => $twilio_number, // Twilio WhatsApp number
                'body' => '', // Leave body empty as we're using a template
                'contentSid' => 'HX4ba5e82ea3ae15a01a24fb6e938f43b2', // Template SID from Twilio
                'templateData' => json_encode([
                    '1' => $customer_name,
                    '2' => $request_id,
                    '3' => $service,
                    '4' => $date,
                    '5' => $time,
                    '6' => $cost,
                    '7' => $salon,
                    '8' => $phone_number,
                ])
            ]
        );

        // Send success response
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully!',
            'sid' => $response->sid,
        ]);
    } catch (Exception $e) {
        // Handle errors
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
        ]);
    }
} else {
    // Invalid input
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid input. All fields are required.',
    ]);
}
?>
