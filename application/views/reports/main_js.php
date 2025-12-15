/**
 * Reports System Main JavaScript
 * ระบบรายงาน - JavaScript หลัก
 */

// Global variables
let currentTheme = 'light';
let chartInstances = {};
let refreshInterval = null;

// Initialize when document is ready
$(document).ready(function() {
    initializeReportsSystem();
});

/**
 * Initialize the reports system
 */
function initializeReportsSystem() {
    console.log('🚀 Initializing Reports System...');
    
    // Initialize components
    initializeCharts();
    initializeDataTables();
    initializeModals();
    initializeTooltips();
    initializeEventHandlers();
    initializeAutoRefresh();
    
    // Setup responsive handlers
    setupResponsiveHandlers();
    
    // Initialize theme
    initializeTheme();
    
    console.log('✅ Reports System initialized successfully');
}

/**
 * Initialize Chart.js charts
 */
function initializeCharts() {
    // Default chart configuration
    Chart.defaults.font.family = 'Kanit, sans-serif';
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = '#e2e8f0';
    Chart.defaults.backgroundColor = 'rgba(59, 130, 246, 0.1)';
    
    // Responsive configuration
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;
    
    console.log('📊 Charts initialized');
}

/**
 * Initialize DataTables
 */
function initializeDataTables() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.data-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/th.json'
            },
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: [-1] } // ไม่ให้เรียงลำดับคอลัมน์สุดท้าย (Actions)
            ]
        });
        
        console.log('📋 DataTables initialized');
    }
}

/**
 * Initialize modals
 */
function initializeModals() {
    // Auto-focus first input in modals
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('input, select, textarea').first().focus();
    });
    
    // Clear forms when modals close
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0]?.reset();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();
    });
    
    console.log('🔲 Modals initialized');
}

/**
 * Initialize tooltips and popovers
 */
function initializeTooltips() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    console.log('💡 Tooltips and popovers initialized');
}

/**
 * Initialize event handlers
 */
function initializeEventHandlers() {
    // Form submission with loading
    $('form.ajax-form').on('submit', handleAjaxForm);
    
    // Confirmation dialogs
    $('[data-confirm]').on('click', handleConfirmAction);
    
    // Auto-save forms
    $('form.auto-save').on('input change', debounce(handleAutoSave, 1000));
    
    // Print buttons
    $('[data-print]').on('click', handlePrint);
    
    // Export buttons
    $('[data-export]').on('click', handleExport);
    
    // Search and filter
    $('.search-input').on('input', debounce(handleSearch, 300));
    $('.filter-select').on('change', handleFilter);
    
    // Navigation active states
    updateNavigationStates();
    
    console.log('🎯 Event handlers initialized');
}

/**
 * Initialize auto-refresh functionality
 */
function initializeAutoRefresh() {
    const refreshInterval = 5 * 60 * 1000; // 5 minutes
    
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            refreshDashboardData();
        }
    }, refreshInterval);
    
    // Refresh when page becomes visible
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            refreshDashboardData();
        }
    });
    
    console.log('🔄 Auto-refresh initialized');
}

/**
 * Setup responsive handlers
 */
function setupResponsiveHandlers() {
    // Handle window resize
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            handleWindowResize();
        }, 150);
    });
    
    // Handle mobile menu
    $('.navbar-toggler').on('click', function() {
        $('.navbar-collapse').toggleClass('show');
    });
    
    // Close mobile menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.navbar').length) {
            $('.navbar-collapse').removeClass('show');
        }
    });
    
    console.log('📱 Responsive handlers setup');
}

/**
 * Initialize theme
 */
function initializeTheme() {
    const savedTheme = localStorage.getItem('reports-theme') || 'light';
    applyTheme(savedTheme);
    
    // Theme toggle button
    $('.theme-toggle').on('click', function() {
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        applyTheme(newTheme);
        localStorage.setItem('reports-theme', newTheme);
    });
    
    console.log('🎨 Theme initialized:', savedTheme);
}

/**
 * Apply theme
 */
function applyTheme(theme) {
    currentTheme = theme;
    $('body').attr('data-theme', theme);
    
    // Update theme toggle icon
    const icon = theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
    $('.theme-toggle i').attr('class', icon);
}

/**
 * Handle AJAX form submission
 */
function handleAjaxForm(e) {
    e.preventDefault();
    
    const form = $(this);
    const formData = new FormData(this);
    const url = form.attr('action') || window.location.href;
    const method = form.attr('method') || 'POST';
    
    // Show loading
    const submitBtn = form.find('[type="submit"]');
    const originalText = submitBtn.text();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>กำลังประมวลผล...');
    
    // Clear previous errors
    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.invalid-feedback').remove();
    
    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            handleAjaxSuccess(response, form);
        },
        error: function(xhr) {
            handleAjaxError(xhr, form);
        },
        complete: function() {
            // Restore button
            submitBtn.prop('disabled', false).text(originalText);
        }
    });
}

