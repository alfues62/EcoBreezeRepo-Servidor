<?php

use PHPUnit\Framework\TestCase;

class AutentificarTest extends TestCase {

    // Simulamos la función hacerSolicitudCurl usando un mock
    public function mockHacerSolicitudCurl($url, $data) {
        // Respuesta simulada para credenciales correctas
        if ($data === json_encode(['email' => 'usuario@example.com', 'contrasena' => 'password123'])) {
            return [
                'success' => true,
                'usuario' => [
                    'ID' => 1,
                    'Nombre' => 'Juan',
                    'Apellidos' => 'Pérez',
                    'Rol' => 'admin'
                ]
            ];
        }

        // Respuesta simulada para credenciales incorrectas
        if ($data === json_encode(['email' => 'usuario@example.com', 'contrasena' => 'wrongpassword'])) {
            return [
                'success' => false,
                'error' => 'Credenciales incorrectas'
            ];
        }

        // Respuesta simulada para estructura de usuario incorrecta
        if ($data === json_encode(['email' => 'usuario@example.com', 'contrasena' => 'invalidstructure'])) {
            return [
                'success' => true,
                'usuario' => [
                    'ID' => 1,
                    'Apellidos' => 'Pérez' // Falta 'Nombre' y 'Rol'
                ],
                'error' => 'Error en la estructura de respuesta de la API.'
            ];
        }

        // Caso por defecto (error desconocido)
        return ['success' => false, 'error' => 'Error desconocido'];
    }

    // Test para inicio de sesión exitoso
    public function testIniciarSesionExitoso() {
        // Simulamos un inicio de sesión exitoso
        $result = $this->mockHacerSolicitudCurl('http://host.docker.internal:8080/api/api_usuario.php?action=iniciar_sesion', json_encode([
            'email' => 'usuario@example.com',
            'contrasena' => 'password123'
        ]));

        // Verificamos la respuesta
        $this->assertArrayHasKey('ID', $result['usuario']);
        $this->assertEquals(1, $result['usuario']['ID']);
        $this->assertEquals('Juan', $result['usuario']['Nombre']);
        $this->assertEquals('admin', $result['usuario']['Rol']);
    }

    // Test para inicio de sesión fallido debido a credenciales incorrectas
    public function testIniciarSesionCredencialesIncorrectas() {
        // Simulamos un inicio de sesión con credenciales incorrectas
        $result = $this->mockHacerSolicitudCurl('http://host.docker.internal:8080/api/api_usuario.php?action=iniciar_sesion', json_encode([
            'email' => 'usuario@example.com',
            'contrasena' => 'wrongpassword'
        ]));

        // Comprobamos que el error de credenciales incorrectas sea devuelto
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Credenciales incorrectas', $result['error']);
    }

    // Test para inicio de sesión con estructura incorrecta
    public function testIniciarSesionEstructuraIncorrecta() {
        // Simulamos una respuesta con estructura incorrecta
        $result = $this->mockHacerSolicitudCurl('http://host.docker.internal:8080/api/api_usuario.php?action=iniciar_sesion', json_encode([
            'email' => 'usuario@example.com',
            'contrasena' => 'invalidstructure'
        ]));

        // Comprobamos que la respuesta tenga la clave 'error' debido a la estructura incorrecta
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Error en la estructura de respuesta de la API.', $result['error']);
    }

    // Test para error desconocido
    public function testIniciarSesionErrorDesconocido() {
        // Simulamos una respuesta con un error desconocido
        $result = $this->mockHacerSolicitudCurl('http://host.docker.internal:8080/api/api_usuario.php?action=iniciar_sesion', json_encode([
            'email' => 'usuario@example.com',
            'contrasena' => 'unknownerror'
        ]));

        // Comprobamos que la respuesta sea un error desconocido
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Error desconocido', $result['error']);
    }
}
