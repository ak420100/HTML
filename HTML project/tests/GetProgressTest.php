<?php
use PHPUnit\Framework\TestCase;

class GetProgressTest extends TestCase
{
    protected function setUp(): void
    {
        $this->conn = $this->createMock(mysqli::class);
        $GLOBALS['conn'] = $this->conn;
        $_SESSION = [];

        if (!function_exists('header')) {
            function header($value) {
                $GLOBALS['headers'][] = $value;
            }
        }

        $GLOBALS['headers'] = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        unset($GLOBALS['headers']);
    }

    private function runGetProgress($session, $getParams)
    {
        $_SESSION = $session;
        $_GET = $getParams;
        
        ob_start();
        include 'get_progress.php';
        return json_decode(ob_get_clean(), true);
    }

    public function testFriendHabitNotFound()
    {
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('execute')->willReturn(true);
        
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')->willReturn(false);
        
        $mockStmt->method('get_result')->willReturn($mockResult);
        $this->conn->method('prepare')->willReturn($mockStmt);

        $result = $this->runGetProgress(
            ['user_id' => 1],
            ['friend_id' => 2, 'habit' => 'running']
        );
        
        $this->assertEquals(
            ['error' => 'Your friend does not have this habit.'],
            $result
        );
    }

    public function testMissingParameters()
    {
        $_SESSION['user_id'] = 1;
        $result = $this->runGetProgress(['user_id' => 1], []);
        $this->assertEquals(['error' => 'Missing friend ID or habit name.'], $result);
    }
}
