<?php
use PHPUnit\Framework\TestCase;

class FriendListTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;
        // Mock the MySQLi connection
        $this->conn = $this->createMock(mysqli::class);
        $GLOBALS['conn'] = $this->conn;

        
        $_POST = [];

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
        $_POST = [];
        unset($GLOBALS['headers'], $GLOBALS['conn']);
    }

    private function runFriendList(): array
    {
        ob_start();
        include __DIR__ . '/../php/friendlist.php';
        return json_decode(ob_get_clean(), true);
    }

    public function testAddFriendSuccess()
    {
        $_SESSION['user_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'friendEmail' => 'friend@test.com',
            'friendName' => 'Friend',
            'friendHabits' => 'running'
        ];

        // Mock database behavior
        $selectUserStmt = $this->createMock(mysqli_stmt::class);
        $selectUserStmt->method('execute')->willReturn(true);
        $selectUserStmt->method('bind_param')->willReturn(true);
        $selectUserStmt->method('bind_result')->willReturnCallback(function (&$id) { $id = 2; });
        $selectUserStmt->method('fetch')->willReturn(true);
        $selectUserStmt->method('close')->willReturn(true);

        $insertStmt = $this->createMock(mysqli_stmt::class);
        $insertStmt->method('bind_param')->willReturn(true);
        $insertStmt->method('execute')->willReturn(true);
        $insertStmt->method('close')->willReturn(true);

        // Return statements in sequence
        $this->conn->method('prepare')->willReturnOnConsecutiveCalls($selectUserStmt, $insertStmt);

        $result = $this->runFriendList();
        $this->assertEquals(['success' => true], $result);
    }

    public function testAddFriendMissingFields()
    {
        $_SESSION['user_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['friendEmail' => ''];

        $result = $this->runFriendList();
        $this->assertEquals(['error' => 'Name and email are required.'], $result);
    }

    public function testAddNonExistentUser()
    {
        $_SESSION['user_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'friendEmail' => 'noexist@test.com',
            'friendName' => 'No User'
        ];

        $selectUserStmt = $this->createMock(mysqli_stmt::class);
        $selectUserStmt->method('execute')->willReturn(true);
        $selectUserStmt->method('bind_param')->willReturn(true);
        $selectUserStmt->method('fetch')->willReturn(false); // No user found
        $selectUserStmt->method('close')->willReturn(true);

        $this->conn->method('prepare')->willReturn($selectUserStmt);

        $result = $this->runFriendList();
        $this->assertEquals(
            ['error' => 'This email is not registered.', 'field' => 'friendEmail'],
            $result
        );
    }
}
