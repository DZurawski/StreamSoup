const PHP = "compare.php";

function IsValidUsername(username) {
    // Returns true if username is only letters and numbers and not empty.
    return /^\w+$/.test(username);
}

function CreateRadarChart(user1, user2, shownames, ratings1, ratings2) {
    // Create a Radar Chart inside the #radar-chart canvas.
    // Radar chart compares how user1 and user2 tend to rate shows.
    // user1 (String) rates shownames[i] ([String]) using ratings1[i] (Num 0 to 10).
    // user2 (String) rates shownames[i] ([String]) using ratings2[i] (Num 0 to 10).
    new Chart($("#radar-chart"), {
        type: "radar",
        options: {
            responsive: false,
            title: {
                position: "top",
                display: true,
                fontSize: 16,
                text: ("Comparison Between " + user1 + " and " + user2)
            },
            legend: {
              position: "left",
              labels: {
                  fontSize: 16
              }
            },
            scale: {
                ticks: {
                    beginAtZero: true,
                    suggestedMax: 10,
                    suggestedMin: 0,
                    maxTicksLimit: 5
                },
                pointLabels: {
                    fontSize: 16,
                    fontStyle: "italic"
                }
            }
        },
        data: {
            labels: shownames,
            datasets: [
                {
                    label: user1 + "'s Preferences",
                    backgroundColor: "rgba(179,181,198,0.2)",
                    borderColor: "rgba(179,181,198,1)",
                    pointBackgroundColor: "rgba(179,181,198,1)",
                    pointBorderColor: "#fff",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: "rgba(179,181,198,1)",
                    data: ratings1
                },
                {
                    label: user2 + "'s Preferences",
                    backgroundColor: "rgba(255,99,132,0.2)",
                    borderColor: "rgba(255,99,132,1)",
                    pointBackgroundColor: "rgba(255,99,132,1)",
                    pointBorderColor: "#fff",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: "rgba(255,99,132,1)",
                    data: ratings2
                }
            ]
        }
    }); 
}

$(document).ready(function () {
    $("#request-form").submit(function (e) {
        e.preventDefault(); // Prevents submission page refresh.
        var user1 = $("#request-user1").val();
        var user2 = $("#request-user2").val();
       
        if ( ! (IsValidUsername(user1) || IsValidUsername(user2))) {
            var err = "Invalid Usernames!\nPlease try nonempty usernames that only contain letters and numbers.";
            $("#chart-section").text(err);
            return;
        }
       
        $.post( PHP, { user1: user1, user2: user2 }).done(function(data) {
            var args   = JSON.parse(data);
            var errors = args[0];
            if (errors) {
                $("#chart-section").text(errors);
            } else {
                CreateRadarChart(user1, user2, args[1], args[2], args[3]);
            }
        });
    });
});