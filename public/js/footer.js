document.addEventListener("DOMContentLoaded", function() {
    var body = document.body;
    var footer = document.querySelector("footer");

    function adjustFooterPosition() {
        var windowHeight = window.innerHeight;
        var bodyHeight = body.scrollHeight;

        // Überprüfe, ob die gesamte Seite vollständig geladen ist
        if (windowHeight >= bodyHeight) {
            footer.style.position = "fixed";
        } else {
            footer.style.position = "static";
        }
    }

    // Initial adjustment of footer position
    adjustFooterPosition();

    // Adjust footer position on window resize
    window.addEventListener("resize", adjustFooterPosition);
});
