<?php
require_once './db/config.php';
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
            SELECT t.*, c.name as category_name,
                   (SELECT COUNT(*) FROM daily_logs WHERE daily_task_id = t.id AND is_completed = 1) as completion_count
            FROM tasks t
            JOIN categories c ON t.category_id = c.id
            ORDER BY c.id, t.id
        ");
        
        $tasks = [];
        while ($row = $query->fetch_assoc()) {
            $tasks[] = $row;
        }
        
        // Get summary
        $fisik = $conn->query("SELECT COUNT(*) as c FROM tasks WHERE category_id = 1")->fetch_assoc()['c'];
        $spiritual = $conn->query("SELECT COUNT(*) as c FROM tasks WHERE category_id = 2")->fetch_assoc()['c'];
        
        echo json_encode([
            'success' => true, 
            'tasks' => $tasks,
            'summary' => [
                'total' => count($tasks),
                'fisik' => (int)$fisik,
                'spiritual' => (int)$spiritual
            ]
        ]);
        break;
        
    case 'get':
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("
            SELECT t.*, c.name as category_name
            FROM tasks t
            JOIN categories c ON t.category_id = c.id
            WHERE t.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($result) {
            echo json_encode(['success' => true, 'task' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not found']);
        }
        break;
        
    case 'create':
        $task_description = trim($_POST['task_description']);
        $category_id = intval($_POST['category_id']);
        
        if (!in_array($category_id, [1, 2])) {
            echo json_encode(['success' => false, 'message' => 'Invalid category']);
            break;
        }
        
        $stmt = $conn->prepare("
            INSERT INTO tasks (task_description, category_id) 
            VALUES (?, ?)
        ");
        $stmt->bind_param("si", $task_description, $category_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        break;
        
    case 'update':
        $id = intval($_POST['id']);
        $task_description = trim($_POST['task_description']);
        $category_id = intval($_POST['category_id']);
        
        if (!in_array($category_id, [1, 2])) {
            echo json_encode(['success' => false, 'message' => 'Invalid category']);
            break;
        }
        
        $stmt = $conn->prepare("
            UPDATE tasks 
            SET task_description = ?, category_id = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sii", $task_description, $category_id, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        break;
        
    case 'delete':
        $id = intval($_POST['id']);
        
        // Check if task is being used
        $check = $conn->prepare("SELECT COUNT(*) as c FROM daily_task WHERE task_id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $count = $check->get_result()->fetch_assoc()['c'];
        $check->close();
        
        if ($count > 0) {
            echo json_encode(['success' => false, 'message' => 'Task is being used in ' . $count . ' day(s). Cannot delete.']);
            break;
        }
        
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        break;
        
    case 'get_categories':
        $query = $conn->query("SELECT * FROM categories ORDER BY id");
        $categories = [];
        while ($row = $query->fetch_assoc()) {
            $categories[] = $row;
        }
        echo json_encode(['success' => true, 'categories' => $categories]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

closeDBConnection($conn);
?>