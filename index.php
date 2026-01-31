<?php
require_once './db/config.php';

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
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/styles.css">
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
                        <div class="page-footer"></div>
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

    <!-- Custom JavaScript -->
    <script src="assets/js/app.js"></script>
</body>

</html>
<?php closeDBConnection($conn); ?>