<?php
use PHPUnit\Framework\TestCase;
$_SESSION['user_id'] = 123;

class IncrementProgressTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;
        $this->conn = $this->createMock(mysqli::class);
        $GLOBALS['conn'] = $this->conn;

        

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

    private function runIncrementProgress()
    {
        ob_start();
        include 'increment_progress.php';
        return json_decode(ob_get_clean(), true);
    }

    public function testNotLoggedIn()
    {
        $result = $this->runIncrementProgress();
        $this->assertEquals(["error" => "Not logged in"], $result);
    }

    public function testMissingHabitName()
    {
        $_SESSION['user_id'] = 1;
        $input = json_encode([]);
        $this->mockInput($input);

        $result = $this->runIncrementProgress();
        $this->assertEquals(["error" => "Missing habit name"], $result);
    }

    public function testHabitNotFound()
    {
        $_SESSION['user_id'] = 1;
        $input = json_encode(['habit' => 'Nonexistent Habit']);
        $this->mockInput($input);

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult(null));

        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runIncrementProgress();
        $this->assertEquals(["error" => "Habit not found"], $result);
    }

    public function testUpdateProgressSuccess()
    {
        $_SESSION['user_id'] = 1;
        $input = json_encode(['habit' => 'My Habit']);
        $this->mockInput($input);

        $habitData = [
            'id' => 1,
            'progress_count' => 1,
            'duration' => 10,
            'last_updated' => (new DateTime())->modify('-1 day')->format('Y-m-d H:i:s')
        ];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult($habitData));

        $updateStmt = $this->createMock(mysqli_stmt::class);
        $updateStmt->method('execute')->willReturn(true);

        $this->conn->method('prepare')
            ->willReturnOnConsecutiveCalls($stmt, $updateStmt);

        $result = $this->runIncrementProgress();
        $this->assertEquals([
            "success" => "Progress updated",
            "progress" => 2,
            "percent" => 20
        ], $result);
    }

    public function testUpdateProgressTooSoon()
    {
        $_SESSION['user_id'] = 1;
        $input = json_encode(['habit' => 'My Habit']);
        $this->mockInput($input);

        $habitData = [
            'id' => 1,
            'progress_count' => 1,
            'duration' => 10,
            'last_updated' => (new DateTime())->format('Y-m-d H:i:s') // Last updated now
        ];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult($habitData));

        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runIncrementProgress();
        $this->assertEquals(["error" => "You can only mark progress once every 24 hours."], $result);
    }

    private function createMockResult($data)
    {
        $result = $this->createMock(mysqli_result::class);
        $result->method('fetch_assoc')->willReturn($data);
        return $result;
    }

    private function mockInput($data)
    {
        $inputStream = fopen('php://input', 'w');
        fwrite($inputStream, $data);
        fclose($inputStream);
    }
}
