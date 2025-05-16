<?php
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
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

    private function runSettings()
    {
        ob_start();
        include 'settings.php'; // Change to your PHP file name
        return json_decode(ob_get_clean(), true);
    }

    // Test fetching settings when user is logged in
    public function testFetchSettings()
    {
        $_SESSION['user_id'] = 1;

        $settingsData = [
            'dark_mode' => 1,
            'notification_enabled' => 0
        ];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult($settingsData));

        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runSettings();

        $this->assertEquals($settingsData, $result);
    }

    // Test saving settings
    public function testSaveSettingsUpdate()
    {
        $_SESSION['user_id'] = 1;

        $data = json_encode(['dark_mode' => 1, 'notification_enabled' => 1]);
        $this->mockInput($data);

        $stmtSelect = $this->createMock(mysqli_stmt::class);
        $stmtSelect->method('execute')->willReturn(true);
        $stmtSelect->method('store_result')->willReturn(null);
        $stmtSelect->method('num_rows')->willReturn(1); // Simulate existing settings

        $stmtUpdate = $this->createMock(mysqli_stmt::class);
        $stmtUpdate->method('execute')->willReturn(true);

        $this->conn->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtSelect, $stmtUpdate);

        $result = $this->runSettings();

        $this->assertEquals(['success' => 'Settings updated.'], $result);
    }

    // Test saving settings when no existing settings are found
    public function testSaveSettingsInsert()
    {
        $_SESSION['user_id'] = 1;

        $data = json_encode(['dark_mode' => 0, 'notification_enabled' => 0]);
        $this->mockInput($data);

        $stmtSelect = $this->createMock(mysqli_stmt::class);
        $stmtSelect->method('execute')->willReturn(true);
        $stmtSelect->method('store_result')->willReturn(null);
        $stmtSelect->method('num_rows')->willReturn(0); // Simulate no existing settings

        $stmtInsert = $this->createMock(mysqli_stmt::class);
        $stmtInsert->method('execute')->willReturn(true);

        $this->conn->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtSelect, $stmtInsert);

        $result = $this->runSettings();

        $this->assertEquals(['success' => 'Settings updated.'], $result);
    }

    // Helper method to create a mock result for fetching settings
    private function createMockResult($data)
    {
        $result = $this->createMock(mysqli_result::class);
        $result->method('fetch_assoc')->willReturn($data);
        return $result;
    }

    // Helper method to mock input data
    private function mockInput($data)
    {
        $inputStream = fopen('php://input', 'w');
        fwrite($inputStream, $data);
        fclose($inputStream);
    }
}
