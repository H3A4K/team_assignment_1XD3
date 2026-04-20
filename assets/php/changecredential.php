<?php
/**
 * changecredential.php
 *
 * Updates one of the logged-in user's account fields: email, password,
 * phone number, or address. The specific field to update is selected with
 * the POST `type` parameter, and the new value comes from the matching
 * POST field. Rejects duplicate emails or phone numbers.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: April 17, 2026
 */

include "connect.php";
session_start();

$type = filter_input(INPUT_POST, "type", FILTER_SANITIZE_SPECIAL_CHARS);

if (!isset($_SESSION["email"])) {
    echo "You are not logged in";
    return;
}
$current_email = $_SESSION["email"];

if ($type !== NULL && $type !== false) {
    if ($type == "email") {
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
        if ($email !== NULL && $email !== false) {
            // Check if email exists
            $cmd = "SELECT email FROM accounts WHERE email=?";
            $stmt = $dbh->prepare($cmd);
            $success = $stmt->execute([$email]);
            if (!$success || $stmt->rowCount() > 0) {
                echo "Inputted email already exists";
                return;
            }

            $cmd = "UPDATE accounts SET `email`=? WHERE `email`=?";
            $stmt = $dbh->prepare($cmd);
            $success = $stmt->execute([$email, $current_email]);
            if ($success && $stmt->rowCount() == 1) {
                $_SESSION["email"] = $email;
                echo "Success";
                return;
            }
            else {
                echo "Failed";
                return;
            }
        }
        else {
            echo "Failed to update";
            return;
        }
    }

     if ($type == "password") {
        $input = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
        if ($input !== NULL && $input !== false) {
            $hashed_password = password_hash($input, PASSWORD_DEFAULT);
            $cmd = "UPDATE accounts SET `password`=? WHERE `email`=?";
            $stmt = $dbh->prepare($cmd);
            $success = $stmt->execute([$hashed_password, $current_email]);
            if ($success && $stmt->rowCount() == 1) {
                echo "Success";
                return;
            }
            else {
                echo "Failed";
                return;
            }
        }
        else {
            echo "Failed to update";
            return;
        }
    }

    if ($type == "phone") {
        $input = filter_input(INPUT_POST, "phone", FILTER_SANITIZE_SPECIAL_CHARS);
        if ($input !== NULL && $input !== false) {
            $cmd = "SELECT phonenumber FROM accounts WHERE phonenumber=?";
            $stmt = $dbh->prepare($cmd);
            $success = $stmt->execute([$input]);
            if (!$success || $stmt->rowCount() > 0) {
                echo "Inputted phone number already exists";
                return;
            }

            $cmd = "UPDATE accounts SET `phonenumber`=? WHERE `email`=?";
            $stmt = $dbh->prepare($cmd);
            $success = $stmt->execute([$input, $current_email]);
            if ($success && $stmt->rowCount() == 1) {
                echo "Success";
                return;
            }
            else {
                echo "Failed";
                return;
            }
        }
        else {
            echo "Failed to update";
            return;
        }
    }

    if ($type == "address") {
        $input = filter_input(INPUT_POST, "address", FILTER_SANITIZE_SPECIAL_CHARS);
        if ($input !== NULL && $input !== false) {
            $cmd = "UPDATE accounts SET `address`=? WHERE `email`=?";
            $stmt = $dbh->prepare($cmd);
            $success = $stmt->execute([$input, $current_email]);
            if ($success && $stmt->rowCount() == 1) {
                echo "Success";
                return;
            }
            else {
                echo "Failed";
                return;
            }
        }
        else {
            echo "Failed to update";
            return;
        }
    }
}
else {
    echo "Failed to update";
}