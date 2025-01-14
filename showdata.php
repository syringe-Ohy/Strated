<?php

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "bookborrow"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT Author_name, Book_title, ISBN, count, catagory FROM books"; 
$result = $conn->query($sql);


echo "<div class='table-box'>";
echo "<div class='table-container'>";
echo "<table>";
echo "<thead><tr><th>Author</th><th>Title</th><th>ISBN</th><th>Count</th><th>Category</th></tr></thead>";
echo "<tbody>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Author_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Book_title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ISBN']) . "</td>";
        echo "<td>" . htmlspecialchars($row['count']) . "</td>";
        echo "<td>" . htmlspecialchars($row['catagory']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No data found</td></tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>";
echo "</div>";


$conn->close();
?>