/**
 * Handle AJAX success response
 */
function handleAjaxSuccess(response, form) {
    if (typeof response === 'string') {
        try {
            response = JSON.parse(response);
        } catch (e) {
            console.error('Invalid JSON response:', response);
            showAlert('เกิดข้อผิดพลาดในการประมวลผลข้อมูล', 'danger');
            return;
        }
    }
    
    if (response.success) {
        showAlert(response.message || 'บันทึกข้อมูลสำเร็จ', 'success');
        
        // Handle redirects
        if (response.redirect) {
            setTimeout(() => {
                window.location.href = response.redirect;
            }, 1500);
        }
        
        // Handle modal close
        if (form.closest('.modal').length) {
            form.closest('.modal').modal('hide');
        }
        
        // Handle data refresh
        if (response.refresh) {
            refreshCurrentPage();
        }
        
        // Trigger custom event
        $(document).trigger('ajaxSuccess', [response, form]);
        
    } else {
        // Handle validation errors
        if (response.errors) {
            displayValidationErrors(response.errors, form);
        } else {
            showAlert(response.message || 'เกิดข้อผิดพลาด', 'danger');
        }
    }
}

/**
 * Handle AJAX error response
 */
function handleAjaxError(xhr, form) {
    console.error('AJAX Error:', xhr);
    
    let message = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
    
    if (xhr.status === 422) {
        // Validation errors
        const response = xhr.responseJSON;
        if (response && response.errors) {
            displayValidationErrors(response.errors, form);
            return;
        }
    } else if (xhr.status === 500) {
        message = 'เกิดข้อผิดพลาดในเซิร์ฟเวอร์';
    } else if (xhr.status === 403) {
        message = 'ไม่มีสิทธิ์ในการดำเนินการนี้';
    } else if (xhr.status === 404) {
        message = 'ไม่พบหน้าที่ร้องขอ';
    }
    
    showAlert(message, 'danger');
}

/**
 * Display validation errors
 */
function displayValidationErrors(errors, form) {
    Object.keys(errors).forEach(field => {
        const input = form.find(`[name="${field}"]`);
        const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
        
        input.addClass('is-invalid');
        
        // Remove existing error message
        input.siblings('.invalid-feedback').remove();
        
        // Add new error message
        const errorHtml = `<div class="invalid-feedback">${messages.join('<br>')}</div>`;
        input.after(errorHtml);
    });
    
    // Focus on first error field
    form.find('.is-invalid').first().focus();
}

/**
 * Handle confirmation actions
 */
function handleConfirmAction(e) {
    e.preventDefault();
    
    const element = $(this);
    const message = element.data('confirm') || 'คุณแน่ใจหรือไม่?';
    const title = element.data('title') || 'ยืนยันการดำเนินการ';
    const confirmText = element.data('confirm-text') || 'ยืนยัน';
    const cancelText = element.data('cancel-text') || 'ยกเลิก';
    
    showConfirmDialog(title, message, confirmText, cancelText, function() {
        // Execute the action
        if (element.is('a')) {
            window.location.href = element.attr('href');
        } else if (element.is('button') && element.closest('form').length) {
            element.closest('form').submit();
        } else {
            element.trigger('confirmed');
        }
    });
}

/**
 * Show confirmation dialog
 */
