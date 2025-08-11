<?php
require_once 'includes/config.php';



// Initialize variables
$selectedLocation = isset($_GET['location_id']) ? $_GET['location_id'] : '';
$selectedDate = isset($_GET['date']) ? $_GET['date'] : '';
$selectedTime = isset($_GET['time']) ? $_GET['time'] : '';
$partySize = isset($_GET['party_size']) ? intval($_GET['party_size']) : 2;

// Get available time slots if date is selected
$availableTimes = [];
if ($selectedDate) {
    $availableTimes = getAvailableTimeSlots($selectedDate, $partySize);
}

// Get table availability if both date and time are selected
$occupiedTables = [];
if ($selectedDate && $selectedTime) {
    $occupiedTables = getTableAvailability($selectedDate, $selectedTime);
}

// Get all locations from database
$stmt = $conn->query("SELECT * FROM restaurant_locations WHERE is_active = TRUE");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all tables from database
$stmt = $conn->query("SELECT * FROM restaurant_tables WHERE is_active = TRUE");
$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
$tableData = [];
foreach ($tables as $table) {
    $tableData[$table['table_id']] = $table;
}

// Get all tables from database - update this query
$stmt = $conn->query("SELECT * FROM restaurant_tables WHERE is_active = TRUE");
$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
$tableData = [];
foreach ($tables as $table) {
    $tableData[$table['table_id']] = $table;
}

