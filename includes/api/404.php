<?php
header('HTTP/1.0 404 Not Found');
header('Content-Type: application/json');
echo json_encode([
    'error' => true,
    'message' => 'API endpoint not found'
]);
exit;
?>