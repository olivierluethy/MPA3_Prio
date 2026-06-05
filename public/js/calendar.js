/*
 * Calendar view (FullCalendar v6 global bundle).
 * - Month (all-day) + Week (time-grid) views; dark theme via app.css overrides.
 * - Tasks: all-day on deadline (drag = reschedule).
 * - Time reports WITH start/end: timed events in the week grid (drag = move,
 *   keep duration; resize = change duration). WITHOUT: all-day pills.
 * - Drag/resize is optimistic; on API failure FullCalendar's info.revert()
 *   rolls it back and an in-theme toast is shown.
 * Dates are naive (timeZone:'local', no offsets) -> matches the app's naive
 * UTC storage with no conversion, so there is no timezone drift.
 */
(function () {
    "use strict";

    var el = document.getElementById("calendar");
    if (!el || typeof FullCalendar === "undefined") return;

    function pad(n) { return (n < 10 ? "0" : "") + n; }
    function fmtDate(d) { return d.getFullYear() + "-" + pad(d.getMonth() + 1) + "-" + pad(d.getDate()); }
    function fmtTime(d) { return pad(d.getHours()) + ":" + pad(d.getMinutes()) + ":" + pad(d.getSeconds()); }

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

    var calendar;

    function persist(url, params, info, refetch) {
        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(params).toString(),
            credentials: "same-origin"
        })
            .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
            .then(function (d) {
                if (!d || !d.ok) throw new Error();
                showToast("Change saved", "success");
                if (refetch && calendar) calendar.refetchEvents();
            })
            .catch(function () { info.revert(); showToast("Could not save — change reverted", "error"); });
    }

    calendar = new FullCalendar.Calendar(el, {
        initialView: "dayGridMonth",
        height: "auto",
        firstDay: 1,
        timeZone: "local",
        nowIndicator: true,
        scrollTime: "07:00:00",
        headerToolbar: { left: "prev,next today", center: "title", right: "dayGridMonth,timeGridWeek" },
        buttonText: { today: "Today", month: "Month", week: "Week" },
        editable: true,
        eventStartEditable: true,
        eventDurationEditable: true, // gated per-event via durationEditable from the feed
        dayMaxEvents: true,
        navLinks: true,
        events: "calendar_events",

        eventDrop: function (info) {
            var ev = info.event, ep = ev.extendedProps;
            if (ep.type === "task") {
                persist("update_task_deadline", { id: ep.taskId, deadline: (ev.startStr || "").slice(0, 10) }, info);
            } else if (ep.type === "time") {
                if (ev.allDay) {
                    // moved within the all-day lane / month -> date only (keeps time-of-day)
                    persist("update_time_date", { id: ep.timeId, date: (ev.startStr || "").slice(0, 10) }, info);
                } else if (ev.start) {
                    // time-grid: dragging sets the start; keep the existing duration
                    var durSec = ep.durationSeconds || (ev.end ? (ev.end - ev.start) / 1000 : 0);
                    var end = new Date(ev.start.getTime() + durSec * 1000);
                    persist("update_time_slot", {
                        id: ep.timeId, date: fmtDate(ev.start), start: fmtTime(ev.start), end: fmtTime(end)
                    }, info, true);
                } else {
                    info.revert();
                }
            } else {
                info.revert();
            }
        },

        eventResize: function (info) {
            var ev = info.event, ep = ev.extendedProps;
            if (ep.type === "time" && !ev.allDay && ev.start && ev.end) {
                persist("update_time_slot", {
                    id: ep.timeId, date: fmtDate(ev.start), start: fmtTime(ev.start), end: fmtTime(ev.end)
                }, info, true);
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
                var st = (!ev.allDay && ev.start) ? fmtTime(ev.start) : null;
                var en = (!ev.allDay && ev.end) ? fmtTime(ev.end) : null;
                window.openEditTimeData({
                    id: ep.timeId,
                    date: ep.dateLabel || ep.date,
                    dateIso: ep.date,
                    duration: ep.duration,
                    report: ep.report,
                    start: st,
                    end: en
                });
            }
        }
    });

    calendar.render();

    // Edits via the reused modals -> refresh the calendar in place.
    document.addEventListener("prio:timeChanged", function () { calendar.refetchEvents(); });
    document.addEventListener("prio:taskChanged", function () { calendar.refetchEvents(); });
})();
