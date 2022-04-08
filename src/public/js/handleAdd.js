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
                    alert("added")
                }
            }
        });
    }
    else {
        alert("abe playlist to select kr")
    }
});

$(".pl").click(function (e) {
    e.preventDefault();
    $('.pl').removeClass("bg-neutral-900")
    $(this).addClass("bg-neutral-900")
    currentPl = $(this).attr("data")
});

