<?php
/**
 * Landing page
 *
 * This script is designed to be put online
 * Customers will land on this page to claim their compensation
 * This page will ask them if they want to share their data with Trouble Flight or not
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
$api_key = "qAytATLYboJGbETUn6LfHLVYROHUtXACUgvIDj5rLrT5k1jzlRt2XQ7qRaqhFuvF"; // Your API Key given by Trouble
$affId = "";

// Retrieve the booking data
$booking = loadBookingFromDb($_GET['booking_id']);

// Check the security code
$security_token = hash('sha256', $booking['booking_id'] . $_GET['flight_code'] . $booking['email']);

if ($security_token !== $_GET['security_token']) {
    http_response_code(403);
    die("Security code not valid");
}

// Check if the user approved or not
if (isset($_GET['approve'])) {
    if ($_GET['approve'] === 'yes') {
        $flights = [];
        foreach ($booking['flights'] as $flight) {
            $flights[] = [
                "flight_code" => $flight['flight_code'],
                "departure_date" => $flight['departure_date'],
                "arrival_date" => $flight['departure_date'],
                "departure" => $flight['departure'],
                "destination" => $flight['arrival'],
                "pnr" => $flight['pnr'],
                "trip" => $flight['trip'] === "outward" ? 1 : 2,
            ];
        }

        $passengers = [];
        foreach ($booking['passengers'] as $passenger) {
            $passengers[] = [
                "first_name" => $passenger['first_name'],
                "last_name" => $passenger['last_name'],
                "document" => $passenger['document'],
                "document_type" => $passenger['document_type'],
                "dob" => $passenger['dob'],
            ];
        }

        // Send the data to Trouble Flight and perform a redirect to the claim page
        $data = [
            "price" => 0,
            "currency" => "EUR",
            "flights" => $flights,
            "passengers" => $passengers,
            "language" => $booking['language'],
            "market" => "en",
            "contacts" => [
                "email" => $booking['email'],
                "phone" => $booking['phone']
            ]
        ];

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://a.troubleflight.com/api/v3/claim/partner/new-claim-information");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
        ]);

        // Execute cURL request
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code == 200) {
                // Decode the JSON response
                $response_data = json_decode($response, true);

                // Check if the response contains the tracking booking code
                if (isset($response_data['claim_information']['url']['current'])) {
                    header("Location: " . $response_data['claim_information']['url']['current']);
                    exit;
                } else {
                    echo "Error: Tracking code not found in the response.";
                }
            } else {
                echo "Error: " . $response;
            }
        }
    } else {
        // Just perform a redirect to Trouble Flight with the affiliation code
        $url = match ($booking['language']) {
            'ru' => "https://aff.tfly.eu/s" . $affId,
            'ro' => "https://aff.tfly.eu/r" . $affId,
            'it' => "https://aff.tfly.eu/i" . $affId,
            'es' => "https://aff.tfly.eu/e" . $affId,
            'hu' => "https://aff.tfly.eu/h" . $affId,
            'pl' => "https://aff.tfly.eu/p" . $affId,
            // or english by default
            default => "https://aff.tfly.eu/g" . $affId,
        };
        header("Location: $url");
        exit;
    }
}

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Claim Your Compensation</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #f8f9fa;
            }

            .container {
                text-align: center;
                background-color: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
        </style>
    </head>
    <body>

    <div class="container">
        <h1>Claim your compensation</h1>
        <p>Your flight <?php echo htmlspecialchars($_GET['flight_code']); ?> is eligible for compensation. Do you want
            to share your data with Trouble Flight to make the procedure quicker?</p>
        <a href="?<?php echo $_SERVER['QUERY_STRING'] . '&approve=yes'; ?>" class="btn btn-success">Yes</a>
        <a href="?<?php echo $_SERVER['QUERY_STRING'] . '&approve=no'; ?>" class="btn btn-danger">No</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>

<?php
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

?>