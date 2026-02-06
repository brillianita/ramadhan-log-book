<?php
// save_mood.php - Save Mood Check
require_once '../db/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

$userId = getCurrentUserId();
$mood = intval($_POST['mood'] ?? 0);

// Mood values: 1 = 😁, 2 = 😐, 3 = 😴, 4 = 😟
if ($mood < 1 || $mood > 4) {
    echo json_encode(['success' => false, 'message' => 'Mood value tidak valid']);
    exit;
}

$conn = getDBConnection();

// Check if mood entry exists for today
$today = date('Y-m-d');
$stmt = $conn->prepare("
    SELECT id FROM mood_check 
    WHERE user_id = ? AND DATE(created_at) = ?
");
$stmt->bind_param("is", $userId, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing mood
    $row = $result->fetch_assoc();
    $moodId = $row['id'];
    $stmt->close();
    
    $updateStmt = $conn->prepare("
        UPDATE mood_check 
        SET mood = ? 
        WHERE id = ?
    ");
    $updateStmt->bind_param("ii", $mood, $moodId);
    
    if ($updateStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Mood berhasil diupdate',
            'action' => 'update',
            'mood_id' => $moodId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update mood']);
    }
    $updateStmt->close();
} else {
    // Insert new mood
    $stmt->close();
    
    $insertStmt = $conn->prepare("
        INSERT INTO mood_check (user_id, mood) 
        VALUES (?, ?)
    ");
    $insertStmt->bind_param("ii", $userId, $mood);
    
    if ($insertStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Mood berhasil disimpan',
            'action' => 'insert',
            'mood_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal simpan mood']);
    }
    $insertStmt->close();
}

closeDBConnection($conn);
?>