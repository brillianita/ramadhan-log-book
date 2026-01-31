<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
$conn = getDBConnection();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ramadhan Glow Up - 30 Days</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;600&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-sage: #8A9A5B;
            --secondary-terracotta: #E2725B;
            --bg-ivory: #FDFBF7;
            --text-charcoal: #333333;
            --highlight-gold: #E1AD01;
            --page-width: 176mm;
            --page-height: 250mm;
            --shadow-book: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 20px rgba(0, 0, 0, 0.05);
        }

        body {
            background-color: #e0e0e0;
            font-family: 'Montserrat', sans-serif;
            color: var(--text-charcoal);
            margin: 0;
            padding: 40px 0;
            overflow-x: hidden;
        }

        .font-serif-modern {
            font-family: 'Playfair Display', serif;
        }

        .font-sans-clean {
            font-family: 'Montserrat', sans-serif;
        }

        .font-handwriting {
            font-family: 'Dancing Script', cursive;
        }

        .text-sage {
            color: var(--primary-sage);
        }

        .text-terracotta {
            color: var(--secondary-terracotta);
        }

        .text-gold {
            color: var(--highlight-gold);
        }

        .bg-ivory {
            background-color: var(--bg-ivory);
        }

        .bg-sage {
            background-color: var(--primary-sage);
        }

        .bg-terracotta {
            background-color: var(--secondary-terracotta);
        }

        .book-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 60px;
        }

        .spread {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0;
            max-width: 100%;
        }

        .page {
            width: var(--page-width);
            height: var(--page-height);
            background-color: var(--bg-ivory);
            box-shadow: var(--shadow-book);
            position: relative;
            padding: 30px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        @media (max-width: 800px) {
            .page {
                width: 95vw;
                height: auto;
                min-height: 140vw;
                margin-bottom: 20px;
            }

            .spread {
                gap: 20px;
            }
        }

        .cover-page {
            background-color: var(--primary-sage);
            color: white;
            text-align: center;
            border-radius: 2px 8px 8px 2px;
            position: relative;
        }

        .cover-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.1;
            background-image: radial-gradient(#fff 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .gold-foil-text {
            color: #FFD700;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            background: linear-gradient(45deg, #FFD700, #FDB931, #FFD700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-left {
            border-radius: 4px 0 0 4px;
            background: linear-gradient(90deg, #FDFBF7 95%, #f0eee9 100%);
        }

        .page-right {
            border-radius: 0 4px 4px 0;
            background: linear-gradient(-90deg, #FDFBF7 95%, #f0eee9 100%);
        }

        .highlight-box {
            background-color: rgba(226, 114, 91, 0.1);
            border-left: 4px solid var(--secondary-terracotta);
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin: 15px 0;
        }

        .mood-booster {
            border: 1px dashed var(--highlight-gold);
            background-color: rgba(225, 173, 1, 0.05);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .dotted-line {
            border-bottom: 1.5px dotted #d1d1d1;
            width: 100%;
            min-height: 30px;
            margin-bottom: 5px;
            font-family: 'Dancing Script', cursive;
            font-size: 1.2rem;
            color: #555;
            padding-left: 10px;
            background: transparent;
            outline: none;
        }

        .custom-checkbox input {
            display: none;
        }

        .custom-checkbox label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .custom-checkbox span {
            width: 20px;
            height: 20px;
            border: 2px solid var(--secondary-terracotta);
            border-radius: 4px;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .custom-checkbox input:checked+label span {
            background-color: var(--secondary-terracotta);
            color: white;
        }

        .custom-checkbox input:checked+label span::after {
            content: '✔';
            font-size: 12px;
        }

        .page-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
            font-size: 0.8rem;
            color: #999;
        }

        .progress-bar-container {
            width: 100px;
            height: 4px;
            background-color: #eee;
            border-radius: 2px;
        }

        .progress-bar-fill {
            height: 100%;
            background-color: var(--primary-sage);
            border-radius: 2px;
        }

        .floral-ornament {
            position: absolute;
            opacity: 0.1;
            width: 150px;
            height: 150px;
            pointer-events: none;
        }

        .user-info {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 10px 20px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 1000;
        }

        .save-indicator {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 8px 16px;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 0.85rem;
            z-index: 1000;
            display: none;
        }

        .save-indicator.saving {
            background: #FFF3CD;
            color: #856404;
            display: block;
        }

        .save-indicator.saved {
            background: #D4EDDA;
            color: #155724;
            display: block;
        }

        .save-indicator.error {
            background: #F8D7DA;
            color: #721C24;
            display: block;
        }
    </style>
</head>

<body>
    <!-- User Info Bar -->
    <div class="user-info">
        <span class="font-sans-clean text-sm">👋 <?php echo htmlspecialchars($user['name']); ?></span>
        <a href="logout.php" class="text-xs text-terracotta hover:underline">Logout</a>
    </div>

    <!-- Save Indicator -->
    <div id="saveIndicator" class="save-indicator">
        <span id="saveText">Menyimpan...</span>
    </div>

    <div class="book-container">
        <!-- COVER PREVIEW -->
        <div class="spread">
            <div class="page cover-page shadow-2xl relative flex flex-col items-center justify-between py-16 px-8">
                <div class="cover-pattern"></div>
                <div class="z-10 bg-white/10 backdrop-blur-sm px-4 py-1 rounded-full border border-white/20">
                    <p class="text-xs uppercase tracking-[0.2em] font-sans-clean text-white">Interactive Workbook</p>
                </div>
                <div class="z-10 text-center w-full">
                    <p class="font-sans-clean text-sm tracking-widest mb-4 opacity-90">RAMADHAN JOURNAL FOR THE MODERN MUSLIMAH</p>
                    <h1 class="font-serif-modern text-6xl leading-tight mb-2 gold-foil-text font-bold">Ramadhan<br>Glow Up</h1>
                    <div class="h-1 w-20 bg-[#E1AD01] mx-auto my-6 rounded-full"></div>
                    <h2 class="font-serif-modern text-2xl italic tracking-wide text-white">30 Hari Menata Hati,<br>Merawat Diri</h2>
                </div>
                <div class="z-10 my-4 opacity-80">
                    <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" class="text-white mx-auto">
                        <path d="M12 2L12 22"></path>
                        <path d="M12 12C6 12 2 16 2 22"></path>
                        <path d="M12 12C18 12 22 16 22 22"></path>
                        <path d="M12 2C7 2 7 7 7 7"></path>
                        <path d="M12 2C17 2 17 7 17 7"></path>
                    </svg>
                    <p class="text-[10px] mt-2 font-handwriting text-xl">Wulan Setya Ningsih</p>
                </div>
                <div class="z-10 self-end">
                    <div class="w-20 h-20 rounded-full border-2 border-white/30 flex items-center justify-center rotate-[-15deg] backdrop-blur-sm bg-white/5">
                        <p class="text-[8px] text-center leading-tight font-sans-clean font-bold">READ<br>WRITE<br>HEAL</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-500 font-sans-clean mb-2">▼ 30 DAYS OF RAMADHAN ▼</p>
            <p class="text-xs text-gray-400">Scroll untuk melihat semua hari</p>
        </div>

        <!-- INSIDE SPREAD: ALL DAYS -->
        <div>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM daily_content ORDER BY day ASC");
            while ($row = mysqli_fetch_assoc($res)):
                $currentDailyId = $row['id'];
            ?>
                <div class="spread mb-6" data-day="<?= $row['day'] ?>">
                    <!-- LEFT PAGE (MATERI) -->
                    <div class="page page-left">
                        <svg class="floral-ornament top-0 right-0 -mr-8 -mt-8 text-sage rotate-90" viewBox="0 0 100 100" fill="currentColor">
                            <path d="M50 0 C20 0 0 20 0 50 C0 80 20 100 50 100 C80 100 100 80 100 50 C100 20 80 0 50 0 Z M50 90 C30 90 10 70 10 50 C10 30 30 10 50 10 C70 10 90 30 90 50 C90 70 70 90 50 90 Z" opacity="0.1" />
                        </svg>
                        <div class="flex items-baseline justify-between mb-6 border-b-2 border-dashed border-[#8A9A5B] pb-2">
                            <h2 class="font-serif-modern text-4xl text-sage font-bold">Day <?= $row['day'] ?></h2>
                            <span class="font-sans-clean text-xs uppercase tracking-widest text-terracotta font-bold">The Foundation</span>
                        </div>
                        <div class="mood-booster flex gap-3 items-start">
                            <i class="fas fa-star text-gold mt-1"></i>
                            <div>
                                <p class="font-sans-clean text-sm italic text-gray-600">
                                    "<?= htmlspecialchars($row['surah_text']) ?>"
                                </p>
                                <p class="text-xs text-right mt-1 font-bold text-sage">(<?= htmlspecialchars($row['surah_name']) ?>)</p>
                            </div>
                        </div>
                        <h3 class="font-serif-modern text-2xl mb-2 text-gray-800"><?= htmlspecialchars($row['title']) ?></h3>
                        <p class="font-sans-clean text-xs text-terracotta uppercase tracking-wide mb-4"><?= htmlspecialchars($row['sub_title']) ?></p>
                        <div class="prose prose-sm font-sans-clean text-gray-600 leading-relaxed text-justify text-[13px]">
                            <p class="mb-3">
                                <span class="font-handwriting text-xl text-sage block mb-1">Assalamu'alaikum, Ramadhan!</span>
                            </p>
                            <p class="mb-3">
                                <?= nl2br(htmlspecialchars($row['description'])) ?>
                            </p>
                            <div class="highlight-box text-xs italic bg-gray-100 border-l-4 border-gray-400">
                                <p class="font-bold text-gray-700 not-italic mb-1">✨ Tips Psikologis Ibu Wulan:</p>
                                "<?= htmlspecialchars($row['tips']) ?>"
                            </div>
                            <p>
                                Kunci hari ini adalah: "<?= htmlspecialchars($row['daily_focus_key']) ?>"
                            </p>
                        </div>
                        <div class="page-footer">
                        </div>
                    </div>

                    <!-- RIGHT PAGE (ACTION) -->
                    <div class="page page-right">
                        <svg class="floral-ornament top-0 left-0 -ml-8 -mt-8 text-sage" viewBox="0 0 100 100" fill="currentColor">
                            <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="2" fill="none" />
                            <path d="M50 10 L50 90 M10 50 L90 50" stroke="currentColor" stroke-width="2" />
                        </svg>
                        <h3 class="font-handwriting text-3xl text-terracotta mb-6 text-center transform -rotate-2">Action Plan Hari Ini</h3>
                        <div class="grid grid-cols-1 gap-6 mb-6">
                            <!-- Section Fisik -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center gap-2 mb-3 border-b pb-2">
                                    <i class="fas fa-sun text-terracotta"></i>
                                    <h4 class="font-sans-clean font-bold text-sm text-gray-700">Merawat Diri (Fisik)</h4>
                                </div>
                                <?php
                                $res_fisik = mysqli_query($conn, "SELECT dt.id, t.task_description AS task_desc FROM daily_task dt LEFT JOIN tasks t ON t.id = dt.task_id WHERE t.category_id = 1 AND dt.daily_content_id = " . (int)$row['id']);
                                while ($row_fisik = mysqli_fetch_assoc($res_fisik)):
                                ?>
                                    <div class="custom-checkbox">
                                        <input type="checkbox" 
                                               id="task_<?= $currentDailyId ?>_<?= $row_fisik['id'] ?>" 
                                               data-task-id="<?= $row_fisik['id'] ?>"
                                               data-daily-id="<?= $currentDailyId ?>">
                                        <label for="task_<?= $currentDailyId ?>_<?= $row_fisik['id'] ?>">
                                            <span></span><?= htmlspecialchars($row_fisik['task_desc']) ?>
                                        </label>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <!-- Section Hati -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-center gap-2 mb-3 border-b pb-2">
                                    <i class="fas fa-heart text-sage"></i>
                                    <h4 class="font-sans-clean font-bold text-sm text-gray-700">Menata Hati (Spiritual)</h4>
                                </div>
                                <?php
                                $res_spirit = mysqli_query($conn, "SELECT dt.id, t.task_description AS task_desc FROM daily_task dt JOIN tasks t ON dt.task_id = t.id WHERE t.category_id = 2 AND dt.daily_content_id = " . (int)$row['id']);
                                while ($row_spirit = mysqli_fetch_assoc($res_spirit)):
                                ?>
                                    <div class="custom-checkbox">
                                        <input type="checkbox" 
                                               id="task_<?= $currentDailyId ?>_<?= $row_spirit['id'] ?>" 
                                               data-task-id="<?= $row_spirit['id'] ?>"
                                               data-daily-id="<?= $currentDailyId ?>">
                                        <label for="task_<?= $currentDailyId ?>_<?= $row_spirit['id'] ?>">
                                            <span></span><?= htmlspecialchars($row_spirit['task_desc']) ?>
                                        </label>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <!-- Journaling Area -->
                        <div class="flex-grow">
                            <h4 class="font-serif-modern text-lg mb-2 text-gray-800">Journaling: Intention Setting</h4>
                            <p class="text-xs text-gray-500 mb-2">Apa "Why" (Alasan Kuat) aku ikut Ramadhan tahun ini?</p>
                            <div class="dotted-line journal-why" 
                                 contenteditable="true" 
                                 data-daily-id="<?= $currentDailyId ?>" 
                                 data-field="why_1"></div>
                            <div class="dotted-line journal-why" 
                                 contenteditable="true" 
                                 data-daily-id="<?= $currentDailyId ?>" 
                                 data-field="why_2"></div>
                            <div class="dotted-line journal-why" 
                                 contenteditable="true" 
                                 data-daily-id="<?= $currentDailyId ?>" 
                                 data-field="why_3"></div>
                            <p class="text-xs text-gray-500 mt-4 mb-2">Satu kebiasaan buruk yang ingin aku "puasakan" hari ini?</p>
                            <div class="dotted-line journal-habit" 
                                 contenteditable="true" 
                                 data-daily-id="<?= $currentDailyId ?>" 
                                 data-field="habit_1"></div>
                            <div class="dotted-line journal-habit" 
                                 contenteditable="true" 
                                 data-daily-id="<?= $currentDailyId ?>" 
                                 data-field="habit_2"></div>
                            <!-- Save Button for Journal -->
                            <!-- <div class="mt-4 text-center">
                                <button data-daily-content-id="<?= $currentDailyId ?>" 
                                        class="saveJournalBtn bg-sage hover:bg-[#7a8a4b] text-white font-sans-clean font-semibold px-6 py-2 rounded-lg transition duration-200 text-sm">
                                    <i class="fas fa-save mr-2"></i>Simpan Journal Day <?= $row['day'] ?>
                                </button>
                            </div> -->
                        </div>
                        <!-- Footer Tracker -->
                        <div class="page-footer bg-sage/10 -mx-8 -mb-8 px-8 py-4 mt-4 border-t-0 flex flex-col gap-2">
                            <div class="flex justify-between items-center w-full">
                                <span class="text-[10px] font-bold text-sage uppercase">Mood Check</span>
                                <div class="flex gap-2 text-lg text-gray-400 mood-container" data-daily-id="<?= $currentDailyId ?>">
                                    <button class="mood-btn hover:scale-110 transition" data-mood="1" data-daily-id="<?= $currentDailyId ?>">😁</button>
                                    <button class="mood-btn hover:scale-110 transition" data-mood="2" data-daily-id="<?= $currentDailyId ?>">😐</button>
                                    <button class="mood-btn hover:scale-110 transition" data-mood="3" data-daily-id="<?= $currentDailyId ?>">😴</button>
                                    <button class="mood-btn hover:scale-110 transition" data-mood="4" data-daily-id="<?= $currentDailyId ?>">😟</button>
                                </div>
                            </div>
                            <div class="flex justify-between items-center w-full">
                                <span class="text-[10px] font-bold text-sage uppercase">Water</span>
                                <div class="flex gap-1 text-blue-300 text-xs water-container" data-daily-id="<?= $currentDailyId ?>">
                                    <?php for($i = 1; $i <= 8; $i++): ?>
                                    <i class="fas fa-tint water-drop cursor-pointer <?= $i > 4 ? 'opacity-30' : '' ?>" 
                                       data-glass="<?= $i ?>" 
                                       data-daily-id="<?= $currentDailyId ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- Save All Button -->
                            <div class="mt-2 pt-2 border-t border-sage/20">
                                <button class="saveAllBtn w-full bg-terracotta hover:bg-[#d2625b] text-white font-sans-clean font-bold py-2 px-2 rounded-lg transition duration-200 text-sm flex items-center justify-center gap-2"
                                        data-daily-content-id="<?= $currentDailyId ?>">
                                    <i class="fas fa-check-circle"></i>
                                    Simpan Semua Progress Day <?= $row['day'] ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        // Global water levels tracker (per day)
        const waterLevels = {};

        // Show save indicator
        function showSaveIndicator(status, message) {
            const indicator = document.getElementById('saveIndicator');
            const text = document.getElementById('saveText');
            indicator.className = 'save-indicator ' + status;
            text.textContent = message;

            if (status !== 'saving') {
                setTimeout(() => {
                    indicator.style.display = 'none';
                }, 2000);
            }
        }

        // Function untuk auto-resize font
        function autoResizeFont(element) {
            const charCount = element.textContent.length;
            if (charCount > 120) {
                element.style.fontSize = '0.9rem';
            } else if (charCount > 80) {
                element.style.fontSize = '1rem';
            } else {
                element.style.fontSize = '1.2rem';
            }
        }

        // Load journal data for a specific day
        function loadJournalData(dailyContentId) {
            fetch(`get_user_data.php?daily_content_id=${dailyContentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.journal) {
                        const journal = data.data.journal;
                        const whyLines = (journal.ramadhan_why || '').split('\n');
                        const habitLines = (journal.bad_habit || '').split('\n');

                        // Load "Why" lines
                        whyLines.forEach((line, index) => {
                            const el = document.querySelector(
                                `.journal-why[data-daily-id="${dailyContentId}"][data-field="why_${index + 1}"]`
                            );
                            if (el && line) {
                                el.textContent = line;
                                autoResizeFont(el);
                            }
                        });

                        // Load "Habit" lines
                        habitLines.forEach((line, index) => {
                            const el = document.querySelector(
                                `.journal-habit[data-daily-id="${dailyContentId}"][data-field="habit_${index + 1}"]`
                            );
                            if (el && line) {
                                el.textContent = line;
                                autoResizeFont(el);
                            }
                        });

                        console.log(`Journal loaded for day ${dailyContentId}`);
                    }
                })
                .catch(error => console.error(`Error loading journal for day ${dailyContentId}:`, error));
        }

        // Load all user data
        async function loadUserData() {
            try {
                // Get all unique daily content IDs
                const dailyContentIds = [...new Set(
                    Array.from(document.querySelectorAll('[data-daily-content-id]'))
                    .map(el => el.dataset.dailyContentId)
                )];

                // Load data for each day
                for (const dailyContentId of dailyContentIds) {
                    const response = await fetch(`get_user_data.php?daily_content_id=${dailyContentId}`);
                    const result = await response.json();

                    if (result.success) {
                        const data = result.data;

                        // Load completed tasks for this day
                        if (data.completed_tasks) {
                            Object.keys(data.completed_tasks).forEach(taskId => {
                                const checkbox = document.querySelector(
                                    `input[data-task-id="${taskId}"][data-daily-id="${dailyContentId}"]`
                                );
                                if (checkbox && data.completed_tasks[taskId]) {
                                    checkbox.checked = true;
                                }
                            });
                        }

                        // Load journal for this day
                        loadJournalData(dailyContentId);

                        // Load mood for this day
                        if (data.mood) {
                            const moodBtn = document.querySelector(
                                `.mood-btn[data-mood="${data.mood}"][data-daily-id="${dailyContentId}"]`
                            );
                            if (moodBtn) {
                                moodBtn.classList.add('text-terracotta');
                                moodBtn.style.transform = 'scale(1.2)';
                            }
                        }

                        // Load water level for this day
                        if (data.water_level) {
                            waterLevels[dailyContentId] = parseInt(data.water_level);
                            updateWaterDisplay(dailyContentId);
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading user data:', error);
            }
        }

        // Save checkbox progress
        async function saveProgress(taskId, isCompleted, dailyContentId) {
            showSaveIndicator('saving', 'Menyimpan...');

            const formData = new FormData();
            formData.append('daily_content_id', dailyContentId);
            formData.append('task_id', taskId);
            formData.append('is_completed', isCompleted ? 1 : 0);

            try {
                const response = await fetch('save_progress.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showSaveIndicator('saved', '✓ Tersimpan');
                } else {
                    showSaveIndicator('error', '✗ Gagal menyimpan');
                }
            } catch (error) {
                showSaveIndicator('error', '✗ Error');
                console.error('Error:', error);
            }
        }

        // Save journal
        function saveJournal(dailyContentId) {
            showSaveIndicator('saving', 'Menyimpan journal...');

            const whyElements = document.querySelectorAll(`.journal-why[data-daily-id="${dailyContentId}"]`);
            const habitElements = document.querySelectorAll(`.journal-habit[data-daily-id="${dailyContentId}"]`);

            const whyLines = Array.from(whyElements).map(el => el.textContent.trim());
            const habitLines = Array.from(habitElements).map(el => el.textContent.trim());

            const formData = new FormData();
            formData.append('daily_content_id', dailyContentId);
            formData.append('ramadhan_why', whyLines.join('\n'));
            formData.append('bad_habit', habitLines.join('\n'));

            fetch('save_journal.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showSaveIndicator('saved', '✓ Journal tersimpan');
                } else {
                    showSaveIndicator('error', '✗ ' + (result.message || 'Gagal menyimpan journal'));
                }
            })
            .catch(error => {
                showSaveIndicator('error', '✗ Error koneksi');
                console.error('Error:', error);
            });
        }

        // Save mood
        async function saveMood(mood, dailyContentId) {
            showSaveIndicator('saving', 'Menyimpan mood...');

            const formData = new FormData();
            formData.append('daily_content_id', dailyContentId);
            formData.append('mood', mood);

            try {
                const response = await fetch('save_mood.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showSaveIndicator('saved', '✓ Mood tersimpan');

                    // Update UI only for this day
                    document.querySelectorAll(`.mood-btn[data-daily-id="${dailyContentId}"]`).forEach(btn => {
                        btn.classList.remove('text-terracotta');
                        btn.style.transform = '';
                    });
                    const selectedBtn = document.querySelector(
                        `.mood-btn[data-mood="${mood}"][data-daily-id="${dailyContentId}"]`
                    );
                    if (selectedBtn) {
                        selectedBtn.classList.add('text-terracotta');
                        selectedBtn.style.transform = 'scale(1.2)';
                    }
                } else {
                    showSaveIndicator('error', '✗ Gagal menyimpan mood');
                }
            } catch (error) {
                showSaveIndicator('error', '✗ Error');
                console.error('Error:', error);
            }
        }

        // Update water display for specific day
        function updateWaterDisplay(dailyContentId) {
            const level = waterLevels[dailyContentId] || 0;
            const drops = document.querySelectorAll(`.water-drop[data-daily-id="${dailyContentId}"]`);
            
            drops.forEach((drop, index) => {
                const glassNum = parseInt(drop.dataset.glass);
                if (glassNum <= level) {
                    drop.classList.remove('opacity-30');
                } else {
                    drop.classList.add('opacity-30');
                }
            });
        }

        // Save water level
        async function saveWaterLevel(level, dailyContentId) {
            showSaveIndicator('saving', 'Menyimpan...');

            const formData = new FormData();
            formData.append('daily_content_id', dailyContentId);
            formData.append('level', level);

            try {
                const response = await fetch('save_water.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showSaveIndicator('saved', '✓ Water level tersimpan');
                } else {
                    showSaveIndicator('error', '✗ Gagal menyimpan');
                }
            } catch (error) {
                showSaveIndicator('error', '✗ Error');
                console.error('Error:', error);
            }
        }

        // Save all data for a specific day
        async function saveAll(dailyContentId) {
            showSaveIndicator('saving', 'Menyimpan semua data...');

            let allSuccess = true;
            let errorMessages = [];

            try {
                // 1. Save all checked tasks for this day
                const checkboxes = document.querySelectorAll(
                    `input[type="checkbox"][data-daily-id="${dailyContentId}"]:checked`
                );
                for (const checkbox of checkboxes) {
                    const taskId = checkbox.dataset.taskId;
                    const formData = new FormData();
                    formData.append('daily_content_id', dailyContentId);
                    formData.append('task_id', taskId);
                    formData.append('is_completed', 1);

                    const response = await fetch('save_progress.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (!result.success) {
                        allSuccess = false;
                        errorMessages.push('Tasks');
                        break;
                    }
                }

                // 2. Save journal
                const whyElements = document.querySelectorAll(`.journal-why[data-daily-id="${dailyContentId}"]`);
                const habitElements = document.querySelectorAll(`.journal-habit[data-daily-id="${dailyContentId}"]`);
                const whyLines = Array.from(whyElements).map(el => el.textContent.trim());
                const habitLines = Array.from(habitElements).map(el => el.textContent.trim());

                if (whyLines.some(line => line) || habitLines.some(line => line)) {
                    const journalData = new FormData();
                    journalData.append('daily_content_id', dailyContentId);
                    journalData.append('ramadhan_why', whyLines.join('\n'));
                    journalData.append('bad_habit', habitLines.join('\n'));

                    const journalResponse = await fetch('save_journal.php', {
                        method: 'POST',
                        body: journalData
                    });
                    const journalResult = await journalResponse.json();
                    if (!journalResult.success) {
                        allSuccess = false;
                        errorMessages.push('Journal');
                    }
                }

                // 3. Save mood if selected
                const selectedMood = document.querySelector(
                    `.mood-btn.text-terracotta[data-daily-id="${dailyContentId}"]`
                );
                if (selectedMood) {
                    const mood = selectedMood.dataset.mood;
                    const moodData = new FormData();
                    moodData.append('daily_content_id', dailyContentId);
                    moodData.append('mood', mood);

                    const moodResponse = await fetch('save_mood.php', {
                        method: 'POST',
                        body: moodData
                    });
                    const moodResult = await moodResponse.json();
                    if (!moodResult.success) {
                        allSuccess = false;
                        errorMessages.push('Mood');
                    }
                }

                // 4. Save water level
                const currentWaterLevel = waterLevels[dailyContentId] || 0;
                if (currentWaterLevel > 0) {
                    const waterData = new FormData();
                    waterData.append('daily_content_id', dailyContentId);
                    waterData.append('level', currentWaterLevel);

                    const waterResponse = await fetch('save_water.php', {
                        method: 'POST',
                        body: waterData
                    });
                    const waterResult = await waterResponse.json();
                    if (!waterResult.success) {
                        allSuccess = false;
                        errorMessages.push('Water');
                    }
                }

                // Show result
                if (allSuccess) {
                    showSaveIndicator('saved', '✓ Semua data berhasil tersimpan!');
                } else {
                    showSaveIndicator('error', '✗ Gagal menyimpan: ' + errorMessages.join(', '));
                }

            } catch (error) {
                showSaveIndicator('error', '✗ Terjadi kesalahan');
                console.error('Error:', error);
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', () => {
            // Load existing data
            loadUserData();

            // Checkbox listeners
            document.querySelectorAll('input[type="checkbox"][data-task-id]').forEach(checkbox => {
                checkbox.addEventListener('change', (e) => {
                    const taskId = e.target.dataset.taskId;
                    const dailyId = e.target.dataset.dailyId;
                    const isCompleted = e.target.checked;
                    saveProgress(taskId, isCompleted, dailyId);
                });
            });

            // Save Journal Button listeners (event delegation)
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.saveJournalBtn');
                if (btn) {
                    e.preventDefault();
                    const dailyContentId = btn.dataset.dailyContentId;
                    saveJournal(dailyContentId);
                }
            });

            // Mood listeners
            document.querySelectorAll('.mood-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const mood = btn.dataset.mood;
                    const dailyId = btn.dataset.dailyId;
                    saveMood(mood, dailyId);
                });
            });

            // Water tracker listeners
            document.querySelectorAll('.water-drop').forEach(drop => {
                drop.addEventListener('click', () => {
                    const glass = parseInt(drop.dataset.glass);
                    const dailyId = drop.dataset.dailyId;
                    waterLevels[dailyId] = glass;
                    updateWaterDisplay(dailyId);
                    saveWaterLevel(glass, dailyId);
                });
            });

            // Save All button listeners
            document.querySelectorAll('.saveAllBtn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const dailyContentId = btn.dataset.dailyContentId;
                    saveAll(dailyContentId);
                });
            });

            // Auto-resize font on input
            document.querySelectorAll('.dotted-line').forEach(line => {
                line.addEventListener('input', (e) => {
                    autoResizeFont(e.target);
                });
            });
        });
    </script>
</body>

</html>