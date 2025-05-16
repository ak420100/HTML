<?php
use PHPUnit\Framework\TestCase;

class LogoutTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        session_start();
        
        $_SESSION['user_id'] = 123;
        $_SESSION['username'] = 'testuser';
    }

    protected function tearDown(): void
    {
        session_unset();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function testLogoutDestroysSession()
    {
        $this->assertArrayHasKey('user_id', $_SESSION);
        
        ob_start();
        include 'logout.php';
        ob_end_clean();
        
        $this->assertEmpty($_SESSION);
        $this->assertEquals(PHP_SESSION_NONE, session_status());
    }

    public function testLogoutRedirects()
  {
      global $mockHeaders;
      $mockHeaders = [];
      function header($value) {
          global $mockHeaders;
          $mockHeaders[] = $value;
      }
      
      include 'logout.php';
      
      $this->assertContains('Location: login.html', $mockHeaders);
  }
}
