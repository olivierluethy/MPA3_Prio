/*
 * Calendar view (FullCalendar v6 global bundle).
 * - Month + week (time-grid) views; dark theme via app.css overrides.
 * - Tasks (by deadline) and time reports (by date) come from /calendar_events.
 * - Drag to reschedule: optimistic move, persisted via the thin update
 *   endpoints; on failure FullCalendar's info.revert() rolls it back + toast.
 * - Click reuses the existing task / time-record edit modals.
 * Dates are all-day (YYYY-MM-DD) and timeZone:'local' — matches the app's
 * naive (UTC) storage with no conversion, so there is no timezone drift.
 */
(function () {
    "use strict";

    var el = document.getElementById("calendar");
    if (!el || typeof FullCalendar === "undefined") return;

    function showToast(message, kind) {
        var host = document.getElementById("toastHost");
        if (!host) {
            host = document.createElement("div");
            host.id = "toastHost";
            host.className = "fixed bottom-5 right-5 z-[1100] flex flex-col gap-2";
            document.body.appendChild(host);
        }
        var t = document.createElement("div");
        t.className = "toast " + (kind === "error" ? "toast-error" : "toast-success");
        t.setAttribute("role", "status");
        t.textContent = message;
        host.appendChild(t);
        setTimeout(function () {
            t.classList.add("toast-hide");
            setTimeout(function () { t.remove(); }, 300);
        }, 3200);
    }

    function persist(url, params, info) {
        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(params).toString(),
            credentials: "same-origin"
        })
            .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
            .then(function (d) { if (!d || !d.ok) throw new Error(); showToast("Change saved", "success"); })
            .catch(function () { info.revert(); showToast("Could not save — change reverted", "error"); });
    }

    var calendar = new FullCalendar.Calendar(el, {
        initialView: "dayGridMonth",
        height: "auto",
        firstDay: 1,
        timeZone: "local",
        headerToolbar: { left: "prev,next today", center: "title", right: "dayGridMonth,timeGridWeek" },
        buttonText: { today: "Today", month: "Month", week: "Week" },
        editable: true,
        eventStartEditable: true,
        eventDurationEditable: false,
        dayMaxEvents: true,
        navLinks: true,
        events: "calendar_events",

        eventDrop: function (info) {
            var ep = info.event.extendedProps;
            var newDate = (info.event.startStr || "").slice(0, 10);
            if (!newDate) { info.revert(); return; }
            if (ep.type === "task") {
                persist("update_task_deadline", { id: ep.taskId, deadline: newDate }, info);
            } else if (ep.type === "time") {
                persist("update_time_date", { id: ep.timeId, date: newDate }, info);
            } else {
                info.revert();
            }
        },

        eventClick: function (info) {
            info.jsEvent.preventDefault();
            var ev = info.event, ep = ev.extendedProps;
            if (ep.type === "task" && typeof window.openTaskModalEdit === "function") {
                window.openTaskModalEdit({
                    id: ep.taskId, title: ev.title, description: ep.description,
                    motivation: ep.motivation, priority: ep.priority, deadline: ep.deadline
                });
            } else if (ep.type === "time" && typeof window.openEditTimeData === "function") {
                window.openEditTimeData({ id: ep.timeId, date: ep.date, duration: ep.duration, report: ep.report });
            }
        }
    });

    calendar.render();

    // A time record edited via the reused modal -> refresh the calendar in place.
    document.addEventListener("prio:timeChanged", function () { calendar.refetchEvents(); });
})();
