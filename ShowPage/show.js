const PHP = "show.php";

function SetSlideToggle(button, target) {
    $(button).click(function () {
        $(target).slideToggle("slow");
    });
}

$(document).ready(function () {
    SetSlideToggle("#search-title", "#search-panel");
    SetSlideToggle("#watch-title", "#watch-panel");
    
    $("#search-form").submit(function (e) {
        e.preventDefault(); // Prevents submission page refresh
        $.post( PHP,
                { term: $("#search-term").val(), action: "search" },
                function (data) { $("#search-result").html(data); }
        );
    });
    
    $("#watch-form").submit(function (e) {
        e.preventDefault(); // Prevents submission page refresh
        $.post( PHP,
                { user: $("#watch-name").val(), show: $("#watch-show").val(), action: "watch" },
                function (data) { $("#watch-result").html(data); }
        );
    });
});