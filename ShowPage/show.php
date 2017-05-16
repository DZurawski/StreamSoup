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
    
    function SearchShow($connect) {
        $term   = htmlspecialchars($_POST['term']);
        $sql    = "SELECT * FROM TVShow WHERE name LIKE '%$term%'";
        $result = $connect->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo $row["Name"] . "<br>";
                echo "Genre: "       . $row["Genre"]       . "<br>";
                echo "Description: " . $row["description"] . "<br>";
                echo "Seasons: "     . $row["numSeasons"]  . "<br>";
                echo "Episodes: "    . $row["numEpisodes"] . "<br>";
                EchoHostingServices($connect, $row["Name"]);
                echo "<br>";
            }
        } else {
            echo "0 results";
        }
    }
    
    function EchoHostingServices($connect, $show) {
        $sql    = "SELECT serviceName FROM Hosts WHERE showName LIKE '$show'";
        $result = $connect->query($sql);
        echo "Hosting Services:<br>";
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo $row["serviceName"] . "<br>";
            }
        } else {
            echo "None<br>";
        }
    }
    
    function AddToWatchedList($connect) {
        $user = htmlspecialchars($_POST['user']);
        $show = htmlspecialchars($_POST['show']);
        $sql  = "INSERT INTO HasWatched (username, showname) VALUES ('$user', '$show')";
        if ( ! IsValidName($user)) {
            echo "Invalid name! Please input a username containing only english letters and digits.";
        } elseif ( ! HasUser($connect, $user)) {
            echo "'$user' is not a member of this website. Please choose a different username.";
        } elseif (HasAlreadyWatched($connect, $user, $show)) { 
            echo "'$user' has already watched '$show'.";
        } elseif ( ! IsValidShow($connect, $show)) {
            echo "'$show' could not be found among all the TV shows.";
        } elseif ($connect->query($sql) === TRUE) {
            echo "'$user' has now successfully watched '$show'.";
        } else {
            echo "Error: " . $sql . $connect->error;
        }
    }
    
    function HasUser($connect, $name) {
        // Return if the database already has a User with $name Username.
        $sql = "SELECT Username FROM User WHERE Username LIKE '$name'";
        $result = $connect->query($sql);
        return $result->num_rows > 0;
    }
    
    function HasAlreadyWatched($connect, $user, $show) {
        $sql = "SELECT * FROM HasWatched WHERE username LIKE '$user' AND showname LIKE '$show' LIMIT 1";
        $result = $connect->query($sql);
        return $result->num_rows > 0;
    }
    
    function IsValidShow($connect, $show) {
        $sql = "SELECT * FROM TVShow WHERE Name LIKE '$show' LIMIT 1";
        $result = $connect->query($sql);
        return $result->num_rows > 0;
    }
    
    $connect = MakeConnection();
    $action  = htmlspecialchars($_POST['action']);
    switch($action) {
        case "search":
            SearchShow($connect);
            break;
        case "watch":
            AddToWatchedList($connect);
            break;
    }
    $connect->close();
?>