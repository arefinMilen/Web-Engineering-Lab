<?php 
require_once 'config.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Handle booking actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $event_id = $_POST['event_id'];
    
    try {
        if ($action === 'book') {
            // Check if already booked
            $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? AND event_id = ?");
            $stmt->execute([$user_id, $event_id]);
            if ($stmt->fetch()) {
                $message = 'You have already booked/joined waitlist for this event!';
                $message_type = 'error';
            } else {
                // Check ticket availability
                $stmt = $pdo->prepare("SELECT available_tickets FROM events WHERE id = ?");
                $stmt->execute([$event_id]);
                $event = $stmt->fetch();
                
                if ($event['available_tickets'] > 0) {
                    // Book ticket
                    $pdo->beginTransaction();
                    
                    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, event_id, status) VALUES (?, ?, 'confirmed')");
                    $stmt->execute([$user_id, $event_id]);
                    
                    $stmt = $pdo->prepare("UPDATE events SET available_tickets = available_tickets - 1 WHERE id = ?");
                    $stmt->execute([$event_id]);
                    
                    $pdo->commit();
                    $message = 'Ticket booked successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'No tickets available for this event.';
                    $message_type = 'error';
                }
            }
        } elseif ($action === 'waitlist') {
            // Check if already in system
            $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? AND event_id = ?");
            $stmt->execute([$user_id, $event_id]);
            if ($stmt->fetch()) {
                $message = 'You are already in the system for this event!';
                $message_type = 'error';
            } else {
                $stmt = $pdo->prepare("INSERT INTO bookings (user_id, event_id, status) VALUES (?, ?, 'waiting')");
                $stmt->execute([$user_id, $event_id]);
                
                $message = 'Added to waiting list successfully!';
                $message_type = 'success';
            }
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = 'Action failed. Please try again.';
        $message_type = 'error';
    }
}

// Get all events
$stmt = $pdo->query("SELECT * FROM events ORDER BY date ASC");
$events = $stmt->fetchAll();

// Get user bookings
$stmt = $pdo->prepare("
    SELECT b.*, e.name, e.date, e.category 
    FROM bookings b 
    JOIN events e ON b.event_id = e.id 
    WHERE b.user_id = ? 
    ORDER BY e.date ASC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Campus Fest 2025</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>🎉 Campus Fest 2025 - Student Portal</h1>
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

        <!-- Available Events -->
        <div class="card">
            <h2>📅 Available Events</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Tickets Available</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <?php
                            // Check if user already booked/in waitlist
                            $stmt = $pdo->prepare("SELECT status FROM bookings WHERE user_id = ? AND event_id = ?");
                            $stmt->execute([$user_id, $event['id']]);
                            $user_booking = $stmt->fetch();
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($event['name']); ?></strong></td>
                                <td><?php echo $event['category']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($event['date'])); ?></td>
                                <td><?php echo $event['available_tickets']; ?></td>
                                <td>
                                    <?php if ($event['available_tickets'] > 0): ?>
                                        <span class="status-available">●</span> Available
                                    <?php else: ?>
                                        <span class="status-sold-out">●</span> Sold Out
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user_booking): ?>
                                        <?php if ($user_booking['status'] === 'confirmed'): ?>
                                            <span class="btn btn-success btn-sm">✓ Booked</span>
                                        <?php else: ?>
                                            <span class="btn btn-warning btn-sm">⏳ In Waitlist</span>
                                        <?php endif; ?>
                                    <?php elseif ($event['available_tickets'] > 0): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="book">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Book Now</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="waitlist">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <button type="submit" class="btn btn-warning btn-sm">Join Waitlist</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- My Bookings -->
        <div class="card">
            <h2>🎫 My Bookings</h2>
            <?php if (count($bookings) > 0): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($booking['name']); ?></strong></td>
                                    <td><?php echo $booking['category']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($booking['date'])); ?></td>
                                    <td>
                                        <?php if ($booking['status'] === 'confirmed'): ?>
                                            <span class="btn btn-success btn-sm">✓ Confirmed</span>
                                        <?php else: ?>
                                            <span class="btn btn-warning btn-sm">⏳ Waiting List</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">No bookings yet. Book some events above!</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>