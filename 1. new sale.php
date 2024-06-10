<?php
/**
 * New Sale
 *
 * This script is designed to send a new flight ticket sale to Trouble Flight.
 * Trouble Flight will monitor the flight and in case of disruption call the webhook.
 *
 * @package    Trouble Flight Example
 * @subpackage Email
 * @version    1.0.0
 * @since      2024
 * @license    MIT License
 * Author: Claudio Mulas <claudio.mulas@troubleflight.com>
 *
 * © 2024 Doorify Tech SRL. All rights reserved.
 */

// Configuration
$api_key = "qAytATLYboJGbETUn6LfHLVYROHUtXACUgvIDj5rLrT5k1jzlRt2XQ7qRaqhFuvF"; // Your API Key given by Trouble Flight

// Example data coming from your system
$booking_id = "ABC123456";
$flights = [
    [
        "flight_code" => "FR123",
        "departure_date" => "2024-01-01 10:00",
    ],
    [
        "flight_code" => "U2456",
        "departure_date" => "2024-01-02 10:00",
    ],
    [
        "flight_code" => "W4789",
        "departure_date" => "2024-01-02 13:00",
    ],
];

// Generate a security code using SHA-256
$security_code = hash('sha256', $booking_id . $api_key);

$language = "ro"; // Default language (en, ru, ro, it, es, hu, pl)

// Prepare data for sending
$data = [
    'booking_id' => $booking_id,
    'flights' => $flights,
    'security_code' => $security_code
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://a.troubleflight.com/api/v3/claim/partner/new-booking");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key,
]);

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code == 200) {
        // Decode the JSON response
        $response_data = json_decode($response, true);

        // Check if the response contains the tracking booking code
        if (isset($response_data['tracking_booking']['code'])) {
            $tracking_code = $response_data['tracking_booking']['code'];
            echo "Tracking Code: " . $tracking_code;
        } else {
            echo "Error: Tracking code not found in the response.";
        }
    } else {
        echo "Error: " . $response;
    }
}

// Close cURL
curl_close($ch);