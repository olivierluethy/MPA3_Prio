/*
 * In-place edit / delete for time records on the Time overview page.
 * Reuses the existing endpoints (edit_time POST, delete_time) via fetch and
 * swaps the #timeCards container with the freshly server-rendered markup, so
 * durations/totals stay server-formatted and there is NO page navigation.
 * Accessible: focus trap, Esc, click-outside, aria-* (set in the markup).
 */
(function () {
    "use strict";

    var editModal = document.getElementById("editTimeModal");
    var deleteModal = document.getElementById("deleteTimeModal");
    if (!editModal && !deleteModal) return;

    var editForm = document.getElementById("editTimeForm");
    var confirmDelete = document.getElementById("confirmDeleteTime");
    var pendingDeleteId = null;
    var lastFocused = null;

    function anyOpen() {
        return (editModal && !editModal.classList.contains("hidden")) ||
               (deleteModal && !deleteModal.classList.contains("hidden"));
    }
    function openModal(modal) {
        lastFocused = document.activeElement;
        document.body.classList.add("overflow-hidden");
        modal.classList.remove("hidden");
        var focusable = modal.querySelector("input:not([type=hidden]), button, [tabindex]");
        if (focusable) setTimeout(function () { focusable.focus(); }, 30);
    }
    function closeModal(modal) {
        modal.classList.add("hidden");
        if (!anyOpen()) document.body.classList.remove("overflow-hidden");
        if (lastFocused && typeof lastFocused.focus === "function") lastFocused.focus();
    }

    function clearWarnings(form) {
        form.querySelectorAll(".warning").forEach(function (w) { w.remove(); });
    }
    function warn(el, msg) {
        var label = document.createElement("label");
        label.className = "warning";
        label.textContent = msg;
        (el.closest("div") || el.parentNode).appendChild(label);
    }

    function hmsToSec(str) {
        if (!str) return 0;
        var p = String(str).split(":");
        return (parseInt(p[0], 10) || 0) * 3600 + (parseInt(p[1], 10) || 0) * 60 + (parseInt(p[2], 10) || 0);
    }

    // ---- Open (data-driven, reusable from any page e.g. the Calendar) -------
    window.openEditTimeData = function (d) {
        clearWarnings(editForm);
        editForm.setAttribute("data-id", d.id);
        editForm.setAttribute("data-date-iso", d.dateIso || "");
        document.getElementById("editTime_date").textContent = d.date || "—";
        document.getElementById("editTime_report").value = d.report || "";
        var ed = document.getElementById("editTimeDuration");
        if (ed && ed.durationEditor) {
            if (d.start && d.end) ed.durationEditor.setWindow(d.start, d.end);
            else ed.durationEditor.setDuration(hmsToSec(d.duration));
        }
        openModal(editModal);
    };
    window.openEditTime = function (btn) { window.openEditTimeData(btn.dataset); };
    window.openDeleteTime = function (btn) {
        pendingDeleteId = btn.dataset.id;
        openModal(deleteModal);
    };

    // ---- Close interactions -------------------------------------------------
    [editModal, deleteModal].forEach(function (m) {
        if (!m) return;
        m.querySelectorAll("[data-time-close]").forEach(function (b) {
            b.addEventListener("click", function () { closeModal(m); });
        });
        m.addEventListener("click", function (e) { if (e.target === m) closeModal(m); });
    });
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            if (editModal && !editModal.classList.contains("hidden")) closeModal(editModal);
            if (deleteModal && !deleteModal.classList.contains("hidden")) closeModal(deleteModal);
        } else if (e.key === "Tab") {
            trapFocus(e);
        }
    });
    function trapFocus(e) {
        var modal = (editModal && !editModal.classList.contains("hidden")) ? editModal
                  : (deleteModal && !deleteModal.classList.contains("hidden")) ? deleteModal : null;
        if (!modal) return;
        var f = modal.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]):not([type=hidden]), [tabindex]:not([tabindex="-1"])');
        if (!f.length) return;
        var first = f[0], last = f[f.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }

    // ---- Re-render the cards in place ---------------------------------------
    function refreshCards(html) {
        var doc = new DOMParser().parseFromString(html, "text/html");
        var fresh = doc.getElementById("timeCards");
        var current = document.getElementById("timeCards");
        if (fresh && current) {
            current.replaceWith(fresh);
            var input = document.getElementById("myInput");
            if (input && input.value.trim() !== "" && typeof searchFor === "function") searchFor();
        }
        // Let other pages (e.g. the Calendar) react to a time-record change.
        document.dispatchEvent(new CustomEvent("prio:timeChanged"));
    }

    // After a save, refresh the cards in place (Time overview) or notify the
    // calendar; works for both the duration (edit_time) and window
    // (update_time_slot) paths.
    function afterSave() {
        closeModal(editModal);
        if (document.getElementById("timeCards")) {
            fetch("zeituebersicht", { credentials: "same-origin" })
                .then(function (r) { return r.text(); })
                .then(refreshCards);
        } else {
            document.dispatchEvent(new CustomEvent("prio:timeChanged"));
        }
    }

    // ---- Save (edit) --------------------------------------------------------
    if (editForm) {
        editForm.addEventListener("submit", function (e) {
            e.preventDefault();
            clearWarnings(editForm);
            var report = document.getElementById("editTime_report");
            var ed = document.getElementById("editTimeDuration");
            var state = (ed && ed.durationEditor) ? ed.durationEditor.getState() : { seconds: 0, hhmmss: "00:00:00", hasWindow: false };
            var errors = false;
            if (report.value.trim() === "") { warn(report, "Please enter a report!"); errors = true; }
            if (state.seconds <= 0 && !state.hasWindow) { warn(ed || report, "Please enter a duration!"); errors = true; }
            if (errors) return;

            var id = editForm.getAttribute("data-id");
            var url, body = new URLSearchParams();
            if (state.hasWindow) {
                // window mode -> start/end (duration derived) via update_time_slot
                url = "update_time_slot";
                body.set("id", id);
                body.set("date", editForm.getAttribute("data-date-iso") || "");
                body.set("start", state.start);
                body.set("end", state.end);
                body.set("rapport", report.value);
            } else {
                // duration-only mode -> edit_time (clears any window server-side)
                url = "edit_time?id=" + encodeURIComponent(id);
                body.set("rapport", report.value);
                body.set("time", state.hhmmss);
            }

            fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded", "X-Requested-With": "XMLHttpRequest" },
                body: body.toString(),
                credentials: "same-origin"
            })
                .then(function (r) { if (!r.ok) throw new Error(); afterSave(); })
                .catch(function () { warn(ed || report, "Could not save — please try again."); });
        });
    }

    // ---- Confirm delete -----------------------------------------------------
    if (confirmDelete) {
        confirmDelete.addEventListener("click", function () {
            if (!pendingDeleteId) return;
            fetch("delete_time?id=" + encodeURIComponent(pendingDeleteId), {
                method: "GET",
                credentials: "same-origin"
            })
                .then(function (r) { return r.text(); })
                .then(function (html) { refreshCards(html); closeModal(deleteModal); pendingDeleteId = null; })
                .catch(function () { closeModal(deleteModal); });
        });
    }
})();
