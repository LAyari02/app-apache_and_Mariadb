<?php
$mysqli = new mysqli("db", "user", "password", "testdb");
if ($mysqli->connect_errno) {
    echo "Échec de connexion à MariaDB: " . $mysqli->connect_error;
} else {
    echo "Connexion réussie à MariaDB!";
}
?>