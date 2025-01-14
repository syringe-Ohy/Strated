<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "bookborrow";

    $author = trim($_POST["author"]);
    $title = trim($_POST["title"]);
    $isbn = trim($_POST["isbn"]);
    $totalCount = intval($_POST["totalCount"]);
    $category = trim($_POST["category"]);

    $errors = [];

    if (!preg_match("/^[a-zA-Z\s]+$/", $author)) {
        $errors[] = "Author name must contain only alphabets and spaces.";
    }
    if (!preg_match("/^\d{13}$/", $isbn)) {
        $errors[] = "ISBN must be exactly 13 digits.";
    }
    if ($totalCount <= 0) {
        $errors[] = "Total count must be a positive number.";
    }

    if (empty($category)) {
        $errors[] = "Please select a category.";
    }

    if (empty($errors)) {
        $conn = new mysqli($servername, $username, $password, $dbname);

     
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        
        $checkISBN = "SELECT * FROM books WHERE ISBN = ?";
        $stmt = $conn->prepare($checkISBN);
        if ($stmt) {
            $stmt->bind_param("s", $isbn);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $errors[] = "The ISBN number is already entered in the database.";
            }
            $stmt->close(); 
        } else {
            echo "<h3>Error preparing duplicate check: " . $conn->error . "</h3>";
        }

        if (empty($errors)) {
            $insertSQL = "INSERT INTO books (Author_name, Book_title, ISBN, count, catagory)
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertSQL);
            if ($stmt) {
                $stmt->bind_param("sssds", $author, $title, $isbn, $totalCount, $category);
                if ($stmt->execute()) {
                    echo 
                    "<script type='text/javascript'>
                            alert('Form submitted and data inserted successfully!');
                            window.location.href = 'LLAABB1.html'; 
                          </script>";
                } else {
                    echo "<h3>Error inserting data: " . $stmt->error . "</h3>";
                }
                $stmt->close();
            } else {
                echo "<h3>Error preparing insert statement: " . $conn->error . "</h3>";
            }
        }

        $conn->close();
    }

    if (!empty($errors)) {
        echo "<h3>Form Errors:</h3><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }
}
?>
