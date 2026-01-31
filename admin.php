<?php
require_once '../db/config.php';

// Simple auth check - you can enhance this
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();

// Get all users with their data counts
$usersQuery = "
    SELECT 
        u.id,
        u.name,
        u.email,
        u.created_at,
        COUNT(DISTINCT dl.id) as tasks_completed,
        COUNT(DISTINCT uj.id) as journals_written,
        COUNT(DISTINCT mc.id) as moods_tracked,
        COUNT(DISTINCT wl.id) as water_logs
    FROM users u
    LEFT JOIN daily_logs dl ON u.id = dl.user_id
    LEFT JOIN user_journals uj ON u.id = uj.user_id
    LEFT JOIN mood_check mc ON u.id = mc.user_id
    LEFT JOIN water_level wl ON u.id = wl.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
";

$usersResult = $conn->query($usersQuery);

// Get selected user detail
$selectedUserId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$userDetail = null;

if ($selectedUserId) {
    // Get user info
    $userStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $userStmt->bind_param("i", $selectedUserId);
    $userStmt->execute();
    $userDetail = $userStmt->get_result()->fetch_assoc();
    $userStmt->close();
    
    // Get tasks completed
    $tasksStmt = $conn->prepare("
        SELECT dl.*, t.task_description, t.category_id, dc.day
        FROM daily_logs dl
        JOIN tasks t ON dl.daily_task_id = t.id
        JOIN daily_content dc ON dl.daily_content_id = dc.id
        WHERE dl.user_id = ? AND dl.is_completed = 1
        ORDER BY dl.created_at DESC
    ");
    $tasksStmt->bind_param("i", $selectedUserId);
    $tasksStmt->execute();
    $tasksResult = $tasksStmt->get_result();
    
    // Get journals
    $journalsStmt = $conn->prepare("
        SELECT uj.*, dc.day, dc.title
        FROM user_journals uj
        JOIN daily_content dc ON uj.daily_content_id = dc.id
        WHERE uj.user_id = ?
        ORDER BY uj.created_at DESC
    ");
    $journalsStmt->bind_param("i", $selectedUserId);
    $journalsStmt->execute();
    $journalsResult = $journalsStmt->get_result();
    
    // Get mood tracking
    $moodStmt = $conn->prepare("
        SELECT * FROM mood_check 
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 30
    ");
    $moodStmt->bind_param("i", $selectedUserId);
    $moodStmt->execute();
    $moodResult = $moodStmt->get_result();
    
    // Get water tracking
    $waterStmt = $conn->prepare("
        SELECT * FROM water_level 
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 30
    ");
    $waterStmt->bind_param("i", $selectedUserId);
    $waterStmt->execute();
    $waterResult = $waterStmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ramadhan Glow Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f5f5;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .mood-emoji {
            font-size: 24px;
        }
    </style>
</head>
<body class="p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800" style="font-family: 'Playfair Display', serif;">
                    📊 Admin Dashboard
                </h1>
                <p class="text-gray-600 mt-1">Ramadhan Glow Up - User Data Overview</p>
            </div>
            <div class="flex gap-3">
                <a href="dashboard.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-user mr-2"></i>My Dashboard
                </a>
                <a href="index.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                <i class="fas fa-users text-3xl mb-2"></i>
                <h3 class="text-2xl font-bold"><?php echo $usersResult->num_rows; ?></h3>
                <p class="text-sm opacity-90">Total Users</p>
            </div>
            <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white">
                <i class="fas fa-check-circle text-3xl mb-2"></i>
                <h3 class="text-2xl font-bold">
                    <?php 
                    $totalTasks = $conn->query("SELECT COUNT(*) as total FROM daily_logs WHERE is_completed = 1")->fetch_assoc()['total'];
                    echo $totalTasks;
                    ?>
                </h3>
                <p class="text-sm opacity-90">Tasks Completed</p>
            </div>
            <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
                <i class="fas fa-book text-3xl mb-2"></i>
                <h3 class="text-2xl font-bold">
                    <?php 
                    $totalJournals = $conn->query("SELECT COUNT(*) as total FROM user_journals")->fetch_assoc()['total'];
                    echo $totalJournals;
                    ?>
                </h3>
                <p class="text-sm opacity-90">Journals Written</p>
            </div>
            <div class="card bg-gradient-to-br from-orange-500 to-orange-600 text-white">
                <i class="fas fa-smile text-3xl mb-2"></i>
                <h3 class="text-2xl font-bold">
                    <?php 
                    $totalMoods = $conn->query("SELECT COUNT(*) as total FROM mood_check")->fetch_assoc()['total'];
                    echo $totalMoods;
                    ?>
                </h3>
                <p class="text-sm opacity-90">Mood Entries</p>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-users text-blue-600 mr-2"></i>Registered Users
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-4">ID</th>
                            <th class="text-left py-3 px-4">Name</th>
                            <th class="text-left py-3 px-4">Email</th>
                            <th class="text-center py-3 px-4">Tasks</th>
                            <th class="text-center py-3 px-4">Journals</th>
                            <th class="text-center py-3 px-4">Moods</th>
                            <th class="text-center py-3 px-4">Water Logs</th>
                            <th class="text-left py-3 px-4">Registered</th>
                            <th class="text-center py-3 px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $usersResult->fetch_assoc()): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $user['id']; ?></td>
                            <td class="py-3 px-4 font-semibold"><?php echo htmlspecialchars($user['name']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="py-3 px-4 text-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">
                                    <?php echo $user['tasks_completed']; ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-sm">
                                    <?php echo $user['journals_written']; ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-sm">
                                    <?php echo $user['moods_tracked']; ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">
                                    <?php echo $user['water_logs']; ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="?user_id=<?php echo $user['id']; ?>" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User Detail -->
        <?php if ($userDetail): ?>
        <div class="card bg-gradient-to-r from-purple-50 to-blue-50">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-user-circle text-purple-600 mr-2"></i>
                    Detail: <?php echo htmlspecialchars($userDetail['name']); ?>
                </h2>
                <a href="admin.php" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>

            <!-- Tasks Completed -->
            <div class="mb-6">
                <h3 class="text-lg font-bold mb-3 text-gray-700">
                    <i class="fas fa-check-square text-green-600 mr-2"></i>Tasks Completed
                </h3>
                <div class="bg-white rounded-lg p-4">
                    <?php if ($tasksResult->num_rows > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <?php while ($task = $tasksResult->fetch_assoc()): ?>
                            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($task['task_description']); ?></p>
                                    <p class="text-xs text-gray-600">Day <?php echo $task['day']; ?> • <?php echo date('d M Y H:i', strtotime($task['created_at'])); ?></p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 italic">Belum ada tasks yang diselesaikan</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Journals -->
            <div class="mb-6">
                <h3 class="text-lg font-bold mb-3 text-gray-700">
                    <i class="fas fa-book text-purple-600 mr-2"></i>Journal Entries
                </h3>
                <div class="bg-white rounded-lg p-4 space-y-4">
                    <?php if ($journalsResult->num_rows > 0): ?>
                        <?php while ($journal = $journalsResult->fetch_assoc()): ?>
                        <div class="border-l-4 border-purple-500 pl-4 py-2">
                            <p class="font-bold text-gray-800 mb-2">Day <?php echo $journal['day']; ?>: <?php echo htmlspecialchars($journal['title']); ?></p>
                            <div class="mb-2">
                                <p class="text-sm font-semibold text-purple-600">Why Ramadhan:</p>
                                <p class="text-sm text-gray-700"><?php echo nl2br(htmlspecialchars($journal['ramadhan_why'])); ?></p>
                            </div>
                            <div class="mb-2">
                                <p class="text-sm font-semibold text-purple-600">Bad Habit:</p>
                                <p class="text-sm text-gray-700"><?php echo nl2br(htmlspecialchars($journal['bad_habit'])); ?></p>
                            </div>
                            <p class="text-xs text-gray-500"><?php echo date('d M Y H:i', strtotime($journal['created_at'])); ?></p>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-gray-500 italic">Belum ada journal yang ditulis</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mood & Water -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mood Tracking -->
                <div>
                    <h3 class="text-lg font-bold mb-3 text-gray-700">
                        <i class="fas fa-smile text-orange-600 mr-2"></i>Mood Tracking
                    </h3>
                    <div class="bg-white rounded-lg p-4 space-y-2">
                        <?php if ($moodResult->num_rows > 0): ?>
                            <?php 
                            $moodEmojis = [1 => '😁', 2 => '😐', 3 => '😴', 4 => '😟'];
                            while ($mood = $moodResult->fetch_assoc()): 
                            ?>
                            <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                                <span class="mood-emoji"><?php echo $moodEmojis[$mood['mood']]; ?></span>
                                <span class="text-sm text-gray-600"><?php echo date('d M Y H:i', strtotime($mood['created_at'])); ?></span>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-gray-500 italic">Belum ada mood yang dicatat</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Water Tracking -->
                <div>
                    <h3 class="text-lg font-bold mb-3 text-gray-700">
                        <i class="fas fa-tint text-blue-600 mr-2"></i>Water Intake
                    </h3>
                    <div class="bg-white rounded-lg p-4 space-y-2">
                        <?php if ($waterResult->num_rows > 0): ?>
                            <?php while ($water = $waterResult->fetch_assoc()): ?>
                            <div class="flex justify-between items-center p-2 bg-blue-50 rounded">
                                <div class="flex gap-1">
                                    <?php for ($i = 0; $i < $water['level']; $i++): ?>
                                        <i class="fas fa-tint text-blue-500"></i>
                                    <?php endfor; ?>
                                    <span class="ml-2 font-semibold text-blue-700"><?php echo $water['level']; ?>/8 gelas</span>
                                </div>
                                <span class="text-sm text-gray-600"><?php echo date('d M Y H:i', strtotime($water['created_at'])); ?></span>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-gray-500 italic">Belum ada water intake yang dicatat</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php closeDBConnection($conn); ?>