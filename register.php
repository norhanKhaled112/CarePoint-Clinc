<?php
// ===== إعدادات الـ Response =====
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// قبول POST فقط
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once 'DB.php';

// استقبال البيانات من الـ fetch request
$data = json_decode(file_get_contents('php://input'), true);

// إذا مش JSON، جرب $_POST
if (!$data) {
    $data = $_POST;
}

// ===== تنظيف البيانات =====
$role       = isset($data['role'])       ? trim($data['role'])       : '';
$full_name  = isset($data['full_name'])  ? trim($data['full_name'])  : '';
$phone      = isset($data['phone'])      ? trim($data['phone'])      : '';
$email      = isset($data['email'])      ? trim($data['email'])      : '';
$password   = isset($data['password'])   ? $data['password']         : '';
$confirm    = isset($data['confirm'])    ? $data['confirm']          : '';
$department = isset($data['department']) ? trim($data['department']) : '';

// ===== Validation =====
if (empty($full_name)) {
    echo json_encode(['success' => false, 'message' =>'full name is required']);
    exit;    
}

if (!preg_match('/^[0-9+\-\s]{7,15}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'uncorrect phone number']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'uncorrect email address']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'password must be at least 6 characters']);
    exit;
}

if ($password !== $confirm) {
    echo json_encode(['success' => false, 'message' => ' passwords do not match']);
    exit;
}

if ($role === 'Doctor' && empty($department)) {
    echo json_encode(['success' => false, 'message' => 'please select a department']);
    exit;
}

// ===== تشفير كلمة المرور =====
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// ===== التحقق من عدم تكرار الإيميل =====
$table = ($role === 'Doctor') ? 'doctors' : 'patients';

$check = $conn->prepare("SELECT id FROM $table WHERE email = ?");
$check->bind_param('s', $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'this email is already registered']);
    $check->close();
    exit;
}
$check->close();

// ===== إدخال البيانات =====
if ($role === 'Doctor') {
    $stmt = $conn->prepare(
        "INSERT INTO doctors (full_name, phone, email, department, password) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('sssss', $full_name, $phone, $email, $department, $hashed_password);
} else {
    $stmt = $conn->prepare(
        "INSERT INTO patients (full_name, phone, email, password) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('ssss', $full_name, $phone, $email, $hashed_password);
}

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! You can now login.'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'An error occurred while registering: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>