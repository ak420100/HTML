<?php
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;
        // Mock DB connection
        $this->conn = $this->createMock(mysqli::class);
        $GLOBALS['conn'] = $this->conn;

        // Define fake session functions only if not defined
        if (!function_exists('header')) {
            function header($value) {
                $GLOBALS['headers'][] = $value;
            }
        }

        if (!function_exists('session_start')) {
            function session_start() {}
        }

   
        $_SESSION['user_id'] = 1;
        $GLOBALS['headers'] = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        unset($GLOBALS['headers']);
    }

    public function testPasswordsDoNotMatch()
    {
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
        $input = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'new_password' => 'password123',
            'confirm_password' => 'password123'
        ];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(false);
        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runAccountScript($input);
        $this->assertEquals(["error" => "Failed to update account info."], $result);
    }

    public function testSuccessfulAccountUpdate()
    {
        $input = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'new_password' => 'password123',
            'confirm_password' => 'password123'
        ];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runAccountScript($input);
        $this->assertEquals(["success" => "Account updated successfully."], $result);
    }

    private function createMockResult($data)
    {
        $result = $this->createMock(mysqli_result::class);
        $calls = is_array($data) ? [...$data, null] : [null];
        $result->method('fetch_assoc')->willReturnOnConsecutiveCalls(...$calls);
        return $result;
    }

    private function runAccountScript(array $postData): array
    {
        $_POST = $postData;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = 1;

        ob_start();
        include __DIR__ . '/../php/account.php'; // ✅ Adjust if needed
        $output = ob_get_clean();

        return json_decode($output, true);
    }
}
