<?php
use PHPUnit\Framework\TestCase;

class LogoutTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;
      
        if (!function_exists('session_start')) {
            function session_start() { return true; }
        }

        if (!function_exists('session_destroy')) {
            function session_destroy() {
                $_SESSION = [];
                return true;
            }
        }

        if (!function_exists('header')) {
            function header($value) {
                $GLOBALS['mock_headers'][] = $value;
            }
        }

        $_SESSION['user_id'] = 123;
        $_SESSION['username'] = 'testuser';
        $GLOBALS['mock_headers'] = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        unset($GLOBALS['mock_headers']);
    }

    public function testLogoutDestroysSession()
    {
        $this->assertArrayHasKey('user_id', $_SESSION);

        ob_start();
        include 'logout.php';
        ob_end_clean();

        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $this->assertArrayNotHasKey('username', $_SESSION);
    }

    public function testLogoutRedirects()
    {
        ob_start();
        include 'logout.php';
        ob_end_clean();

        $this->assertContains('Location: login.html', $GLOBALS['mock_headers']);
    }
}
