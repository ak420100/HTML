<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;
        $this->conn = $this->createMock(mysqli::class);
        $GLOBALS['conn'] = $this->conn;
       

        if (!function_exists('session_start')) {
            function session_start() {
                return true;
            }
        }

        if (!function_exists('session_regenerate_id')) {
            function session_regenerate_id($delete_old_session = false) {
                return true;
            }
        }
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['conn']);
        $_SESSION = [];
    }

    private function runLogin($mockedData): array
    {
        // Create a mock stream for php://input
        stream_wrapper_unregister("php");
        stream_wrapper_register("php", MockPhpStream::class);
        file_put_contents("php://input", json_encode($mockedData));

        ob_start();
        include 'login.php';
        $output = ob_get_clean();

        stream_wrapper_restore("php");

        return json_decode($output, true);
    }

    public function testEmptyCredentials(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $output = $this->runLogin([]);
        $this->assertEquals(['error' => 'Email and password are required.'], $output);
    }

    public function testInvalidEmailFormat(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $output = $this->runLogin(['email' => 'invalid-email', 'password' => '123']);
        $this->assertEquals(['error' => 'Invalid email format.'], $output);
    }

    public function testUserNotFound(): void
    {
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult([]));

        $this->conn->method('prepare')->willReturn($stmt);

        $output = $this->runLogin(['email' => 'noexist@test.com', 'password' => 'Pass']);
        $this->assertEquals(['error' => 'Invalid email or password.'], $output);
    }

    public function testWrongPassword(): void
    {
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult([
            ['id' => 1, 'password' => password_hash('correctpass', PASSWORD_DEFAULT), 'email' => 'test@test.com']
        ]));

        $this->conn->method('prepare')->willReturn($stmt);

        $output = $this->runLogin(['email' => 'test@test.com', 'password' => 'wrongpass']);
        $this->assertEquals(['error' => 'Invalid email or password.'], $output);
    }

    public function testSuccessfulLogin(): void
    {
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult([
            ['id' => 111, 'password' => password_hash('rightpass', PASSWORD_DEFAULT), 'email' => 'test@test.com']
        ]));

        $this->conn->method('prepare')->willReturn($stmt);

        $output = $this->runLogin(['email' => 'test@test.com', 'password' => 'rightpass']);
        $this->assertEquals(['success' => 'Login successful!', 'userId' => 111], $output);
        $this->assertEquals(111, $_SESSION['user_id']);
        $this->assertEquals('test@test.com', $_SESSION['user_email']);
    }

    private function createMockResult($rows)
    {
        $result = $this->createMock(mysqli_result::class);
        $result->method('fetch_assoc')->willReturnOnConsecutiveCalls(...array_merge($rows, [null]));
        return $result;
    }
}

/**
 * Helper stream wrapper to mock php://input
 */
class MockPhpStream {
    private $index = 0;
    private static $data;
    public $context;

    public static function setContent($content) {
        self::$data = $content;
    }

    public function stream_open() { $this->index = 0; return true; }
    public function stream_read($count) {
        $ret = substr(self::$data, $this->index, $count);
        $this->index += strlen($ret);
        return $ret;
    }
    public function stream_eof() { return $this->index >= strlen(self::$data); }
    public function stream_stat() { return []; }
}
?>
