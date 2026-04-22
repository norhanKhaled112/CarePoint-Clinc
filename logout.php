<?php
session_start();
session_destroy();
header('location: index.html');
echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>