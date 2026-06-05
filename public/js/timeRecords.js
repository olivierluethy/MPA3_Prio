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

    // ---- Open ---------------------------------------------------------------
    window.openEditTime = function (btn) {
        var d = btn.dataset;
        clearWarnings(editForm);
        editForm.setAttribute("data-id", d.id);
        document.getElementById("editTime_date").textContent = d.date || "—";
        document.getElementById("editTime_report").value = d.report || "";
        document.getElementById("editTime_duration").value = d.duration || "";
        openModal(editModal);
    };
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
    }

    // ---- Save (edit) --------------------------------------------------------
    if (editForm) {
        editForm.addEventListener("submit", function (e) {
            e.preventDefault();
            clearWarnings(editForm);
            var report = document.getElementById("editTime_report");
            var duration = document.getElementById("editTime_duration");
            var errors = false;
            if (report.value.trim() === "") { warn(report, "Please enter a report!"); errors = true; }
            if (duration.value.trim() === "") { warn(duration, "Please enter a time!"); errors = true; }
            if (errors) return;

            var id = editForm.getAttribute("data-id");
            var body = new URLSearchParams();
            body.set("rapport", report.value);
            body.set("time", duration.value);

            fetch("edit_time?id=" + encodeURIComponent(id), {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: body.toString(),
                credentials: "same-origin"
            })
                .then(function (r) { return r.text(); })
                .then(function (html) { refreshCards(html); closeModal(editModal); })
                .catch(function () { warn(report, "Could not save — please try again."); });
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
