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
