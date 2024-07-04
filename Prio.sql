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
  status INT NOT NULL DEFAULT 0, /* Nicht erledigt: 0, Erledigt: 1 */
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
-- Tabelle 'Rapport'
--

CREATE TABLE rapport (
  rapportId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  rapport VARCHAR(255) NOT NULL,
  zeit TIME NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  fk_aufgabeId INT NOT NULL,
  FOREIGN KEY (fk_aufgabeId) REFERENCES aufgabe(aufgabeId)
);