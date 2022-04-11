var currentPl = ""
$(".addtopl").click(function (e) {
    var id = $(this).attr("data").trim()
    e.preventDefault();
    if (currentPl.trim()) {
        $.ajax({
            type: "POST",
            url: "/index/addPlaylist",
            data: { plId: currentPl, itemId: id },
            success: function (response) {
                console.log(response)
                if (response == '200') {
                    addNotification("song has been added")
                }
            }
        });
    }
    else {
        addNotification("abe playlist to select kr")
    }
});

$(".pl").click(function (e) {
    e.preventDefault();
    $('.pl').removeClass("bg-green-200")
    $(this).addClass("bg-green-200")
    currentPl = $(this).attr("data")
});

