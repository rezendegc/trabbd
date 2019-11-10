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
$u = $_REQUEST["u"];
$n = $_REQUEST["n"];
$r = $_REQUEST["r"];

if ($r === null || $r === "") {
    $r = 2;
}

$q = str_replace(" ", " +", $q);

$query = "SELECT * FROM trabbd.users WHERE MATCH(name, username) AGAINST ('+" . $q . "*' IN BOOLEAN MODE) AND (name, username) > ('" . $n . "', '" . $u . "') AND relevancia <= '" . $r . "' ORDER BY relevancia DESC, name, username LIMIT 16;";

$result = mysqli_query($conn, $query);

$count = 0;

echo "<table>
<tr>
<th width=30%>ID</th>
<th width=25%>Name</th>
<th width=25%>Username</th>
<th width=20%>Relevancia</th>
</tr>";
while ($row = mysqli_fetch_array($result)) {
    $count++;
    if ($count === 16) {
        break;
    }
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td class='name'>" . $row['name'] . "</td>";
    echo "<td class='username'>" . $row['username'] . "</td>";
    echo "<td class='relevancia'>" . $row['relevancia'] . "</td>";
    echo "</tr>";
}
echo "</table>";

if ($u !== null && $u !== "") {
    echo '<button id="prev" onclick="previousPage()"> Voltar página </button>';
}

if ($count === 16) {
    echo '<button id="next" style="float: right" onclick="nextPage()"> Próxima página </button>';
}

$conn->close();
?>