<?php
use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function testConnectionSuccess()
    {
        // Mock connection parameters
        $servername = "mysql.hostinger.com";
        $username = "u626296519_root1";
        $password = "DatabasePassword2!";
        $database = "u626296519_DB";

        // Create a connection
        $conn = new mysqli($servername, $username, $password, $database);

        // Check if connection is successful
        $this->assertFalse($conn->connect_error, "Connection should be successful");
        
        // Close the connection
        $conn->close();
    }

    public function testConnectionFailure()
    {
        // Use wrong credentials to test failure
        $conn = new mysqli("invalid_host", "invalid_user", "invalid_password", "invalid_db");

        // Check if connection fails
        $this->assertTrue($conn->connect_error, "Connection should fail with invalid credentials");
    }
}
