/*
 * About page — premium parallax + scroll reveal.
 * Vanilla, performant (IntersectionObserver + requestAnimationFrame),
 * and respects prefers-reduced-motion. Replaces the old jQuery parallax.js.
 */
(function () {
    "use strict";

    var reduce = window.matchMedia &&
                 window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    /* --- Scroll reveal ---------------------------------------------------- */
    var revealEls = document.querySelectorAll("[data-reveal]");
    if (revealEls.length) {
        if (reduce || !("IntersectionObserver" in window)) {
            revealEls.forEach(function (el) { el.classList.add("is-visible"); });
        } else {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15, rootMargin: "0px 0px -10% 0px" });
            revealEls.forEach(function (el) { io.observe(el); });
        }
    }

    /* --- Multi-layer parallax -------------------------------------------- */
    var layers = Array.prototype.slice.call(document.querySelectorAll("[data-parallax]"));
    if (layers.length && !reduce) {
        var ticking = false;

        var update = function () {
            var y = window.scrollY || window.pageYOffset;
            for (var i = 0; i < layers.length; i++) {
                var speed = parseFloat(layers[i].getAttribute("data-parallax")) || 0.2;
                layers[i].style.transform = "translate3d(0," + (y * speed).toFixed(2) + "px,0)";
            }
            ticking = false;
        };

        var onScroll = function () {
            if (!ticking) {
                window.requestAnimationFrame(update);
                ticking = true;
            }
        };

        window.addEventListener("scroll", onScroll, { passive: true });
        update();
    }
})();
