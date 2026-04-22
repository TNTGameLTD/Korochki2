/**
 * Панель администратора - дополнительные функции
 * Всплывающие сообщения, фильтрация, подтверждения действий
 */

class AdminPanel {
    constructor() {
        this.init();
    }

    init() {
        this.initConfirmDialogs();
        this.initStatusChanges();
        this.initFilterPresets();
        this.showNotifications();
        this.initSearchHighlight();
    }

    // Подтверждение действий с кастомным диалогом
    initConfirmDialogs() {
        const statusForms = document.querySelectorAll('.status-form');
        
        statusForms.forEach(form => {
            const select = form.querySelector('.status-select');
            const originalStatus = form.dataset.originalStatus;
            
            select.addEventListener('change', (e) => {
                const newStatus = e.target.value;
                if (!newStatus) return;
                
                // Создаем кастомное модальное окно подтверждения
                const confirmed = this.showConfirmDialog(
                    'Подтверждение',
                    `Сменить статус заявки с "${originalStatus}" на "${newStatus}"?`
                );
                
                if (confirmed) {
                    this.showToast('Статус изменен', 'success');
                    form.submit();
                } else {
                    select.value = ''; // Сбрасываем выбор
                }
            });
        });
    }

    // Кастомный диалог подтверждения
    showConfirmDialog(title, message) {
        // Создаем оверлей
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeIn 0.2s;
        `;
        
        // Создаем диалог
        const dialog = document.createElement('div');
        dialog.className = 'confirm-dialog';
        dialog.style.cssText = `
            background: white;
            border-radius: 16px;
            padding: 24px;
            max-width: 320px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: slideUp 0.3s;
        `;
        
        dialog.innerHTML = `
            <h3 style="margin: 0 0 12px 0; color: #333;">${title}</h3>
            <p style="margin: 0 0 24px 0; color: #666;">${message}</p>
            <div style="display: flex; gap: 12px;">
                <button class="confirm-yes" style="
                    flex: 1;
                    padding: 12px;
                    background: #667eea;
                    color: white;
                    border: none;
                    border-radius: 10px;
                    cursor: pointer;
                    font-weight: 600;
                ">Да</button>
                <button class="confirm-no" style="
                    flex: 1;
                    padding: 12px;
                    background: #e2e8f0;
                    color: #4a5568;
                    border: none;
                    border-radius: 10px;
                    cursor: pointer;
                    font-weight: 600;
                ">Нет</button>
            </div>
        `;
        
        overlay.appendChild(dialog);
        document.body.appendChild(overlay);
        
        // Добавляем стили анимации
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes slideUp {
                from { transform: translateY(20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
        
        return new Promise((resolve) => {
            dialog.querySelector('.confirm-yes').addEventListener('click', () => {
                overlay.remove();
                resolve(true);
            });
            
            dialog.querySelector('.confirm-no').addEventListener('click', () => {
                overlay.remove();
                resolve(false);
            });
            
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    overlay.remove();
                    resolve(false);
                }
            });
        });
    }

    // Всплывающие уведомления (тосты)
    showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        
        const colors = {
            success: '#48bb78',
            error: '#f56565',
            info: '#667eea',
            warning: '#ed8936'
        };
        
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${colors[type] || colors.info};
            color: white;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 2000;
            animation: slideIn 0.3s;
            max-width: 300px;
            font-weight: 500;
        `;
        
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, duration);
        
        // Добавляем анимации
        if (!document.querySelector('#toast-animations')) {
            const style = document.createElement('style');
            style.id = 'toast-animations';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    // Отслеживание изменений статуса
    initStatusChanges() {
        const urlParams = new URLSearchParams(window.location.search);
        const statusChanged = sessionStorage.getItem('statusChanged');
        
        if (statusChanged) {
            this.showToast(`Статус изменен на "${statusChanged}"`, 'success');
            sessionStorage.removeItem('statusChanged');
        }
        
        // Сохраняем изменение перед отправкой формы
        document.querySelectorAll('.status-form').forEach(form => {
            form.addEventListener('submit', () => {
                const select = form.querySelector('.status-select');
                sessionStorage.setItem('statusChanged', select.value);
            });
        });
    }

    // Фильтрация с сохранением состояния
    initFilterPresets() {
        const filterSelect = document.getElementById('statusFilter');
        if (!filterSelect) return;
        
        // Сохраняем выбранный фильтр
        filterSelect.addEventListener('change', () => {
            localStorage.setItem('adminFilterStatus', filterSelect.value);
        });
        
        // Восстанавливаем фильтр при загрузке
        const savedFilter = localStorage.getItem('adminFilterStatus');
        if (savedFilter && !window.location.search.includes('status=')) {
            filterSelect.value = savedFilter;
        }
    }

    // Подсветка недавно измененных заявок
    initSearchHighlight() {
        const rows = document.querySelectorAll('tbody tr');
        const highlightId = sessionStorage.getItem('highlightAppId');
        
        if (highlightId) {
            rows.forEach(row => {
                const firstCell = row.cells[0];
                if (firstCell && firstCell.textContent === highlightId) {
                    row.style.transition = 'background 0.5s';
                    row.style.background = '#fef3c7';
                    
                    setTimeout(() => {
                        row.style.background = '';
                    }, 2000);
                    
                    sessionStorage.removeItem('highlightAppId');
                }
            });
        }
        
        // Добавляем обработчики для подсветки при изменении
        document.querySelectorAll('.status-form').forEach(form => {
            form.addEventListener('submit', () => {
                const appId = form.querySelector('input[name="app_id"]').value;
                sessionStorage.setItem('highlightAppId', appId);
            });
        });
    }

    // Показ уведомлений из URL параметров
    showNotifications() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('saved')) {
            this.showToast('Изменения сохранены', 'success');
            this.cleanUrl();
        }
    }

    // Очистка параметров URL
    cleanUrl() {
        const url = new URL(window.location);
        url.searchParams.delete('saved');
        window.history.replaceState({}, document.title, url);
    }

    // Экспорт данных (дополнительная функция)
    exportToCSV() {
        const table = document.querySelector('table');
        if (!table) return;
        
        let csv = [];
        const rows = table.querySelectorAll('tr');
        
        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const rowData = Array.from(cols).map(col => {
                let text = col.textContent.trim();
                // Экранируем запятые
                if (text.includes(',')) {
                    text = `"${text}"`;
                }
                return text;
            });
            csv.push(rowData.join(','));
        });
        
        const csvContent = csv.join('\n');
        const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', `applications_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showToast('Данные экспортированы', 'success');
    }
}

