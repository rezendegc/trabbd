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
<th>ID</th>
<th>Name</th>
<th>Username</th>
<th>Relevancia</th>
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