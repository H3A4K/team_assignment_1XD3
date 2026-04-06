<?php

try {
    $dbh = new PDO(
        "mysql:host=localhost;dbname=CK-assignment",
        "root",
        "",
    );
} catch (Exception $e) {
    die("ERROR: COULDN'T CONNECT TO DATABASE {$e->getMessage()}");
}

return $dbh;