-- =============================================================
--  Agenda de Contactos · Esquema de base de datos (MySQL/MariaDB)
-- =============================================================
--  En local puedes crear la base con las dos líneas siguientes.
--  En hosting gratuito (InfinityFree, etc.) la base ya la crea el
--  panel: selecciónala en phpMyAdmin y ejecuta solo el CREATE TABLE.
-- -------------------------------------------------------------
-- CREATE DATABASE IF NOT EXISTS agenda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE agenda;

CREATE TABLE IF NOT EXISTS contactos (
    id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    nombre          VARCHAR(60)   NOT NULL,
    apellido        VARCHAR(60)   NOT NULL,
    telefono        VARCHAR(20)   NOT NULL,
    foto            VARCHAR(255)  NOT NULL,
    email           VARCHAR(100)  NULL,
    direccion       VARCHAR(150)  NULL,
    notas           TEXT          NULL,
    creado_en       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_nombre (nombre, apellido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
