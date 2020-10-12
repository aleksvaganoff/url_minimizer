document.addEventListener("DOMContentLoaded", function () {
    function copyToClipboard(elem, balloonElem) {
        var copyText = document.querySelector(elem);
        copyText.select();
        copyText.setSelectionRange(0, 99999);

        document.execCommand("copy");
        if (balloonElem) {
            var balloon = document.querySelector(balloonElem);
            balloon.style.display = 'block';
            setTimeout(function () {
                balloon.style.display = 'none';
            }, 3000);
        }
    }

    document.getElementById('copy_button').addEventListener('click', function () {
        copyToClipboard('#shorten_url', '#balloon');
    });
});