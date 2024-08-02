<?php

include "db.php";

// Добавление новой записи
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $name = $_POST['name'];
    $name = $db->real_escape_string($name);

    // Проверка на уникальность имени
    $sql = "SELECT * FROM telegram WHERE name='$name'";
    $result = $db->query($sql);

    if ($result->num_rows == 0) {
        $sql = "INSERT INTO telegram (name) VALUES ('$name')";
        if ($db->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $db->error;
        }
    } else {
        echo "Name already exists.";
    }
}

// Изменение состояния check
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle'])) {
    $id = $_POST['id'];
    $id = $db->real_escape_string($id);

    $sql = "UPDATE telegram SET `check` = NOT `check` WHERE id='$id'";
    if ($db->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $db->error;
    }
}

// Получение всех записей, где check = False
$sql = "SELECT * FROM telegram WHERE `check` = FALSE";
$result = $db->query($sql);

$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ники</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
        }
        button {
            margin: 5px;
        }
    </style>
</head>
<body>

<h1>ники</h1>

<form method="POST" action="">
    <input type="text" name="name" placeholder="Name" required>
    <button type="submit" name="add">Add Record</button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Check</th>
        <th>Actions</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . ($row["check"] ? 'True' : 'False') . "</td>";
            echo "<td>
                <form method='POST' action='' style='display:inline;'>
                    <input type='hidden' name='id' value='" . $row["id"] . "'>
                    <button type='submit' name='toggle'>Toggle Check</button>
                </form>
            </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No records found</td></tr>";
    }
    ?>
</table>

</body>
</html>
