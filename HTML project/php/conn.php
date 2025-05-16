<?php
if (!isset($GLOBALS['conn'])) {
    $conn = new mysqli("localhost", "user", "pass", "db");
}
?>
