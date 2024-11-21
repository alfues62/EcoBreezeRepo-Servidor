-- 1. Insertar un rol
INSERT INTO ROL (RolID, Rol) 
VALUES (2, 'User');
-- 1. Insertar un rol
INSERT INTO ROL (RolID, Rol) 
VALUES (1, 'Admin');
INSERT INTO TIPOGAS (TipoGas) 
VALUES ('Ozono');

-- 4. Insertar un umbral
INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES (1, 100.0, 'Alto', 1);  -- Asegúrate de que TIPOGAS_TipoID sea correcto.


-- 6. Verificar el SensorID recién insertado
SELECT * FROM MEDICION;  -- Asegúrate de que el SensorID sea correcto.

INSERT INTO `EcoBreeze`.`MEDICION` (`Valor`, `Lon`, `Lat`, `Fecha`, `Hora`, `TIPOGAS_TipoID`, `SENSOR_ID_Sensor`)
VALUES
-- Ozono (TIPOGAS_TipoID = 4)
(0.07, '-0.3758', '39.4702', '2024-11-07', '11:00:00', 2, 3);
(0.04, '-0.3758', '39.4702', '2024-11-29', '12:00:00', 3, 4),
(0.06, '-0.3758', '39.4702', '2024-11-05', '14:00:00', 4, 5),
(0.02, '-0.3758', '39.4702', '2024-11-17', '15:00:00', 5, 6),
(0.08, '-0.3758', '39.4702', '2024-11-12', '16:00:00', 2, 7),
(0.10, '-0.3758', '39.4702', '2024-11-08', '17:00:00', 3, 8),
(0.01, '-0.3758', '39.4702', '2024-11-02', '18:00:00', 4, 9),
(0.09, '-0.3758', '39.4702', '2024-11-26', '19:00:00', 5, 10);




DELETE FROM USUARIO WHERE id=12;

DELIMITER //

CREATE TRIGGER asignar_categoria 
BEFORE INSERT ON MEDICION
FOR EACH ROW
BEGIN
    DECLARE categoria VARCHAR(45);

    -- Asignar la categoría basada en el tipo de gas y el valor de la medición
    SELECT Categoria INTO categoria
    FROM UMBRAL
    WHERE TIPOGAS_TipoID = NEW.TIPOGAS_TipoID
      AND NEW.Valor <= ValorUmbral
    ORDER BY ValorUmbral DESC
    LIMIT 1;

    -- Asignar la categoría encontrada a la nueva medición
    SET NEW.Categoria = categoria;
END//

DELIMITER ;





