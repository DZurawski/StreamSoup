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
    
    function HasShow($connect, $show) {
        $sql = "SELECT * FROM TVShow WHERE Name LIKE '$show' LIMIT 1";
        $result = $connect->query($sql);
        return $result->num_rows > 0;
    }
    
    function HasAlreadyWatched($connect, $user, $show) {
        $sql = "SELECT * FROM HasWatched WHERE username LIKE '$user' AND showname LIKE '$show' LIMIT 1";
        $result = $connect->query($sql);
        return $result->num_rows > 0;
    }
    
    function InsertReview($connect) {
        $user = htmlspecialchars($_POST['user']);
        $show = htmlspecialchars($_POST['show']);
        $text = htmlspecialchars($_POST['text']);
        $rate = htmlspecialchars($_POST['rate']);
        
        $sql = "INSERT INTO UserFeedback (UserName, ShowName, textReview, numericalRating)
                VALUES ('$user', '$show', '$text', '$rate')";
        
        if ( ! IsValidName($user)) {
            echo "Invalid name! Please input a user name containing only english letters and digits.";
        } elseif ( ! HasUser($connect, $user)) {
            echo "Database does not have a user with username: '$user'";
        } elseif ( ! HasShow($connect, $show)) {
            echo "Database does not have a show titled: '$show'";
        } elseif ($connect->query($sql) === TRUE) {
            echo "Successfully added the review!";
            if ( ! HasAlreadyWatched($connect, $user, $show)) {
                $sql2  = "INSERT INTO HasWatched (username, showname) VALUES ('$user', '$show')";
                $connect->query($sql2);
            }
        } else {
            echo "Error: " . $sql . $connect->error;
        }
    }
    
    $connect = MakeConnection();
    $action  = htmlspecialchars($_POST['action']);
    switch($action) {
        case "insert":
            InsertReview($connect);
            break;
    }
    $connect->close();
?>