<?php
use PHPUnit\Framework\TestCase;

class GetThemeTest extends TestCase
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
            function session_start() {}
        }

        if (!function_exists('header')) {
            function header($value) {
                $GLOBALS['headers'][] = $value;
            }
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        unset($GLOBALS['conn']);
        unset($GLOBALS['headers']);
    }

    public function testGetThemeWhenNotLoggedIn()
    {
        ob_start();
        include __DIR__ . '/../php/get_theme.php';
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertEquals(['error' => 'Not logged in'], $response);
    }

    public function testGetThemeWhenLoggedIn()
    {
        $_SESSION['user_id'] = 1;

        // Mock prepared statement
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->method('bind_param')->willReturn(true);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('bind_result')->willReturnCallback(function (&$theme) {
            $theme = 'pink';
        });
        $mockStmt->method('fetch')->willReturn(true);
        $mockStmt->method('close')->willReturn(true);

        // Return mock statement on prepare
        $this->conn->method('prepare')->willReturn($mockStmt);

        ob_start();
        include __DIR__ . '/../php/get_theme.php';
        $output = ob_get_clean();

        $response = json_decode($output, true);
        $this->assertEquals(['theme' => 'pink'], $response);
    }
}
