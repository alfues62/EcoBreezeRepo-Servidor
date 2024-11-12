-- MySQL Script generated by MySQL Workbench
-- Mon Nov 11 20:37:59 2024
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema a
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema EcoBreeze
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema EcoBreeze
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `EcoBreeze` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `EcoBreeze` ;

-- -----------------------------------------------------
-- Table `EcoBreeze`.`ROL`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`ROL` (
  `RolID` INT NOT NULL,
  `Rol` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`RolID`),
  UNIQUE INDEX `RolID_UNIQUE` (`RolID` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `EcoBreeze`.`USUARIO`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`USUARIO` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(45) NOT NULL,
  `Apellidos` VARCHAR(45) NOT NULL,
  `Email` VARCHAR(45) NOT NULL,
  `ContrasenaHash` VARCHAR(255) NOT NULL,
  `Verificado` TINYINT NOT NULL DEFAULT '0',
  `TokenVerificacion` VARCHAR(255) NULL,
  `expiracion_token` VARCHAR(255) NULL,
  `token_recuperacion` VARCHAR(255) NULL DEFAULT NULL,
  `expiracion_recuperacion` VARCHAR(255) NULL DEFAULT 0,
  `ROL_RolID` INT NOT NULL,
  PRIMARY KEY (`ID`, `ROL_RolID`),
  UNIQUE INDEX `ID_UNIQUE` (`ID` ASC) VISIBLE,
  UNIQUE INDEX `Email_UNIQUE` (`Email` ASC) VISIBLE,
  INDEX `fk_USUARIO_ROL1_idx` (`ROL_RolID` ASC) VISIBLE,
  CONSTRAINT `fk_USUARIO_ROL1`
    FOREIGN KEY (`ROL_RolID`)
    REFERENCES `EcoBreeze`.`ROL` (`RolID`))
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `EcoBreeze`.`SENSOR`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`SENSOR` (
  `SensorID` INT NOT NULL AUTO_INCREMENT,
  `MAC` VARCHAR(45) NOT NULL,
  `USUARIO_ID` INT NOT NULL,
  PRIMARY KEY (`SensorID`, `USUARIO_ID`, `MAC`),
  UNIQUE INDEX `MAC_UNIQUE` (`MAC` ASC) VISIBLE,
  UNIQUE INDEX `ID Sensor_UNIQUE` (`SensorID` ASC) VISIBLE,
  INDEX `fk_SENSOR_USUARIO1_idx` (`USUARIO_ID` ASC) VISIBLE,
  CONSTRAINT `fk_SENSOR_USUARIO1`
    FOREIGN KEY (`USUARIO_ID`)
    REFERENCES `EcoBreeze`.`USUARIO` (`ID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `EcoBreeze`.`TIPOGAS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`TIPOGAS` (
  `TipoID` INT NOT NULL AUTO_INCREMENT,
  `TipoGas` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`TipoID`),
  UNIQUE INDEX `TipoID_UNIQUE` (`TipoID` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


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
    REFERENCES `EcoBreeze`.`TIPOGAS` (`TipoID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `EcoBreeze`.`MEDICION`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `EcoBreeze`.`MEDICION` (
  `IDMedicion` INT NOT NULL AUTO_INCREMENT,
  `Valor` FLOAT NULL DEFAULT NULL,
  `Lon` VARCHAR(45) NULL DEFAULT NULL,
  `Lat` VARCHAR(45) NULL DEFAULT NULL,
  `Fecha` DATE NULL DEFAULT NULL,
  `Hora` TIME NULL DEFAULT NULL,
  `TIPOGAS_TipoID` INT NOT NULL,
  `UMBRAL_ID` INT NOT NULL,
  `Categoria` VARCHAR(45) NOT NULL DEFAULT 'Null',
  `SENSOR_ID_Sensor` INT NOT NULL,
  PRIMARY KEY (`IDMedicion`, `TIPOGAS_TipoID`, `UMBRAL_ID`, `SENSOR_ID_Sensor`),
  UNIQUE INDEX `SENSOR_ID_Sensor_UNIQUE` (`SENSOR_ID_Sensor` ASC) VISIBLE,
  INDEX `fk_MEDICION_TIPOGAS1_idx` (`TIPOGAS_TipoID` ASC) VISIBLE,
  INDEX `fk_MEDICION_UMBRAL1_idx` (`UMBRAL_ID` ASC) VISIBLE,
  INDEX `fk_MEDICION_SENSOR1_idx` (`SENSOR_ID_Sensor` ASC) VISIBLE,
  CONSTRAINT `fk_MEDICION_SENSOR1`
    FOREIGN KEY (`SENSOR_ID_Sensor`)
    REFERENCES `EcoBreeze`.`SENSOR` (`SensorID`),
  CONSTRAINT `fk_MEDICION_TIPOGAS1`
    FOREIGN KEY (`TIPOGAS_TipoID`)
    REFERENCES `EcoBreeze`.`TIPOGAS` (`TipoID`),
  CONSTRAINT `fk_MEDICION_UMBRAL1`
    FOREIGN KEY (`UMBRAL_ID`)
    REFERENCES `EcoBreeze`.`UMBRAL` (`ID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
