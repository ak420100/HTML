<?php
use PHPUnit\Framework\TestCase;

class GetThemeTest extends TestCase
{
    private $conn;
    
    protected function setUp(): void
    {
        $this->conn = new mysqli('localhost', 'username', 'password', 'test_db');
        
    }
    
    protected function tearDown(): void
    {
        $this->conn->close();
    }
    
    public function testGetThemeWhenNotLoggedIn()
    {
        ob_start();
        include 'get_theme.php';
        
        $output = ob_get_clean();
        $response = json_decode($output, true);
        
        $this->assertEquals(['error' => 'Not logged in'], $response);
    }
    
    public function testGetThemeWhenLoggedIn()
    {
        $_SESSION['user_id'] = 1;

        $this->conn->query("INSERT INTO users (id, theme) VALUES (1, 'pink')");
        
        ob_start();
        include 'get_theme.php';
        
        $output = ob_get_clean();
        $response = json_decode($output, true);
        
        $this->assertEquals(['theme' => 'pink'], $response);
    }
}
