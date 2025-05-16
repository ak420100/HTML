<?php
use PHPUnit\Framework\TestCase;

class GetChallengeDataTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli('localhost', 'test_user', 'test_password', 'test_db');
      
        $this->conn->query("
            CREATE TABLE IF NOT EXISTS habits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                name VARCHAR(255),
                duration INT,
                duration_unit VARCHAR(10),
                progress_count INT DEFAULT 0
            )
        ");
      
        $_SESSION = [];
        $_GET = [];
    }

    protected function tearDown(): void
    {
        $this->conn->query("DROP TABLE IF EXISTS habits");
        $this->conn->close();
    }

    private function runScript(): array
    {
        ob_start();
        include 'get_challenge_data.php';
        return json_decode(ob_get_clean(), true);
    }

    public function testMissingParameters()
    {
        $_SESSION['user_id'] = 1;
        $response = $this->runScript();
        $this->assertEquals(['error' => 'Missing friend ID or habit name.'], $response);
    }

    public function testFriendHabitNotFound()
    {
        $_SESSION['user_id'] = 1;
        $_GET = ['friend_id' => 999, 'habit' => 'nonexistent'];
        
        $this->conn->query("INSERT INTO habits (user_id, name) VALUES (1, 'running')");
        
        $response = $this->runScript();
        $this->assertEquals(['error' => 'Your friend does not have this habit.'], $response);
    }

    public function testProgressCalculation()
    {
        $this->conn->query("
            INSERT INTO habits (user_id, name, duration, progress_count)
            VALUES (1, 'reading', 10, 3), (2, 'reading', 10, 7)
        ");
        
        $_SESSION['user_id'] = 1;
        $_GET = ['friend_id' => 2, 'habit' => 'reading'];
        
        $response = $this->runScript();
        
        $this->assertEquals(30, $response['me']['percent']);  // 3/10
        $this->assertEquals(70, $response['friend']['percent']); // 7/10
    }
}
