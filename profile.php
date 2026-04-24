<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: registerform.php');
    exit;
}

require_once 'db.php';

$user_id   = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// تغيير status (للدكتور بس)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appt_id']) && $user_role === 'Doctor') {
    $appt_id    = (int)$_POST['appt_id'];
    $new_status = $_POST['new_status'];
    if (in_array($new_status, ['confirmed', 'cancelled', 'pending'])) {
        $upd = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
        $upd->bind_param('sii', $new_status, $appt_id, $user_id);
        $upd->execute();
        $upd->close();
    }
    header('Location: profile.php');
    exit;
}

// جيب بيانات المستخدم
if ($user_role === 'Doctor') {
    $stmt = $conn->prepare("SELECT full_name, phone, email, department, created_at FROM doctors WHERE id = ?");
} else {
    $stmt = $conn->prepare("SELECT full_name, phone, email, created_at FROM patients WHERE id = ?");
}
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// جيب المواعيد
$appointments = [];
if ($user_role === 'Doctor') {
    $appt = $conn->prepare("
        SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.notes,
               p.full_name AS other_name, p.phone AS other_phone
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
} else {
    $appt = $conn->prepare("
        SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.notes,
               d.full_name AS other_name, d.department AS other_phone
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
}
$appt->bind_param('i', $user_id);
$appt->execute();
$appointments = $appt->get_result()->fetch_all(MYSQLI_ASSOC);
$appt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - CarePoint</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #3a4a47;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            padding: 30px 20px;
        }
        .wrapper { width: 100%; max-width: 750px; }
        .profile-card {
            background: white; border-radius: 20px; padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            text-align: center; margin-bottom: 25px;
        }
        .avatar {
            width: 100px; height: 100px; border-radius: 50%;
            background: linear-gradient(135deg, #005461, #3a4a47);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 40px; color: white;
        }
        .profile-name { font-size: 22px; font-weight: 700; color: #005461; margin-bottom: 5px; }
        .profile-role {
            display: inline-block; background: #e6f4f6; color: #005461;
            padding: 4px 14px; border-radius: 20px; font-size: 13px;
            font-weight: 500; margin-bottom: 30px;
        }
        .info-list { text-align: left; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .info-item { display: flex; align-items: center; gap: 15px; padding: 12px 0; border-bottom: 1px solid #f7fafc; }
        .info-icon {
            width: 38px; height: 38px; border-radius: 10px; background: #e6f4f6;
            display: flex; align-items: center; justify-content: center;
            color: #005461; font-size: 15px; flex-shrink: 0;
        }
        .info-label { font-size: 11px; color: #a0aec0; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 14px; color: #3a4a47; font-weight: 500; }
        .logout-btn {
            display: block; width: 100%; margin-top: 25px; padding: 12px;
            background: #005461; color: white; border: none; border-radius: 10px;
            font-size: 14px; font-weight: 600; cursor: pointer;
            text-decoration: none; transition: background 0.2s;
        }
        .logout-btn:hover { background: #3a4a47; }

        /* Appointments */
        .appointments-card {
            background: white; border-radius: 20px; padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .appointments-card h3 {
            color: #005461; font-size: 18px; font-weight: 700;
            margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
        }
        .appt-item {
            border: 1.5px solid #e2e8f0; border-radius: 12px;
            padding: 15px 18px; margin-bottom: 12px;
            display: flex; justify-content: space-between;
            align-items: center; flex-wrap: wrap; gap: 10px;
        }
        .appt-name  { font-weight: 600; color: #005461; font-size: 15px; }
        .appt-sub   { font-size: 13px; color: #718096; margin-top: 3px; }
        .appt-notes { font-size: 12px; color: #a0aec0; margin-top: 4px; font-style: italic; }
        .appt-date  { font-size: 14px; font-weight: 600; color: #3a4a47; }
        .appt-hour  { font-size: 13px; color: #718096; margin-top: 3px; }
        .status {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 12px; font-weight: 500; margin-top: 5px;
        }
        .status.pending   { background: #fef3cd; color: #856404; }
        .status.confirmed { background: #d1fae5; color: #065f46; }
        .status.cancelled { background: #fee2e2; color: #991b1b; }
        .no-appts { text-align: center; color: #a0aec0; padding: 20px; font-size: 14px; }

        /* Status buttons */
        .status-form { display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap; }
        .status-form button {
            padding: 4px 10px; border: none; border-radius: 8px;
            font-size: 12px; font-weight: 500; cursor: pointer;
            font-family: 'Poppins', sans-serif; transition: opacity 0.2s;
        }
        .status-form button:hover { opacity: 0.8; }
        .btn-confirm  { background: #d1fae5; color: #065f46; }
        .btn-cancel   { background: #fee2e2; color: #991b1b; }
        .btn-pending  { background: #fef3cd; color: #856404; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Profile Info -->
    <div class="profile-card">
        <div class="avatar">
            <i class="fa-solid <?= $user_role === 'Doctor' ? 'fa-user-doctor' : 'fa-user' ?>"></i>
        </div>
        <div class="profile-name"><?= htmlspecialchars($user['full_name']) ?></div>
        <span class="profile-role"><?= $user_role ?></span>

        <div class="info-list">
            <div class="info-item">
                <div class="info-icon"><i class="fa-regular fa-envelope"></i></div>
                <div>
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                <div>
                    <div class="info-label">Phone</div>
                    <div class="info-value"><?= htmlspecialchars($user['phone']) ?></div>
                </div>
            </div>
            <?php if ($user_role === 'Doctor'): ?>
            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-stethoscope"></i></div>
                <div>
                    <div class="info-label">Department</div>
                    <div class="info-value"><?= htmlspecialchars($user['department']) ?></div>
                </div>
            </div>
            <?php endif; ?>
            <div class="info-item">
                <div class="info-icon"><i class="fa-regular fa-calendar"></i></div>
                <div>
                    <div class="info-label">Member Since</div>
                    <div class="info-value"><?= date('d M Y', strtotime($user['created_at'])) ?></div>
                </div>
            </div>
        </div>

        <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>

    <!-- Appointments -->
    <div class="appointments-card">
        <h3>
            <i class="fa-solid fa-calendar-check"></i>
            <?= $user_role === 'Doctor' ? 'Patient Appointments' : 'My Appointments' ?>
        </h3>

        <?php if (empty($appointments)): ?>
            <div class="no-appts"><i class="fa-regular fa-calendar-xmark"></i> No appointments yet.</div>
        <?php else: ?>
            <?php foreach ($appointments as $a): ?>
            <div class="appt-item">
                <div>
                    <div class="appt-name">
                        <i class="fa-solid <?= $user_role === 'Doctor' ? 'fa-user' : 'fa-user-doctor' ?>"></i>
                        <?= htmlspecialchars($a['other_name']) ?>
                    </div>
                    <div class="appt-sub"><?= htmlspecialchars($a['other_phone']) ?></div>
                    <?php if ($a['notes']): ?>
                    <div class="appt-notes">"<?= htmlspecialchars($a['notes']) ?>"</div>
                    <?php endif; ?>

                    <!-- زراير تغيير الـ status للدكتور بس -->
                    <?php if ($user_role === 'Doctor' &&  $a['status'] === 'pending'): ?>
                    <form method="POST" class="status-form">
                        <input type="hidden" name="appt_id"    value="<?= $a['id'] ?>">
                        <input type="hidden" name="new_status" value="confirmed">
                        <button type="submit" class="btn-confirm"><i class="fa-solid fa-check"></i> Confirm</button>
                    </form>
                    <form method="POST" class="status-form">
                        <input type="hidden" name="appt_id"    value="<?= $a['id'] ?>">
                        <input type="hidden" name="new_status" value="cancelled">
                        <button type="submit" class="btn-cancel"><i class="fa-solid fa-xmark"></i> Cancel</button>
                    </form>
                    <?php endif; ?>
                </div>

                <div style="text-align:right">
                    <div class="appt-date"><?= date('d M Y', strtotime($a['appointment_date'])) ?></div>
                    <div class="appt-hour"><?= date('h:i A', strtotime($a['appointment_time'])) ?></div>
                    <span class="status <?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
