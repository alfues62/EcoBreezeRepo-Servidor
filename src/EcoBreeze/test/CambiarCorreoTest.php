<?php

use PHPUnit\Framework\TestCase;

class CambiarCorreoTest extends TestCase {

    private function mockHacerSolicitudCurl($url, $data) {
        $dataArray = json_decode($data, true);

        if ($dataArray['email'] === 'usuario@example.com' && $dataArray['nuevo_correo'] === 'nuevo@example.com') {
            return ['success' => true];
        }

        if ($dataArray['email'] === 'usuario@example.com' && $dataArray['nuevo_correo'] === 'existente@example.com') {
            return ['success' => false, 'error' => 'El nuevo correo ya está registrado.'];
        }

        if ($dataArray['email'] === 'inexistente@example.com') {
            return ['success' => false, 'error' => 'El correo actual no está registrado.'];
        }

        return ['success' => false, 'error' => 'Error desconocido.'];
    }

    private function cambiarCorreoMock($email, $nuevoCorreo) {
        $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=cambiar_correo';

        $data = json_encode([
            'email' => $email,
            'nuevo_correo' => $nuevoCorreo
        ]);

        $result = $this->mockHacerSolicitudCurl($url, $data);

        if (isset($result['success']) && $result['success']) {
            return ['success' => 'Correo cambiado con éxito'];
        } else {
            $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
            return ['error' => $error_message];
        }
    }

    public function testCambiarCorreoExitoso() {
        $result = $this->cambiarCorreoMock('usuario@example.com', 'nuevo@example.com');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('success', $result, 'El array debería contener la clave "success".');
        $this->assertEquals('Correo cambiado con éxito', $result['success']);
    }

    public function testCambiarCorreoYaRegistrado() {
        $result = $this->cambiarCorreoMock('usuario@example.com', 'existente@example.com');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('El nuevo correo ya está registrado.', $result['error']);
    }

    public function testCambiarCorreoNoRegistrado() {
        $result = $this->cambiarCorreoMock('inexistente@example.com', 'nuevo@example.com');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('El correo actual no está registrado.', $result['error']);
    }

    public function testCambiarCorreoErrorDesconocido() {
        $result = $this->cambiarCorreoMock('unknown@example.com', 'nuevo@example.com');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('Error desconocido.', $result['error']);
    }
}
