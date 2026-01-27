<?php
// get_user_data.php - Retrieve User Data for Display
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

$userId = getCurrentUserId();
$dailyContentId = intval($_GET['daily_content_id'] ?? 1); // Default Day 1

$conn = getDBConnection();

// Get completed tasks
$tasksStmt = $conn->prepare("
    SELECT daily_task_id, is_completed 
    FROM daily_logs 
    WHERE user_id = ? AND daily_content_id = ?
");
$tasksStmt->bind_param("ii", $userId, $dailyContentId);
$tasksStmt->execute();
$tasksResult = $tasksStmt->get_result();

$completedTasks = [];
while ($row = $tasksResult->fetch_assoc()) {
    $completedTasks[$row['daily_task_id']] = (bool)$row['is_completed'];
}
$tasksStmt->close();

// Get journal entry
$journalStmt = $conn->prepare("
    SELECT ramadhan_why, bad_habit 
    FROM user_journals 
    WHERE user_id = ? AND daily_content_id = ?
");
$journalStmt->bind_param("ii", $userId, $dailyContentId);
$journalStmt->execute();
$journalResult = $journalStmt->get_result();

$journal = null;
if ($journalResult->num_rows > 0) {
    $journal = $journalResult->fetch_assoc();
}
$journalStmt->close();

// Get today's mood
$today = date('Y-m-d');
$moodStmt = $conn->prepare("
    SELECT mood 
    FROM mood_check 
    WHERE user_id = ? AND DATE(created_at) = ?
    ORDER BY created_at DESC 
    LIMIT 1
");
$moodStmt->bind_param("is", $userId, $today);
$moodStmt->execute();
$moodResult = $moodStmt->get_result();

$mood = null;
if ($moodResult->num_rows > 0) {
    $moodRow = $moodResult->fetch_assoc();
    $mood = intval($moodRow['mood']);
}
$moodStmt->close();

// Get today's water level
$waterStmt = $conn->prepare("
    SELECT level 
    FROM water_level 
    WHERE user_id = ? AND DATE(created_at) = ?
    ORDER BY created_at DESC 
    LIMIT 1
");
$waterStmt->bind_param("is", $userId, $today);
$waterStmt->execute();
$waterResult = $waterStmt->get_result();

$waterLevel = 0;
if ($waterResult->num_rows > 0) {
    $waterRow = $waterResult->fetch_assoc();
    $waterLevel = intval($waterRow['level']);
}
$waterStmt->close();

closeDBConnection($conn);

// Return all data
echo json_encode([
    'success' => true,
    'data' => [
        'completed_tasks' => $completedTasks,
        'journal' => $journal,
        'mood' => $mood,
        'water_level' => $waterLevel
    ]
]);
?>