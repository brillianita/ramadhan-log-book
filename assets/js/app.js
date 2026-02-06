/**
 * app.js - Ramadhan Glow Up Application
 * Main JavaScript file for handling user interactions and data management
 */

// Global water levels tracker (per day)
const waterLevels = {};

/**
 * Show save indicator with status message
 * @param {string} status - 'saving', 'saved', or 'error'
 * @param {string} message - Message to display
 */
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

/**
 * Auto-resize font based on content length
 * @param {HTMLElement} element - The element to resize
 */
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

/**
 * Load journal data for a specific day
 * @param {number} dailyContentId - The daily content ID
 */
function loadJournalData(dailyContentId) {
    fetch(`../../services/get_user_data.php?daily_content_id=${dailyContentId}`)
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

/**
 * Load all user data from server
 */
async function loadUserData() {
    try {
        // Get all unique daily content IDs
        const dailyContentIds = [...new Set(
            Array.from(document.querySelectorAll('[data-daily-content-id]'))
            .map(el => el.dataset.dailyContentId)
        )];

        // Load data for each day
        for (const dailyContentId of dailyContentIds) {
            const response = await fetch(`../../services/get_user_data.php?daily_content_id=${dailyContentId}`);
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

/**
 * Save checkbox progress
 * @param {number} taskId - The task ID
 * @param {boolean} isCompleted - Whether the task is completed
 * @param {number} dailyContentId - The daily content ID
 */
async function saveProgress(taskId, isCompleted, dailyContentId) {
    showSaveIndicator('saving', 'Menyimpan...');

    const formData = new FormData();
    formData.append('daily_content_id', dailyContentId);
    formData.append('task_id', taskId);
    formData.append('is_completed', isCompleted ? 1 : 0);

    try {
        const response = await fetch('../../services/save_progress.php', {
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

/**
 * Save journal entries
 * @param {number} dailyContentId - The daily content ID
 */
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

    fetch('../../services/save_journal.php', {
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

/**
 * Save user mood
 * @param {number} mood - The mood value (1-4)
 * @param {number} dailyContentId - The daily content ID
 */
async function saveMood(mood, dailyContentId) {
    showSaveIndicator('saving', 'Menyimpan mood...');

    const formData = new FormData();
    formData.append('daily_content_id', dailyContentId);
    formData.append('mood', mood);

    try {
        const response = await fetch('../../services/save_mood.php', {
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

/**
 * Update water display for specific day
 * @param {number} dailyContentId - The daily content ID
 */
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

/**
 * Save water level
 * @param {number} level - Water level (0-8)
 * @param {number} dailyContentId - The daily content ID
 */
async function saveWaterLevel(level, dailyContentId) {
    showSaveIndicator('saving', 'Menyimpan...');

    const formData = new FormData();
    formData.append('daily_content_id', dailyContentId);
    formData.append('level', level);

    try {
        const response = await fetch('../../services/save_water.php', {
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

/**
 * Save all data for a specific day
 * @param {number} dailyContentId - The daily content ID
 */
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

            const response = await fetch('../../services/save_progress.php', {
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

            const journalResponse = await fetch('../../services/save_journal.php', {
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

            const moodResponse = await fetch('../../services/save_mood.php', {
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

            const waterResponse = await fetch('../../services/save_water.php', {
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

/**
 * Initialize event listeners when DOM is ready
 */
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