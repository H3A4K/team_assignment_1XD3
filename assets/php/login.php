<?php
include "connect.php";
session_start();

$email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

if ($email != NULL && $password != NULL) {
    // Check if email is an existing user
    $cmd = "SELECT * FROM users WHERE email=?"; // TODO: change to users
    $stmt = $dbh->prepare($cmd);
    $success = $stmt->execute([$email]);
    if (!$success) {
        echo "There was an error with the database";
        return;
    }

    if ($stmt->rowCount() != 1) {
        echo "No user found with the entered email";
        return;
    }

    // Check if password matches
    $row = $stmt->fetch();
    if (password_verify($password, $row["password"])) {
        $_SESSION["userID"] = $row["userID"];
        $_SESSION["email"] = $email;
        echo "Logged in";
        return;
    }
    else {
        echo "Incorrect Password";
        return;
    }


}
else {
    echo "One of your inputs was invalid";
}