// Инициализация статистики (дополнительная функция)
class StatisticsWidget {
    constructor(applications) {
        this.applications = applications;
        this.render();
    }

    render() {
        const stats = this.calculateStats();
        const container = document.querySelector('.stats-container');
        if (!container) return;
        
        container.innerHTML = `
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px;">
                <div style="background: #ebf4ff; padding: 12px; border-radius: 12px; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #667eea;">${stats.new}</div>
                    <div style="color: #4a5568; font-size: 13px;">Новых</div>
                </div>
                <div style="background: #fef3c7; padding: 12px; border-radius: 12px; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #d69e2e;">${stats.active}</div>
                    <div style="color: #4a5568; font-size: 13px;">В обучении</div>
                </div>
                <div style="background: #c6f6d5; padding: 12px; border-radius: 12px; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: #38a169;">${stats.completed}</div>
                    <div style="color: #4a5568; font-size: 13px;">Завершено</div>
                </div>
            </div>
        `;
    }

    calculateStats() {
        return {
            new: this.applications.filter(a => a.status === 'Новая').length,
            active: this.applications.filter(a => a.status === 'Идет обучение').length,
            completed: this.applications.filter(a => a.status === 'Обучение завершено').length
        };
    }
}

// Функция для поиска по таблице
class TableSearch {
    constructor(tableId) {
        this.table = document.getElementById(tableId);
        if (!this.table) return;
        
        this.createSearchInput();
    }

    createSearchInput() {
        const searchDiv = document.createElement('div');
        searchDiv.className = 'form-group';
        searchDiv.style.marginBottom = '15px';
        searchDiv.innerHTML = `
            <input type="text" 
                   id="tableSearch" 
                   placeholder="🔍 Поиск по таблице..." 
                   style="padding: 10px 14px; border-radius: 10px; border: 1px solid #e0e0e0;">
        `;
        
        this.table.parentNode.insertBefore(searchDiv, this.table);
        
        document.getElementById('tableSearch').addEventListener('input', (e) => {
            this.filterTable(e.target.value.toLowerCase());
        });
    }

    filterTable(query) {
        const rows = this.table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }
}

// Функция сортировки таблицы
class TableSorter {
    constructor(tableId) {
        this.table = document.getElementById(tableId);
        if (!this.table) return;
        
        this.initSorting();
    }

    initSorting() {
        const headers = this.table.querySelectorAll('th');
        
        headers.forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => this.sortTable(index));
            
            // Добавляем индикатор сортировки
            header.innerHTML += ' <span style="opacity: 0.5;">⇅</span>';
        });
    }

    sortTable(columnIndex) {
        const tbody = this.table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        const isAscending = this.table.dataset.sortColumn === String(columnIndex) && 
                           this.table.dataset.sortOrder === 'asc';
        
        rows.sort((a, b) => {
            const aVal = a.cells[columnIndex].textContent.trim();
            const bVal = b.cells[columnIndex].textContent.trim();
            
            // Проверяем, является ли значение датой или числом
            if (!isNaN(aVal) && !isNaN(bVal)) {
                return isAscending ? bVal - aVal : aVal - bVal;
            }
            
            return isAscending 
                ? bVal.localeCompare(aVal) 
                : aVal.localeCompare(bVal);
        });
        
        rows.forEach(row => tbody.appendChild(row));
        
        this.table.dataset.sortColumn = columnIndex;
        this.table.dataset.sortOrder = isAscending ? 'desc' : 'asc';
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    // Основная панель администратора
    const adminPanel = new AdminPanel();
    
    // Поиск по таблице
    new TableSearch('applicationsTable');
    
    // Сортировка таблицы
    new TableSorter('applicationsTable');
    
    // Добавляем кнопку экспорта
    const headerDiv = document.querySelector('.filter-bar');
    if (headerDiv) {
        const exportBtn = document.createElement('button');
        exportBtn.className = 'btn';
        exportBtn.style.cssText = 'width: auto; padding: 10px 16px; margin-left: 10px; background: #48bb78;';
        exportBtn.innerHTML = '📊 Экспорт CSV';
        exportBtn.addEventListener('click', () => adminPanel.exportToCSV());
        headerDiv.appendChild(exportBtn);
    }
    
    // Собираем данные для статистики (если они переданы из PHP)
    if (window.applicationsData) {
        new StatisticsWidget(window.applicationsData);
    }
});

// Экспортируем для использования в глобальной области
window.AdminPanel = AdminPanel;