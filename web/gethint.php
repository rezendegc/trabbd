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

table, td, th {
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
<body>

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

$query = "SELECT * FROM trabbd.users WHERE name like '%" . $q . "%' ORDER BY relevancia LIMIT 15;";

$result = mysqli_query($conn, $query);

echo "<table>
<tr>
<th width=30%>ID</th>
<th width=25%>Name</th>
<th width=25%>Username</th>
<th width=20%>Relevancia</th>
</tr>";
while($row = mysqli_fetch_array($result)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['username'] . "</td>";
    echo "<td>" . $row['relevancia'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>