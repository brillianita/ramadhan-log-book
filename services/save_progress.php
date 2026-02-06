<?php
// save_progress.php - Save Daily Task Progress
require_once '../db/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

$userId = getCurrentUserId();
$dailyContentId = intval($_POST['daily_content_id'] ?? 0);
$taskId = intval($_POST['task_id'] ?? 0);
$isCompleted = intval($_POST['is_completed'] ?? 0);

if ($dailyContentId === 0 || $taskId === 0) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$conn = getDBConnection();

// Check if record exists
$stmt = $conn->prepare("
    SELECT id FROM daily_logs 
    WHERE user_id = ? AND daily_content_id = ? AND daily_task_id = ?
");
$stmt->bind_param("iii", $userId, $dailyContentId, $taskId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing record
    $row = $result->fetch_assoc();
    $logId = $row['id'];
    $stmt->close();
    
    $updateStmt = $conn->prepare("
        UPDATE daily_logs 
        SET is_completed = ? 
        WHERE id = ?
    ");
    $updateStmt->bind_param("ii", $isCompleted, $logId);
    
    if ($updateStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Progress berhasil diupdate',
            'action' => 'update'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update progress']);
    }
    $updateStmt->close();
} else {
    // Insert new record
    $stmt->close();
    
    $insertStmt = $conn->prepare("
        INSERT INTO daily_logs (user_id, daily_content_id, daily_task_id, is_completed) 
        VALUES (?, ?, ?, ?)
    ");
    $insertStmt->bind_param("iiii", $userId, $dailyContentId, $taskId, $isCompleted);
    
    if ($insertStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Progress berhasil disimpan',
            'action' => 'insert',
            'log_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal simpan progress']);
    }
    $insertStmt->close();
}

closeDBConnection($conn);
?>