// creating the foundations
<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        $this->conn = $this->createMock(mysqli::class);
        $GLOBALS['conn'] = $this->conn;

        $_SESSION = [];

        if (!function_exists('session_start')) {
            function session_start() {}
        }
        
        if (!function_exists('session_regenerate_id')) {
            function session_regenerate_id($delete = false) { return true; }
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    private function mockJsonInput(array $input)
    {
        $GLOBALS['mocked_input'] = $input;
        
        if (!function_exists('file_get_contents')) {
            function file_get_contents($filename) {
                return json_encode($GLOBALS['mocked_input']);
            }
        }
        
        if (!function_exists('json_decode')) {
            function json_decode($json, $assoc) {
                return $GLOBALS['mocked_input'];
            }
        }
    }

    private function runLogin()
    {
        ob_start();
        include 'login.php';
        return json_decode(ob_get_clean(), true);
    }

    // Test functions
    public function testEmptyCredentials()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $input = [];
        
        $this->mockJsonInput($input);
        $output = $this->runLogin();
        
        $this->assertEquals(['error' => 'Email and password are required.'], $output);
    }

    public function testInvalidEmailFormat()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $input = ['email' => 'not-an-email', 'password' => '123'];
        
        $this->mockJsonInput($input);
        $output = $this->runLogin();
        
        $this->assertEquals(['error' => 'Invalid email format.'], $output);
    }

    public function testUserNotFound()
    {
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('num_rows')->willReturn(0);
        
        $this->conn->method('prepare')->willReturn($stmt);

        $this->mockJsonInput(['email' => 'noexist@test.com', 'password' => 'Pass']);
        $output = $this->runLogin();
        
        $this->assertEquals(['error' => 'Invalid email or password.'], $output);
    }
}

public function testWrongPassword()
    {
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('num_rows')->willReturn(1);
        $stmt->method('bind_result')->willReturnCallback(function(&$id, &$hash) {
            $id = 1;
            $hash = password_hash('correctpass', PASSWORD_DEFAULT);
        });
        $stmt->method('fetch')->willReturn(true);
        
        $this->conn->method('prepare')->willReturn($stmt);
        
        $this->mockJsonInput(['email' => 'test@test.com', 'password' => 'wrongpass']);
        $output = $this->runLoginScript();
        
        $this->assertEquals(['error' => 'Invalid email or password.'], $output);
    }

public function testSuccessfulLogin()
    {
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('num_rows')->willReturn(1);
        $stmt->method('bind_result')->willReturnCallback(function(&$id, &$hash) {
            $id = 111;
            $hash = password_hash('rightpass', PASSWORD_DEFAULT);
        });
        $stmt->method('fetch')->willReturn(true);
        
        $this->conn->method('prepare')->willReturn($stmt);
        
        $this->mockJsonInput(['email' => 'test@test.com', 'password' => 'rightpass']);
        $output = $this->runLoginScript();
        
        // Check output
        $this->assertEquals([
            'success' => 'Login successful!',
            'userId' => 111
        ], $output);
        
        // Check session was set
        $this->assertEquals(111, $_SESSION['user_id']);
        $this->assertEquals('test@test.com', $_SESSION['user_email']);
    }
                           
