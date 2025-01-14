<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "bookborrow"; 

 
    $isbn = trim($_POST["isbn"]);
    $newAuthor = trim($_POST["new-author"]);
    $newTitle = trim($_POST["new-title"]);
    $newCount = trim($_POST["new-count"]);
    $newCategory = trim($_POST["new-category"]);

    
    $errors = [];



    if (empty($newAuthor) && empty($newTitle) && empty($newCount) && empty($newCategory)) {
        $errors[] = "At least one field must be provided for update.";
    }

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $checkISBNQuery = "SELECT * FROM books WHERE ISBN = ?";
    $stmt = $conn->prepare($checkISBNQuery);
    if ($stmt) {
        $stmt->bind_param("s", $isbn);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
          
            echo "No book found with ISBN: $isbn. Update failed.";
        } else {
            $updateQuery = "UPDATE books SET ";

            $updateFields = [];
            $params = [];

            if (!empty($newAuthor)) {
                $updateFields[] = "Author_name = ?";
                $params[] = $newAuthor;
            }

            if (!empty($newTitle)) {
                $updateFields[] = "Book_title = ?";
                $params[] = $newTitle;
            }

            if (!empty($newCount)) {
                $updateFields[] = "count = ?";
                $params[] = $newCount;
            }

            if (!empty($newCategory)) {
                $updateFields[] = "catagory = ?";
                $params[] = $newCategory;
            }

            $updateQuery .= implode(", ", $updateFields) . " WHERE ISBN = ?";
            $params[] = $isbn; 

            $stmtUpdate = $conn->prepare($updateQuery);
            if ($stmtUpdate) {
                $types = str_repeat("s", count($params) - 1) . "s"; 
                array_unshift($params, $types); 

                call_user_func_array([$stmtUpdate, 'bind_param'], $params);

                if ($stmtUpdate->execute()) {
                    echo "<script type='text/javascript'>
                            alert('Book with ISBN: $isbn has been updated successfully!');
                            window.location.href = 'LLAABB1.html'; 
                          </script>";
                } else {
                    echo "Error updating record: " . $stmtUpdate->error;
                }
                $stmtUpdate->close();
            } else {
                echo "Error preparing update statement: " . $conn->error;
            }
        }

        $stmt->close();
    } else {
        echo "Error preparing ISBN check statement: " . $conn->error;
    }

    $conn->close();
}
?>
