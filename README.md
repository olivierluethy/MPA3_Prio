# MPA2_Prio
Ein Projekt für die zweite Mini PA.

## Aufgabenstellung
Ziel ist eine Applikation für das Priorisieren von Aufgaben, welche es dem Nutzer ebenfalls erlaubt, seine eingesetzte Zeit für die einzelnen Aufgaben zu erfassem. Des Weiteren kann eine Deadline für eine Aufgabe gesetzt werden, die bei Nichteinhaltung zu einem Mangelpunkt führt. Bei gesammelten 10 Mangel Punkten wird der Account gesperrt und ist nur durch das Einreichen eines 5000 Zeichen langes Essays mit dem Titel “Warum habe ich meine Aufgaben vernachlässigt” freischaltbar, die Validierung des Essays erfolgt durch den Administrator. Beim erfolgreichen Abschliessen vor der Deadline einer Aufgabe wird bei einem positiven Mangelpunktestand ein Punkt abgezogen.
Aufgaben umfassen Titel, Beschreibung (min 60 Zeichen), Motivation (min 60 Zeichen, wieso will man die Aufgabe erreichen), Deadline (optional) und Priorität.
In der Beschreibung und Motivation ist es möglich, mittels Texteditor geordnete und ungeordnete Listen zu erstellen, sowie Zeichenformate wie Bold und Underline einzusetzen.
In einer Listenansicht sind alle nicht abgeschlossenen Aufgaben aufgeführt, von Priorität hoch bis niedrig, durch das Anklicken von Buttons kann die Priorität geändert werden.
Die Liste der Aufgaben ist nach verschiedenen Kriterien sortierbar: Alphabetisch, Priorität, Deadline.
Wird an einer Aufgabe gearbeitet, kann die Zeiterfassung für die Aufgabe aktiviert werden.
Erstellte Zeiteinträge können bearbeitet und gelöscht werden.
Registrierung von Benutzern mit E-Mail, Benutzernamen und Passwort.
Login mit Benutzernamen oder E-Mail und Passwort.
Adminbereich für das Review von Essays und das Freischalten von automatisch gesperrten Accounts aufgrund von 10 Mangelpunkten.

Die Webanwendung wird in PHP realisiert und ist für die mobile Ansicht optimiert. Für die Persistenz der Daten soll eine relationale Datenbank eingesetzt werden.

Die Dokumentation umfasst neben dem Entwurf und der Realisierung einen Testplan. Das Testing des Projekts wird ausschliesslich auf manuelle Integrationstests begrenzt. Die Dokumentation enthält ausserdem eine Benutzeranleitung für Laien und eine Beschreibung der Systemkonfiguration.

Als Vorarbeit wird ein Texteditor gesucht, der die Anforderungen in der Aufgabenstellung erfüllt und eine hohe Benutzerfreundlichkeit bietet, ein allfälliger Build und das Verwenden in einem PHP MVC Projekt werden dokumentiert.

Für die Aufgabe muss ein privates Repo im internen Gitea erstellt werden. Der Name des Repos ist zwingend “MPA2_PROJEKTNAME”. An jedem Tag, an dem an der MPA gearbeitet wird, wird vor Feierabend der aktuelle Stand der Dokumentation an die verantwortliche Fachkraft gesendet.

