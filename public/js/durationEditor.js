/*
 * Shared DURATION editor (not a time-of-day input).
 * - Numeric h/m/s inputs are the SOURCE OF TRUTH; value = integer seconds,
 *   emitted as HH:MM:SS (hidden field) to match the existing model/endpoints.
 * - Optional dual-handle window (two native range inputs = keyboard-accessible)
 *   binds bidirectionally to the numbers: dragging sets start/end -> duration;
 *   typing keeps the start and moves the end. The window auto-scales around the
 *   entry so short entries are still adjustable at second precision.
 * Each editor element exposes `el.durationEditor = { getState, setDuration,
 * setWindow, reset }`.
 */
(function () {
    "use strict";

    function fmtDur(sec) {
        sec = Math.max(0, sec | 0);
        var h = Math.floor(sec / 3600), m = Math.floor((sec % 3600) / 60), s = sec % 60, p = [];
        if (h) p.push(h + "h");
        if (m) p.push(m + "m");
        if (s) p.push(s + "s");
        return p.length ? p.join(" ") : "0s";
    }
    function pad(n) { return (n < 10 ? "0" : "") + n; }
    function hms(sec) { sec = Math.max(0, sec | 0); return pad(Math.floor(sec / 3600)) + ":" + pad(Math.floor((sec % 3600) / 60)) + ":" + pad(sec % 60); }
    function clockLabel(sec) { sec = Math.max(0, Math.min(86399, sec | 0)); return hms(sec); }
    function parseHMS(str) {
        if (!str) return 0;
        var p = String(str).split(":");
        return (parseInt(p[0], 10) || 0) * 3600 + (parseInt(p[1], 10) || 0) * 60 + (parseInt(p[2], 10) || 0);
    }

    function initEditor(root) {
        if (root.durationEditor) return root.durationEditor;

        var hEl = root.querySelector('[data-dur="h"]');
        var mEl = root.querySelector('[data-dur="m"]');
        var sEl = root.querySelector('[data-dur="s"]');
        var totalEl = root.querySelector("[data-dur-total]");
        var errEl = root.querySelector("[data-dur-error]");
        var outEl = root.querySelector('[data-out="hhmmss"]');

        var hasWindowUI = root.getAttribute("data-window") === "1";
        var startRange = root.querySelector('[data-range="start"]');
        var endRange = root.querySelector('[data-range="end"]');
        var fillEl = root.querySelector("[data-fill]");
        var startLabel = root.querySelector("[data-start-label]");
        var endLabel = root.querySelector("[data-end-label]");
        var total2El = root.querySelector("[data-dur-total-2]");
        var windowBody = root.querySelector("[data-window-body]");
        var windowToggle = root.querySelector("[data-window-toggle]");
        var windowToggleLabel = root.querySelector("[data-window-toggle-label]");
        var windowClear = root.querySelector("[data-window-clear]");

        var windowActive = false;
        var winMin = 0, winMax = 86399;
        var defaultStart = 9 * 3600;

        function clampNum(el, max) {
            var v = parseInt(el.value, 10);
            if (isNaN(v) || v < 0) v = 0;
            if (v > max) v = max;
            return v;
        }
        function numericSeconds() { return clampNum(hEl, 23) * 3600 + clampNum(mEl, 59) * 60 + clampNum(sEl, 59); }
        function writeNumeric(sec) {
            sec = Math.max(0, Math.min(23 * 3600 + 59 * 60 + 59, sec | 0));
            hEl.value = Math.floor(sec / 3600);
            mEl.value = Math.floor((sec % 3600) / 60);
            sEl.value = sec % 60;
        }
        function showError(msg) { if (!errEl) return; if (msg) { errEl.textContent = msg; errEl.style.display = ""; } else { errEl.style.display = "none"; } }

        function paintWindow() {
            if (!hasWindowUI || !startRange) return;
            var a = parseInt(startRange.value, 10), b = parseInt(endRange.value, 10), span = (winMax - winMin) || 1;
            fillEl.style.left = (((a - winMin) / span) * 100) + "%";
            fillEl.style.width = Math.max(0, (((b - a) / span) * 100)) + "%";
            startLabel.textContent = clockLabel(a);
            endLabel.textContent = clockLabel(b);
            startRange.setAttribute("aria-valuetext", clockLabel(a));
            endRange.setAttribute("aria-valuetext", clockLabel(b));
            if (total2El) total2El.textContent = fmtDur(b - a);
        }
        function setWindowBounds(startSec, endSec) {
            var dur = Math.max(0, endSec - startSec), padSec = Math.max(300, dur);
            winMin = Math.max(0, startSec - padSec);
            winMax = Math.min(86399, endSec + padSec);
            if (winMax - winMin < 600) winMax = Math.min(86399, winMin + 600);
            [startRange, endRange].forEach(function (r) { r.min = winMin; r.max = winMax; r.step = 1; });
            startRange.value = startSec;
            endRange.value = Math.min(endSec, winMax);
            paintWindow();
        }
        function refreshTotal() {
            writeNumeric(numericSeconds());
            var sec = numericSeconds();
            if (totalEl) totalEl.textContent = fmtDur(sec);
            if (outEl) outEl.value = hms(sec);
            showError("");
            return sec;
        }

        function onNumericInput() {
            var sec = refreshTotal();
            if (windowActive && startRange) {
                var start = parseInt(startRange.value, 10), end = start + sec;
                if (end > winMax || end < winMin) setWindowBounds(start, end);
                else { endRange.value = end; paintWindow(); }
            }
        }
        [hEl, mEl, sEl].forEach(function (el) { el.addEventListener("input", onNumericInput); el.addEventListener("change", onNumericInput); });

        if (hasWindowUI) {
            function onRange(which) {
                var a = parseInt(startRange.value, 10), b = parseInt(endRange.value, 10);
                if (a > b) { if (which === "start") b = a; else a = b; startRange.value = a; endRange.value = b; }
                var sec = b - a;
                writeNumeric(sec);
                if (totalEl) totalEl.textContent = fmtDur(sec);
                if (outEl) outEl.value = hms(sec);
                showError("");
                paintWindow();
            }
            startRange.addEventListener("input", function () { onRange("start"); });
            endRange.addEventListener("input", function () { onRange("end"); });

            function openWindow(startSec, endSec) {
                windowActive = true;
                windowBody.style.display = "";
                if (windowToggleLabel) windowToggleLabel.textContent = "Editing start / end window";
                setWindowBounds(startSec, endSec);
                writeNumeric(endSec - startSec);
                refreshTotal();
            }
            windowToggle.addEventListener("click", function () {
                if (windowActive) { windowBody.style.display = (windowBody.style.display === "none") ? "" : "none"; return; }
                var dur = numericSeconds(), start = defaultStart, end = Math.min(86399, start + dur);
                openWindow(start, end);
            });
            windowClear.addEventListener("click", function () {
                windowActive = false;
                windowBody.style.display = "none";
                if (windowToggleLabel) windowToggleLabel.textContent = "Set a start / end window";
                refreshTotal();
            });
        }

        var api = {
            getState: function () {
                var sec = refreshTotal();
                var st = { seconds: sec, hhmmss: hms(sec), hasWindow: false, start: null, end: null };
                if (windowActive && startRange) {
                    st.hasWindow = true;
                    st.start = clockLabel(parseInt(startRange.value, 10));
                    st.end = clockLabel(parseInt(endRange.value, 10));
                }
                return st;
            },
            setDuration: function (sec) {
                if (hasWindowUI) {
                    windowActive = false;
                    if (windowBody) windowBody.style.display = "none";
                    if (windowToggleLabel) windowToggleLabel.textContent = "Set a start / end window";
                }
                writeNumeric(sec || 0);
                refreshTotal();
            },
            setWindow: function (startHMS, endHMS) {
                var startSec = parseHMS(startHMS), endSec = parseHMS(endHMS);
                if (!hasWindowUI) { this.setDuration(endSec - startSec); return; }
                windowActive = true;
                windowBody.style.display = "";
                if (windowToggleLabel) windowToggleLabel.textContent = "Editing start / end window";
                setWindowBounds(startSec, endSec);
                writeNumeric(endSec - startSec);
                refreshTotal();
            },
            reset: function () { this.setDuration(0); }
        };
        root.durationEditor = api;
        api.setDuration(parseInt(root.getAttribute("data-initial-seconds"), 10) || 0);
        return api;
    }

    function initAll() {
        document.querySelectorAll("[data-duration-editor]").forEach(function (el) { initEditor(el); });
    }
    window.initDurationEditors = initAll;
    if (document.readyState !== "loading") initAll();
    else document.addEventListener("DOMContentLoaded", initAll);
})();
