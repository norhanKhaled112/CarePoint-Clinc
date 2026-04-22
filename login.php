<?php
// ===== إعدادات الـ Response =====
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once 'DB.php';

// استقبال البيانات
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$role     = isset($data['role'])     ? trim($data['role'])     : '';
$email    = isset($data['email'])    ? trim($data['email'])    : '';
$password = isset($data['password']) ? $data['password']       : '';

// ===== Validation =====
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'uncorrect email address']);
    exit;
}

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'password is required']);
    exit;
}

// ===== البحث في الجدول المناسب =====
$table = ($role === 'Doctor') ? 'doctors' : 'patients';

$stmt = $conn->prepare("SELECT id, full_name, email, password FROM $table WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'uncorrect email ']);
    $stmt->close();
    exit;
}

$user = $result->fetch_assoc();

// ===== التحقق من كلمة المرور =====
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'uncorrect  password']);
    $stmt->close();
    exit;
}

// ===== حفظ الـ Session =====
$_SESSION['user_id']   = $user['id'];
$_SESSION['user_name'] = $user['full_name'];
$_SESSION['user_email']= $user['email'];
$_SESSION['user_role'] = $role;

echo json_encode([
    'success'   => true,
    'message'   => 'Login successful!',
    'user' => [
        'id'    => $user['id'],
        'name'  => $user['full_name'],
        'email' => $user['email'],
        'role'  => $role
    ]
]);

$stmt->close();
$conn->close();
?>