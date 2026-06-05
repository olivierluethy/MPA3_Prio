/*
 * In-page Create / Edit Task modal.
 * Reuses the existing POST endpoints (add_task / edit_task?id=...) so all
 * backend logic, routes, encryption and save behaviour are unchanged.
 * Handles open/close, populating from data-* attributes, CKEditor, validation
 * (mirrors validationAddTask.js) and keyboard accessibility.
 */
(function () {
    "use strict";

    var modal = document.getElementById("taskModal");
    if (!modal) return;

    var form = document.getElementById("taskForm");
    var titleEl = document.getElementById("taskModalTitle");
    var submitBtn = document.getElementById("taskSubmit");
    var EDITORS = ["task_description", "task_motivation"];

    function hasCK() { return typeof window.CKEDITOR !== "undefined"; }

    // Lazily create the two CKEditor instances; resolve once both are ready.
    function ensureEditors(onReady) {
        if (!hasCK()) { onReady(); return; }
        var pending = EDITORS.length;
        var done = function () { if (--pending === 0) onReady(); };
        EDITORS.forEach(function (name) {
            if (CKEDITOR.instances[name]) { done(); return; }
            CKEDITOR.replace(name).on("instanceReady", done);
        });
    }

    function setData(name, html) {
        if (hasCK() && CKEDITOR.instances[name]) {
            CKEDITOR.instances[name].setData(html || "");
        } else {
            var el = document.getElementById(name);
            if (el) el.value = html || "";
        }
    }

    function editorValue(name) {
        if (hasCK() && CKEDITOR.instances[name]) return CKEDITOR.instances[name].getData();
        var el = document.getElementById(name);
        return el ? el.value : "";
    }

    function clearWarnings() {
        form.querySelectorAll(".warning").forEach(function (w) { w.remove(); });
    }

    function openModal() {
        clearWarnings();
        document.body.classList.add("overflow-hidden");
        modal.classList.remove("hidden");
        var t = document.getElementById("task_title");
        if (t) setTimeout(function () { t.focus(); }, 50);
    }

    function closeModal() {
        modal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
    }

    // ---- Create ------------------------------------------------------------
    window.openTaskModal = function () {
        form.setAttribute("action", "add_task");
        titleEl.textContent = "Add Task";
        submitBtn.textContent = "Add task";
        document.getElementById("task_title").value = "";
        document.getElementById("task_deadline").value = "";
        var prio = document.getElementById("task_priority");
        if (prio.options.length) prio.selectedIndex = 0;
        openModal();
        ensureEditors(function () {
            setData("task_description", "");
            setData("task_motivation", "");
        });
    };

    // ---- Edit (data-driven, reusable from any page e.g. the Calendar) ------
    window.openTaskModalEdit = function (d) {
        form.setAttribute("action", "edit_task?id=" + encodeURIComponent(d.id));
        titleEl.textContent = "Edit Task";
        submitBtn.textContent = "Save changes";
        document.getElementById("task_title").value = d.title || "";
        document.getElementById("task_deadline").value = d.deadline || "";
        var prio = document.getElementById("task_priority");
        if (d.priority) prio.value = d.priority;
        openModal();
        ensureEditors(function () {
            setData("task_description", d.description || "");
            setData("task_motivation", d.motivation || "");
        });
    };
    window.openEditTask = function (btn) { window.openTaskModalEdit(btn.dataset); };

    // ---- Close interactions ------------------------------------------------
    modal.querySelectorAll("[data-task-close]").forEach(function (b) {
        b.addEventListener("click", closeModal);
    });
    modal.addEventListener("click", function (e) {
        if (e.target === modal) closeModal(); // click on backdrop
    });
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && !modal.classList.contains("hidden")) closeModal();
    });

    // ---- Validation (mirrors validationAddTask.js) -------------------------
    function warn(fieldId, msg) {
        var el = document.getElementById(fieldId);
        var wrap = el.closest("div") || el.parentNode;
        var label = document.createElement("label");
        label.className = "warning";
        label.textContent = msg;
        wrap.appendChild(label);
    }

    form.addEventListener("submit", function (evt) {
        evt.preventDefault();
        clearWarnings();
        var errors = false;

        var title = document.getElementById("task_title").value.trim();
        var desc = editorValue("task_description");
        var motiv = editorValue("task_motivation");
        var deadline = document.getElementById("task_deadline").value.trim();
        var prio = document.getElementById("task_priority").value;

        if (title === "") { warn("task_title", "Please enter a title!"); errors = true; }

        if (desc.trim() === "") { warn("task_description", "Please enter a description!"); errors = true; }
        else if (desc.length < 60) { warn("task_description", "Please enter at least 60 characters!"); errors = true; }

        if (motiv.trim() === "") { warn("task_motivation", "Please enter a motivation!"); errors = true; }
        else if (motiv.length < 60) { warn("task_motivation", "Please enter at least 60 characters!"); errors = true; }

        if (deadline === "") { warn("task_deadline", "Please enter a deadline!"); errors = true; }

        if (prio === "") { warn("task_priority", "Please enter a priority!"); errors = true; }
        else if (parseInt(prio, 10) <= 0) { warn("task_priority", "Please enter a priority higher than 0!"); errors = true; }

        if (errors) {
            return;
        }
        // Make sure CKEditor content is written back to the textareas before POST.
        if (hasCK()) {
            EDITORS.forEach(function (name) {
                if (CKEDITOR.instances[name]) CKEDITOR.instances[name].updateElement();
            });
        }

        // Submit via AJAX so the calendar (and anywhere else) can save without a
        // full-page redirect. The controller answers JSON for XHR; the standalone
        // add_task/edit_task pages (plain POST) keep their original redirect flow.
        if (submitBtn) submitBtn.disabled = true;
        fetch(form.getAttribute("action"), {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: new FormData(form),
            credentials: "same-origin"
        })
            .then(function (r) { return r.json().catch(function () { return { ok: r.ok }; }); })
            .then(function (d) {
                if (submitBtn) submitBtn.disabled = false;
                if (!d || !d.ok) {
                    warn("task_priority", (d && d.error) ? d.error : "Could not save the task.");
                    return;
                }
                closeModal();
                document.dispatchEvent(new CustomEvent("prio:taskChanged"));
            })
            .catch(function () {
                if (submitBtn) submitBtn.disabled = false;
                warn("task_priority", "Could not save the task. Please try again.");
            });
    });
})();
