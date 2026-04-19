<?php
/**
 * signup.php
 *
 * Server-side signup endpoint. Validates the submitted email, password,
 * phone number, and address, ensures the email and phone aren't already
 * taken, and creates a new row in the users table with a hashed password.
 * Replies with a plain-text status message for the client JavaScript.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: March 30, 2026
 */

include "connect.php";

$email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
$phone = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_SPECIAL_CHARS);
$address = filter_input(INPUT_POST, "address", FILTER_SANITIZE_SPECIAL_CHARS);

if ($email != NULL && $password != NULL && $phone != NULL && $address != NULL) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    // Check if user with email already exists
    $cmd = "SELECT * FROM users WHERE email=?";
    $stmt = $dbh->prepare($cmd);
    $success = $stmt->execute([$email]);
    if (!$success) {
        echo "There was an error with the database";
        return;
    }

    if ($stmt->rowCount() > 0) {
        echo "A user with your email already exists";
        return;
    }

    // Check if user with a phone number already exists
    $cmd = "SELECT * FROM users WHERE phonenumber=?";
    $stmt = $dbh->prepare($cmd);
    $success = $stmt->execute([$phone]);
    if (!$success) {
        echo "There was an error with the database";
        return;
    }

    if ($stmt->rowCount() > 0) {
        echo "A user with your phone number already exists";
        return;
    }

    // Otherwise, create the account
    $cmd = "INSERT INTO users (password, email, phonenumber, address) values (?, ?, ?, ?)";
    $stmt = $dbh->prepare($cmd);
    $args = [$hashedPassword, $email, $phone, $address];
    $success = $stmt->execute($args);
    if (!$success) {
        echo "There was an error with the database";
        return;
    }

    // Account created successfully
    echo "Your account was created successfully";
    return;
} else {
    echo "One of your inputted values was invalid";
}
