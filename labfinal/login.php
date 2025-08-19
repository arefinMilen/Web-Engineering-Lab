<?php 
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            if ($user['role'] === 'admin') {
                redirect('admin.php');
            } else {
                redirect('student.php');
            }
        } else {
            $error = 'Invalid Student ID or Password';
        }
    } catch (PDOException $e) {
        $error = 'Login failed. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Campus Fest 2025</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>🎉 Campus Fest 2025</h1>
        <a href="index.php" class="back-btn">← Back to Home</a>
    </div>

    <div class="container">
        <div class="form-card">
            <h2>Student Login</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" placeholder="xxx-xx-xxxx" required>
                    <small>Format: 123-45-6789</small>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Login</button>
            </form>

            <div class="form-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <p><strong>Admin Login:</strong> Student ID: 000-00-0000, Password: admin123@</p>
            </div>
        </div>
    </div>
</body>
</html>