<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
if ($user['role'] !== 'admin') {
    die('Access denied. Admin only.');
}

$conn = getDBConnection();

// Statistics
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$activeUsers = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM daily_logs WHERE is_completed = 1")->fetch_assoc()['count'];
$totalLogsFilled = $conn->query("SELECT COUNT(*) as count FROM daily_logs WHERE is_completed = 1")->fetch_assoc()['count'];
$totalDays = $conn->query("SELECT COUNT(*) as count FROM daily_content")->fetch_assoc()['count'];
$totalJournals = $conn->query("SELECT COUNT(*) as count FROM user_journals")->fetch_assoc()['count'];
$avgWater = $conn->query("SELECT AVG(level) as avg FROM water_level")->fetch_assoc()['avg'] ?? 0;

// Charts data
$dailyLogsData = [];
$q = $conn->query("SELECT dc.day, COUNT(dl.id) as count FROM daily_content dc LEFT JOIN daily_logs dl ON dc.id = dl.daily_content_id AND dl.is_completed = 1 GROUP BY dc.day ORDER BY dc.day LIMIT 30");
while ($r = $q->fetch_assoc()) $dailyLogsData[] = $r;

$taskPopularityData = [];
$q = $conn->query("SELECT t.task_description, COUNT(dl.id) as count FROM tasks t LEFT JOIN daily_logs dl ON t.id = dl.daily_task_id AND dl.is_completed = 1 GROUP BY t.id, t.task_description ORDER BY count DESC LIMIT 5");
while ($r = $q->fetch_assoc()) $taskPopularityData[] = $r;

$merawatDiri = $conn->query("SELECT COUNT(*) as c FROM daily_logs dl JOIN tasks t ON dl.daily_task_id = t.id WHERE t.category_id = 1 AND dl.is_completed = 1")->fetch_assoc()['c'];
$menataHati = $conn->query("SELECT COUNT(*) as c FROM daily_logs dl JOIN tasks t ON dl.daily_task_id = t.id WHERE t.category_id = 2 AND dl.is_completed = 1")->fetch_assoc()['c'];

$moodData = ['1' => 0, '2' => 0, '3' => 0, '4' => 0];
$q = $conn->query("SELECT mood, COUNT(*) as count FROM mood_check GROUP BY mood");
while ($r = $q->fetch_assoc()) $moodData[$r['mood']] = $r['count'];

$completionPercentage = ($totalUsers > 0 && $totalDays > 0) ? round(($totalLogsFilled / ($totalUsers * $totalDays * 6)) * 100) : 0;

