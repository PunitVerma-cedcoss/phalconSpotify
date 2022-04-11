$(".open-filter").click(function (e) {
    e.preventDefault();
    $(".filter").toggleClass("hidden")
});

//handle close notification
$("body").on("click", ".close-noti", function (e) {
    e.preventDefault();
    $(this).parent().fadeOut()
});

function addNotification(msg) {
    var m = `
    <div class="noti flex m-2">
        <div class="n-1  bg-white flex items-center w-60 bg-gradient-to-r from-green-900 to-cyan-900 rounded-l-lg text-gray-200">
            <ion-icon name="alert-circle" class="text-4xl p-1"></ion-icon>
            <p class="text-sm text-gray-300">${msg}</p>
        </div>
        <div class="n-2 close-noti  bg-gradient-to-l from-green-900 border-l to-cyan-900 bg-rose-500 text-gray-400 hover:text-gray-300 cursor-pointer h-100 flex items-center justify-center p-3 rounded-r-lg">
            <ion-icon name="close" class=" text-2xl text-gray-200"></ion-icon>
        </div>
    </div>
    `
    $(".notifications").prepend(m)
}


// handle expand
$(".expand").click(function (e) {
    e.preventDefault();
    // $(this).children().attr("name", "chevron-down")
    var plid = $(this).parent().attr("data")
    // send ajax to fetch current pl
    if (plid.trim()) {
        fetchPlayList(plid)
    }
    else {
        addNotification("playlist id is invalid")
    }
    console.log("expanding")
    $(".backdrop").fadeIn()

});

// handle modaal
$(".backdrop").click(function (e) {
    e.preventDefault();
    if (e.target != this)
        return
    e.stopPropagation();
    $(this).fadeOut("slow", "linear")
    setTimeout(() => {
        $(".modaal-child").html(`
        <div class="p-3 flex justify-center items-center">
        <button type="button" class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow-lg rounded-md text-white bg-green-500 hover:bg-indigo-400 transition ease-in-out duration-150 cursor-not-allowed" disabled="">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Fetching Playlist...
        </button>
    </div>
    `)
    }, 1000);
});

function fetchPlayList(plid) {
    $.ajax({
        type: "POST",
        url: "/index/fetchPlaylist",
        data: { plid: plid },
        success: function (response) {
            var response = JSON.parse(response)
            console.log(response)
            $(".modaal-child").empty()
            var m = `
            <div class="modaal-top bg-gray-100 rounded-t-lg flex text-gray-800 justify-between items-center p-3 border-b">
                <img src="${response["images"][0]["url"]}" class="rounded-lg shadow-lg mr-2" style="width:50px;height:50px" />
                    <div class="grow">
                        <p><span class="text-sm font-medium">PlayList</span> : 4958438389r9u (${response["tracks"]["items"].length})</p>
                        <p class="text-xs">Public : <span class="text-green-700">${response["public"]}</span></p>
                        <p class="text-xs">Collaborative : <span class="text-green-700">${response["collaborative"]}</span></p>
                        <p class="text-xs">Description : <span class="text-green-700">${response["description"]}</span></p>
                    </div>
                    <ion-icon name="close-outline" class="cursor-pointer close-modal text-2xl bg-green-500 text-white rounded-md shadow-lg hover:ring-2 hover:ring-offset-2 hover:ring-green-500"></ion-icon>
                </div>
                <div class="modal-body divide-y overflow-auto max-h-80">
            `
            response["tracks"]["items"].forEach(i => {
                // console.log(i)
                console.log(i["track"]["name"])
                m += `
                <div class="tile-pl text-gray-800 flex items-center p-3">
                        <img src="${i["track"]["album"]["images"][2]["url"]}" class="rounded-lg shadow" alt="">
                        <div class="ml-2 grow">
                            <p class="text-lg">${i["track"]["name"]}</p>
                            <p class="text-sm text-gray-700">${i["track"]["album"]["name"]}</p>
                            <p class="text-xs text-gray-600">${parseTime(i["added_at"])}</p>
                        </div>

                        <ion-icon name="trash" data="${i["track"]["uri"]}" class="delete text-2xl bg-rose-500 p-1 cursor-pointer text-white rounded-md shadow-lg hover:ring-2 hover:ring-offset-2 hover:ring-rose-500"></ion-icon>
                    </div>
                `
            });
            m += `
            </div>
            <div class="modal-footer bg-gray-100 rounded-b-lg p-3 border-t flex justify-end items-center">
                <button class="close-modal text-white bg-rose-500 rounded-md shadow-lg px-2 py-1 focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">Close</button>
                <button class="close-modal ml-2 text-white bg-green-500 rounded-md shadow-lg px-2 py-1 focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Apply</button>
            </div>
            `
            $(".modaal-child").html(m)

        }
    });
}

function parseTime(date) {
    var ms = Date.parse(date)
    return ms
}

// handle delete item from playlist
$("body").on("click", ".delete", function () {
    var id = $(this).attr("data").trim()
    if (id) {
        $(this).parent().fadeOut()
        addNotification("item successfully removed.")
        deleteFromPlaylist(currentPl, id)
    }
});

function deleteFromPlaylist(plid, id) {
    $.ajax({
        type: "POST",
        url: "/index/delteFromPlaylist",
        data: { plid: plid, iid: id },
        success: function (response) {
            console.log(response)
        }
    });
}

// modal close
$("body").on("click", ".close-modal", function () {
    $(".backdrop").fadeOut()
});


// handle player close
$(".close-player").click(function (e) {
    e.preventDefault();
    $(".player").fadeOut()
    $(".open-player").fadeIn()
});

// handle player open
$(".open-player").click(function (e) {
    e.preventDefault();
    $(".player").fadeIn()
    $(".open-player").fadeOut()
});

$('.play-player').click(function (e) {
    e.preventDefault();
    addNotification("please Upgrade to premium")
});