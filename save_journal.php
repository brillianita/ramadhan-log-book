<?php
// save_journal.php - Save User Journal Entries
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

$userId = getCurrentUserId();
$dailyContentId = intval($_POST['daily_content_id'] ?? 0);
$ramadhanWhy = trim($_POST['ramadhan_why'] ?? '');
$badHabit = trim($_POST['bad_habit'] ?? '');

if ($dailyContentId === 0) {
    echo json_encode(['success' => false, 'message' => 'Daily content ID tidak valid']);
    exit;
}

$conn = getDBConnection();

// Check if journal entry exists
$stmt = $conn->prepare("
    SELECT id FROM user_journals 
    WHERE user_id = ? AND daily_content_id = ?
");
$stmt->bind_param("ii", $userId, $dailyContentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing journal
    $row = $result->fetch_assoc();
    $journalId = $row['id'];
    $stmt->close();
    
    $updateStmt = $conn->prepare("
        UPDATE user_journals 
        SET ramadhan_why = ?, bad_habit = ? 
        WHERE id = ?
    ");
    $updateStmt->bind_param("ssi", $ramadhanWhy, $badHabit, $journalId);
    
    if ($updateStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Journal berhasil diupdate',
            'action' => 'update',
            'journal_id' => $journalId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update journal']);
    }
    $updateStmt->close();
} else {
    // Insert new journal
    $stmt->close();
    
    $insertStmt = $conn->prepare("
        INSERT INTO user_journals (user_id, daily_content_id, ramadhan_why, bad_habit) 
        VALUES (?, ?, ?, ?)
    ");
    $insertStmt->bind_param("iiss", $userId, $dailyContentId, $ramadhanWhy, $badHabit);
    
    if ($insertStmt->execute()) {
        $journalId = $conn->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Journal berhasil disimpan',
            'action' => 'insert',
            'journal_id' => $journalId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal simpan journal']);
    }
    $insertStmt->close();
}

closeDBConnection($conn);
?>