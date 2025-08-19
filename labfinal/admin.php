<?php 
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$message = '';
$message_type = '';

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    try {
        if ($action === 'add_event') {
            $name = $_POST['name'];
            $category = $_POST['category'];
            $date = $_POST['date'];
            $total_tickets = $_POST['total_tickets'];
            
            $stmt = $pdo->prepare("INSERT INTO events (name, category, date, total_tickets, available_tickets) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category, $date, $total_tickets, $total_tickets]);
            
            $message = 'Event added successfully!';
            $message_type = 'success';
        }
        elseif ($action === 'update_tickets') {
            $event_id = $_POST['event_id'];
            $new_total = $_POST['total_tickets'];
            
            $pdo->beginTransaction();
            
            // Get current booking count
            $stmt = $pdo->prepare("SELECT COUNT(*) as booked FROM bookings WHERE event_id = ? AND status = 'confirmed'");
            $stmt->execute([$event_id]);
            $booked = $stmt->fetch()['booked'];
            
            $available = max(0, $new_total - $booked);
            
            // Update event
            $stmt = $pdo->prepare("UPDATE events SET total_tickets = ?, available_tickets = ? WHERE id = ?");
            $stmt->execute([$new_total, $available, $event_id]);
            
            // If more tickets available, promote from waiting list
            if ($available > 0) {
                $stmt = $pdo->prepare("
                    SELECT b.id FROM bookings b 
                    WHERE b.event_id = ? AND b.status = 'waiting' 
                    ORDER BY b.created_at ASC 
                    LIMIT ?
                ");
                $stmt->execute([$event_id, $available]);
                $waiting_bookings = $stmt->fetchAll();
                
                foreach ($waiting_bookings as $booking) {
                    $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
                    $stmt->execute([$booking['id']]);
                    
                    $stmt = $pdo->prepare("UPDATE events SET available_tickets = available_tickets - 1 WHERE id = ?");
                    $stmt->execute([$event_id]);
                }
            }
            
            $pdo->commit();
            $message = 'Tickets updated successfully!';
            $message_type = 'success';
        }
        elseif ($action === 'approve_waitlist') {
            $booking_id = $_POST['booking_id'];
            
            $pdo->beginTransaction();
            
            // Get booking and event info
            $stmt = $pdo->prepare("
                SELECT b.*, e.available_tickets 
                FROM bookings b 
                JOIN events e ON b.event_id = e.id 
                WHERE b.id = ?
            ");
            $stmt->execute([$booking_id]);
            $booking = $stmt->fetch();
            
            if ($booking['available_tickets'] > 0) {
                // Approve booking
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
                $stmt->execute([$booking_id]);
                
                // Decrease available tickets
                $stmt = $pdo->prepare("UPDATE events SET available_tickets = available_tickets - 1 WHERE id = ?");
                $stmt->execute([$booking['event_id']]);
                
                $pdo->commit();
                $message = 'Waitlist approved successfully!';
                $message_type = 'success';
            } else {
                $message = 'No tickets available to approve waitlist.';
                $message_type = 'error';
            }
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = 'Action failed. Please try again.';
        $message_type = 'error';
    }
}

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_events FROM events");
$total_events = $stmt->fetch()['total_events'];

$stmt = $pdo->query("SELECT COUNT(*) as total_students FROM users WHERE role = 'student'");
$total_students = $stmt->fetch()['total_students'];

$stmt = $pdo->query("SELECT COUNT(*) as total_bookings FROM bookings WHERE status = 'confirmed'");
$total_bookings = $stmt->fetch()['total_bookings'];

$stmt = $pdo->query("SELECT COUNT(*) as total_waitlist FROM bookings WHERE status = 'waiting'");
$total_waitlist = $stmt->fetch()['total_waitlist'];

// Get all events
$stmt = $pdo->query("SELECT * FROM events ORDER BY date ASC");
$events = $stmt->fetchAll();

// Get waiting list
$stmt = $pdo->query("
    SELECT b.*, e.name as event_name, u.full_name, u.student_id 
    FROM bookings b 
    JOIN events e ON b.event_id = e.id 
    JOIN users u ON b.user_id = u.id 
    WHERE b.status = 'waiting' 
    ORDER BY e.name, b.created_at ASC
");
$waitlist = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Campus Fest 2025</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>🎉 Campus Fest 2025 - Admin Portal</h1>
        <div class="header-right">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</span>
            <a href="logout.php" class="btn btn-sm">Logout</a>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Dashboard -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3><?php echo $total_events; ?></h3>
                <p>Total Events</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_students; ?></h3>
                <p>Registered Students</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_bookings; ?></h3>
                <p>Confirmed Bookings</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_waitlist; ?></h3>
                <p>Waiting List</p>
            </div>
        </div>

        <!-- Add New Event -->
        <div class="card">
            <h2>➕ Add New Event</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_event">
                <div class="form-row">
                    <div class="form-group">
                        <label>Event Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="Music">Music</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Gaming">Gaming</option>
                            <option value="Tech">Tech</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label>Total Tickets</label>
                        <input type="number" name="total_tickets" min="1" required>
                    </div>