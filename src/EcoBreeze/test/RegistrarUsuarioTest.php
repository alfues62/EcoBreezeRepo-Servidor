<?php

use PHPUnit\Framework\TestCase;

class RegistrarUsuarioTest extends TestCase {

    private function mockHacerSolicitudCurl($url, $data) {
        $dataArray = json_decode($data, true);

        if ($dataArray['email'] === 'usuario@example.com') {
            return ['success' => true];
        }

        if ($dataArray['email'] === 'error@example.com') {
            return ['success' => false, 'error' => 'Email ya registrado.'];
        }

        return ['success' => false, 'error' => 'Error desconocido.'];
    }

    private function mockEnviarCorreoVerificacion($email, $token, $nombre, $apellidos) {
        if ($email === 'noenviado@example.com') {
            return "error: Simulación de fallo en el envío del correo.";
        }

        if ($email === 'usuario@example.com') {
            return "success";
        }

        return "error: Error desconocido en el correo.";
    }

    private function registrarUsuarioMock($nombre, $apellidos, $email, $contrasena) {
        $token = bin2hex(random_bytes(16));
        $url = 'http://host.docker.internal:8080/api/api_usuario.php?action=registrar';

        $data = json_encode([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'contrasena' => $contrasena,
            'token' => $token,
        ]);

        $result = $this->mockHacerSolicitudCurl($url, $data);

        if (isset($result['success']) && $result['success']) {
            $correoResultado = $this->mockEnviarCorreoVerificacion($email, $token, $nombre, $apellidos);
            if ($correoResultado === 'success') {
                return 'Usuario registrado con éxito y correo de verificación enviado.';
            } else {
                return [
                    'error' => 'Usuario registrado con éxito, pero hubo un problema al enviar el correo de verificación.'
                ];
            }
        } else {
            $error_message = htmlspecialchars($result['error'] ?? 'Error desconocido.');
            return ['error' => $error_message];
        }
    }

    public function testRegistrarUsuarioExitosoConCorreo() {
        $result = $this->registrarUsuarioMock('Juan', 'Pérez', 'usuario@example.com', 'password123');

        $this->assertIsString($result, 'El resultado debería ser un string.');
        $this->assertEquals('Usuario registrado con éxito y correo de verificación enviado.', $result);
    }

    public function testRegistrarUsuarioFalloEnRegistro() {
        $result = $this->registrarUsuarioMock('Juan', 'Pérez', 'error@example.com', 'password123');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('Email ya registrado.', $result['error']);
    }

    public function testRegistrarUsuarioErrorDesconocido() {
        $result = $this->registrarUsuarioMock('Juan', 'Pérez', 'unknown@example.com', 'password123');

        $this->assertIsArray($result, 'El resultado debería ser un array.');
        $this->assertArrayHasKey('error', $result, 'El array debería contener la clave "error".');
        $this->assertEquals('Error desconocido.', $result['error']);
    }
}
?>
