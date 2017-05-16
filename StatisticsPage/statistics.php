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
    
    function SQLColumnToArray($column, $key) {
        $array = array();
        while($row = $column->fetch_assoc()) {
            $array[] = $row[$key];
        }
        return $array;
    }
    
    function NumberOfShows($connect, $service) {
        $sql = "SELECT DISTINCT showName FROM Hosts WHERE serviceName LIKE '$service'";
        $result = $connect->query($sql);
        return count(SQLColumnToArray($result, "showName"));
    }
    
    function ServicePrice($connect, $service) {
        $sql ="SELECT MonthlyPrice FROM StreamingService WHERE Name LIKE '$service'";
        $result = $connect->query($sql);
        return SQLColumnToArray($result, "MonthlyPrice")[0];
    }
    
    function AverageRating($connect, $service, $users) {
        $rating   = 0;
        $numshows = 0;
        
        $sql       = "SELECT showName FROM Hosts WHERE serviceName LIKE '$service'";
        $hostshows = SQLColumnToArray($connect->query($sql), "showName");
        
        for ($u = 0; $u < count($users); $u++) {
            $sql     = "SELECT * FROM UserFeedback WHERE UserName LIKE '$users[$u]'";
            $shows   = SQLColumnToArray($connect->query($sql), "ShowName");
            for ($s = 0; $s < count($shows); $s++) {
                if (in_array($shows[$s], $hostshows)) {
                    $sql = "SELECT numericalRating
                            FROM UserFeedback
                            WHERE UserName LIKE '$users[$u]'
                            AND   ShowName LIKE '$shows[$s]'";
                    $rates = SQLColumnToArray($connect->query($sql), "numericalRating");
                    if ($rates) {
                        $numshows++;
                        $rating += $rates[0];
                    } else {
                        $score = ShowGenreScore($connect, $users[$u], $shows[$s]);
                        if ($score > 0) {
                            $numshows++;
                            $rating += $score;
                        }
                    }
                }
            }
        }
        
        if ($numshows > 0) {
            return $rating / $numshows;
        } else {
            return 0;
        }
    }
    
    function ShowGenreScore($connect, $user, $show) {
        $sql    = "SELECT Genre FROM TVShow WHERE Name LIKE '$show'";
        $result = $connect->query($sql);
        if ($result) {
            $genre = SQLColumnToArray($result, "Genre")[0];
            $sql = "SELECT numericalRating
                    FROM UserFeedback
                    WHERE UserName LIKE '$user'
                    AND ShowName IN (SELECT Name FROM TVShow WHERE Genre LIKE '$genre')";
            $ratings = SQLColumnToArray($connect->query($sql), "numericalRating");
            if (count($ratings) > 0) {
                return array_sum($ratings) / count($ratings);
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }
    
    function ValueOfService($connect, $service, $users) {
        $num    = 1;// NumberOfShows($connect, $service);
        $price  = 1;//ServicePrice($connect, $service);
        $rating = AverageRating($connect, $service, $users);
        return $num * $rating / $price;
    }
    
    function GetPersonal($connect, $user, $service) {
        return ValueOfService($connect, $service, [$user]);
    }
    
    function GetFriends($connect, $user, $service) {
        $sql = "SELECT DISTINCT UserName2 FROM FriendsWith WHERE UserName1 LIKE '$user'";
        $friends = SQLColumnToArray($connect->query($sql), "UserName2");
        return ValueOfService($connect, $service, $friends);
    }
    
    function GetAll($connect, $service) {
        $sql = "SELECT DISTINCT Username FROM User";
        $all = SQLColumnToArray($connect->query($sql), "Username");
        return ValueOfSErvice($connect, $service, $all);
    }
    
    function GetData($connect, $user, $service) {
        $personal = GetPersonal($connect, $user, $service);
        $friends  = GetFriends($connect, $user, $service);
        $allusers = GetAll($connect, $service);
        return [$personal, $friends, $allusers];
    }
    
    function BarChartArguments($connect) {
        $user = htmlspecialchars($_POST['user']);
        if ( ! HasUser($connect, $user)) {
            $err = "User '$user' does not exist within the database.";
            return json_encode([$err, [], [], []]);
        } else {
            $hulu        = GetData($connect, $user, "Hulu");
            $crunchyroll = GetData($connect, $user, "CrunchyRoll");
            $showtime    = GetData($connect, $user, "Showtime");
            return json_encode(["", $hulu, $crunchyroll, $showtime]);
        }
    }
    
    $connect = MakeConnection();
    echo BarChartArguments($connect);
    $connect->close();
?>
