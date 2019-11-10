<!DOCTYPE html>
<html>

<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 1em;
            table-layout: fixed;
            overflow: hidden;
        }

        table,
        td,
        th {
            border: 1px solid white;
            padding: 5px;
        }

        td {
            text-align: left;
        }

        th {
            text-align: center;
            background-color: #ACADA8;
        }
    </style>
</head>

<?php
$servername = "127.0.0.1";
$username = "root";
$password = "root";
$db = "trabbd";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// get the q parameter from URL
$q = $_REQUEST["q"];

$q = str_replace(" ", " +", $q);

$query = "SELECT name, username FROM trabbd.users WHERE MATCH(name, username) AGAINST ('+" . $q . "*' IN BOOLEAN MODE) ORDER BY relevancia DESC, name, username DESC LIMIT 3;";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_array($result)) {
    echo $row['name'] . "(" . $row['username'] . ")" . "<br />";
}

$conn->close();
?>