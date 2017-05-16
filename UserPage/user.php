<?php
    function MakeConnection() {
        // Return a connection object associated with the following attributes.
        $servername = "webhost.engr.illinois.edu";
        $username   = "streamsoup_test";
        $password   = "streaming123";
        $dbname     = "streamsoup_411project";
        $connect    = new mysqli($servername, $username, $password, $dbname);
        if ($connect->connect_error) {
            exit("Connection failed: " . $connect->connect_error);
        }
        return $connect;
    }
    
    function IsValidName($name) {
        // Return boolean of if $name is alphanumeric.
        return ! preg_match('/[^A-Za-z0-9]/', $name);
    }
    
    function HasUser($connect, $name) {
        // Return if the database already has a User with $name Username.
        $sql = "SELECT Username FROM User WHERE Username LIKE '$name'";
        $result = $connect->query($sql);
        return $result->num_rows > 0;
    }
    
    function InsertUser($connect) {
        // Insert a user into the database.
        $name = htmlspecialchars($_POST['name']);
        $sql  = "INSERT INTO User (Username) VALUES ('$name')";
        if ( ! IsValidName($name)) {
            echo "Invalid name! Please input a user name containing only english letters and digits.";
        } elseif (HasUser($connect, $name)) { 
            echo "Database already has a User with username: '$name'";
        } elseif ($connect->query($sql) === TRUE) {
            echo "User '$name' successfully added!";
        } else {
            echo "Error: " . $sql . $connect->error;
        }
    }

    function DeleteUser($connect) {
        // Delete a user with $name username from the database.
        $name = htmlspecialchars($_POST['name']);
        $sql  = "DELETE FROM User WHERE Username='$name'";
        if ( ! IsValidName($name)) {
            echo "Invalid name! Please input a user name containing only english letters and digits.";
        } elseif ( ! HasUser($connect, $name)) {
            echo "Cannot find user with username: '$name'. Cannot delete.";
        } elseif ($connect->query($sql) === TRUE) {
            echo "User '$name' successfully deleted!";
        } else {
            echo "Error: " . $sql . $connection->error;
        }
    }
    
    function EditUser($connect) {
        // Edit a user with $name username to have instead $edit username.
        $name = htmlspecialchars($_POST['name']);
        $edit = htmlspecialchars($_POST['edit']);
        $sql  = "UPDATE User SET Username='$edit' WHERE Username='$name'";
        if ( ! IsValidName($name) && IsValidName($edit)) {
            echo "Invalid name! Please input a user name containing only english letters and digits.";
            return;
        } elseif ( ! HasUser($connect, $name)) {
            echo "No user with username: '$name' exists. Cannot edit.";
        } elseif (HasUser($connect, $edit)) {
            echo "There is already a user with username: '$edit'! Cannot edit.";
        } elseif ($connect->query($sql) === TRUE) {
            echo "Edit was successful!";
        } else {
            echo "Error: " . $sql . $connection->error;
        }
    }
    
    function SearchUser($connect) {
        // Search for users with usernames containing $term.
        $term = htmlspecialchars($_POST['term']);
        $sql  = "SELECT Username FROM User WHERE Username LIKE '%$term%'";
        if ( ! IsValidName($term)) {
            echo "Invalid term! Please input a term containing only english letters and digits.";
            return;
        } 
        $result = $connect->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo $row["Username"] . ", ";
            }
        } else {
            echo "0 results";
        }
    }
    
    $connect = MakeConnection();
    $action  = htmlspecialchars($_POST['action']);
    switch($action) {
        case "insert":
            InsertUser($connect);
            break;
        case "delete":
            DeleteUser($connect);
            break;
        case "edit":
            EditUser($connect);
            break;
        case "search":
            SearchUser($connect);
            break;
    }
    $connect->close();
?>