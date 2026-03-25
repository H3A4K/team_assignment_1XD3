<?php

try {
    $dbh = new PDO(
        "mysql:host=localhost;dbname=paten164_db",
        "root",
        "",
    );
} catch (Exception $e) {
    die("ERROR: COULDN'T CONNECT TO DATABASE {$e->getMessage()}");
}

return $dbh;