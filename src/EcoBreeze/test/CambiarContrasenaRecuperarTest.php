<?php

use PHPUnit\Framework\TestCase;

class CambiarContrasenaRecuperarTest extends TestCase {

    private function mockHacerSolicitudCurl($url, $data) {
        $dataArray = json_decode($data, true);

        if ($dataArray['email'] === 'usuario@example.com' && $dataArray['nueva_contrasena'] === 'NuevaPassword123') {
            return ['success' => true];
        }

        if ($dataArray['email'] === 'error@example.com') {
            return ['success' => false, 'error' => 'El correo no está registrado.'];
        }

        if ($dataArray['email'] === 'error_api@example.com') {
            return ['success' => false, 'error' => 'Error de conexión con la API.'];
        }

        return ['success' => false, 'error' => 'Error desconocido.'];
    }

    private function cambiarContrasenaRecuperarMock($email, $nuevaContrasena) {
        $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=cambiar_contrasena_recuperar';

        $data = json_encode([
            'email' => $email,
            'nueva_contrasena' => $nuevaContrasena
        ]);

        $result = $this->mockHacerSolicitudCurl($url, $data);

        if (isset($result['success']) && $result['success']) {
            return ['success' => 'Contraseña cambiada con éxito'];
        } else {
            $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
            return ['error' => $error_message];
        }
    }

    public function testCambiarContrasenaExitoso() {
        $result = $this->cambiarContrasenaRecuperarMock('usuario@example.com', 'NuevaPassword123');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('success', $result, 'El array debería contener la clave "success".');
        $this->assertEquals('Contraseña cambiada con éxito', $result['success']);
    }

    public function testCambiarContrasenaCorreoNoRegistrado() {
        $result = $this->cambiarContrasenaRecuperarMock('error@example.com', 'NuevaPassword123');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('El correo no está registrado.', $result['error']);
    }

    public function testCambiarContrasenaErrorConexionAPI() {
        $result = $this->cambiarContrasenaRecuperarMock('error_api@example.com', 'NuevaPassword123');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('Error de conexión con la API.', $result['error']);
    }

    public function testCambiarContrasenaErrorDesconocido() {
        $result = $this->cambiarContrasenaRecuperarMock('unknown@example.com', 'NuevaPassword123');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('Error desconocido.', $result['error']);
    }
}
