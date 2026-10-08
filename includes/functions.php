<?php
/**
 * Common helper functions used across the site.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sanitize user input
function clean($str) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($str));
}

// Check if a customer is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check if the logged-in user is an admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit();
}

// Force login before accessing a page
function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

// Force admin login before accessing admin pages
function require_admin() {
    if (!is_logged_in() || !is_admin()) {
        redirect('login.php');
    }
}

// Flash message helpers (simple session-based alerts)
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function show_flash() {
    $flash = get_flash();
    if ($flash) {
        $type = $flash['type'] === 'error' ? 'danger' : $flash['type'];
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
            . htmlspecialchars($flash['message'])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

// Format currency (Nepali Rupees)
function format_price($amount) {
    return 'Rs. ' . number_format($amount, 2);
}

// Calculate number of nights between two dates
function nights_between($checkin, $checkout) {
    $d1 = new DateTime($checkin);
    $d2 = new DateTime($checkout);
    $diff = $d1->diff($d2)->days;
    return $diff > 0 ? $diff : 1;
}

/**
 * Check whether a given room is available for the requested date range.
 * A room is unavailable if any existing (non-cancelled) booking overlaps
 * the requested check-in/check-out window.
 */
function is_room_available($conn, $room_id, $check_in, $check_out, $exclude_booking_id = null) {
    $room_id = (int) $room_id;
    $check_in = clean($check_in);
    $check_out = clean($check_out);

    $sql = "SELECT booking_id FROM bookings
            WHERE room_id = $room_id
            AND status != 'cancelled'
            AND check_in < '$check_out'
            AND check_out > '$check_in'";

    if ($exclude_booking_id) {
        $sql .= " AND booking_id != " . (int) $exclude_booking_id;
    }

    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) === 0;
}
