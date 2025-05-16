<?php
use PHPUnit\Framework\TestCase;

class GetChallengeDataTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;
        
        $_GET = [];

        if (!function_exists('header')) {
            function header($value) {
                $GLOBALS['headers'][] = $value;
            }
        }

        if (!function_exists('session_start')) {
            function session_start() {}
        }

        $this->conn = $this->createMock(mysqli::class);
        $GLOBALS['conn'] = $this->conn;
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_GET = [];
        unset($GLOBALS['conn']);
        unset($GLOBALS['headers']);
    }

    private function runScript(): array
    {
        ob_start();
        include __DIR__ . '/../php/get_challenge_data.php'; // Adjust path if needed
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
        $_GET = ['friend_id' => 2, 'habit' => 'nonexistent'];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult([]));

        $this->conn->method('prepare')->willReturn($stmt);

        $response = $this->runScript();
        $this->assertEquals(['error' => 'Your friend does not have this habit.'], $response);
    }

    public function testProgressCalculation()
    {
        $_SESSION['user_id'] = 1;
        $_GET = ['friend_id' => 2, 'habit' => 'reading'];

        $stmt1 = $this->createMock(mysqli_stmt::class);
        $stmt1->method('bind_param')->willReturn(true);
        $stmt1->method('execute')->willReturn(true);
        $stmt1->method('get_result')->willReturn($this->createMockResult([
            ['progress_count' => 3, 'duration' => 10]
        ]));

        $stmt2 = $this->createMock(mysqli_stmt::class);
        $stmt2->method('bind_param')->willReturn(true);
        $stmt2->method('execute')->willReturn(true);
        $stmt2->method('get_result')->willReturn($this->createMockResult([
            ['progress_count' => 7, 'duration' => 10]
        ]));

        $this->conn->method('prepare')->willReturnOnConsecutiveCalls($stmt1, $stmt2);

        $response = $this->runScript();

        $this->assertEquals(30, $response['me']['percent']);    // 3/10 = 30%
        $this->assertEquals(70, $response['friend']['percent']); // 7/10 = 70%
    }
    private function createMockResult($data)
    {
        $result = $this->createMock(mysqli_result::class);
    
        // Safely ensure we have an array of rows
        $calls = is_array($data) ? $data : [];
    
        // Append `null` to simulate end of fetch_assoc()
        $calls[] = null;
    
        $result->method('fetch_assoc')->willReturnOnConsecutiveCalls(...$calls);
    
        return $result;
    }
    
}
