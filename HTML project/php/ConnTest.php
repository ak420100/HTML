<?php
use PHPUnit\Framework\TestCase;

class ConnTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;
        if (!function_exists('session_start')) {
            function session_start() {}
        }
     
    }

    public function testConnectionSuccess()
    {
        $servername = "mysql.hostinger.com";
        $username = "u626296519_root1";
        $password = "DatabasePassword2!";
        $database = "u626296519_DB";

        $conn = @new mysqli($servername, $username, $password, $database);

        $this->assertTrue($conn && !$conn->connect_error, "Expected successful database connection");
        $conn->close();
    }

    public function testConnectionFailure()
    {
        $conn = @new mysqli("invalid_host", "invalid_user", "invalid_password", "invalid_db");

        $this->assertTrue($conn->connect_error !== null, "Expected connection to fail with invalid credentials");
    }
}
