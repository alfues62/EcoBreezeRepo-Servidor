<?php

use PHPUnit\Framework\TestCase;

class CambiarContrasenaTest extends TestCase {

    private function mockHacerSolicitudCurl($url, $data) {
        $dataArray = json_decode($data, true);

        if ($dataArray['id'] === 1 && $dataArray['contrasena_actual'] === 'password123' && $dataArray['nueva_contrasena'] === 'newpassword123') {
            return ['success' => true];
        }

        if ($dataArray['id'] === 1 && $dataArray['contrasena_actual'] !== 'password123') {
            return ['success' => false, 'error' => 'La contraseña actual es incorrecta.'];
        }

        if ($dataArray['id'] === 999) {
            return ['success' => false, 'error' => 'El usuario no existe.'];
        }

        return ['success' => false, 'error' => 'Error desconocido.'];
    }

    private function cambiarContrasenaMock($id, $contrasenaActual, $nuevaContrasena) {
        $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=cambiar_contrasena';

        $data = json_encode([
            'id' => $id,
            'contrasena_actual' => $contrasenaActual,
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
        $result = $this->cambiarContrasenaMock(1, 'password123', 'newpassword123');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('success', $result, 'El array debería contener la clave "success".');
        $this->assertEquals('Contraseña cambiada con éxito', $result['success']);
    }

    public function testCambiarContrasenaIncorrecta() {
        $result = $this->cambiarContrasenaMock(1, 'wrongpassword', 'newpassword123');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('La contraseña actual es incorrecta.', $result['error']);
    }

    public function testCambiarContrasenaUsuarioInexistente() {
        $result = $this->cambiarContrasenaMock(999, 'password123', 'newpassword123');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('El usuario no existe.', $result['error']);
    }

    public function testCambiarContrasenaErrorDesconocido() {
        $result = $this->cambiarContrasenaMock(1, 'password123', 'unexpectederror');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('Error desconocido.', $result['error']);
    }
}