## Bewertung
Bewertet wird nach dem vollen Umfang des Kriterienkatalogs Teil A und B von der PA 2022 bewertet. Zusätzlich werden wir die Code-Qualität anhand folgender dieser individuellen Kriterien bewertet:
121 - Software Ergonomie
123 - Kommentare
125 - Gliederung des Programms
164 - Fehlerbehandlung
166 - Lesbarer Code
Individuelle Kriterien sind Kriterien, die der Betrieb zusätzlich zu den schon vorhandenen, nicht verhandelbaren, Standardkriterien stellen muss. Diese sind mehr auf die Arbeit zugeschnitten, wobei die Standardkriterien mehr allgemein sind. In der PA wird es noch einen Teil C geben, der die Präsentation bewertet. Dazu werden es sieben individuelle Kriterien sein. Nachfolgend unsere Firmenvorgaben zum Codestyle:
Die Beschriftung erfolgt im üblichen Standard der verwendeten Programmiersprache. Wenn es unklar ist, werden sämtliche Variablen, Funktionen und Methoden in camelCase deklariert, ausgenommen Klassen in PascalCase.
Sämtliche Namen von Variablen, Funktionen, Methoden und Klassen sind so gewählt, dass diese auf ihren Nutzen hinweisen.
Variablen sind zuoberst bei Funktionen und Methoden deklariert.
Der Code ist sinnvoll eingerückt und nicht alles auf einer Linie. Innerhalb des Projekts sind die Einrückungszeichen überall gleich, entweder Tabs oder Spaces.

## Verwendete Quellen
How to get the current date and time in PHP?
https://stackoverflow.com/questions/470617/how-do-i-get-the-current-date-and-time-in-php#:~:text=PHP's%20time()%20returns%20a,format%20it%20to%20your%20needs.&text=the%202nd%20argument%20of%20the,time()%20if%20left%20empty.

How to make a hero page?
https://www.w3schools.com/howto/tryit.asp?filename=tryhow_css_hero_image

How to get tables where the datetime is 24 hours ago?
https://stackoverflow.com/questions/3800735/select-to-table-where-datetime-is-24-hours-ago

How to make a Stopwatch with JavaScript?
https://www.youtube.com/watch?v=49f1cjZWRJA

Source Code:
https://github.com/TylerPottsDev/yt-js-stopwatch

How to get a JavaScript variable value in PHP?
https://stackoverflow.com/questions/9789283/how-to-get-javascript-variable-value-in-php

How to pass and retrieving multiple parameters in JavaScript?
https://stackoverflow.com/questions/16996803/passing-and-retrieving-multiple-parameters-in-jquery-from-one-page-to-another-pa

How to make a circle button in CSS?
https://stackoverflow.com/questions/38320878/circle-button-css

How to add a border radius to a table row?
https://stackoverflow.com/questions/4094126/how-to-add-border-radius-on-table-row

How to make an time input field for hours, minutes and seconds? 
https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/time

How to get the latest datetime in MySQL?
https://stackoverflow.com/questions/3264661/how-to-get-latest-date-and-time-in-php-and-mysql-using-a-select-statement

How to add a target="_black" attribute to JavaScript for window.location?
https://stackoverflow.com/questions/18476373/how-to-add-target-blank-to-javascript-window-location

How to check if date is in the past in PHP?
https://www.itsolutionstuff.com/post/how-to-check-if-date-is-past-date-in-phpexample.html

How to get the Datetime of now with PHP?
https://www.w3schools.com/php/func_date_strtotime.asp

How to sort data in PHP MySQL?
https://www.youtube.com/watch?v=ft-B4DFWUUc

How to check if timestamp is greater than 24 hours from now in PHP?
https://stackoverflow.com/questions/17627058/php-check-if-timestamp-is-greater-than-24-hours-from-now

How to change CSS style of a HTML Select Option?
https://moderncss.dev/custom-select-styles-with-pure-css/

How to replace the Textarea Element with Class Name?
https://ckeditor.com/latest/samples/old/replacebyclass.html

How to make comments in HTML?
https://www.w3schools.com/html/html_comments.asp

How to style a input field beautifuly?
https://www.w3schools.com/css/css_form.asp

How to style a button beautifuly?
https://www.w3schools.com/css/css3_buttons.asp

Is date 24 hours old or not?
https://www.sitepoint.com/community/t/test-that-date-is-in-the-last-24-hours/6335

## Lösungen zu Problemen
[Zu was]:
- [Beschreibung]
[Link]