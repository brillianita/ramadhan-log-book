// =========================================
// ADMIN DASHBOARD - CRUD JAVASCRIPT
// =========================================

// Global variables
let currentEditId = null;
let currentEditType = null;
let allTasks = [];

// =========================================
// UTILITY FUNCTIONS
// =========================================

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white font-medium animate-fade-in`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.getElementById(modalId).classList.add('flex');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.getElementById(modalId).classList.remove('flex');
    currentEditId = null;
    currentEditType = null;
}

// =========================================
// MASTER KONTEN RAMADHAN
// =========================================

async function loadKontenData() {
    try {
        const response = await fetch('api_konten.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            renderKontenTable(data.content);
        }
    } catch (error) {
        console.error('Error loading konten:', error);
        showToast(`Error loading data ${error}`, 'error');
    }
}

function renderKontenTable(content) {
    const tbody = document.getElementById('kontenTableBody');
    if (!tbody) return;
    
    if (content.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-12 text-gray-500">
                    <i class="fas fa-inbox text-5xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium">Belum ada konten</p>
                    <p class="text-sm">Klik "Tambah Konten" untuk menambahkan</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = content.map(item => `
        <tr class="border-b hover:bg-gray-50 transition">
            <td class="px-6 py-4">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">
                    ${item.day}
                </div>
            </td>
            <td class="px-6 py-4">
                <p class="font-semibold text-gray-900">${item.title}</p>
                <p class="text-sm text-gray-500">${item.sub_title}</p>
            </td>
            <td class="px-6 py-4">
                <p class="text-sm text-gray-700">${item.surah_name}</p>
                <p class="text-xs text-gray-500">${item.surah_text.substring(0, 50)}...</p>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    ${item.daily_focus_key}
                </span>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    ${item.task_count} tasks
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex gap-2">
                    <button onclick="editKonten(${item.id})" class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg transition">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteKonten(${item.id}, '${item.title}')" class="text-red-600 hover:text-red-800 p-2 hover:bg-red-50 rounded-lg transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

async function openKontenModal() {
    currentEditId = null;
    document.getElementById('kontenForm').reset();
    document.getElementById('kontenModalTitle').textContent = 'Tambah Konten Ramadhan';
    
    // Load available tasks
    await loadAvailableTasks();
    
    openModal('kontenModal');
}

