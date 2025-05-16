<?php
use PHPUnit\Framework\TestCase;

class CreateHabTest extends TestCase
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

    
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        if (!function_exists('header')) {
            function header($location, $replace = true, $http_response_code = null) {
                $GLOBALS['header_location'] = $location;
            }
        }

        if (!function_exists('session_start')) {
            function session_start() {}
        }

        if (!function_exists('custom_die')) {
            function custom_die($message = '') {
                $GLOBALS['die_message'] = $message;
                throw new Exception($message);
            }
        }
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['header_location'], $GLOBALS['die_message'], $GLOBALS['conn']);
        $_SESSION = [];
        $_POST = [];
    }

    private function runCreateHab(): array
    {
        ob_start();
        try {
            include __DIR__ . '/../php/createhab.php';
            return ['output' => ob_get_clean()];
        } catch (Exception $e) {
            ob_end_clean();
            return ['error' => $e->getMessage()];
        }
    }

    public function testMissingFields()
    {
        $_SESSION['user_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [];

        $result = $this->runCreateHab();
        $this->assertEquals("Error: All fields are required!", $result['error'] ?? '');
    }

    public function testSuccessfulCreation()
    {
        $_SESSION['user_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'habName' => 'Exercise',
            'durationNumber' => 30,
            'durationUnit' => 'days'
        ];

        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('close')->willReturn(true);

        $this->conn->method('prepare')->willReturn($mockStmt);
        $this->conn->method('error')->willReturn('');

        $result = $this->runCreateHab();

        $this->assertEquals("index1.php", $GLOBALS['header_location'] ?? '');
        $this->assertArrayNotHasKey('error', $result);
    }

    public function testDatabaseError()
    {
        $_SESSION['user_id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'habName' => 'Exercise',
            'durationNumber' => 30,
            'durationUnit' => 'days'
        ];

        $this->conn->method('prepare')->willReturn(false);
        $this->conn->method('error')->willReturn('Database error');

        $result = $this->runCreateHab();

        $this->assertStringContainsString("Database error", $result['error'] ?? '');
    }
}
