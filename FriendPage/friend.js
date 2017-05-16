const PHP = "friend.php";

function SetSlideToggle(button, target) {
    $(button).click(function () {
        $(target).slideToggle("slow");
    });
}

$(document).ready(function () {
    SetSlideToggle("#insert-title",      "#insert-panel");
    SetSlideToggle("#delete-title",      "#delete-panel");
    SetSlideToggle("#edit-title",        "#edit-panel");
    SetSlideToggle("#search-title",      "#search-panel");
     
    $("#insert-form").submit(function (e) {
        e.preventDefault(); // Prevents submisssion page refresh
        $.post( PHP,
                { name: $("#insert-name").val(), action: "insert", friend: $("#insert-friend").val() },
                function (data) { $("#insert-result").text(data); }
        );
    });
    
    $("#delete-form").submit(function (e) {
        e.preventDefault(); // Prevents submisssion page refresh
        $.post( PHP,
                { name: $("#delete-name").val(), action: "delete", friend: $("#delete-friend").val() },
                function (data) { $("#delete-result").text(data); }
        );
    });
    
    $("#search-form").submit(function (e) {
        e.preventDefault(); // Prevents submisssion page refresh
        $.post( PHP,
                { term: $("#search-term").val(), action: "search" },
                function (data) { $("#search-result").text(data); }
        );
    });
});