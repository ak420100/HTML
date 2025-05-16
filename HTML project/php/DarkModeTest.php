<?php
use PHPUnit\Framework\TestCase;

class DarkModeTest extends TestCase
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
        unset($GLOBALS['headers'], $GLOBALS['conn']);
    }

    private function runDarkMode(): array
    {
        ob_start();
        include __DIR__ . '/../php/darkmode.php';
        return json_decode(ob_get_clean(), true);
    }

    public function testDarkModeEnabled()
    {
        $_SESSION['user_id'] = 1;

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(true);
        $stmt->method('close')->willReturn(true);
        $stmt->method('bind_result')->willReturnCallback(function (&$dark_mode) {
            $dark_mode = 1;
        });

        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runDarkMode();
        $this->assertEquals(["dark" => true], $result);
    }

    public function testDarkModeDisabled()
    {
        $_SESSION['user_id'] = 1;

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(true);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(true);
        $stmt->method('close')->willReturn(true);
        $stmt->method('bind_result')->willReturnCallback(function (&$dark_mode) {
            $dark_mode = 0;
        });

        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runDarkMode();
        $this->assertEquals(["dark" => false], $result);
    }
}
