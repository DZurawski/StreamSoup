const PHP = "statistics.php";

function CreateBarChart(huluData, crunchyRollData, showtimeData) {
    new Chart($("#bar-chart"), {
    type: 'bar',
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                },
                scaleLabel: {
                    display: true,
                    labelString: 'Value Per Dollar Spent On Service'
                }
            }]
        }
    },
    data: {
        labels: ["For this User", "For their Friends", "For all Users"],
        datasets: [
        {
            label: "Hulu",
            backgroundColor: [
                'rgba(255, 99, 132, 0.5)',
                'rgba(255, 99, 132, 0.5)',
                'rgba(255, 99, 132, 0.5)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(255,99,132,1)',
                'rgba(255,99,132,1)',
            ],
            borderWidth: 1,
            data: huluData
        }, 
        {
            label: "CrunchyRoll",
            backgroundColor: [
                'rgba(153, 102, 255, 0.5)',
                'rgba(153, 102, 255, 0.5)',
                'rgba(153, 102, 255, 0.5)'
            ],
            borderColor: [
                'rgba(75, 92, 92, 1)',
                'rgba(75, 92, 92, 1)',
                'rgba(75, 92, 92, 1)'
            ],
            borderWidth: 1,
            data: crunchyRollData
        },
        {
            label: "Showtime",
            backgroundColor: [
                'rgba(255, 159, 64, 0.5)',
                'rgba(255, 159, 64, 0.5)',
                'rgba(255, 159, 64, 0.5)'
            ],
            borderColor: [
                'rgba(75, 50, 125, 1)',
                'rgba(75, 50, 125, 1)',
                'rgba(75, 50, 125, 1)'
            ],
            borderWidth: 1,
            data: showtimeData
        }
    ]
}
    
});
}

$(document).ready(function () {
    $("#request-form").submit(function (e) {
        e.preventDefault(); // Prevents submission page refresh.
        var user = $("#request-user").val();
        $.post( PHP, { user: user }).done(function(data) {
            var args   = JSON.parse(data);
            var errors = args[0];
            if (errors) {
                $("#chart-section").text(errors);
            } else {
                CreateBarChart(args[1], args[2], args[3]);
            }
        });
    });
});

