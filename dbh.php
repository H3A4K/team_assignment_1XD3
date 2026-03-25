<?php

try {
    $dbh = new PDO(
        "mysql:host=localhost;dbname=_db",
        "root",
        "",
    );
} catch (Exception $e) {
    die("ERROR: COULDN'T CONNECT TO DATABASE {$e->getMessage()}");
}

return $dbh;