closeDBConnection($conn);
$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ramadhan Glow Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="admin_crud.js" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); }
        .nav-link { transition: all 0.2s ease; }
        .nav-link.active { background: #ecfdf5; color: #047857; font-weight: 600; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); }
        .nav-link:not(.active):hover { background: #f9fafb; color: #111827; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <div class="md:hidden fixed top-0 left-0 right-0 z-50 bg-emerald-700 text-white p-4 flex items-center justify-between shadow">
            <h1 class="font-bold text-lg">Ramadhan Glow Up</h1>
            <button onclick="toggleSidebar()"><i class="fas fa-bars text-xl"></i></button>
        </div>

        <div id="sidebar" class="sidebar fixed inset-y-0 left-0 z-40 w-64 bg-white shadow-xl transform transition-transform md:translate-x-0 md:static md:h-screen border-r border-gray-100 mt-16 md:mt-0">
            <div class="h-full flex flex-col">
                <div class="p-6 border-b hidden md:flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center text-white"><i class="fas fa-moon"></i></div>
                    <div><h1 class="font-bold text-gray-800 text-lg">Ramadhan</h1><p class="text-xs text-gray-400">Glow Up Admin</p></div>
                </div>
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <a href="?page=dashboard" class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500"><i class="fas fa-chart-line w-5"></i><span>Dashboard</span></a>
                    <a href="?page=master-content" class="nav-link <?= $page === 'master-content' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500"><i class="fas fa-book-quran w-5"></i><span>Master Konten</span></a>
                    <a href="?page=master-tasks" class="nav-link <?= $page === 'master-tasks' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500"><i class="fas fa-list-check w-5"></i><span>Master Tasks</span></a>
                    <a href="?page=monitoring" class="nav-link <?= $page === 'monitoring' ? 'active' : '' ?> flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500"><i class="fas fa-users w-5"></i><span>Monitoring</span></a>
                </nav>
                <div class="p-4 border-t">
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gray-50 mb-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-200 flex items-center justify-center text-emerald-800 font-bold text-xs"><?= strtoupper(substr($user['name'], 0, 2)) ?></div>
                        <div class="flex-1 overflow-hidden"><p class="text-sm font-medium text-gray-900 truncate"><?= htmlspecialchars($user['name']) ?></p><p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($user['email']) ?></p></div>
                    </div>
                    <a href="logout.php" class="w-full flex items-center justify-center gap-2 text-red-500 hover:bg-red-50 py-2 rounded-lg text-sm font-medium"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>

        <div id="overlay" class="fixed inset-0 bg-black/20 z-30 md:hidden backdrop-blur-sm hidden" onclick="toggleSidebar()"></div>

        <main class="flex-1 overflow-x-hidden pt-16 md:pt-0 h-screen overflow-y-auto">
            <div class="p-4 md:p-8 max-w-7xl mx-auto">
                <?php if ($page === 'dashboard'): ?>
                <div class="space-y-8">
                    <div><h1 class="text-2xl font-bold text-gray-900">Dashboard Statistik</h1><p class="text-gray-500">Visualisasi data Ramadhan Glow Up</p></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="stat-card bg-white p-6 rounded-2xl border shadow-sm"><div class="p-3 bg-emerald-50 rounded-xl mb-4 inline-block"><i class="fas fa-users text-emerald-600 text-2xl"></i></div><p class="text-sm text-gray-500 font-medium mb-1">User Terdaftar</p><h3 class="text-3xl font-bold text-gray-900"><?= $totalUsers ?></h3><p class="text-xs text-gray-400 mt-1"><?= $activeUsers ?> aktif</p></div>
                        <div class="stat-card bg-white p-6 rounded-2xl border shadow-sm"><div class="p-3 bg-blue-50 rounded-xl mb-4 inline-block"><i class="fas fa-check-circle text-blue-600 text-2xl"></i></div><p class="text-sm text-gray-500 font-medium mb-1">Tasks Completed</p><h3 class="text-3xl font-bold text-gray-900"><?= $totalLogsFilled ?></h3><p class="text-xs text-gray-400 mt-1">Total selesai</p></div>
                        <div class="stat-card bg-white p-6 rounded-2xl border shadow-sm"><div class="p-3 bg-purple-50 rounded-xl mb-4 inline-block"><i class="fas fa-pen-fancy text-purple-600 text-2xl"></i></div><p class="text-sm text-gray-500 font-medium mb-1">Journal Terisi</p><h3 class="text-3xl font-bold text-gray-900"><?= $totalJournals ?></h3><p class="text-xs text-gray-400 mt-1">Refleksi harian</p></div>
                        <div class="stat-card bg-white p-6 rounded-2xl border shadow-sm"><div class="p-3 bg-cyan-50 rounded-xl mb-4 inline-block"><i class="fas fa-glass-water text-cyan-600 text-2xl"></i></div><p class="text-sm text-gray-500 font-medium mb-1">Rata-rata Air</p><h3 class="text-3xl font-bold text-gray-900"><?= round($avgWater, 1) ?><span class="text-xl text-gray-400">/8</span></h3><p class="text-xs text-gray-400 mt-1">Gelas/hari</p></div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-2xl border shadow-sm"><h3 class="text-lg font-bold text-gray-800 mb-6">Tasks per Hari</h3><div style="height:300px"><canvas id="dailyChart"></canvas></div></div>
                        <div class="bg-white p-6 rounded-2xl border shadow-sm"><h3 class="text-lg font-bold text-gray-800 mb-6">Kategori</h3><div style="height:300px" class="flex items-center justify-center"><canvas id="categoryChart"></canvas></div></div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-2xl border shadow-sm"><h3 class="text-lg font-bold text-gray-800 mb-6">Top Tasks</h3><div style="height:300px"><canvas id="taskChart"></canvas></div></div>
                        <div class="bg-white p-6 rounded-2xl border shadow-sm"><h3 class="text-lg font-bold text-gray-800 mb-6">Mood User</h3><div style="height:300px" class="flex items-center justify-center"><canvas id="moodChart"></canvas></div></div>
                    </div>
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-lg p-6"><h3 class="text-lg font-bold mb-4 flex items-center gap-2 text-emerald-800"><i class="fas fa-lightbulb text-yellow-500"></i> Insights</h3><ul class="space-y-2 text-gray-700"><li class="flex gap-2"><span class="text-emerald-600">•</span><span>Completion rate <strong><?= $completionPercentage ?>%</strong></span></li><li class="flex gap-2"><span class="text-emerald-600">•</span><span><strong><?= $totalJournals ?></strong> journal terisi</span></li><li class="flex gap-2"><span class="text-emerald-600">•</span><span>Air rata-rata <strong><?= round($avgWater, 1) ?> gelas/hari</strong></span></li></ul></div>
                </div>

                <?php elseif ($page === 'master-content'): ?>
                <div class="space-y-6">
                    <div class="flex justify-between items-center"><div><h1 class="text-2xl font-bold text-gray-900">Master Konten</h1><p class="text-gray-500">Kelola 30 hari Ramadhan</p></div><button onclick="openKontenModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2"><i class="fas fa-plus"></i> Tambah</button></div>
                    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden"><table class="w-full"><thead class="bg-gray-50 border-b"><tr><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">HARI</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">JUDUL</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">SURAH</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">FOKUS</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">TASKS</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">AKSI</th></tr></thead><tbody id="kontenTableBody"><tr><td colspan="6" class="text-center py-8 text-gray-400">Loading...</td></tr></tbody></table></div>
                </div>

                <?php elseif ($page === 'master-tasks'): ?>
                <div class="space-y-6">
                    <div class="flex justify-between items-center"><div><h1 class="text-2xl font-bold text-gray-900">Master Tasks</h1><p class="text-gray-500">Kelola tasks harian</p></div><button onclick="openTaskModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2"><i class="fas fa-plus"></i> Tambah</button></div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6"><div class="bg-white rounded-xl p-6 shadow-sm border"><p class="text-gray-600 text-sm mb-2">Total</p><h3 id="totalTasks" class="text-4xl font-bold text-gray-800">0</h3></div><div class="bg-emerald-50 rounded-xl p-6 shadow-sm border border-emerald-100"><p class="text-emerald-700 text-sm mb-2">Merawat Diri</p><h3 id="fisikCount" class="text-4xl font-bold text-emerald-700">0</h3></div><div class="bg-blue-50 rounded-xl p-6 shadow-sm border border-blue-100"><p class="text-blue-700 text-sm mb-2">Menata Hati</p><h3 id="spiritualCount" class="text-4xl font-bold text-blue-700">0</h3></div></div>
                    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden"><table class="w-full"><thead class="bg-gray-50 border-b"><tr><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">DESKRIPSI</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">KATEGORI</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">USAGE</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">AKSI</th></tr></thead><tbody id="tasksTableBody"><tr><td colspan="4" class="text-center py-8 text-gray-400">Loading...</td></tr></tbody></table></div>
                </div>

                <?php elseif ($page === 'monitoring'): ?>
                <div class="space-y-6">
                    <div><h1 class="text-2xl font-bold text-gray-900">Monitoring User</h1><p class="text-gray-500">Monitor progres user</p></div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6"><div class="bg-white rounded-xl p-6 shadow-sm border"><p class="text-gray-600 text-sm mb-2">Total User</p><h3 id="monitorTotalUsers" class="text-4xl font-bold">0</h3></div><div class="bg-emerald-50 rounded-xl p-6 shadow-sm border border-emerald-100"><p class="text-emerald-700 text-sm mb-2">Aktif</p><h3 id="monitorActiveUsers" class="text-4xl font-bold text-emerald-700">0</h3></div><div class="bg-blue-50 rounded-xl p-6 shadow-sm border border-blue-100"><p class="text-blue-700 text-sm mb-2">Progres</p><h3 id="monitorAvgProgress" class="text-4xl font-bold text-blue-700">0%</h3></div><div class="bg-purple-50 rounded-xl p-6 shadow-sm border border-purple-100"><p class="text-purple-700 text-sm mb-2">Logs</p><h3 id="monitorTotalLogs" class="text-4xl font-bold text-purple-700">0</h3></div></div>
                    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden"><table class="w-full"><thead class="bg-gray-50 border-b"><tr><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">USER</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">HARI</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">TASKS</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">PROGRES</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">STATUS</th><th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">AKSI</th></tr></thead><tbody id="monitoringTableBody"><tr><td colspan="6" class="text-center py-8 text-gray-400">Loading...</td></tr></tbody></table></div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- MODALS -->
    <div id="kontenModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4"><div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto"><div class="p-6 border-b flex justify-between sticky top-0 bg-white"><h2 id="kontenModalTitle" class="text-2xl font-bold">Tambah Konten</h2><button onclick="closeModal('kontenModal')"><i class="fas fa-times text-xl"></i></button></div><form id="kontenForm" onsubmit="saveKonten(event)" class="p-6 space-y-4"><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-semibold mb-2">Hari *</label><input type="number" id="day" name="day" min="1" max="30" required class="w-full border rounded-lg px-4 py-2"></div><div><label class="block text-sm font-semibold mb-2">Surah *</label><input type="text" id="surah_name" name="surah_name" required class="w-full border rounded-lg px-4 py-2"></div></div><div><label class="block text-sm font-semibold mb-2">Teks Surah *</label><textarea id="surah_text" name="surah_text" required rows="2" class="w-full border rounded-lg px-4 py-2"></textarea></div><div><label class="block text-sm font-semibold mb-2">Judul *</label><input type="text" id="title" name="title" required class="w-full border rounded-lg px-4 py-2"></div><div><label class="block text-sm font-semibold mb-2">Sub Judul *</label><input type="text" id="sub_title" name="sub_title" required class="w-full border rounded-lg px-4 py-2"></div><div><label class="block text-sm font-semibold mb-2">Deskripsi *</label><textarea id="description" name="description" required rows="3" class="w-full border rounded-lg px-4 py-2"></textarea></div><div><label class="block text-sm font-semibold mb-2">Tips *</label><textarea id="tips" name="tips" required rows="3" class="w-full border rounded-lg px-4 py-2"></textarea></div><div><label class="block text-sm font-semibold mb-2">Kunci Fokus *</label><input type="text" id="daily_focus_key" name="daily_focus_key" required class="w-full border rounded-lg px-4 py-2"></div><div><label class="block text-sm font-semibold mb-2">Tasks</label><div id="taskCheckboxes" class="border rounded-lg p-4 max-h-48 overflow-y-auto bg-gray-50">Loading...</div></div><div class="grid grid-cols-2 gap-4"><button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-lg font-semibold">Simpan</button><button type="button" onclick="closeModal('kontenModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold">Batal</button></div></form></div></div>

    <div id="taskModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4"><div class="bg-white rounded-2xl max-w-lg w-full"><div class="p-6 border-b flex justify-between"><h2 id="taskModalTitle" class="text-2xl font-bold">Tambah Task</h2><button onclick="closeModal('taskModal')"><i class="fas fa-times text-xl"></i></button></div><form id="taskForm" onsubmit="saveTask(event)" class="p-6 space-y-4"><div><label class="block text-sm font-semibold mb-2">Deskripsi *</label><input type="text" id="task_description" name="task_description" required class="w-full border rounded-lg px-4 py-2"></div><div><label class="block text-sm font-semibold mb-2">Kategori *</label><select id="category_id" name="category_id" required class="w-full border rounded-lg px-4 py-2"><option value="">Pilih...</option><option value="1">Merawat Diri</option><option value="2">Menata Hati</option></select></div><div class="grid grid-cols-2 gap-4"><button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-lg font-semibold">Simpan</button><button type="button" onclick="closeModal('taskModal')" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold">Batal</button></div></form></div></div>

    <div id="userDetailModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4"><div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto"><div class="p-6 border-b flex justify-between sticky top-0 bg-white"><h2 class="text-2xl font-bold">Detail User</h2><button onclick="closeModal('userDetailModal')"><i class="fas fa-times text-xl"></i></button></div><div id="userDetailContent" class="p-6">Loading...</div></div></div>

    <script>
        function toggleSidebar(){const s=document.getElementById('sidebar'),o=document.getElementById('overlay');s.classList.toggle('open');o.classList.toggle('hidden');}
        <?php if($page==='dashboard'): ?>
        new Chart(document.getElementById('dailyChart'),{type:'line',data:{labels:[<?php foreach($dailyLogsData as $l)echo "'D".$l['day']."',";?>],datasets:[{label:'Tasks',data:[<?php foreach($dailyLogsData as $l)echo $l['count'].',';?>],borderColor:'#10b981',backgroundColor:'rgba(16,185,129,0.1)',tension:0.4,fill:true,borderWidth:3,pointRadius:5,pointBackgroundColor:'#10b981',pointBorderColor:'#fff',pointBorderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:'#f3f4f6'}}}}});
        new Chart(document.getElementById('categoryChart'),{type:'doughnut',data:{labels:['Merawat Diri','Menata Hati'],datasets:[{data:[<?=$merawatDiri?>,<?=$menataHati?>],backgroundColor:['#10b981','#3b82f6'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},cutout:'65%'}});
        new Chart(document.getElementById('taskChart'),{type:'bar',data:{labels:[<?php foreach($taskPopularityData as $t)echo "'".substr($t['task_description'],0,20)."',";?>],datasets:[{data:[<?php foreach($taskPopularityData as $t)echo $t['count'].',';?>],backgroundColor:['#10b981','#34d399','#6ee7b7','#a7f3d0','#d1fae5'],borderRadius:8}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,grid:{display:false}},y:{grid:{display:false}}}}});
        new Chart(document.getElementById('moodChart'),{type:'doughnut',data:{labels:['😁 Happy','😐 Neutral','😴 Tired','😟 Sad'],datasets:[{data:[<?=implode(',',array_values($moodData))?>],backgroundColor:['#22c55e','#eab308','#3b82f6','#ef4444'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},cutout:'60%'}});
        <?php endif;?>
    </script>
</body>
</html>