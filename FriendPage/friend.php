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
        $sql    = "SELECT Username FROM User WHERE Username LIKE '$name'";
        $result = $connect->query($sql);
        return $result->num_rows > 0;
    }
    
    function HasFriendsWith($connect, $name, $friend) {
        // Return if the dtabase already has a FriendsWith entry between $name and $friend Usernames.
        $sql    = "SELECT * FROM FriendsWith WHERE UserName1 LIKE '$name' AND UserName2 LIKE '$friend'";
        $result = $connect->query($sql);
        return $result->num_rows > 0;
    }
    
    function InsertFriend($connect) {
        // Insert a friendship into the database using two usernames.
        $name   = htmlspecialchars($_POST['name']);
        $friend = htmlspecialchars($_POST['friend']);
        $sql1   = "INSERT INTO FriendsWith (Username1, UserName2) VALUES ('$name', '$friend')";
        $sql2   = "INSERT INTO FriendsWith (Username1, UserName2) VALUES ('$friend', '$name')";
        if ( ! (IsValidName($name) && IsValidName($friend))) {
            echo "Invalid name! Please input a user name containing only english letters and digits.";
        } elseif ( ! HasUser($connect, $name)) {
            echo "Database does not have a user with username: '$name'";
        } elseif ( ! HasUser($connect, $friend)) {
            echo "Database does not have a user with username: '$friend'";
        } elseif (HasFriendsWith($connect, $name, $friend)) { 
            echo "Database already has a friendship established between '$name' and '$friend'.";
        } elseif ($connect->query($sql1) === TRUE && $connect->query($sql2) === TRUE) {
            echo "Friendship between '$name' and '$friend' successfully added!";
        } else {
            echo "Error: " . $sql1 . $connection->error;
            echo "Error: " . $sql2 . $connection->error;
        }
    }

    function DeleteFriend($connect) {
        // Delete a user with $name username from the database.
        $name   = htmlspecialchars($_POST['name']);
        $friend = htmlspecialchars($_POST['friend']);
        $sql1   = "DELETE FROM FriendsWith WHERE UserName1='$name' AND UserName2='$friend'";
        $sql2   = "DELETE FROM FriendsWith WHERE UserName1='$friend' AND UserName2='$name'";
        if ( ! (IsValidName($name) && IsValidName($friend))) {
            echo "Invalid name! Please input a user name containing only english letters and digits.";
        } elseif ( ! HasUser($connect, $name)) {
            echo "Cannot find user with username: '$name'. Cannot delete.";
        } elseif ( ! HasUser($connect, $name)) {
            echo "Cannot find user with username: '$friend'. Cannot delete.";
        } elseif ( ! HasFriendsWith($connect, $name, $friend)) {
            echo "There exists no friendship between '$name' and '$friend'. Cannot delete.";
        } elseif ($connect->query($sql1) === TRUE && $connect->query($sql2)) {
            echo "Friendship between '$name' and '$friend' officially ended.";
        } else {
            echo "Error: " . $sql . $connection->error;
            echo "Error: " . $sq2 . $connection->error;
        }
    }
        
    function SearchFriend($connect) {
        // Search for users who are friend with username $term.
        $term = htmlspecialchars($_POST['term']);
        $sql  = "SELECT UserName2 FROM FriendsWith WHERE UserName1='$term'";
        if ( ! IsValidName($term)) {
            echo "Invalid name! Please input a name containing only english letters and digits.";
            return;
        } elseif ( ! HasUser($connect, $term)) {
            echo "Username does not exist within the database. No friends found.";
            return;
        }
        $result = $connect->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo $row["UserName2"] . ", ";
            }
        } else {
            echo "0 results";
        }
    }
    
    $connect = MakeConnection();
    $action  = htmlspecialchars($_POST['action']);
    switch($action) {
        case "insert":
            InsertFriend($connect);
            break;
        case "delete":
            DeleteFriend($connect);
            break;
        case "search":
            SearchFriend($connect);
            break;
    }
    $connect->close();
?>