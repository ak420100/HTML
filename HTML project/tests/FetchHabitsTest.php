<?php
use PHPUnit\Framework\TestCase;

class FetchHabitsTest extends TestCase
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

        if (!function_exists('session_start')) {
            function session_start() {}
        }

        $GLOBALS['headers'] = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        unset($GLOBALS['headers']);
    }

    private function runFetchHabits()
    {
        ob_start();
        include 'fetch_habits.php';
        return json_decode(ob_get_clean(), true);
    }

    public function testUserNotLoggedIn()
    {
        $result = $this->runFetchHabits();
        $this->assertEquals(['error' => 'You are not logged in'], $result);
    }

    public function testDatabaseConnectionFailure()
    {
        $_SESSION['user_id'] = 1;

        // Simulate database connection failure
        $this->conn->method('connect_error')->willReturn(true);

        $result = $this->runFetchHabits();
        $this->assertEquals(['error' => 'Database connection failed'], $result);
    }

    public function testSuccessfulDataRetrieval()
    {
        $_SESSION['user_id'] = 1;

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(null);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult([
            ['habit_name' => 'Exercise', 'duration' => 30, 'created_at' => '2023-01-01', 'progress_count' => 1],
            ['habit_name' => 'Read', 'duration' => 10, 'created_at' => '2023-01-02', 'progress_count' => 0]
        ]));

        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runFetchHabits();
        $expected = [
            ['habit_name' => 'Exercise', 'duration' => '30', 'created_at' => '2023-01-01', 'progress_status' => 'progress'],
            ['habit_name' => 'Read', 'duration' => '10', 'created_at' => '2023-01-02', 'progress_status' => 'no-progress'],
        ];
        $this->assertEquals($expected, $result);
    }

    private function createMockResult($data)
    {
        $result = $this->createMock(mysqli_result::class);
        $result->method('fetch_assoc')->willReturnOnConsecutiveCalls(...$data, null);
        return $result;
    }
}
