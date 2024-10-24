-- MySQL Script generated by MySQL Workbench
-- Thu Oct 24 10:11:22 2024
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema EcoBreeze
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema EcoBreeze
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `EcoBreeze` DEFAULT CHARACTER SET utf8 ;
USE `EcoBreeze` ;

-- -----------------------------------------------------
-- Table `EcoBreeze`.`TIPOGAS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`TIPOGAS` (
  `TipoID` INT NOT NULL AUTO_INCREMENT,
  `TipoGas` VARCHAR(45) NULL,
  PRIMARY KEY (`TipoID`),
  UNIQUE INDEX `TipoID_UNIQUE` (`TipoID` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `EcoBreeze`.`UMBRAL`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`UMBRAL` (
  `ID` INT NOT NULL,
  `ValorUmbral` FLOAT NOT NULL,
  `Categoria` VARCHAR(45) NOT NULL,
  `TIPOGAS_TipoID` INT NOT NULL,
  PRIMARY KEY (`ID`, `TIPOGAS_TipoID`),
  UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) VISIBLE,
  INDEX `fk_UMBRAL_TIPOGAS1_idx` (`TIPOGAS_TipoID` ASC) VISIBLE,
  CONSTRAINT `fk_UMBRAL_TIPOGAS1`
    FOREIGN KEY (`TIPOGAS_TipoID`)
    REFERENCES `EcoBreeze`.`TIPOGAS` (`TipoID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `EcoBreeze`.`ROL`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`ROL` (
  `RolID` INT NOT NULL,
  `Rol` VARCHAR(45) NULL,
  PRIMARY KEY (`RolID`),
  UNIQUE INDEX `RolID_UNIQUE` (`RolID` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `EcoBreeze`.`USUARIO`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`USUARIO` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(45) NOT NULL,
  `Apellidos` VARCHAR(45) NOT NULL,
  `Email` VARCHAR(45) NOT NULL,
  `ContrasenaHash` VARCHAR(255) NOT NULL,
  `TFA_Secret` VARCHAR(64) NOT NULL,
  `Verificado` TINYINT(1) NOT NULL DEFAULT  0,
  `TokenVerificacion` VARCHAR(255) NOT NULL,
  `ROL_RolID` INT NOT NULL,
  PRIMARY KEY (`ID`, `ROL_RolID`),
  UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) VISIBLE,
  INDEX `fk_USUARIO_ROL1_idx` (`ROL_RolID` ASC) VISIBLE,
  UNIQUE INDEX `Email_UNIQUE` (`Email` ASC) VISIBLE,
  CONSTRAINT `fk_USUARIO_ROL1`
    FOREIGN KEY (`ROL_RolID`)
    REFERENCES `EcoBreeze`.`ROL` (`RolID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `EcoBreeze`.`SENSOR`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`SENSOR` (
  `SensorID` INT NOT NULL AUTO_INCREMENT,
  `MAC` VARCHAR(45) NOT NULL,
  `USUARIO_ID` INT NOT NULL,
  PRIMARY KEY (`SensorID`, `USUARIO_ID`, `MAC`),
  INDEX `fk_SENSOR_USUARIO1_idx` (`USUARIO_ID` ASC) VISIBLE,
  UNIQUE INDEX `MAC_UNIQUE` (`MAC` ASC) VISIBLE,
  UNIQUE INDEX `ID Sensor_UNIQUE` (`SensorID` ASC) VISIBLE,
  CONSTRAINT `fk_SENSOR_USUARIO1`
    FOREIGN KEY (`USUARIO_ID`)
    REFERENCES `EcoBreeze`.`USUARIO` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `EcoBreeze`.`MEDICION`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`MEDICION` (
  `IDMedicion` INT NOT NULL AUTO_INCREMENT,
  `Valor` FLOAT NULL,
  `Lon` VARCHAR(45) NULL,
  `Lat` VARCHAR(45) NULL,
  `Fecha` DATE NULL,
  `Hora` TIME NULL,
  `TIPOGAS_TipoID` INT NOT NULL,
  `UMBRAL_ID` INT NOT NULL,
  `SENSOR_ID_Sensor` INT NOT NULL,
  PRIMARY KEY (`IDMedicion`, `TIPOGAS_TipoID`, `UMBRAL_ID`, `SENSOR_ID_Sensor`),
  INDEX `fk_MEDICION_TIPOGAS1_idx` (`TIPOGAS_TipoID` ASC) VISIBLE,
  INDEX `fk_MEDICION_UMBRAL1_idx` (`UMBRAL_ID` ASC) VISIBLE,
  INDEX `fk_MEDICION_SENSOR1_idx` (`SENSOR_ID_Sensor` ASC) VISIBLE,
  UNIQUE INDEX `SENSOR_ID_Sensor_UNIQUE` (`SENSOR_ID_Sensor` ASC) VISIBLE,
  CONSTRAINT `fk_MEDICION_TIPOGAS1`
    FOREIGN KEY (`TIPOGAS_TipoID`)
    REFERENCES `EcoBreeze`.`TIPOGAS` (`TipoID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_MEDICION_UMBRAL1`
    FOREIGN KEY (`UMBRAL_ID`)
    REFERENCES `EcoBreeze`.`UMBRAL` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_MEDICION_SENSOR1`
    FOREIGN KEY (`SENSOR_ID_Sensor`)
    REFERENCES `EcoBreeze`.`SENSOR` (`SensorID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- 2. Insertar rol Usuario
INSERT INTO ROL (RolID, Rol) 
VALUES (2, 'User');
-- 1. Insertar rol administrador
INSERT INTO ROL (RolID, Rol) 
VALUES (1, 'Admin');
