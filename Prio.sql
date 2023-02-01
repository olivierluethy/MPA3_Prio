DROP DATABASE IF EXISTS prio;
CREATE DATABASE prio;
USE prio;

--
-- Tabelle 'Benutzer'
--

CREATE TABLE benutzer (
  benutzerId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  mangelpunkte INT,
  role TINYINT(2), /* Normaler Benutzer: 0, Admin: 1, Gesperrt: 2 */
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

--
-- Tabelle 'Aufgabe'
--

CREATE TABLE aufgabe (
  aufgabeId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  titel VARCHAR(100) NOT NULL,
  beschreibung TEXT NOT NULL,
  motivation TEXT NOT NULL,
  deadline DATE,
  prioritaet INT NOT NULL,
  status INT NOT NULL, /* Nicht erledigt: 0, Erledigt: 1 */
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  fk_benutzerId INT NOT NULL,
  FOREIGN KEY (fk_benutzerId) REFERENCES benutzer(benutzerId)
);

--
-- Tabelle 'Essays'
--

CREATE TABLE essays (
  essayId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  titel VARCHAR(255) NOT NULL,
  essay TEXT NOT NULL,
  status INT NOT NULL, /* Offen: 1, Geschlossen: 2 */
  fk_benutzerId INT NOT NULL,
  FOREIGN KEY (fk_benutzerId) REFERENCES benutzer(benutzerId)
);

--
-- Tabelle 'Zeiteinträge'
--

CREATE TABLE zeiteintraege (
  zeiteintraegeId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  zeit TIME NOT NULL,
  fk_aufgabeId INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (fk_aufgabeId) REFERENCES aufgabe(aufgabeId)
);

/* Beispiel Benutzer */
INSERT INTO `benutzer` (`email`, `password`, `role`, `created_at`) VALUES
/* Admin */
('kauz@kauz.ch', '$2y$10$obgm5U7eZWbqYcDoC4YcB.EMC1yAuhj8d0jx1MEK/IURpIrIbzED.', 1, '2022-08-05 13:55:59'), /* Passwort: Kauz123 */
/* Normaler Benutzer */
('test@test.ch', '$2y$10$obgm5U7eZWbqYcDoC4YcB.EMC1yAuhj8d0jx1MEK/IURpIrIbzED.', 0, '2022-08-05 13:55:59');