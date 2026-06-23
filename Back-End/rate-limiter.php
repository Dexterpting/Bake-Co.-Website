<?php
/**
 * Checks and records rate limit attempts for a given IP and action.
 *
 * @param mysqli $conn          Active database connection
 * @param string $action        Unique label e.g. 'submit_order', 'admin_login'
 * @param int    $maxAttempts   Max allowed attempts within the window
 * @param int    $windowSeconds Time window in seconds
 * @return array ['allowed' => bool, 'retry_after' => int seconds, 'remaining' => int]
 */
function checkRateLimit($conn, $action, $maxAttempts = 5, $windowSeconds = 600) {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? 'unknown';

    // Only keep the first IP if forwarded list has multiple
    $ip = trim(explode(',', $ip)[0]);
    $ip = $conn->real_escape_string($ip);
    $action = $conn->real_escape_string($action);

    // Clean up old entries for this ip+action (keeps table small)
    $conn->query("DELETE FROM rate_limits WHERE ip_address = '$ip' AND action = '$action' AND attempt_time < DATE_SUB(NOW(), INTERVAL $windowSeconds SECOND)");

    // Count attempts within the window
    $result = $conn->query("SELECT COUNT(*) as count, MIN(attempt_time) as oldest FROM rate_limits WHERE ip_address = '$ip' AND action = '$action' AND attempt_time >= DATE_SUB(NOW(), INTERVAL $windowSeconds SECOND)");
    $row = $result->fetch_assoc();
    $count = (int) $row['count'];

    if ($count >= $maxAttempts) {
        $oldestTime = strtotime($row['oldest']);
        $retryAfter = max(0, ($oldestTime + $windowSeconds) - time());
        return [
            'allowed'     => false,
            'retry_after' => $retryAfter,
            'remaining'   => 0
        ];
    }

    // Record this attempt
    $conn->query("INSERT INTO rate_limits (ip_address, action) VALUES ('$ip', '$action')");

    return [
        'allowed'     => true,
        'retry_after' => 0,
        'remaining'   => $maxAttempts - $count - 1
    ];
}
?>