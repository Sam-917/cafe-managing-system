<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'errors' => []];
    
    try {
        // Validate and sanitize all inputs
        $requiredFields = [
            'location_id' => 'int',
            'table_id' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'phone' => 'string',
            'email' => 'email',
            'date' => 'date',
            'time' => 'time',
            'party_size' => 'int'
        ];

        $data = [];
        foreach ($requiredFields as $field => $type) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                $response['errors'][] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
                continue;
            }

            $value = trim($_POST[$field]);
            
            switch ($type) {
                case 'int':
                    $data[$field] = (int)$value;
                    break;
                case 'string':
                    $data[$field] = sanitize($value);
                    break;
                case 'email':
                    $data[$field] = filter_var($value, FILTER_SANITIZE_EMAIL);
                    if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                        $response['errors'][] = 'Invalid email format';
                    }
                    break;
                case 'date':
                    if (!DateTime::createFromFormat('Y-m-d', $value)) {
                        $response['errors'][] = 'Invalid date format';
                    }
                    $data[$field] = $value;
                    break;
                case 'time':
                    if (!DateTime::createFromFormat('H:i', $value)) {
                        $response['errors'][] = 'Invalid time format';
                    }
                    $data[$field] = $value;
                    break;
            }
        }

        // Special requests are optional
        $data['special_requests'] = isset($_POST['special_requests']) ? sanitize($_POST['special_requests']) : '';

        // Validate location exists
        if (empty($response['errors'])) {
            $stmt = $conn->prepare("SELECT * FROM restaurant_locations WHERE location_id = ? AND is_active = 1");
            $stmt->execute([$data['location_id']]);
            $location = $stmt->fetch();

            if (!$location) {
                $response['errors'][] = 'Selected location is not available';
            }
        }

        // Validate table exists and can accommodate party size
        if (empty($response['errors'])) {
            $stmt = $conn->prepare("SELECT * FROM restaurant_tables WHERE table_id = ? AND location_id = ? AND is_active = 1");
            $stmt->execute([$data['table_id'], $data['location_id']]);
            $table = $stmt->fetch();

            if (!$table) {
                $response['errors'][] = 'Selected table is not available';
            } elseif ($data['party_size'] > $table['capacity']) {
                $response['errors'][] = 'Number of guests exceeds table capacity';
            }
        }

        // Calculate end time (1.5 hours after start time)
        $startTime = $data['time'];
        $endTime = date('H:i:s', strtotime($startTime) + 5400); // 1.5 hours in seconds

        // Check for conflicting reservations
        if (empty($response['errors'])) {
            $stmt = $conn->prepare("
                SELECT reservation_id FROM reservations 
                WHERE table_id = ? 
                AND reservation_date = ? 
                AND (
                    (start_time < ? AND end_time > ?)
                    AND status IN ('confirmed', 'pending')
                )
            ");
            $stmt->execute([
                $data['table_id'],
                $data['date'],
                $endTime,
                $startTime
            ]);

            if ($stmt->rowCount() > 0) {
                $response['errors'][] = 'This table is already booked for the selected time';
            }
        }

        if (empty($response['errors'])) {
            // Generate confirmation number
            $confirmationNumber = generateConfirmationNumber();
            $customerName = $data['first_name'] . ' ' . $data['last_name'];

            // Insert reservation
            $stmt = $conn->prepare("
                INSERT INTO reservations (
                    table_id, customer_name, phone, email, 
                    reservation_date, start_time, end_time, 
                    guests, special_requests, status, confirmation_number
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $success = $stmt->execute([
                $data['table_id'],
                $customerName,
                $data['phone'],
                $data['email'],
                $data['date'],
                $startTime,
                $endTime,
                $data['party_size'],
                $data['special_requests'],
                'confirmed',
                $confirmationNumber
            ]);

            if ($success) {
                $response = [
                    'success' => true,
                    'confirmationNumber' => $confirmationNumber,
                    'reservationDetails' => [
                        'customer' => $customerName,
                        'date' => date('F j, Y', strtotime($data['date'])),
                        'time' => date('g:i A', strtotime($startTime)),
                        'guests' => $data['party_size'],
                        'location' => $location['name'],
                        'address' => $location['address'] . ', ' . $location['city'],
                        'table' => $table['name']
                    ]
                ];
            } else {
                $response['errors'][] = 'Failed to save reservation';
            }
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        $response['errors'][] = 'A database error occurred. Please try again.';
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        $response['errors'][] = 'An unexpected error occurred. Please try again.';
    }

    echo json_encode($response);
    exit;
}

// If not POST request, redirect to home
header('Location: ../reservation.php');
exit;
?>