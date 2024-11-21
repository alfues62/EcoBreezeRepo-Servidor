-- IMPORTANTE INIT

-- 1. Insertar un rol
INSERT INTO ROL (RolID, Rol) 
VALUES (2, 'User');
INSERT INTO ROL (RolID, Rol) 
VALUES (1, 'Admin');
-- 2. Insertar Tipos Gas
INSERT INTO TIPOGAS (TipoGas) 
VALUES ('03');
INSERT INTO TIPOGAS (TipoGas) 
VALUES ('CO');
INSERT INTO TIPOGAS (TipoGas) 
VALUES ('NO2');
INSERT INTO TIPOGAS (TipoGas) 
VALUES ('S04');
-- 3. Insertar Umbrales
INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES 
(1, 0, "Bajo", 4),
(2, 1, "Normal", 4),
(3, 2, "Alto", 4);
INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES 
(1, 0, "Bajo", 5),
(2, 3, "Medio", 5),
(3, 6, "Alto", 5);
INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES 
(1, 0, "Bajo", 6),
(2, 2, "Medio", 6),
(3, 4, "Alto", 6);
INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES 
(1, 0, "Bajo", 7),
(2, 3, "Medio", 7),
(3, 5, "Alto", 7);

-- 3. Insertar un usuario
INSERT INTO USUARIO (Nombre, Apellidos, Email, ContrasenaHash, ROL_RolID) 
VALUES ('prueba', 'prueba', 'prueba@example.com', 2234324, 2);  -- RolID debe existir.

-- 4. Insertar un sensor
INSERT INTO SENSOR (MAC, USUARIO_ID) 
VALUES ('00:1A:2B:3C:4D:5E', 1);  -- USUARIO_ID debe existir.

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
(3.5, '-0.3758', '39.4702', '2024-11-14', '10:00:00', 2, 1);

show triggers;

DROP TRIGGER IF EXISTS asignar_categoria;

SELECT * FROM UMBRAL;

DELETE FROM UMBRAL WHERE id=1;