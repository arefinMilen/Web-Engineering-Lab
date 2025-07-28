<?php
// Include your DB connection
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Collect form data
  $title = $_POST['title'];
  $author = $_POST['author'];
  $description = $_POST['description'];

  // Insert query
  $query = "INSERT INTO books (title, author, description) VALUES ('$title', '$author', '$description')";

  // Execute query
  if (mysqli_query($connection, $query)) {
    echo "✅ Book submitted successfully!";
  } else {
    echo "❌ Error: " . mysqli_error($connection);
  }
}
?>

<!-- HTML Form part -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Submission Form</title>
</head>
<body>
  <h2>📚 Submit a Book</h2>
  <form action="basic.php" method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Author:</label><br>
    <input type="text" name="author" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="4" cols="40" required></textarea><br><br>

    <button type="submit">Submit Book</button>
  </form>
</body>
</html>
