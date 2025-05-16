    <?php
    header('Content-Type: application/json');
    include 'conn.php'; // Include the database connection
    session_start();


    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        error_log("Session user_id not set");
        echo json_encode(["error" => "User not logged in"]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    error_log("Logged-in user_id: $user_id");


    // Retrieve the username from the session
    $username = $_SESSION['username'];
    error_log("Logged-in username: $username");

    // Check connection
    if ($conn->connect_error) {
        echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
        exit;
    }

    // Prepare the SQL query to fetch habits for the logged-in user
    $sql = "SELECT 
        habits.name AS habit_name,
        CONCAT(habits.duration, ' ', habits.duration_unit) AS duration,
        habits.created_at
    FROM
        habits
    WHERE
        habits.user_id = ?";
        
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);


    // Output the data as JSON
    echo json_encode($habits);

    $stmt->close();
    $conn->close();
    ?>