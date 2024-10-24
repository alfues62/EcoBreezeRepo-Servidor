-- 1. Insertar un rol
INSERT INTO ROL (RolID, Rol) 
VALUES (2, 'User');
-- 1. Insertar un rol
INSERT INTO ROL (RolID, Rol) 
VALUES (1, 'Admin');
INSERT INTO TIPOGAS (TipoGas) 
VALUES ('Ozono');

-- 2. Insertar un usuario
INSERT INTO USUARIO (Nombre, Apellidos, Email, ROL_RolID) 
VALUES ('Admin', 'Admin', 'admin@example.com', 1);  -- RolID debe existir.



-- 4. Insertar un umbral
INSERT INTO UMBRAL (ID, ValorUmbral, Categoria, TIPOGAS_TipoID) 
VALUES (1, 100.0, 'Alto', 1);  -- Asegúrate de que TIPOGAS_TipoID sea correcto.

-- 5. Insertar un sensor
INSERT INTO SENSOR (MAC, USUARIO_ID) 
VALUES ('00:1A:2B:3C:4D:5E', 1);  -- USUARIO_ID debe existir.

-- 6. Verificar el SensorID recién insertado
SELECT * FROM USUARIO;  -- Asegúrate de que el SensorID sea correcto.

-- 7. Insertar una medición
INSERT INTO MEDICION (Valor, Lon, Lat, Fecha, Hora, TIPOGAS_TipoID, UMBRAL_ID, SENSOR_ID_Sensor) 
VALUES (25.6, '0.1234', '39.4567', '2024-10-23', '14:30:00', 1, 1, 1);  -- SENSOR_ID_Sensor debe existir en SENSOR.
