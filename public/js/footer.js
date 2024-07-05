document.addEventListener("DOMContentLoaded", function() {
    var body = document.body;
    var footer = document.querySelector("footer");

    function adjustFooterPosition() {
        var windowHeight = window.innerHeight;
        var windowWidth = window.innerWidth;
        var bodyHeight = body.scrollHeight;

        // Anpassung der Position des Footers basierend auf der Fensterhöhe und -breite
        if (windowHeight >= bodyHeight) {
            footer.style.position = "fixed";
            footer.style.bottom = "0"; // Footer unten fixieren
            footer.style.width = windowWidth + "px"; // Breite des Footers an Fensterbreite anpassen
        } else {
            footer.style.position = "static";
        }
    }

    // Initiale Anpassung der Position des Footers
    adjustFooterPosition();

    // Anpassung der Position des Footers bei Änderung der Fenstergröße
    window.addEventListener("resize", adjustFooterPosition);
});
