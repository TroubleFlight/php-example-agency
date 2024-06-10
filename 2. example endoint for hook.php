<?php
/**
 * Webhook eligible-booking
 *
 * This script is designed to be put online
 * Trouble Flight will call the url to this script when a flight become eligible for compensation
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

// Example data coming from the call
$_POST['booking_id'] = "ABC123456";
$_POST['code'] = "UQ5No9NWUkkMm8q8Z3gI";
$_POST['compensation'] = 250;
$_POST['departure_date'] = "2023-04-02";
$_POST['flight_code'] = "FR123";
$_POST['language'] = "ro";
$_POST['regulations'] = ["EU_EC261_2004", "AL_2013"];
$_POST['security_code'] = "d2c30437c0a7e471e9a62553a95fc7783579d1c4f534d04a7bf169c30a476886";
$_POST['status'] = "eligible";

// Check the security code
$security_code = hash('sha256', $_POST['booking_id'] . $api_key);
if ($security_code !== $_POST['security_code']) {
    http_response_code(403);
    die("Security code not valid");
}

// Retrieve the booking data
$booking = loadBookingFromDb($_POST['booking_id']);

// Define where (in your website) the user will land
// Please assure to implement some security check to avoid unauthorized access
$security_token = hash('sha256', $booking['booking_id'] . $_POST['flight_code'] . $booking['email']);

$url = "https://compensation.example.com/3.+landing.php?booking_id=" . $booking['booking_id'] . "&flight_code=" . $_POST['flight_code'] . "&security_token=" . $security_token;

// Send the invite with the custom url
$subject = "Your flight is eligible for " . $_POST['compensation'] . " euro of compensation";
$message = "Your flight is eligible for compensation. You can claim your compensation by clicking on the following link: " . $url;

// Send the email
mail($booking['email'], $subject, $message);


// This is an example so we just hard code the booking data, in your case you can connect to db, api and retrieve the
// booking data from your system
function loadBookingFromDb($booking_id)
{
    return [
        "booking_id" => $booking_id,
        "email" => "claudio.mulas@troubleflight.com",
        "phone" => "+40756801789",
        "language" => "en",
        "first_name" => "Claudio",
        "last_name" => "Mulas",
        "flights" => [
            [
                "flight_code" => "FR123",
                "departure_date" => "2024-01-01 10:00",
                "departure" => "MXP",
                "arrival" => "STN",
                "pnr" => "ABC123",
                "trip" => "outward"
            ],
            [
                "flight_code" => "U2456",
                "departure_date" => "2024-01-02 10:00",
                "departure" => "STN",
                "arrival" => "OTP",
                "pnr" => "DEF456",
                "trip" => "outward"
            ],
            [
                "flight_code" => "W4789",
                "departure_date" => "2024-01-02 13:00",
                "departure" => "OTP",
                "arrival" => "STN",
                "pnr" => "GHI789",
                "trip" => "return"
            ],
        ],
        "passengers" => [
            [
                "first_name" => "Claudio",
                "last_name" => "Mulas",
                "document" => "123456789",
                "document_type" => "passport",
                "dob" => "1980-01-01",
            ],
            [
                "first_name" => "John",
                "last_name" => "Doe",
                "document" => "987654321",
                "document_type" => "passport",
                "dob" => "1980-01-01",
            ],
        ]
    ];
}