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
    
    function HasUser($connect, $name) {
        // Return if the database already has a User with $name Username.
        $sql    = "SELECT Username FROM User WHERE Username LIKE '$name'";
        $result = $connect->query($sql);
        return $result->num_rows > 0;
    }
    
    function CommonWatchedShows($connect, $user1, $user2, $limit) {
        // Return a list of the shows that both user1 and user2 have reviewed.
        // limit is the max number of shows returned.
        $sql = "SELECT DISTINCT showname
                FROM UserFeedback 
                WHERE username LIKE '$user1'
                AND UserFeedback.showname IN
                (
                    SELECT DISTINCT showname
                    FROM UserFeedback
                    WHERE username LIKE '$user2'
                )
                LIMIT $limit";
        $result = $connect->query($sql);
        if ($result) {
            $return = array();
            while($row = $result->fetch_assoc()) {
                array_push($return, $row["showname"]);
            }
            return $return;
        } else {
            return array();
        }
    }
    
    function ShowRatings($connect, $shownames, $user) {
        // Return a list of ratings corresponding to how user rated each show from shownames.
        $rating = array();
        for ($i = 0; $i < count($shownames); $i++) {
            $show = $shownames[$i];
            $sql = "SELECT numericalrating
                    FROM UserFeedback
                    WHERE username LIKE '$user'
                    AND showname LIKE '$show'
                    LIMIT 1";
            $result = $connect->query($sql);
            if ($result) {
                $row = $result->fetch_assoc();
                array_push($rating, $row["numericalrating"]);
            } else {
                array_push($rating, 0);
            }
        }
        return $rating;
    }
    
    function RadarArguments($connect) {
        // Return a JSON string consisting of:
        // [error_string, shownames_list, user1_ratings, user2_ratings]
        $user1   = htmlspecialchars($_POST['user1']);
        $user2   = htmlspecialchars($_POST['user2']);
        
        if ( ! HasUser($connect, $user1)) {
            $error = "No user '$user1' could be found.";
            return json_encode([$error, [], [], []]);
        } elseif ( ! HasUser($connect, $user2)) {
            $error = "No user '$user2' could be found.";
            return json_encode([$error, [], [], []]);
        }
        
        $maxshows  = 8;
        $shownames = CommonWatchedShows($connect, $user1, $user2, $maxshows);
        $ratings1  = ShowRatings($connect, $shownames, $user1);
        $ratings2  = ShowRatings($connect, $shownames, $user2);

        if (count($shownames) < 1) {
            $error = "'$user1' and '$user2' have not watched any of the same shows.";
            return json_encode([$error, [], [], []]);
        }
        
        return json_encode(["", $shownames, $ratings1, $ratings2]);
    }
    
    $connect = MakeConnection();
    echo RadarArguments($connect);
    $connect->close();
?>