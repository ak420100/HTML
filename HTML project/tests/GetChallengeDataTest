<?php
use PHPUnit\Framework\TestCase;

class GetChallengeDataTest extends TestCase
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

    private function runGetChallengeData()
    {
        ob_start();
        include 'get_challenge_data.php';
        return json_decode(ob_get_clean(), true);
    }

    public function testUserNotLoggedIn()
    {
        $result = $this->runGetChallengeData();
        $this->assertEquals(['error' => 'You must be logged in.'], $result);
    }

    public function testMissingParameters()
    {
        $_SESSION['user_id'] = 1;
        $_GET['friend_id'] = 0; // Simulate missing friend_id
        $_GET['habit'] = '';

        $result = $this->runGetChallengeData();
        $this->assertEquals(['error' => 'Missing friend ID or habit name.'], $result);
    }

    public function testFriendHabitNotFound()
    {
        $_SESSION['user_id'] = 1;
        $_GET['friend_id'] = 2;
        $_GET['habit'] = 'Running';

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(null);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult(null));

        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runGetChallengeData();
        $this->assertEquals(['error' => 'Your friend does not have this habit.'], $result);
    }

    public function testUserHabitNotFoundAndCopy()
    {
        $_SESSION['user_id'] = 1;
        $_GET['friend_id'] = 2;
        $_GET['habit'] = 'Running';

        // Mock friend's habit with a valid duration unit
        $friendHabit = [
            'name' => 'Running',
            'duration' => 30,
            'duration_unit' => 'days', // Valid unit
            'progress_count' => 5
        ];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(null);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult($friendHabit));

        $this->conn->method('prepare')->willReturn($stmt);

        // Mock user's habit not found
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(null);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult(null));

        $this->conn->method('prepare')->willReturn($stmt);

        // Mock insertion of friend's habit for the user
        $insertStmt = $this->createMock(mysqli_stmt::class);
        $insertStmt->method('execute')->willReturn(true);
        $this->conn->method('prepare')->willReturn($insertStmt);

        // Re-fetch user's habit
        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(null);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult([
            ['progress_count' => 0, 'duration' => 30]
        ]));

        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runGetChallengeData();
        $expected = [
            'me' => [
                'progress' => 0,
                'duration' => 30,
                'percent' => 0
            ],
            'friend' => [
                'progress' => 5,
                'duration' => 30,
                'percent' => 16
            ]
        ];
        $this->assertEquals($expected, $result);
    }

    public function testSuccessfulDataRetrieval()
    {
        $_SESSION['user_id'] = 1;
        $_GET['friend_id'] = 2;
        $_GET['habit'] = 'Running';

        // Mock friend's habit with valid duration
        $friendHabit = [
            'name' => 'Running',
            'duration' => 30,
            'duration_unit' => 'days', // Valid unit
            'progress_count' => 5
        ];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(null);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult($friendHabit));

        $this->conn->method('prepare')->willReturn($stmt);

        // Mock user's habit
        $userHabit = [
            'progress_count' => 10,
            'duration' => 30
        ];

        $stmt = $this->createMock(mysqli_stmt::class);
        $stmt->method('bind_param')->willReturn(null);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('get_result')->willReturn($this->createMockResult($userHabit));

        $this->conn->method('prepare')->willReturn($stmt);

        $result = $this->runGetChallengeData();
        $expected = [
            'me' => [
                'progress' => 10,
                'duration' => 30,
                'percent' => 33
            ],
            'friend' => [
                'progress' => 5,
                'duration' => 30,
                'percent' => 16
            ]
        ];
        $this->assertEquals($expected, $result);
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
