<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isLoggedIn() || getCurrentUser()['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$conn = getDBConnection();
$action = $_REQUEST['action'] ?? 'list';

switch ($action) {
    case 'list':
        $query = $conn->query("
            SELECT dc.*, 
                   (SELECT COUNT(*) FROM daily_task WHERE daily_content_id = dc.id) as task_count
            FROM daily_content dc 
            ORDER BY dc.day ASC
        ");
        
        $content = [];
        while ($row = $query->fetch_assoc()) {
            $content[] = $row;
        }
        
        echo json_encode(['success' => true, 'content' => $content]);
        break;
        
    case 'get':
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM daily_content WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($result) {
            // Get linked tasks
            $taskStmt = $conn->prepare("
                SELECT t.id, t.task_description, t.category_id
                FROM tasks t
                JOIN daily_task dt ON t.id = dt.task_id
                WHERE dt.daily_content_id = ?
            ");
            $taskStmt->bind_param("i", $id);
            $taskStmt->execute();
            $taskResult = $taskStmt->get_result();
            $tasks = [];
            while ($task = $taskResult->fetch_assoc()) {
                $tasks[] = $task;
            }
            $taskStmt->close();
            
            $result['linked_tasks'] = $tasks;
            echo json_encode(['success' => true, 'content' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not found']);
        }
        break;
        
    case 'create':
        $day = intval($_POST['day']);
        $surah_text = trim($_POST['surah_text']);
        $surah_name = trim($_POST['surah_name']);
        $title = trim($_POST['title']);
        $sub_title = trim($_POST['sub_title']);
        $description = trim($_POST['description']);
        $tips = trim($_POST['tips']);
        $daily_focus_key = trim($_POST['daily_focus_key']);
        
        // Check if day already exists
        $check = $conn->prepare("SELECT id FROM daily_content WHERE day = ?");
        $check->bind_param("i", $day);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $check->close();
            echo json_encode(['success' => false, 'message' => 'Day already exists']);
            break;
        }
        $check->close();
        
        $stmt = $conn->prepare("
            INSERT INTO daily_content 
            (surah_text, surah_name, title, sub_title, day, description, tips, daily_focus_key) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssisss", $surah_text, $surah_name, $title, $sub_title, $day, $description, $tips, $daily_focus_key);
        
        if ($stmt->execute()) {
            $insertId = $stmt->insert_id;
            
            // Link tasks if provided
            if (!empty($_POST['task_ids'])) {
                $taskIds = json_decode($_POST['task_ids'], true);
                $taskStmt = $conn->prepare("INSERT INTO daily_task (task_id, daily_content_id) VALUES (?, ?)");
                foreach ($taskIds as $taskId) {
                    $taskStmt->bind_param("ii", $taskId, $insertId);
                    $taskStmt->execute();
                }
                $taskStmt->close();
            }
            
            echo json_encode(['success' => true, 'id' => $insertId]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        break;
        
    case 'update':
        $id = intval($_POST['id']);
        $day = intval($_POST['day']);
        $surah_text = trim($_POST['surah_text']);
        $surah_name = trim($_POST['surah_name']);
        $title = trim($_POST['title']);
        $sub_title = trim($_POST['sub_title']);
        $description = trim($_POST['description']);
        $tips = trim($_POST['tips']);
        $daily_focus_key = trim($_POST['daily_focus_key']);
        
        // Check if day already exists for another record
        $check = $conn->prepare("SELECT id FROM daily_content WHERE day = ? AND id != ?");
        $check->bind_param("ii", $day, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $check->close();
            echo json_encode(['success' => false, 'message' => 'Day already used by another content']);
            break;
        }
        $check->close();
        
        $stmt = $conn->prepare("
            UPDATE daily_content 
            SET surah_text = ?, surah_name = ?, title = ?, sub_title = ?, day = ?, description = ?, tips = ?, daily_focus_key = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssssisssi", $surah_text, $surah_name, $title, $sub_title, $day, $description, $tips, $daily_focus_key, $id);
        
        if ($stmt->execute()) {
            // Update linked tasks
            if (isset($_POST['task_ids'])) {
                // Delete old links
                $delStmt = $conn->prepare("DELETE FROM daily_task WHERE daily_content_id = ?");
                $delStmt->bind_param("i", $id);
                $delStmt->execute();
                $delStmt->close();
                
                // Insert new links
                $taskIds = json_decode($_POST['task_ids'], true);
                if (!empty($taskIds)) {
                    $taskStmt = $conn->prepare("INSERT INTO daily_task (task_id, daily_content_id) VALUES (?, ?)");
                    foreach ($taskIds as $taskId) {
                        $taskStmt->bind_param("ii", $taskId, $id);
                        $taskStmt->execute();
                    }
                    $taskStmt->close();
                }
            }
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        break;
        
    case 'delete':
        $id = intval($_POST['id']);
        
        $stmt = $conn->prepare("DELETE FROM daily_content WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        break;
        
    case 'get_available_tasks':
        $query = $conn->query("
            SELECT t.*, c.name as category_name
            FROM tasks t
            JOIN categories c ON t.category_id = c.id
            ORDER BY c.id, t.id
        ");
        
        $tasks = [];
        while ($row = $query->fetch_assoc()) {
            $tasks[] = $row;
        }
        
        echo json_encode(['success' => true, 'tasks' => $tasks]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

closeDBConnection($conn);
?>