<?php
include 'db.php'; // Your connection file

// Delete logic
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM books WHERE id=$id");
    header("Location: view_book.php");
    exit();
}

// Fetch all books
$books = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4 text-center text-blue-700">📚 Book List</h2>

        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-blue-100 text-gray-700 text-left">
                    <th class="border p-3">ID</th>
                    <th class="border p-3">Title</th>
                    <th class="border p-3">Author</th>
                    <th class="border p-3">Genre</th>
                    <th class="border p-3">Description</th>
                    <th class="border p-3">Best Selling</th>
                    <th class="border p-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($book = $books->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-100">
                        <td class="border p-3"><?= $book['id'] ?></td>
                        <td class="border p-3"><?= htmlspecialchars($book['title']) ?></td>
                        <td class="border p-3"><?= htmlspecialchars($book['author']) ?></td>
                        <td class="border p-3"><?= htmlspecialchars($book['genre']) ?></td>
                        <td class="border p-3"><?= htmlspecialchars($book['description']) ?></td>
                        <td class="border p-3 text-center"><?= $book['best_selling'] ? '✅' : '❌' ?></td>
                        <td class="border p-3 space-x-2">
                            <a href="update_book.php?id=<?= $book['id'] ?>" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Update</a>
                            <a href="view_book.php?delete=<?= $book['id'] ?>" onclick="return confirm('Are you sure you want to delete this book?');"
                                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>