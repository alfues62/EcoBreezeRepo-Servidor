
INSERT INTO USUARIO (Nombre, Apellidos, Email, ROL_RolID) 
VALUES ('Juan', 'Pérez', 'juan.perez@example.com', 1);  -- RolID debe existir.


INSERT INTO TIPOGAS (TipoGas) 
VALUES ('Ozono');


INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES (1, 100.0, 'Alto', 1);

INSERT INTO SENSOR (MAC, USUARIO_ID) 
VALUES ('00:1A:2B:3C:4D:5E', 1);  -- USUARIO_ID debe existir.


INSERT INTO MEDICION (Valor, Lon, Lat, Fecha, Hora, TIPOGAS_TipoID, UMBRAL_ID, SENSOR_ID_Sensor) 
VALUES (25.6, '0.1234', '39.4567', '2024-10-23', '14:30:00', 1, 1, 1);  -- SENSOR_ID_Sensor debe existir en SENSOR.

USE EcoBreeze

UPDATE TIPOGAS
SET TipoGas = 'Ozono'
WHERE TIPOGAS_TipoID = 0;

SELECT * FROM ROL;
SELECT * FROM TIPOGAS;
