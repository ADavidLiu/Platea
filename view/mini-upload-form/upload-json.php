<?php
    require_once '../../videos/configuration.php';

    // Create connection
    $conn = new mysqli($mysqlHost, $mysqlUser, $mysqlPass);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM platea_admin.videos ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    $id = "";

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $id = $row["id"];
        }
    } else {
        echo "0 results";
    }

    $base64 = $_POST["base64"]; 
    $sql = "UPDATE platea_admin.videos SET json = '" . $base64 . "' WHERE id = " . $id . ";";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();

?>
