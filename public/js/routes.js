/* Um das aktuelle Jahreszahl erhalten, um es dann auf den Footer anzuzeigen */
let currentYear = new Date().getFullYear();
document.getElementById('year').innerHTML = '&copy; ' + currentYear + ' Prio. All rights Reserved.';

/* Um zur Route zu gelangen um auf die Startseite zu gelangen */
function home() {
    location.href = "home";
}
/* Um zur Route zu gelangen um zur Loginseite zu gelangen */
function goToLogin() {
    location.href = "login";
}
/* Um zur Route zu gelangen um zum Logout zu gelangen */
function zuLogout() {
    location.href = "logout";
}

function write_essay() {
    document.getElementById("essay").style.display = "block";
    document.getElementById("showEssayField").style.display = "none";
}

function aufgaben() {
    location.href = "home";
}

function showEssay(id) {
    window.open("essay?id=" + id, '_blank');
}

function addTask() {
    location.href = "add_task";
}

function accept(essayId, userId) {
    location.href = "accept?essayId=" + essayId + "&userId=" + userId;
}

function refuse(essayId, userId) {
    location.href = "refuse?essayId=" + essayId + "&userId=" + userId;
}

function completeTask(id) {
    location.href = "complete_task?id=" + id;
}

function higherPrio(id) {
    location.href = "higherPrio?id=" + id;
}

function zeiterfassung() {
    location.href = "zeituebersicht";
}

function lowerPrio(id) {
    location.href = "lowerPrio?id=" + id;
}

function deleteTask(id) {
    location.href = "delete_task?id=" + id;
}

function editTask(id) {
    location.href = "edit_task?id=" + id;
}

function editTime(id) {
    location.href = "edit_time?id=" + id;
}

function deleteTime(id) {
    location.href = "delete_time?id=" + id;
}