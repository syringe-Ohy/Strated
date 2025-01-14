<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["delete"])) {
        
        $isbnToDelete = trim($_POST["delete-isbn"]);

  
        $servername = "localhost";
        $username = "root";
        $password = ""; 
        $dbname = "bookborrow"; 

       
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $checkISBNQuery = "SELECT * FROM books WHERE ISBN = ?";
        $stmt = $conn->prepare($checkISBNQuery);
        if ($stmt) {
            $stmt->bind_param("s", $isbnToDelete);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                
                $deleteQuery = "DELETE FROM books WHERE ISBN = ?";
                $stmtDelete = $conn->prepare($deleteQuery);
                if ($stmtDelete) {
                    $stmtDelete->bind_param("s", $isbnToDelete);
                    if ($stmtDelete->execute()) {
                        
                        echo "<script type='text/javascript'>
                                alert('Record with ISBN: $isbnToDelete has been deleted successfully!');
                                 window.location.href = 'LLAABB1.html'; 
                               
                              </script>";
                    } else {
                        echo "<p>Error deleting record: " . $stmtDelete->error . "</p>";
                    }
                    $stmtDelete->close();
                }
            } else {
               
                echo "<p>No book found with ISBN: $isbnToDelete. Deletion failed.</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Error preparing statement: " . $conn->error . "</p>";
        }

        $conn->close();
    }
}
?>
