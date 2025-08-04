<?php
include 'db.php';

// Fetch existing book
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $book = $conn->query("SELECT * FROM books WHERE id=$id")->fetch_assoc();
}

// Handle update submission
if (isset($_POST['update'])) {
    $id           = $_POST['id'];
    $title        = mysqli_real_escape_string($conn, $_POST['title']);
    $author       = mysqli_real_escape_string($conn, $_POST['author']);
    $genre        = mysqli_real_escape_string($conn, $_POST['genre']);
    $description  = mysqli_real_escape_string($conn, $_POST['description']);
    $best_selling = isset($_POST['best_selling']) ? 1 : 0;

    $sql = "UPDATE books SET 
                title='$title',
                author='$author',
                genre='$genre',
                description='$description',
                best_selling=$best_selling
            WHERE id=$id";

    if ($conn->query($sql)) {
        header("Location: view_book.php");
        exit();
    } else {
        echo "❌ Update Failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">

    <div class="bg-white shadow-xl p-10 rounded-lg w-full max-w-xl">
        <h2 class="text-2xl font-bold mb-6 text-blue-700 text-center">✏️ Update Book</h2>

        <form method="POST" class="space-y-5">
            <input type="hidden" name="id" value="<?= $book['id'] ?>">

            <div>
                <label class="block font-medium mb-1">Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required
                    class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block font-medium mb-1">Author</label>
                <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required
                    class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block font-medium mb-1">Genre</label>
                <select name="genre" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Select Genre --</option>
                    <?php
                    $genres = ["Fiction", "Non-fiction", "Science", "Biography", "Fantasy", "Mystery"];
                    foreach ($genres as $g) {
                        $selected = ($book['genre'] === $g) ? 'selected' : '';
                        echo "<option value='$g' $selected>$g</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Description</label>
                <textarea name="description" rows="4" required
                    class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($book['description']) ?></textarea>
            </div>

            <div class="flex items-center space-x-2">
                <input type="checkbox" name="best_selling" value="1" <?= $book['best_selling'] ? 'checked' : '' ?>
                    class="w-5 h-5 text-blue-600 border-gray-300 rounded">
                <label class="text-sm">Best Selling</label>
            </div>

            <div class="text-center pt-4">
                <button type="submit" name="update"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded">
                    ✅ Update Book
                </button>
            </div>
        </form>
    </div>

</body>

</html>