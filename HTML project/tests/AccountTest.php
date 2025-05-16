<?php
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
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

    public function testPasswordsDoNotMatch()
    {
        $_SESSION['user_id'] = 1;
        $input = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'new_password' => 'password123',
            'confirm_password' => 'password456'
        ];

        $result = $this->runAccountScript($input);
        $this->assertEquals(["error" => "Passwords do not match."], $result);
    }

    public function testFailedAccountUpdate()
    {
        $_SESSION['user_id'] = 1;
        $input = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'new_password' => 'password123',
            'confirm_password' => 'password123'
        ];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(false); // Simulate failure
        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runAccountScript($input);
        $this->assertEquals(["error" => "Failed to update account info."], $result);
    }

    public function testSuccessfulAccountUpdate()
    {
        $_SESSION['user_id'] = 1;
        $input = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'new_password' => 'password123',
            'confirm_password' => 'password123'
        ];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true); // Simulate success
        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runAccountScript($input);
        $this->assertEquals(["success" => "Account updated successfully."], $result);
    }
    

    private function createMockResult($data)
    {
        $result = $this->createMock(mysqli_result::class);
        if (is_array($data)) {
            $result->method('fetch_assoc')->willReturnOnConsecutiveCalls(...$data, null);
        } else {
            $result->method('fetch_assoc')->willReturn(null);
        }
        return $result;
    }
}
