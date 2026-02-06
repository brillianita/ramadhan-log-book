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
        // Get summary
        $totalUsers = $conn->query("SELECT COUNT(*) as c FROM users WHERE role = 'user'")->fetch_assoc()['c'];
        $activeUsers = $conn->query("
            SELECT COUNT(DISTINCT user_id) as c
            FROM daily_logs
            WHERE is_completed = 1
        ")->fetch_assoc()['c'];
        
        $totalDays = $conn->query("SELECT COUNT(*) as c FROM daily_content")->fetch_assoc()['c'];
        $totalLogs = $conn->query("SELECT COUNT(*) as c FROM daily_logs WHERE is_completed = 1")->fetch_assoc()['c'];
        $avgProgress = $totalUsers > 0 && $totalDays > 0 ? round(($totalLogs / ($totalUsers * $totalDays * 6)) * 100) : 0;
        
        $summary = [
            'total_users' => (int)$totalUsers,
            'active_users' => (int)$activeUsers,
            'avg_progress' => (int)$avgProgress,
            'total_logs' => (int)$totalLogs
        ];
        
        // Get user list with progress
        $query = $conn->query("
            SELECT 
                u.id,
                u.name,
                u.email,
                u.created_at,
                COUNT(DISTINCT CASE WHEN dl.is_completed = 1 THEN dl.daily_content_id END) as days_filled,
                COUNT(CASE WHEN dl.is_completed = 1 THEN 1 END) as tasks_completed,
                (SELECT COUNT(*) FROM user_journals WHERE user_id = u.id) as journals_written,
                ROUND((COUNT(DISTINCT CASE WHEN dl.is_completed = 1 THEN dl.daily_content_id END) / $totalDays) * 100) as progress
            FROM users u
            LEFT JOIN daily_logs dl ON u.id = dl.user_id
            WHERE u.role = 'user'
            GROUP BY u.id, u.name, u.email, u.created_at
            ORDER BY progress DESC, u.name ASC
        ");
        
        $users = [];
        while ($row = $query->fetch_assoc()) {
            $row['days_filled'] = (int)$row['days_filled'];
            $row['tasks_completed'] = (int)$row['tasks_completed'];
            $row['journals_written'] = (int)$row['journals_written'];
            $row['progress'] = (int)$row['progress'];
            $users[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'summary' => $summary,
            'users' => $users
        ]);
        break;
        
    case 'get_user_detail':
        $userId = intval($_GET['user_id']);
        
        // Get user info
        $userStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $user = $userStmt->get_result()->fetch_assoc();
        $userStmt->close();
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            break;
        }
        
        // Get daily progress
        $dailyStmt = $conn->prepare("
            SELECT 
                dc.day,
                dc.title,
                COUNT(CASE WHEN dl.is_completed = 1 THEN 1 END) as completed_tasks,
                (SELECT COUNT(*) FROM daily_task WHERE daily_content_id = dc.id) as total_tasks
            FROM daily_content dc
            LEFT JOIN daily_logs dl ON dc.id = dl.daily_content_id AND dl.user_id = ?
            GROUP BY dc.id, dc.day, dc.title
            ORDER BY dc.day
        ");
        $dailyStmt->bind_param("i", $userId);
        $dailyStmt->execute();
        $dailyResult = $dailyStmt->get_result();
        $dailyProgress = [];
        while ($row = $dailyResult->fetch_assoc()) {
            $dailyProgress[] = $row;
        }
        $dailyStmt->close();
        
        // Get journals
        $journalStmt = $conn->prepare("
            SELECT uj.*, dc.day, dc.title
            FROM user_journals uj
            JOIN daily_content dc ON uj.daily_content_id = dc.id
            WHERE uj.user_id = ?
            ORDER BY dc.day DESC
        ");
        $journalStmt->bind_param("i", $userId);
        $journalStmt->execute();
        $journalResult = $journalStmt->get_result();
        $journals = [];
        while ($row = $journalResult->fetch_assoc()) {
            $journals[] = $row;
        }
        $journalStmt->close();
        
        // Get mood history
        $moodStmt = $conn->prepare("
            SELECT mood, created_at
            FROM mood_check
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 30
        ");
        $moodStmt->bind_param("i", $userId);
        $moodStmt->execute();
        $moodResult = $moodStmt->get_result();
        $moods = [];
        while ($row = $moodResult->fetch_assoc()) {
            $moods[] = $row;
        }
        $moodStmt->close();
        
        // Get water history
        $waterStmt = $conn->prepare("
            SELECT level, created_at
            FROM water_level
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 30
        ");
        $waterStmt->bind_param("i", $userId);
        $waterStmt->execute();
        $waterResult = $waterStmt->get_result();
        $water = [];
        while ($row = $waterResult->fetch_assoc()) {
            $water[] = $row;
        }
        $waterStmt->close();
        
        echo json_encode([
            'success' => true,
            'user' => $user,
            'daily_progress' => $dailyProgress,
            'journals' => $journals,
            'moods' => $moods,
            'water' => $water
        ]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

closeDBConnection($conn);
?>