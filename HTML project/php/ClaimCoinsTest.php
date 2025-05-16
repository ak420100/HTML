<?php
use PHPUnit\Framework\TestCase;

class ClaimCoinsTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;
        $this->conn = $this->createMock(mysqli::class);
        $GLOBALS['conn'] = $this->conn;

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

    private function runClaimCoins(): array
    {
        $_SESSION['user_id'] = 1; // ✅ Ensure user is "logged in"
        
        ob_start();
        include __DIR__ . '/../php/claim_coins.php'; // ✅ Update path if needed
        return json_decode(ob_get_clean(), true);
    }

    public function testAlreadyClaimedToday()
    {
        $testStatement = $this->createMock(mysqli_stmt::class);

        $testStatement->method('execute')->willReturn(true);
        $testStatement->method('bind_result')->willReturnCallback(
            function (&$coins, &$last_claimed) {
                $coins = 100;
                $last_claimed = date('Y-m-d H:i:s', strtotime('-1 hour'));
            }
        );
        $testStatement->method('fetch')->willReturn(true);
        $this->conn->method('prepare')->willReturn($testStatement);

        $result = $this->runClaimCoins();

        $this->assertEquals([
            'error' => 'Already claimed today',
            'next_claim' => date('Y-m-d H:i:s', strtotime('+23 hours'))
        ], $result);
    }

    public function testSuccessfulClaim()
    {
        $selectTest = $this->createMock(mysqli_stmt::class);
        $selectTest->method('execute')->willReturn(true);
        $selectTest->method('bind_result')->willReturnCallback(
            function (&$coins, &$last_claimed) {
                $coins = 100;
                $last_claimed = null;
            }
        );
        $selectTest->method('fetch')->willReturn(true);

        $updateTest = $this->createMock(mysqli_stmt::class);
        $updateTest->method('execute')->willReturn(true);

        $this->conn->method('prepare')
            ->willReturnOnConsecutiveCalls($selectTest, $updateTest);

        $result = $this->runClaimCoins();

        $this->assertEquals([
            'success' => 'Claimed 50 coins!',
            'new_balance' => 150
        ], $result);
    }
}