async function editKonten(id) {
    currentEditId = id;
    document.getElementById('kontenModalTitle').textContent = 'Edit Konten Ramadhan';
    
    try {
        const response = await fetch(`api_konten.php?action=get&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const item = data.content;
            document.getElementById('day').value = item.day;
            document.getElementById('surah_name').value = item.surah_name;
            document.getElementById('surah_text').value = item.surah_text;
            document.getElementById('title').value = item.title;
            document.getElementById('sub_title').value = item.sub_title;
            document.getElementById('description').value = item.description;
            document.getElementById('tips').value = item.tips;
            document.getElementById('daily_focus_key').value = item.daily_focus_key;
            
            // Load tasks and check linked ones
            await loadAvailableTasks();
            const linkedTaskIds = item.linked_tasks.map(t => t.id);
            document.querySelectorAll('input[name="task_ids[]"]').forEach(checkbox => {
                checkbox.checked = linkedTaskIds.includes(parseInt(checkbox.value));
            });
            
            openModal('kontenModal');
        }
    } catch (error) {
        console.error('Error loading konten:', error);
        showToast(`Error loading data ${error}`, 'error');
    }
}

async function loadAvailableTasks() {
    try {
        const response = await fetch('api_konten.php?action=get_available_tasks');
        const data = await response.json();
        
        if (data.success) {
            allTasks = data.tasks;
            renderTaskCheckboxes(data.tasks);
        }
    } catch (error) {
        console.error('Error loading tasks:', error);
    }
}

function renderTaskCheckboxes(tasks) {
    const container = document.getElementById('taskCheckboxes');
    if (!container) return;
    
    const groupedTasks = {};
    tasks.forEach(task => {
        if (!groupedTasks[task.category_name]) {
            groupedTasks[task.category_name] = [];
        }
        groupedTasks[task.category_name].push(task);
    });
    
    container.innerHTML = Object.entries(groupedTasks).map(([category, categoryTasks]) => `
        <div class="mb-4">
            <p class="font-semibold text-sm text-gray-700 mb-2">${category}</p>
            <div class="space-y-2 pl-4">
                ${categoryTasks.map(task => `
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="task_ids[]" value="${task.id}" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm text-gray-700">${task.task_description}</span>
                    </label>
                `).join('')}
            </div>
        </div>
    `).join('');
}

async function deleteKonten(id, title) {
    if (!confirm(`Hapus konten "${title}"?\n\nSemua data terkait akan ikut terhapus!`)) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        const response = await fetch('api_konten.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('Konten berhasil dihapus');
            loadKontenData();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting konten:', error);
        showToast('Terjadi kesalahan', 'error');
    }
}

async function saveKonten(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', currentEditId ? 'update' : 'create');
    if (currentEditId) {
        formData.append('id', currentEditId);
    }
    
    // Get checked task IDs
    const taskIds = Array.from(document.querySelectorAll('input[name="task_ids[]"]:checked'))
        .map(cb => cb.value);
    formData.append('task_ids', JSON.stringify(taskIds));
    
    try {
        const response = await fetch('api_konten.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            showToast(currentEditId ? 'Konten berhasil diupdate' : 'Konten berhasil ditambahkan');
            closeModal('kontenModal');
            loadKontenData();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error saving konten:', error);
        showToast('Terjadi kesalahan', 'error');
    }
}

// =========================================
// MASTER TASKS
// =========================================

async function loadTasksData() {
    try {
        const response = await fetch('api_tasks.php?action=list');
        const data = await response.json();
        console.log("dta", data)
        if (data.success) {
            renderTasksTable(data.tasks);
            updateTasksSummary(data.summary);
        }
    } catch (error) {
        console.error('Error loading tasks:', error);
        showToast(`Error loading data ${error}`, 'error');
    }
}

function updateTasksSummary(summary) {
    document.getElementById('totalTasks').textContent = summary.total;
    document.getElementById('fisikCount').textContent = summary.fisik;
    document.getElementById('spiritualCount').textContent = summary.spiritual;
}

function renderTasksTable(tasks) {
    const tbody = document.getElementById('tasksTableBody');
    if (!tbody) return;
    
    if (tasks.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-12 text-gray-500">
                    <i class="fas fa-inbox text-5xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium">Belum ada task</p>
                    <p class="text-sm">Klik "Tambah Task" untuk menambahkan</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = tasks.map(task => `
        <tr class="border-b hover:bg-gray-50 transition">
            <td class="px-6 py-4">
                <p class="font-semibold text-gray-900">${task.task_description}</p>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${
                    task.category_id === 1 ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'
                }">
                    ${task.category_name}
                </span>
            </td>
            <td class="px-6 py-4">
                <span class="text-sm text-gray-600">${task.completion_count} kali diselesaikan</span>
            </td>
            <td class="px-6 py-4">
                <div class="flex gap-2">
                    <button onclick="editTask(${task.id})" class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg transition">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteTask(${task.id}, '${task.task_description}')" class="text-red-600 hover:text-red-800 p-2 hover:bg-red-50 rounded-lg transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function openTaskModal() {
    currentEditId = null;
    document.getElementById('taskForm').reset();
    document.getElementById('taskModalTitle').textContent = 'Tambah Task';
    openModal('taskModal');
}

async function editTask(id) {
    currentEditId = id;
    document.getElementById('taskModalTitle').textContent = 'Edit Task';
    
    try {
        const response = await fetch(`api_tasks.php?action=get&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const task = data.task;
            document.getElementById('task_description').value = task.task_description;
            document.getElementById('category_id').value = task.category_id;
            
            openModal('taskModal');
        }
    } catch (error) {
        console.error('Error loading task:', error);
        showToast(`Error loading data ${error}`, 'error');
    }
}

async function deleteTask(id, description) {
    if (!confirm(`Hapus task "${description}"?`)) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        const response = await fetch('api_tasks.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('Task berhasil dihapus');
            loadTasksData();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting task:', error);
        showToast('Terjadi kesalahan', 'error');
    }
}

async function saveTask(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', currentEditId ? 'update' : 'create');
    if (currentEditId) {
        formData.append('id', currentEditId);
    }
    
    try {
        const response = await fetch('api_tasks.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            showToast(currentEditId ? 'Task berhasil diupdate' : 'Task berhasil ditambahkan');
            closeModal('taskModal');
            loadTasksData();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error saving task:', error);
        showToast('Terjadi kesalahan', 'error');
    }
}

// =========================================
// MONITORING
// =========================================

async function loadMonitoringData() {
    try {
        const response = await fetch('api_monitoring.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            updateMonitoringSummary(data.summary);
            renderMonitoringTable(data.users);
        }
    } catch (error) {
        console.error('Error loading monitoring:', error);
        showToast(`Error loading data ${error}`, 'error');
    }
}

function updateMonitoringSummary(summary) {
    document.getElementById('monitorTotalUsers').textContent = summary.total_users;
    document.getElementById('monitorActiveUsers').textContent = summary.active_users;
    document.getElementById('monitorAvgProgress').textContent = summary.avg_progress + '%';
    document.getElementById('monitorTotalLogs').textContent = summary.total_logs;
}

function renderMonitoringTable(users) {
    const tbody = document.getElementById('monitoringTableBody');
    if (!tbody) return;
    
    if (users.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-12 text-gray-500">
                    <i class="fas fa-users text-5xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium">Belum ada user</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = users.map(user => {
        const statusClass = user.progress >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        const statusText = user.progress >= 50 ? 'Aktif' : 'Tidak Aktif';
        
        return `
            <tr class="border-b hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-200 flex items-center justify-center text-emerald-800 font-bold text-sm">
                            ${user.name.substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">${user.name}</p>
                            <p class="text-sm text-gray-500">${user.email}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-700">
                    ${user.days_filled} hari
                </td>
                <td class="px-6 py-4 text-gray-700">
                    ${user.tasks_completed} tasks
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 transition-all" style="width: ${user.progress}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 w-12 text-right">${user.progress}%</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                        ${statusText}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <button onclick="viewUserDetail(${user.id})" class="text-blue-600 hover:text-blue-800 flex items-center gap-1 font-medium">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

async function viewUserDetail(userId) {
    try {
        const response = await fetch(`api_monitoring.php?action=get_user_detail&user_id=${userId}`);
        const data = await response.json();
        
        if (data.success) {
            renderUserDetailModal(data);
            openModal('userDetailModal');
        }
    } catch (error) {
        console.error('Error loading user detail:', error);
        showToast(`Error loading data ${error}`, 'error');
    }
}

function renderUserDetailModal(data) {
    const user = data.user;
    const container = document.getElementById('userDetailContent');
    
    container.innerHTML = `
        <div class="space-y-6">
            <!-- User Info -->
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-16 h-16 rounded-full bg-emerald-200 flex items-center justify-center text-emerald-800 font-bold text-xl">
                    ${user.name.substring(0, 2).toUpperCase()}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">${user.name}</h3>
                    <p class="text-gray-600">${user.email}</p>
                    <p class="text-sm text-gray-500">Bergabung: ${new Date(user.created_at).toLocaleDateString('id-ID')}</p>
                </div>
            </div>
            
            <!-- Daily Progress -->
            <div>
                <h4 class="font-bold text-gray-800 mb-3">Progress Harian (${data.daily_progress.length} hari)</h4>
                <div class="max-h-64 overflow-y-auto space-y-2">
                    ${data.daily_progress.map(day => {
                        const percentage = day.total_tasks > 0 ? Math.round((day.completed_tasks / day.total_tasks) * 100) : 0;
                        return `
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                        ${day.day}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">${day.title}</p>
                                        <p class="text-sm text-gray-600">${day.completed_tasks}/${day.total_tasks} tasks</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold ${percentage === 100 ? 'text-green-600' : 'text-gray-700'}">${percentage}%</p>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
            
            <!-- Recent Journals -->
            <div>
                <h4 class="font-bold text-gray-800 mb-3">Journal Entries (${data.journals.length})</h4>
                <div class="max-h-64 overflow-y-auto space-y-3">
                    ${data.journals.length > 0 ? data.journals.slice(0, 5).map(journal => `
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <p class="font-medium text-gray-900 mb-2">Day ${journal.day}: ${journal.title}</p>
                            <div class="text-sm text-gray-700 space-y-1">
                                <p><strong>Ramadhan Why:</strong> ${journal.ramadhan_why || '-'}</p>
                                <p><strong>Bad Habit:</strong> ${journal.bad_habit || '-'}</p>
                            </div>
                        </div>
                    `).join('') : '<p class="text-gray-500 text-center py-4">Belum ada journal</p>'}
                </div>
            </div>
        </div>
    `;
}

// =========================================
// INITIALIZATION
// =========================================

document.addEventListener('DOMContentLoaded', function() {
    // Check which page is active
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || 'dashboard';
    
    if (page === 'master-content') {
        loadKontenData();
    } else if (page === 'master-tasks') {
        loadTasksData();
    } else if (page === 'monitoring') {
        loadMonitoringData();
    }
});