// Filter tables by selected location if one is selected
$filteredTables = $tables;
if ($selectedLocation) {
    $filteredTables = array_filter($tables, function($table) use ($selectedLocation) {
        return $table['location_id'] == $selectedLocation;
    });
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/reservation.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                <div class="glass-card rounded-4 p-4 p-md-5">

                    
                    
                    <!-- Header -->
                    <div class="text-center mb-5">
                        <h1 class="h2 fw-bold text-gray-800 mb-2">Reserve Your Table</h1>
                        <p class="text-muted mb-4">Select your preferred location, date and time for dining</p>
                         
                        <!-- Progress Steps -->
                        <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
                            <div class="step-indicator active" id="step1">1</div>
                            <div class="bg-gray-300" style="width: 30px; height: 2px;"></div>
                            <div class="step-indicator inactive" id="step2">2</div>
                            <div class="bg-gray-300" style="width: 30px; height: 2px;"></div>
                            <div class="step-indicator inactive" id="step3">3</div>
                            <div class="bg-gray-300" style="width: 30px; height: 2px;"></div>
                            <div class="step-indicator inactive" id="step4">4</div>
                            <div class="bg-gray-300" style="width: 30px; height: 2px;"></div>
                            <div class="step-indicator inactive" id="step5">5</div>
                        </div>
                        <div class="d-flex justify-content-center gap-3 text-sm step-labels">
                            <span class="<?php echo (empty($selectedLocation)) ? 'text-primary fw-medium' : 'text-muted'; ?>">Location</span>
                            <span class="<?php echo (!empty($selectedLocation)) && empty($selectedDate) ? 'text-primary fw-medium' : 'text-muted'; ?>">Date</span>
                            <span class="<?php echo (!empty($selectedDate) && empty($selectedTime)) ? 'text-primary fw-medium' : 'text-muted'; ?>">Time</span>
                            <span class="<?php echo (!empty($selectedTime) && empty($_GET['table_id'])) ? 'text-primary fw-medium' : 'text-muted'; ?>">Table</span>
                            <span class="<?php echo (!empty($_GET['table_id'])) ? 'text-primary fw-medium' : 'text-muted'; ?>">Details</span>
                        </div>
                    </div>

                    <!-- Step 1: Location Selection -->
                    <div id="locationStep" class="step-content">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="fw-semibold mb-4 d-flex align-items-center">
                                    <i class="bi bi-geo-alt me-2 text-primary"></i>
                                    Choose Restaurant Location
                                </h4>
                                
                                <!-- Location Cards -->
                                <div class="row g-3">
                                    <?php foreach ($locations as $location): ?>
                                    <div class="col-md-6">
                                        <div class="location-card bg-white rounded-3 border p-4 h-100 cursor-pointer" 
                                             onclick="selectLocation('<?php echo $location['location_id']; ?>')" 
                                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.1)'" 
                                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.05)'">
                                             
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="fw-semibold mb-0"><?php echo htmlspecialchars($location['name']); ?></h5>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-star-fill text-warning me-1"></i>
                                                    <span class="fw-medium"><?php echo htmlspecialchars($location['rating']);?></span>
                                                    <span class="text-muted ms-1"><?php echo htmlspecialchars($location['reviews']);?></span>
                                                </div>
                                            </div>
                                            <p class="text-muted mb-3">
                                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                                <?php echo htmlspecialchars($location['address']); ?>
                                            </p>
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bi bi-clock text-primary me-2"></i>
                                                    <span class="small">Open: <?php echo date('g:i A', strtotime($location['opening_time'])); ?> - <?php echo date('g:i A', strtotime($location['closing_time'])); ?></span>
                                                </div>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <?php 
                                                    $features = ['Valet Parking', 'Free WiFi', 'Full Bar'];
                                                    foreach ($features as $feature): ?>
                                                    <span class="badge bg-light text-dark"><?php echo $feature; ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            <p class="small text-muted mb-0"><?php echo htmlspecialchars($location['description'] ?? 'Our premium location with excellent service and ambiance.'); ?></p>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Date Selection -->
                    <div id="dateStep" class="step-content d-none">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="fw-semibold mb-4 d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-2 text-primary"></i>
                                    Choose Date
                                </h4>
                                
                                <div class="bg-light rounded-3 p-3 mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt text-primary me-2"></i>
                                        <span class="fw-medium">Selected Location: </span>
                                        <span id="selectedLocationDisplay" class="text-primary ms-2"></span>
                                    </div>
                                </div>
                                
                                <!-- Quick Date Options -->
                                <div class="row g-2 mb-4">
                                    <div class="col-6 col-md-3">
                                        <button class="btn btn-outline-primary w-100 quick-date" onclick="selectQuickDate('today')">
                                            <small>Today</small><br>
                                            <span id="todayDate"></span>
                                        </button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button class="btn btn-outline-primary w-100 quick-date" onclick="selectQuickDate('tomorrow')">
                                            <small>Tomorrow</small><br>
                                            <span id="tomorrowDate"></span>
                                        </button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button class="btn btn-outline-primary w-100 quick-date" onclick="selectQuickDate('weekend')">
                                            <small>This Weekend</small><br>
                                            <span id="weekendDate"></span>
                                        </button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button class="btn btn-outline-primary w-100 quick-date" onclick="selectQuickDate('nextweek')">
                                            <small>Next Week</small><br>
                                            <span id="nextWeekDate"></span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Custom Calendar -->
                                <div class="bg-white rounded-3 p-4 border">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <button class="btn btn-sm btn-outline-secondary" onclick="changeMonth(-1)">
                                            <i class="bi bi-chevron-left"></i>
                                        </button>
                                        <h5 class="mb-0 fw-semibold" id="currentMonth"></h5>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="changeMonth(1)">
                                            <i class="bi bi-chevron-right"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="row text-center text-muted small fw-medium mb-2">
                                        <div class="col">Sun</div>
                                        <div class="col">Mon</div>
                                        <div class="col">Tue</div>
                                        <div class="col">Wed</div>
                                        <div class="col">Thu</div>
                                        <div class="col">Fri</div>
                                        <div class="col">Sat</div>
                                    </div>
                                    
                                    <div id="calendarGrid"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Time Selection -->
                    <div id="timeStep" class="step-content d-none">
                        <h4 class="fw-semibold mb-4 d-flex align-items-center">
                            <i class="bi bi-clock me-2 text-primary"></i>
                            Select Time
                        </h4>
                        
                        <div class="bg-light rounded-3 p-3 mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-geo-alt text-primary me-2"></i>
                                        <span class="fw-medium">Location: </span>
                                        <span id="selectedLocationDisplay2" class="text-primary ms-2"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-calendar-check text-primary me-2"></i>
                                        <span class="fw-medium">Date: </span>
                                        <span id="selectedDateDisplay" class="text-primary ms-2"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Party Size -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Party Size</label>
                                <select class="form-select" id="partySize" onchange="updateAvailableTimes()">
                                    <option value="1">1 Person</option>
                                    <option value="2" selected>2 People</option>
                                    <option value="3">3 People</option>
                                    <option value="4">4 People</option>
                                    <option value="5">5 People</option>
                                    <option value="6">6 People</option>
                                    <option value="7">7 People</option>
                                    <option value="8">8+ People</option>
                                </select>
                            </div>
                        </div>

                        <!-- Time Slots -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">Available Times</h6>
                            <div class="row g-2" id="timeSlots">
                                <?php foreach ($availableTimes as $time): 
                                    $formattedTime = date('g:i A', strtotime($time));
                                ?>
                                <div class="col-6 col-md-4 col-lg-3">
                                    <button class="btn w-100 py-2 time-slot" onclick="selectTime('<?php echo $time; ?>')">
                                        <?php echo $formattedTime; ?>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Table Selection -->
                    <div id="tableStep" class="step-content d-none">
                        <h4 class="fw-semibold mb-4 d-flex align-items-center">
                            <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>
                            Choose Your Table
                        </h4>
                        
                        <div class="bg-light rounded-3 p-3 mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-geo-alt text-primary me-2"></i>
                                        <span class="fw-medium">Location: </span>
                                        <span id="selectedLocationDisplay3" class="text-primary ms-2"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-calendar-check text-primary me-2"></i>
                                        <span class="fw-medium">Date: </span>
                                        <span id="selectedDateDisplay2" class="text-primary ms-2"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-clock text-primary me-2"></i>
                                        <span class="fw-medium">Time: </span>
                                        <span id="selectedTimeDisplay" class="text-primary ms-2"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Restaurant Layout -->
                        <div class="bg-white rounded-3 border p-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-semibold mb-0">Restaurant Layout</h6>
                                <div class="d-flex gap-3 small">
                                    <div class="d-flex align-items-center">
                                        <div class="table-legend available me-1"></div>
                                        <span>Available</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="table-legend occupied me-1"></div>
                                        <span>Occupied</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="table-legend selected me-1"></div>
                                        <span>Selected</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="restaurant-layout position-relative" style="height: 400px; background: #f8f9fa; border-radius: 8px;">
                                <!-- Entrance -->
                                <div class="position-absolute" style="top: 10px; left: 50%; transform: translateX(-50%);">
                                    <div class="bg-secondary text-white px-3 py-1 rounded-pill small">
                                        <i class="bi bi-door-open me-1"></i>Entrance
                                    </div>
                                </div>
                                
                                <!-- Bar Area -->
                                <div class="position-absolute bg-dark rounded" style="top: 50px; right: 20px; width: 120px; height: 60px;">
                                    <div class="text-white text-center pt-3 small">Bar</div>
                                </div>
                                
                                <!-- Kitchen -->
                                <div class="position-absolute bg-warning rounded" style="bottom: 20px; right: 20px; width: 100px; height: 80px;">
                                    <div class="text-center pt-4 small">Kitchen</div>
                                </div>
                                 
                                <!-- Tables -->
                                <?php foreach ($filteredTables as $table): 
                                    $isOccupied = in_array($table['table_id'], $occupiedTables);
                                    $classes = 'restaurant-table';
                                    $classes .= ' table-' . $table['capacity'];
                                    if ($table['type'] === 'booth') $classes .= ' table-booth';
                                    if ($isOccupied) $classes .= ' occupied';
                                ?>
                                <div class="<?php echo $classes; ?>" 
                                    data-table="<?php echo $table['table_id']; ?>" 
                                    onclick="<?php echo $isOccupied ? '' : "selectTable('{$table['table_id']}')"; ?>"
                                    style="top: <?php echo $table['y_position'] ?? rand(80, 320); ?>px; 
                                            left: <?php echo $table['x_position'] ?? rand(20, 220); ?>px;">
                                    <div class="table-number"><?php echo $table['table_id']; ?></div> 
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Table Information Panel -->
                        <div id="tableInfo" class="bg-white rounded-3 border p-4" style="display: none;">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="fw-semibold mb-2" id="tableTitle">Table Information</h6>
                                    <p class="text-muted mb-2" id="tableDescription"></p>
                                    <div class="d-flex gap-3 small">
                                        <span><i class="bi bi-people text-primary me-1"></i><span id="tableCapacity"></span></span>
                                        <span><i class="bi bi-geo-alt text-primary me-1"></i><span id="tableLocation"></span></span>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="badge bg-success fs-6 px-3 py-2">Available</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Reservation Details -->
                    <div id="detailsStep" class="step-content d-none">
                        <h4 class="fw-semibold mb-4 d-flex align-items-center">
                            <i class="bi bi-person-lines-fill me-2 text-primary"></i>
                            Reservation Details
                        </h4>
                        
                        <!-- Booking Summary -->
                        <div class="bg-light rounded-3 p-4 mb-4">
                            <h6 class="fw-semibold mb-3">Booking Summary</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-geo-alt text-primary me-2"></i>
                                        <span id="summaryLocation"></span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-calendar3 text-primary me-2"></i>
                                        <span id="summaryDate"></span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-clock text-primary me-2"></i>
                                        <span id="summaryTime"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-people text-primary me-2"></i>
                                        <span id="summaryPartySize"></span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-grid-3x3-gap text-primary me-2"></i>
                                        <span id="summaryTable"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <form id="reservationForm" action="process_reservation.php" method="POST">
                            <input type="hidden" name="location_id" id="formLocationId">
                            <input type="hidden" name="date" id="formDate">
                            <input type="hidden" name="time" id="formTime">
                            <input type="hidden" name="party_size" id="formPartySize">
                            <input type="hidden" name="table_id" id="formTableId">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">First Name *</label>
                                    <input type="text" class="form-control" name="first_name" id="firstName" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Last Name *</label>
                                    <input type="text" class="form-control" name="last_name" id="lastName" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Email *</label>
                                    <input type="email" class="form-control" name="email" id="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Phone Number *</label>
                                    <input type="tel" class="form-control" name="phone" id="phone" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium">Special Requests</label>
                                    <textarea class="form-control" name="special_requests" id="specialRequests" rows="3" placeholder="Any dietary restrictions, special occasions, or seating preferences..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Navigation Buttons -->  
                    <div class="d-flex justify-content-between mt-5">
                        <div>
                            <!-- Back to Home Button (only shown on Step 1) -->
                            <button href="index.php" class="btn btn-outline-secondary" id="homeBtn" style=" display: none;">
                                <i class="bi bi-house-door me-2"></i>Back to Home
                            </button>
                            
                            <!-- Back Button (shown on Steps 2-5) -->
                            <button class="btn btn-outline-secondary" id="backBtn" onclick="previousStep()" style="display: none;">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </button>
                        </div>
                        
                        <div class="ms-auto">
                            <button class="btn btn-primary" id="nextBtn" onclick="nextStep()">
                                Next <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                            <button class="btn btn-success" id="confirmBtn" onclick="confirmReservation()" style="display: none;">
                                <i class="bi bi-check-circle me-2"></i>Confirm Reservation
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center p-5">
                    <div class="text-success mb-4">
                        <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Reservation Confirmed!</h4>
                    <p class="text-muted mb-4">Your table has been reserved. You'll receive a confirmation email shortly.</p>
                    <div class="bg-light rounded-3 p-3 mb-4">
                        <div class="small text-muted mb-1">Confirmation Number</div>
                        <div class="fw-bold" id="confirmationNumber"></div>
                    </div>
                    <button class="btn btn-primary" onclick="location.reload()">Make Another Reservation</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/reservation.js"></script>
    
    <script>
        // Helper functions that need to be available globally
        function formatDate(date, format = 'long') {
            if (typeof date === 'string') date = new Date(date);
            return format === 'short' 
                ? date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
                : date.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
        }

        function formatTime(time) {
            const [hours, minutes] = time.split(':');
            const hourNum = parseInt(hours, 10);
            const period = hourNum >= 12 ? 'PM' : 'AM';
            const displayHour = hourNum % 12 || 12;
            return `${displayHour}:${minutes} ${period}`;
        }

        // Pass PHP data to JavaScript
        const locationData = <?php 
            $locationData = [];
            foreach ($locations as $loc) {
                $locationData[$loc['location_id']] = $loc;
            }
            echo json_encode($locationData); 
        ?>;
        const tableData = <?php echo json_encode($tableData); ?>;
        const selectedLocation = '<?php echo $selectedLocation; ?>';
        const selectedDate = '<?php echo $selectedDate; ?>';
        const selectedTime = '<?php echo $selectedTime; ?>';
        const selectedTable = '<?php echo isset($_GET['table_id']) ? $_GET['table_id'] : ''; ?>';
        
        // Initialize the form with selected values if they exist
        if (selectedLocation && locationData[selectedLocation]) {
            updateLocationDisplay(locationData[selectedLocation]);
        }
        
        if (selectedDate) {
            updateDateDisplay(selectedDate);
        }
        
        if (selectedTime) {
            updateTimeDisplay(selectedTime);
        }
        
        if (selectedTable && tableData[selectedTable]) {
            updateTableDisplay(tableData[selectedTable]);
        }
        
        // Set party size
        const partySize = <?php echo $partySize; ?>;
        document.getElementById('partySize').value = partySize;
        document.getElementById('formPartySize').value = partySize;
        document.getElementById('summaryPartySize').textContent = partySize + ' ' + (partySize === 1 ? 'Person' : 'People');

        // Helper functions to update displays
        function updateLocationDisplay(location) {
            document.getElementById('selectedLocationDisplay').textContent = location.name;
            document.getElementById('selectedLocationDisplay2').textContent = location.name;
            document.getElementById('selectedLocationDisplay3').textContent = location.name;
            document.getElementById('summaryLocation').textContent = location.name;
            document.getElementById('formLocationId').value = location.location_id;
        }

        function updateDateDisplay(date) {
            document.getElementById('selectedDateDisplay').textContent = formatDate(new Date(date));
            document.getElementById('selectedDateDisplay2').textContent = formatDate(new Date(date));
            document.getElementById('summaryDate').textContent = formatDate(new Date(date));
            document.getElementById('formDate').value = date;
        }

        function updateTimeDisplay(time) {
            document.getElementById('selectedTimeDisplay').textContent = formatTime(time);
            document.getElementById('summaryTime').textContent = formatTime(time);
            document.getElementById('formTime').value = time;
        }

        function updateTableDisplay(table) {
            document.getElementById('summaryTable').textContent = table.name;
            document.getElementById('formTableId').value = table.table_id;
        }
    </script>
</body>
</html>