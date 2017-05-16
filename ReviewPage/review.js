const PHP = "review.php";

$(document).ready(function () {
    $("#insert-form").submit(function (e) {
        e.preventDefault(); // Prevents submission page refresh.
        $.post( PHP,
        {
            user: $("#insert-name").val(),
            show: $("#insert-show").val(),
            text: $("#insert-text").val(),
            rate: document.getElementById("insert-rate").value,
            action: "insert"
        },
            function (data) { $("#insert-result").text(data); }
        );
    });
});