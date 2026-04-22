<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: registerform.php');
    exit;
}

$doctor_name  = isset($_GET['doctor'])     ? htmlspecialchars($_GET['doctor'])     : 'Unknown';
$department   = isset($_GET['department']) ? htmlspecialchars($_GET['department']) : 'Unknown';
$patient_name = $_SESSION['user_name'];

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.php';

    // $patient_id = $_SESSION['user_id'];
    $patient_id=1;
    $doc_name   = $_POST['doctor_name'];
    $date       = $_POST['appointment_date'];
    $time       = $_POST['appointment_time'];
    $notes      = trim($_POST['notes']);

    $stmt = $conn->prepare("SELECT id FROM doctors WHERE full_name LIKE ?");
    $like = '%' . $doc_name . '%';
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();
    $stmt->close();

    if (!$doctor) {
        $error = 'error';
    } elseif (empty($date) || empty($time)) {
        $error = 'يرجى اختيار التاريخ والوقت.';
    } else {
        $doctor_id = $doctor['id'];

        $check = $conn->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?");
        $check->bind_param('iss', $doctor_id, $date, $time);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = 'The appointment is already booked, please choose another time.';
        } else {
            $insert = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, notes) VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param('iisss', $patient_id, $doctor_id, $date, $time, $notes);
            if ($insert->execute()) {
                $success = 'Appointment booked successfully!';
            } else {
                $error = 'An error occurred while booking the appointment.';
            }
            $insert->close();
        }
        $check->close();
        $conn->close();
    }
}

$min_date = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - CarePoint</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #eeeae4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .book-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .card-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .card-header h2 { color: #005461; font-size: 22px; font-weight: 700; }
        .card-header p  { color: #718096; font-size: 14px; margin-top: 5px; }
        .doctor-info {
            background: #e6f4f6;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .doc-icon {
            width: 45px; height: 45px;
            background: #005461;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 18px; flex-shrink: 0;
        }
        .doctor-info h4 { color: #005461; font-size: 15px; font-weight: 600; }
        .doctor-info span { color: #3a4a47; font-size: 13px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 500; color: #3a4a47; margin-bottom: 7px; }
        input, select, textarea {
            width: 100%; padding: 11px 15px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-family: 'Poppins', sans-serif; font-size: 14px;
            color: #2d3748; outline: none; transition: border 0.2s;
        }
        input:focus, select:focus, textarea:focus { border-color: #005461; }
        textarea { resize: none; height: 90px; }
        .submit-btn {
            width: 100%; padding: 13px;
            background: #005461; color: white; border: none;
            border-radius: 10px; font-size: 15px; font-weight: 600;
            cursor: pointer; font-family: 'Poppins', sans-serif;
            transition: background 0.2s; margin-top: 5px;
        }
        .submit-btn:hover { background: #3a4a47; }
        .alert { padding: 12px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; text-align: center; }
        .alert.success { background: #e6f4f6; color: #005461; }
        .alert.error   { background: #fff5f5; color: #c53030; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #718096; font-size: 13px; text-decoration: none; }
        .back-link:hover { color: #005461; }
    </style>
</head>
<body>
<div class="book-card">
    <div class="card-header">
        <h2><i class="fa-solid fa-calendar-check"></i> Book Appointment</h2>
        <p>Hello, <?= htmlspecialchars($patient_name) ?> </p>
    </div>

    <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <div class="doctor-info">
        <div class="doc-icon"><i class="fa-solid fa-user-doctor"></i></div>
        <div>
            <h4>Dr. <?= $doctor_name ?></h4>
            <span><?= $department ?></span>
        </div>
    </div>

    <form method="POST">
        <input type="hidden" name="doctor_name" value="<?= $doctor_name ?>">
        <input type="hidden" name="department"  value="<?= $department ?>">

        <div class="form-group">
            <label><i class="fa-regular fa-calendar"></i> Appointment Date</label>
            <input type="date" name="appointment_date" min="<?= $min_date ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fa-regular fa-clock"></i> Appointment Time</label>
            <select name="appointment_time" required>
                <option value="" disabled selected>Select Time</option>>
                <option value="14:00:00">2:00 PM</option>
                <option value="15:00:00">3:00 PM</option>
                <option value="16:00:00">4:00 PM</option>
                <option value="17:00:00">5:30 PM</option>
                <option value="18:00:00">6:00 PM</option>
            </select>
        </div>

        <div class="form-group">
            <label><i class="fa-regular fa-note-sticky"></i> Notes (Optional)</label>
            <textarea name="notes" placeholder="Enter any additional notes..."></textarea>
        </div>

        <button type="submit" class="submit-btn">
            <i class="fa-solid fa-calendar-check"></i> Confirm Appointment
        </button>
    </form>

    <a href="javascript:history.back()" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
</div>
</body>
</html>