<?php 
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $full_name = $_POST['full_name'];
    
    // Validate student ID format (xxx-xx-xxxx)
    if (!preg_match('/^\d{3}-\d{2}-\d{4}$/', $student_id)) {
        $error = 'Student ID must be in format: xxx-xx-xxxx (e.g., 123-45-6789)';
    }
    // Validate email domain
    elseif (!str_ends_with($email, '@greenfield.edu')) {
        $error = 'Email must end with @greenfield.edu';
    }
    // Validate password strength
    elseif (strlen($password) < 9 || !preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password)) {
        $error = 'Password must be at least 9 characters with letters, numbers, and special characters';
    }
    else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (student_id, email, password, full_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([$student_id, $email, $hashed_password, $full_name]);
            
            $success = 'Registration successful! You can now login.';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'student_id') !== false) {
                $error = 'Student ID already registered';
            } elseif (strpos($e->getMessage(), 'email') !== false) {
                $error = 'Email already registered';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Campus Fest 2025</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>🎉 Campus Fest 2025</h1>
        <a href="index.php" class="back-btn">← Back to Home</a>
    </div>

    <div class="container">
        <div class="form-card">
            <h2>Student Registration</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Student ID *</label>
                    <input type="text" name="student_id" placeholder="123-45-6789" value="<?php echo $_POST['student_id'] ?? ''; ?>" required>
                    <small>Format: xxx-xx-xxxx</small>
                </div>

                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" placeholder="Enter your full name" value="<?php echo $_POST['full_name'] ?? ''; ?>" required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" placeholder="yourname@greenfield.edu" value="<?php echo $_POST['email'] ?? ''; ?>" required>
                    <small>Must use @greenfield.edu email</small>
                </div>

                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" placeholder="Strong password" required>
                    <small>Minimum 9 characters with letters, numbers, and special characters</small>
                </div>

                <button type="submit" class="btn btn-success btn-full">Register</button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>