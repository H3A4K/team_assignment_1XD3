<?php
/**
 * connect.php
 *
 * Creates the shared PDO database handle (`$dbh`) used by every other PHP
 * script in the project to talk to the MySQL database. Included at the top
 * of any file that needs to run queries.
 *
 * Authors: Julien Wallace, Daniel Kogan, Alexander Perlock, Neel Patel, Ekaterina Uhalova
 * Created: March 25, 2026
 */

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
