-- IMPORTANTE INIT

SET GLOBAL TIMEZONE = 'Europe/Madrid';

-- 1. Insertar un rol
INSERT INTO ROL (RolID, Rol) 
VALUES (2, 'User');
INSERT INTO ROL (RolID, Rol) 
VALUES (1, 'Admin');
-- 2. Insertar Tipos Gas
INSERT INTO TIPOGAS (TipoGas) 
VALUES ('O3');
INSERT INTO TIPOGAS (TipoGas) 
VALUES ('CO');
INSERT INTO TIPOGAS (TipoGas) 
VALUES ('NO2');
INSERT INTO TIPOGAS (TipoGas) 
VALUES ('SO4');
-- 3. Insertar Umbrales
INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES 
(1, 0, "Bajo", 1),
(2, 0.05, "Normal", 1),
(3, 0.1, "Alto", 1);
INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES 
(4, 0, "Bajo", 2),
(5, 4.4, "Medio", 2),
(6, 9.4, "Alto", 2);
INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES 
(7, 0, "Bajo", 3),
(8, 0.02, "Medio", 3),
(9, 0.05, "Alto", 3);
INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES 
(10, 0, "Bajo", 4),
(11, 0.02, "Medio", 4),
(12, 0.075, "Alto", 4);

-- 4. Insertar un sensor
INSERT INTO SENSOR (MAC, USUARIO_ID) 
VALUES ('00:1A:2B:3C:4D:5E', 8);  -- USUARIO_ID debe existir.

-- 3. Insertar un usuario
INSERT INTO USUARIO (Nombre, Apellidos, Email, ContrasenaHash, ROL_RolID) 
VALUES ('prueba', 'prueba', 'prueba@example.com', 2234324, 2);  -- RolID debe existir.



DELIMITER //

CREATE TRIGGER asignar_categoria 
BEFORE INSERT ON MEDICION
FOR EACH ROW
BEGIN
    -- Variable para almacenar la categoría encontrada
    DECLARE CategoriaEncontrada VARCHAR(45);

    -- Buscar la categoría correspondiente en la tabla UMBRAL
    SELECT Categoria
    INTO CategoriaEncontrada
    FROM UMBRAL
    WHERE TIPOGAS_TipoID = NEW.TIPOGAS_TipoID
      AND NEW.Valor >= ValorUmbral
    ORDER BY ValorUmbral DESC
    LIMIT 1;

    -- Asignar el resultado a la columna 'Categoria' de la nueva fila
    SET NEW.Categoria = COALESCE(CategoriaEncontrada, 'Peligro');
END//

DELIMITER ;

-- NO IMPORTANTE

INSERT INTO `EcoBreeze`.`MEDICION` (`Valor`, `Lon`, `Lat`, `Fecha`, `Hora`, `TIPOGAS_TipoID`, `SENSOR_ID_Sensor`)
VALUES
-- Ozono (TIPOGAS_TipoID = 4)
(0.03, '-0.3758', '39.4702', '2024-11-20', '10:00:00', 4,1),
(0.05, '-0.3758', '39.4702', '2024-11-20', '10:30:00', 4, 1),
(0.07, '-0.3758', '39.4702', '2024-11-20', '11:00:00', 4, 1),
(0.1, '-0.3758', '39.4702', '2024-11-20', '11:30:00', 4, 1),
(0.12, '-0.3758', '39.4702', '2024-11-20', '12:00:00', 4, 1),
(0.08, '-0.3758', '39.4702', '2024-11-20', '12:30:00', 4, 1),
(0.09, '-0.3758', '39.4702', '2024-11-20', '13:00:00', 4, 1),
(0.06, '-0.3758', '39.4702', '2024-11-20', '13:30:00', 4, 1),
(0.11, '-0.3758', '39.4702', '2024-11-20', '14:00:00', 4, 1),

-- Monóxido de carbono (TIPOGAS_TipoID = 5)
(1.2, '-0.3758', '39.4702', '2024-11-20', '10:00:00', 5, 2),
(2.0, '-0.3758', '39.4702', '2024-11-20', '10:30:00', 5, 2),
(3.5, '-0.3758', '39.4702', '2024-11-20', '11:00:00', 5, 2),
(4.0, '-0.3758', '39.4702', '2024-11-20', '11:30:00', 5, 2),
(5.1, '-0.3758', '39.4702', '2024-11-20', '12:00:00', 5, 2),
(2.5, '-0.3758', '39.4702', '2024-11-20', '12:30:00', 5, 2),
(3.0, '-0.3758', '39.4702', '2024-11-20', '13:00:00', 5, 2),
(3.8, '-0.3758', '39.4702', '2024-11-20', '13:30:00', 5, 2),
(4.5, '-0.3758', '39.4702', '2024-11-20', '14:00:00', 5, 2),

-- Dióxido de nitrógeno (TIPOGAS_TipoID = 6)
(0.015, '-0.3758', '39.4702', '2024-11-20', '10:00:00', 6, 2),
(0.025, '-0.3758', '39.4702', '2024-11-20', '10:30:00', 6, 2),
(0.035, '-0.3758', '39.4702', '2024-11-20', '11:00:00', 6, 2),
(0.05, '-0.3758', '39.4702', '2024-11-20', '11:30:00', 6, 2),
(0.075, '-0.3758', '39.4702', '2024-11-20', '12:00:00', 6, 2),
(0.045, '-0.3758', '39.4702', '2024-11-20', '12:30:00', 6, 2),
(0.055, '-0.3758', '39.4702', '2024-11-20', '13:00:00', 6, 2),
(0.065, '-0.3758', '39.4702', '2024-11-20', '13:30:00', 6, 2),
(0.08, '-0.3758', '39.4702', '2024-11-20', '14:00:00', 6, 2),

-- Dióxido de azufre (TIPOGAS_TipoID = 7)
(0.005, '-0.3758', '39.4702', '2024-11-20', '10:00:00', 7, 2),
(0.01, '-0.3758', '39.4702', '2024-11-20', '10:30:00', 7, 2),
(0.02, '-0.3758', '39.4702', '2024-11-20', '11:00:00', 7, 2),
(0.03, '-0.3758', '39.4702', '2024-11-20', '11:30:00', 7, 2),
(0.04, '-0.3758', '39.4702', '2024-11-20', '12:00:00', 7, 2),
(0.025, '-0.3758', '39.4702', '2024-11-20', '12:30:00', 7, 2),
(0.035, '-0.3758', '39.4702', '2024-11-20', '13:00:00', 7, 2),
(0.045, '-0.3758', '39.4702', '2024-11-20', '13:30:00', 7, 2),
(0.05, '-0.3758', '39.4702', '2024-11-20', '14:00:00', 7, 2);

show triggers;

DROP TRIGGER IF EXISTS asignar_categoria;

SELECT * FROM UMBRAL;

DELETE FROM UMBRAL WHERE id=1;