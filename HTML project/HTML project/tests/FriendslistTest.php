<?php
use PHPUnit\Framework\TestCase;

class FriendListTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli('localhost', 'test_user', 'test_password', 'test_db');
        
        // Create tables if they don't exist
        $this->conn->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE
            )
        ");
        
        $this->conn->query("
            CREATE TABLE IF NOT EXISTS friends (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                friend_id INT,
                status VARCHAR(50),
                email VARCHAR(255)
            )
        ");
        
        $_SESSION = [];
        $_POST = [];
    }

    protected function tearDown(): void
    {
        $this->conn->query("DELETE FROM friends");
        $this->conn->query("DELETE FROM users");
        $this->conn->close();
    }

    public function testAddFriendSuccess()
    {
        $_SESSION['user_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'friendEmail' => 'name@test.com',
            'friendName' => 'Friend',
            'friendHabits' => 'running'
        ];
        
        $this->conn->query("INSERT INTO users (id, email) VALUES (1, 'user@test.com')");
        $this->conn->query("INSERT INTO users (id, email) VALUES (2, 'friend@test.com')");
        
        ob_start();
        include 'friendlist.php';
        $output = json_decode(ob_get_clean(), true);
        
        $this->assertTrue($output['success'] ?? false);
        
        $result = $this->conn->query("SELECT * FROM friends WHERE user_id = 1");
        $this->assertEquals(1, $result->num_rows);
    }

    public function testAddFriendMissingFields()
    {
        $_SESSION['user_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['friendEmail' => ''];
        
        ob_start();
        include 'friendlist.php';
        $output = json_decode(ob_get_clean(), true);
        
        $this->assertEquals(
            ['error' => 'Name and email are required.'],
            $output
        );
    }

    public function testAddNonExistentUser()
    {
        $_SESSION['user_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'friendEmail' => 'noexist@test.com',
            'friendName' => 'No User'
        ];
        
        ob_start();
        include 'friendlist.php';
        $output = json_decode(ob_get_clean(), true);
        
        $this->assertEquals(
            ['error' => 'This email is not registered.', 'field' => 'friendEmail'],
            $output
        );
    }
}