function showConfirmDialog(title, message, confirmText, cancelText, callback) {
    const modalHtml = `
        <div class="modal fade" id="confirmModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            ${title}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${cancelText}</button>
                        <button type="button" class="btn btn-danger" id="confirmAction">${confirmText}</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal
    $('#confirmModal').remove();
    
    // Add new modal
    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    
    // Handle confirm action
    $('#confirmAction').on('click', function() {
        modal.hide();
        if (typeof callback === 'function') {
            callback();
        }
    });
    
    // Show modal
    modal.show();
    
    // Clean up when hidden
    $('#confirmModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

/**
 * Handle auto-save
 */
function handleAutoSave() {
    const form = $(this).closest('form');
    if (form.hasClass('auto-save')) {
        const formData = form.serialize();
        
        // Save to localStorage
        const formId = form.attr('id') || 'auto-save-form';
        localStorage.setItem(`auto-save-${formId}`, formData);
        
        // Show save indicator
        showAutoSaveIndicator();
    }
}

/**
 * Show auto-save indicator
 */
function showAutoSaveIndicator() {
    let indicator = $('.auto-save-indicator');
    
    if (indicator.length === 0) {
        indicator = $(`
            <div class="auto-save-indicator">
                <i class="fas fa-check text-success me-1"></i>
                บันทึกอัตโนมัติ
            </div>
        `).appendTo('body');
    }
    
    indicator.fadeIn(200).delay(2000).fadeOut(200);
}

/**
 * Handle print
 */
function handlePrint(e) {
    e.preventDefault();
    
    const target = $(this).data('print');
    
    if (target) {
        const printContent = $(target).clone();
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>รายงาน - ${document.title}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: 'Kanit', sans-serif; }
                    @media print {
                        .no-print { display: none !important; }
                        .page-break { page-break-before: always; }
                    }
                </style>
            </head>
            <body>
                <div class="container-fluid">
                    ${printContent.html()}
                </div>
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
        
    } else {
        window.print();
    }
}

/**
 * Handle export
 */
function handleExport(e) {
    e.preventDefault();
    
    const button = $(this);
    const format = button.data('export');
    const url = button.data('url');
    
    if (!url) {
        console.error('Export URL not specified');
        return;
    }
    
    // Show loading
    const originalHtml = button.html();
    button.html('<i class="fas fa-spinner fa-spin me-2"></i>กำลังส่งออก...');
    button.prop('disabled', true);
    
    // Create temporary form for download
    const form = $('<form>', {
        method: 'POST',
        action: url,
        style: 'display: none;'
    });
    
    // Add format parameter
    form.append($('<input>', {
        type: 'hidden',
        name: 'format',
        value: format
    }));
    
    // Add current filters
    $('.filter-form input, .filter-form select').each(function() {
        if ($(this).val()) {
            form.append($('<input>', {
                type: 'hidden',
                name: $(this).attr('name'),
                value: $(this).val()
            }));
        }
    });
    
    $('body').append(form);
    form.submit();
    form.remove();
    
    // Restore button after delay
    setTimeout(() => {
        button.html(originalHtml);
        button.prop('disabled', false);
    }, 3000);
    
    showAlert('กำลังเตรียมไฟล์สำหรับดาวน์โหลด...', 'info');
}

/**
 * Handle search
 */
function handleSearch() {
    const searchTerm = $(this).val().toLowerCase();
    const target = $(this).data('target') || '.searchable-item';
    
    $(target).each(function() {
        const item = $(this);
        const text = item.text().toLowerCase();
        
        if (text.includes(searchTerm)) {
            item.show();
        } else {
            item.hide();
        }
    });
    
    // Update results count
    updateSearchResults(searchTerm, target);
}

/**
 * Update search results count
 */
function updateSearchResults(searchTerm, target) {
    const total = $(target).length;
    const visible = $(target).filter(':visible').length;
    
    let resultsText = '';
    if (searchTerm) {
        resultsText = `แสดง ${visible} จาก ${total} รายการ`;
    } else {
        resultsText = `แสดงทั้งหมด ${total} รายการ`;
    }
    
    $('.search-results').text(resultsText);
}

/**
 * Handle filter
 */
function handleFilter() {
    const filters = {};
    
    $('.filter-select').each(function() {
        const name = $(this).attr('name');
        const value = $(this).val();
        if (value) {
            filters[name] = value;
        }
    });
    
    // Apply filters
    applyFilters(filters);
}

/**
 * Apply filters to data
 */
function applyFilters(filters) {
    $('.filterable-item').each(function() {
        const item = $(this);
        let show = true;
        
        Object.keys(filters).forEach(filterName => {
            const filterValue = filters[filterName];
            const itemValue = item.data(filterName);
            
            if (itemValue && itemValue !== filterValue) {
                show = false;
            }
        });
        
        if (show) {
            item.show();
        } else {
            item.hide();
        }
    });
    
    // Update filter results
    updateFilterResults(filters);
}

/**
 * Update filter results
 */
function updateFilterResults(filters) {
    const total = $('.filterable-item').length;
    const visible = $('.filterable-item').filter(':visible').length;
    
    $('.filter-results').text(`แสดง ${visible} จาก ${total} รายการ`);
}

/**
 * Update navigation active states
 */
function updateNavigationStates() {
    const currentPath = window.location.pathname;
    
    $('.nav-link').each(function() {
        const link = $(this);
        const href = link.attr('href');
        
        if (href && currentPath.includes(href.split('/').pop())) {
            link.addClass('active');
        } else {
            link.removeClass('active');
        }
    });
}

/**
 * Handle window resize
 */
function handleWindowResize() {
    // Resize charts
    Object.values(chartInstances).forEach(chart => {
        if (chart && typeof chart.resize === 'function') {
            chart.resize();
        }
    });
    
    // Update responsive elements
    updateResponsiveElements();
}

/**
 * Update responsive elements
 */
function updateResponsiveElements() {
    const isMobile = window.innerWidth < 768;
    
    // Update data table responsive
    if ($.fn.DataTable) {
        $('.data-table').DataTable().responsive.recalc();
    }
    
    // Update card layouts
    if (isMobile) {
        $('.card-deck').addClass('flex-column');
    } else {
        $('.card-deck').removeClass('flex-column');
    }
}

/**
 * Refresh dashboard data
 */
function refreshDashboardData() {
    if (typeof refreshSummaryData === 'function') {
        refreshSummaryData();
    }
    
    // Refresh charts
    refreshChartData();
    
    // Update timestamp
    updateLastRefreshTime();
}

/**
 * Refresh chart data
 */
function refreshChartData() {
    Object.keys(chartInstances).forEach(chartId => {
        const chart = chartInstances[chartId];
        if (chart && chart.config.type) {
            // Refresh chart data based on type
            refreshSpecificChart(chartId, chart);
        }
    });
}

/**
 * Refresh specific chart
 */
function refreshSpecificChart(chartId, chart) {
    // This would be implemented based on specific chart requirements
    console.log(`Refreshing chart: ${chartId}`);
}

/**
 * Update last refresh time
 */
function updateLastRefreshTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('th-TH');
    
    $('.last-refresh-time').text(`อัปเดตล่าสุด: ${timeString}`);
}

/**
 * Refresh current page
 */
function refreshCurrentPage() {
    const preserveFormData = $('.preserve-on-refresh').length > 0;
    
    if (preserveFormData) {
        // Save form data before refresh
        saveFormDataToStorage();
    }
    
    window.location.reload();
}

/**
 * Save form data to localStorage
 */
function saveFormDataToStorage() {
    $('.preserve-on-refresh').each(function() {
        const form = $(this);
        const formId = form.attr('id');
        
        if (formId) {
            const formData = form.serialize();
            localStorage.setItem(`preserve-${formId}`, formData);
        }
    });
}

/**
 * Restore form data from localStorage
 */
function restoreFormDataFromStorage() {
    $('.preserve-on-refresh').each(function() {
        const form = $(this);
        const formId = form.attr('id');
        
        if (formId) {
            const savedData = localStorage.getItem(`preserve-${formId}`);
            if (savedData) {
                // Parse and restore form data
                const data = new URLSearchParams(savedData);
                data.forEach((value, key) => {
                    form.find(`[name="${key}"]`).val(value);
                });
                
                // Clean up
                localStorage.removeItem(`preserve-${formId}`);
            }
        }
    });
}

/**
 * Utility: Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Utility: Format numbers
 */
function formatNumber(number, options = {}) {
    const defaults = {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    };
    
    return new Intl.NumberFormat('th-TH', { ...defaults, ...options }).format(number);
}

/**
 * Utility: Format currency
 */
function formatCurrency(amount, currency = 'THB') {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

/**
 * Utility: Format dates
 */
function formatDate(date, options = {}) {
    const defaults = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    
    return new Date(date).toLocaleDateString('th-TH', { ...defaults, ...options });
}

/**
 * Utility: Show alert message
 */
function showAlert(message, type = 'info', duration = 5000) {
    const alertId = 'alert-' + Date.now();
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${getAlertIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('#alertContainer').append(alertHtml);
    
    // Auto remove
    setTimeout(() => {
        $(`#${alertId}`).alert('close');
    }, duration);
}

/**
 * Get alert icon based on type
 */
function getAlertIcon(type) {
    const icons = {
        success: 'check-circle',
        danger: 'exclamation-triangle',
        warning: 'exclamation-circle',
        info: 'info-circle',
        primary: 'info-circle',
        secondary: 'info-circle'
    };
    
    return icons[type] || 'info-circle';
}

/**
 * Global error handler
 */
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    
    if (e.error.name !== 'ChunkLoadError') {
        showAlert('เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง', 'danger');
    }
});

/**
 * Global unhandled promise rejection handler
 */
window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled promise rejection:', e.reason);
    showAlert('เกิดข้อผิดพลาดในการประมวลผล', 'warning');
});

// Restore form data on page load
$(document).ready(function() {
    restoreFormDataFromStorage();
});

// Export functions for global use
window.ReportsSystem = {
    showAlert,
    showConfirmDialog,
    formatNumber,
    formatCurrency,
    formatDate,
    refreshDashboardData,
    refreshCurrentPage,
    debounce
};