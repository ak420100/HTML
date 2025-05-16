<?php
use PHPUnit\Framework\TestCase;

class CreateHabTest extends TestCase
{
    protected function setUp(): void
    {
        $this->conn = $this->createMock(mysqli::class);
        $GLOBALS['conn'] = $this->conn;
        $_SESSION = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        if (!function_exists('header')) {
            function header($location) {
                $GLOBALS['header_location'] = $location;
            }
        }
      
        if (!function_exists('die')) {
            function die($message) {
                $GLOBALS['die_message'] = $message;
                throw new Exception($message);
            }
        }
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['header_location']);
        unset($GLOBALS['die_message']);
        $_SESSION = [];
        $_POST = [];
    }

    private function runCreateHab()
    {
        ob_start();
        try {
            include 'createhab.php';
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
        
        $this->conn->method('prepare')->willReturn($mockStmt);

        $this->runCreateHab();
        
        $this->assertEquals("index1.php", $GLOBALS['header_location'] ?? '');
    }

   
}
