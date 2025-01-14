<?php

    $studentName = htmlspecialchars(trim($_POST['student-name']));
    $aiubId = htmlspecialchars(trim($_POST['aiub-id']));
    $email = htmlspecialchars(trim($_POST['email']));
    $bookTitle = htmlspecialchars(trim($_POST['book-title']));
    $borrowDate = htmlspecialchars(trim($_POST['borrow-date']));
    $token = htmlspecialchars(trim($_POST['token']));
    $returnDate = htmlspecialchars(trim($_POST['return-date']));
    $fees = htmlspecialchars(trim($_POST['fees']));
    $paid = htmlspecialchars(trim($_POST['paid']));

    $errors = [];

function addUsedToken($usedToken) {
    $usedTokenFile = 'usedToken.json';
    if (file_exists($usedTokenFile)) {
        $jsonData = file_get_contents($usedTokenFile);
        $usedTokens = json_decode($jsonData, true);
    } else {
        $usedTokens = []; 
    }
    if (!in_array($usedToken, $usedTokens)) {
        $usedTokens[] = $usedToken;
        file_put_contents($usedTokenFile, json_encode($usedTokens, JSON_PRETTY_PRINT));
    }
}
function loadTokens() {
    $jsonData = file_get_contents('token.json'); 
    $tokens = json_decode($jsonData, true);
    return $tokens['tokens']; 
}
function loadusedTokens() {
    
    $jsonData1 = file_get_contents('usedToken.json'); 
    $usedtokens = json_decode($jsonData1, true);
    $tokens = $usedtokens['usedTokens'];

    foreach ($usedtokens as $key => $value) {
        if (is_string($value) && !in_array($value, $tokens)) {
            $tokens[] = $value;
        }
    }
    return $tokens;
}



    if (!preg_match("/^[a-zA-Z]+$/", $studentName)) { 
        $errors[] = "Student Name must contain only letters.";
    }

    if (!preg_match("/^\d{2}-\d{5}-\d{1}$/", $aiubId)) {
        $errors[] = "AIUB ID must be in the format xx-xxxxx-x.";
    }

    if (!preg_match("/^\d{2}-\d{5}-\d{1}\@student\.aiub\.edu$/", $email)) {
        $errors[] = "Email must be in the format xx-xxxxx-x@student.aiub.edu.";
    }

    if (!is_numeric($fees) || intval($fees) != $fees || $fees < 0) {
        $errors[] = "Fees must be a non-negative integer.";
    }

    $borrowDateObj = DateTime::createFromFormat("Y-m-d", $borrowDate);
    $returnDateObj = DateTime::createFromFormat("Y-m-d", $returnDate);

    if ($borrowDateObj && $returnDateObj) {
        $dateDiff = $borrowDateObj->diff($returnDateObj)->days;
        if ($dateDiff > 10) {
            if (!empty($token)) {

                $availableTokens = loadTokens(); 
                $Usedtoken=loadusedTokens(); 
        
                if (!in_array($token, $availableTokens)) {
                    $errors[] = "The provided token is not available. Return Date cannot be more than 10 days after the Borrow Date.";}
                
                else{ if(in_array($token,$Usedtoken))
                {
                    
                    $errors[] = "The provided token is already used";
                }
                else{
                    addUsedToken($token);
                }}
            }
            else{
                
                $errors[] = "Return Date cannot be more than 10 days after the Borrow Date.";

            }

        }
        
        elseif ($borrowDateObj == $returnDateObj) {
            $errors[] = "Return Date cannot be the same as Borrow Date.";
        } elseif ($returnDateObj < $borrowDateObj) {
            $errors[] = "Return Date cannot be before Borrow Date.";
        }
    } else {
        $errors[] = "Borrow Date and Return Date must be valid dates.";
    }

    

      
        if (!empty($errors)) {
            echo "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Validation Errors</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        padding: 20px;
                        background-color: #f0f0f0;
                    }
                    h1 {
                        text-align: center;
                    }
                    .error-container {
                        background-color: #ffe6e6;
                        border: 1px solid #cc0000;
                        border-radius: 8px;
                        padding: 20px;
                        margin: 20px auto;
                        max-width: 600px;
                    }
                    .error-item {
                        color: #cc0000;
                        margin: 10px 0;
                    }
                    .resubmit-button {
                        padding: 8px 12px;
                        background-color: #007BFF;
                        color: white;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                        margin-top: 20px;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class='error-container'>
                    <h1>Validation Errors</h1>";
                    foreach ($errors as $error) {
                        echo "<div class='error-item'>$error</div>";
                    }
                    echo "<button class='resubmit-button' onclick='window.location.href=\"LLAABB1.html\"'>Resubmit</button>
                </div>
            </body>
            </html>";
        } else {
            if (isset($_COOKIE['bookTitle']) && $_COOKIE['bookTitle'] == $bookTitle) {
       
                echo "The book '" . $_COOKIE['bookTitle'] . "' is already selected. Please wait until the selection expires.";
            } else {
               
                setcookie("bookTitle", $bookTitle, time() + 20, "/"); 
        
                echo "You have selected the book: " . $bookTitle;
            
            $qrData = "Student Name: $studentName\nAIUB ID: $aiubId\nEmail: $email\nBook Title: $bookTitle\nBorrow Date: $borrowDate\nToken: $token\nReturn Date: $returnDate\nFees: $fees\nPaid: $paid";

            $encodedData = urlencode($qrData);

            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data=$encodedData&size=100x100";
        
            echo "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Submitted Data</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        padding: 20px;
                        background-color: #f0f0f0;
                    }
                    h1 {
                        text-align: center;
                    }
                    .data-container {
                        background-color: #d4edda;
                        border: 1px solid #ccc;
                        border-radius: 8px;
                        padding: 20px;
                        margin: 20px auto;
                        max-width: 600px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    }
                    .data-item {
                        margin: 10px 0;
                    }
                    .print-button {
                        padding: 8px 12px;
                        background-color: #007BFF;
                        color: white;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                        margin-top: 20px;
                    }
                    .qr-code {
                        margin-top: 20px;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class='data-container'>
                    <h1>Submitted Data</h1>
                    <div class='data-item'><strong>Student Name:</strong> $studentName</div>
                    <div class='data-item'><strong>Student AIUB ID:</strong> $aiubId</div>
                    <div class='data-item'><strong>Student Email:</strong> $email</div>
                    <div class='data-item'><strong>Book Title:</strong> $bookTitle</div>
                    <div class='data-item'><strong>Borrow Date:</strong> $borrowDate</div>";
                       if ($dateDiff > 10) {
            echo "<div class='data-item'><strong>Token:</strong> $token</div>";
        }

        echo "
                    <div class='data-item'><strong>Return Date:</strong> $returnDate</div>
                    <div class='data-item'><strong>Fees:</strong> $fees</div>
                    <div class='data-item'><strong>Paid:</strong> $paid</div>
                    <div class='qr-code'>
                        <strong>QR Code for the Submitted Data:</strong><br>
                        <img src='$qrCodeUrl' >
                    </div>
                    <button class='print-button' onclick='window.print()'>Print</button>


                </div>
            </body>
            </html>";
        }
    }

?>
