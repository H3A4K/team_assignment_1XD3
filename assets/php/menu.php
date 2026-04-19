<?php
/**
 * menu.php
 *
 * Returns the full list of menu products as a JSON array. Consumed by the
 * menu page's client-side script to render menu cards.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: March 30, 2026
 */

include "connect.php";

$cmd = "SELECT * FROM products";
$stmt = $dbh->prepare($cmd);
$stmt->execute();
$menu = $stmt->fetchAll();

echo json_encode($menu);