<?php
// save_water.php - Save Water Intake Level
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

$userId = getCurrentUserId();
$level = intval($_POST['level'] ?? 0);

// Level range: 0-8 (8 glasses)
if ($level < 0 || $level > 8) {
    echo json_encode(['success' => false, 'message' => 'Water level tidak valid (0-8)']);
    exit;
}

$conn = getDBConnection();

// Check if water level entry exists for today
$today = date('Y-m-d');
$stmt = $conn->prepare("
    SELECT id FROM water_level 
    WHERE user_id = ? AND DATE(created_at) = ?
");
$stmt->bind_param("is", $userId, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing water level
    $row = $result->fetch_assoc();
    $waterId = $row['id'];
    $stmt->close();
    
    $updateStmt = $conn->prepare("
        UPDATE water_level 
        SET level = ? 
        WHERE id = ?
    ");
    $updateStmt->bind_param("ii", $level, $waterId);
    
    if ($updateStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Water level berhasil diupdate',
            'action' => 'update',
            'water_id' => $waterId,
            'level' => $level
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update water level']);
    }
    $updateStmt->close();
} else {
    // Insert new water level
    $stmt->close();
    
    $insertStmt = $conn->prepare("
        INSERT INTO water_level (user_id, level) 
        VALUES (?, ?)
    ");
    $insertStmt->bind_param("ii", $userId, $level);
    
    if ($insertStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Water level berhasil disimpan',
            'action' => 'insert',
            'water_id' => $conn->insert_id,
            'level' => $level
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal simpan water level']);
    }
    $insertStmt->close();
}

closeDBConnection($conn);
?>