<?php
// coonect database
$conn = new mysqli("localhost", "root", "", "crud");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);