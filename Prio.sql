DROP DATABASE IF EXISTS prio;
CREATE DATABASE prio;
USE prio;

--
-- Tabelle 'Benutzer'
--

CREATE TABLE benutzer (
  benutzerId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(256) NOT NULL UNIQUE,
  password VARCHAR(256) NOT NULL,
  mangelpunkte VARCHAR(256) NOT NULL,
  role VARCHAR(256) NOT NULL, /* Normaler Benutzer: 0, Admin: 1, Gesperrt: 2 */
  salt VARCHAR(256) NOT NULL UNIQUE,
  iv VARCHAR(256) NOT NULL
);

--
-- Tabelle 'Aufgabe'
--

CREATE TABLE aufgabe (
  aufgabeId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  titel VARCHAR(256) NOT NULL,
  beschreibung TEXT NOT NULL,
  motivation TEXT NOT NULL,
  deadline VARCHAR(256) NOT NULL,
  prioritaet VARCHAR(256) NOT NULL,
  status VARCHAR(256) NOT NULL, /* Nicht erledigt: 0, Erledigt: 1 */
  iv VARCHAR(256) NOT NULL,
  created_at VARCHAR(256) NOT NULL,
  fk_benutzerId INT NOT NULL,
  FOREIGN KEY (fk_benutzerId) REFERENCES benutzer(benutzerId)
);

--
-- Tabelle 'Essays'
--

CREATE TABLE essays (
  essayId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  titel VARCHAR(256) NOT NULL,
  essay TEXT NOT NULL,
  status VARCHAR(256) NOT NULL, /* Offen: 1, Geschlossen: 2 */
  iv VARCHAR(256) NOT NULL,
  fk_benutzerId INT NOT NULL,
  FOREIGN KEY (fk_benutzerId) REFERENCES benutzer(benutzerId)
);

--
-- Tabelle 'Rapport'
--

CREATE TABLE rapport (
  rapportId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  rapport VARCHAR(256) NOT NULL,
  zeit VARCHAR(256) NOT NULL,
  start_time VARCHAR(256) NULL, /* optional clock start (encrypted); NULL = duration-only */
  end_time VARCHAR(256) NULL,   /* optional clock end (encrypted); NULL = duration-only */
  iv VARCHAR(256) NOT NULL,
  created_at VARCHAR(256) NOT NULL,
  fk_aufgabeId INT NOT NULL,
  FOREIGN KEY (fk_aufgabeId) REFERENCES aufgabe(aufgabeId)
);