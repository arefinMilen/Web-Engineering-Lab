<?php
include 'db.php';

if (isset($_POST['submit'])) {
    $title        = $_POST['title'];
    $author       = $_POST['author'];
    $genre        = $_POST['genre'];
    $description  = $_POST['description'];
    $best_selling = isset($_POST['best_selling']) ? 1 : 0;

    $sql = "INSERT INTO books (title, author, genre, description, best_selling)
            VALUES ('$title', '$author', '$genre', '$description', $best_selling)";

    if ($conn->query($sql)) {
        echo "<div class='text-green-600 font-bold text-center mt-4'>Book added successfully!</div>";
    } else {
        echo "<div class='text-red-600 font-bold text-center mt-4'>Error: " . $conn->error . "</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP | CRUD</title>


    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-100 to-indigo-100 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white shadow-2xl rounded-2xl p-10 w-full max-w-xl border border-gray-200">
        <h2 class="text-3xl font-extrabold mb-8 text-center text-blue-700">📘 Add a New Book</h2>

        <form method="POST" action="add_book.php" class="space-y-6">
            <!-- Title -->
            <div class="space-y-1">
                <label class="block text-gray-700 text-sm font-semibold">Title</label>
                <input type="text" name="title" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Author -->
            <div class="space-y-1">
                <label class="block text-gray-700 text-sm font-semibold">Author</label>
                <input type="text" name="author" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Genre -->
            <div class="space-y-1">
                <label class="block text-gray-700 text-sm font-semibold">Genre</label>
                <select name="genre" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">-- Select Genre --</option>
                    <option value="Fiction">Fiction</option>
                    <option value="Non-fiction">Non-fiction</option>
                    <option value="Science">Science</option>
                    <option value="Biography">Biography</option>
                    <option value="Fantasy">Fantasy</option>
                    <option value="Mystery">Mystery</option>
                </select>
            </div>

            <!-- Description -->
            <div class="space-y-1">
                <label class="block text-gray-700 text-sm font-semibold">Description</label>
                <textarea name="description" rows="4" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <!-- Best Selling -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="best_selling" value="1"
                    class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label class="text-gray-700 text-sm font-semibold">Best Selling</label>
            </div>

            <!-- Submit Button -->
            <div class="text-center pt-4">
                <button type="submit" name="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg shadow-md transition duration-300">
                    ➕ Add Book
                </button>
            </div>
        </form>
    </div>

</body>

</html>;