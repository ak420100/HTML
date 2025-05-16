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

    private function runAccountScript($input)
    {
        // Mocking the input data
        $inputStream = fopen('php://input', 'w');
        fwrite($inputStream, json_encode($input));
        fclose($inputStream);

        ob_start();
        include 'account.php'; // Adjust to actual file location
        return json_decode(ob_get_clean(), true);
    }

    public function testUserNotLoggedIn()
    {
        $result = $this->runAccountScript([]);
        $this->assertEquals(["error" => "You must be logged in."], $result);
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

    public function testFailedToDeleteExistingHabits()
    {
        $_SESSION['user_id'] = 1;
        $input = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'new_password' => 'password123',
            'confirm_password' => 'password123',
            'habits' => ['Running']
        ];

        // Mock account update
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($stmt);

        // Simulate failure in deleting habits
        $delete_stmt = $this->createMock(mysqli_stmt::class);
        $delete_stmt->method('execute')->willReturn(false);
        $this->conn->method('prepare')->willReturnOnConsecutiveCalls($stmt, $delete_stmt);

        $result = $this->runAccountScript($input);
        $this->assertEquals(["error" => "Failed to delete existing habits."], $result);
    }

    public function testFailedToInsertNewHabits()
    {
        $_SESSION['user_id'] = 1;
        $input = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'new_password' => 'password123',
            'confirm_password' => 'password123',
            'habits' => ['Running']
        ];

        // Mock account update
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($stmt);

        // Simulate successful deletion of habits
        $delete_stmt = $this->createMock(mysqli_stmt::class);
        $delete_stmt->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturnOnConsecutiveCalls($stmt, $delete_stmt);

        // Simulate failure in habit insertion
        $insert_stmt = $this->createMock(mysqli_stmt::class);
        $insert_stmt->method('execute')->willReturn(false);
        $this->conn->method('prepare')->willReturn($insert_stmt);

        $result = $this->runAccountScript($input);
        $this->assertEquals(["error" => "Failed to insert habit: Running"], $result);
    }

    public function testSuccessfulUpdateWithNewHabits()
    {
        $_SESSION['user_id'] = 1;
        $input = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'new_password' => 'password123',
            'confirm_password' => 'password123',
            'habits' => ['Running']
        ];

        // Mock account update
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($stmt);

        // Simulate successful deletion of habits
        $delete_stmt = $this->createMock(mysqli_stmt::class);
        $delete_stmt->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturnOnConsecutiveCalls($stmt, $delete_stmt);

        // Mock successful habit insertion
        $insert_stmt = $this->createMock(mysqli_stmt::class);
        $insert_stmt->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($insert_stmt);

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
