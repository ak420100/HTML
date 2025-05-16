<?php
use PHPUnit\Framework\TestCase;

class DarkModeTest extends TestCase
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

    private function runDarkMode()
    {
        ob_start();
        include 'darkmode.php';
        return json_decode(ob_get_clean(), true);
    }

    public function testDarkModeEnabled()
    {
        $_SESSION['user_id'] = 1;

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(null);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('bind_result')->willReturn(null);
        $stmt->method('fetch')->willReturn(true);
        $stmt->method('close')->willReturn(null);

        $this->conn->method('prepare')->willReturn($stmt);
        $stmt->method('fetch')->willReturn(true);
        $dark_mode = 1; // Dark mode enabled
        $stmt->method('bind_result')->willReturnCallback(function($var) use ($dark_mode) {
            $var = $dark_mode;
        });

        $result = $this->runDarkMode();
        $this->assertEquals(["dark" => true], $result);
    }

    public function testDarkModeDisabled()
    {
        $_SESSION['user_id'] = 1;

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(null);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('bind_result')->willReturn(null);
        $stmt->method('fetch')->willReturn(true);
        $stmt->method('close')->willReturn(null);

        $this->conn->method('prepare')->willReturn($stmt);
        $stmt->method('fetch')->willReturn(true);
        $dark_mode = 0; //
