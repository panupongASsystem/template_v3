/**
 * Reports System Main JavaScript - Pastel Theme Edition
 * ระบบรายงาน - JavaScript หลักสำหรับ Pastel Theme
 */

// Global variables
let currentTheme = 'pastel';
let chartInstances = {};
let refreshInterval = null;
let animationQueue = [];

// Initialize when document is ready
$(document).ready(function() {
    initializePastelReportsSystem();
    
    // Auto-initialize features
    initializeAutoFeatures();
});

/**
 * Auto-initialize common features
 */
function initializeAutoFeatures() {
    // Add ripple effect to buttons
    $(document).on('click', '.btn:not(.no-ripple)', function(e) {
        PastelUtils.Animation.ripple(this, e);
    });
    
    // Add sparkle effect to primary buttons
    $(document).on('click', '.btn-primary, .btn-success', function(e) {
        PastelUtils.Animation.sparkle(e.pageX, e.pageY);
    });
    
    // Initialize form validation
    $('[data-validate]').each(function() {
        const field = this;
        
        // Validate on blur
        $(field).on('blur', function() {
            PastelValidation.validateField(field);
        });
        
        // Clear errors on focus
        $(field).on('focus', function() {
            PastelValidation.clearErrors(field);
        });
        
        // Add required indicator
        if (field.dataset.validate.includes('required')) {
            const label = $(`label[for="${field.id}"]`);
            if (label.length && !label.find('.required-indicator').length) {
                label.append('<span class="required-indicator"> *</span>');
            }
        }
    });
    
    // Auto-save forms
    $('form.auto-save').on('input change', PastelUtils.Performance.debounce(function() {
        const formData = $(this).serialize();
        const formId = this.id || 'auto-save-form';
        localStorage.setItem(`pastel_autosave_${formId}`, formData);
        
        // Show auto-save indicator
        showAutoSaveIndicator();
    }, 1000));
    
    // Restore auto-saved data
    $('form.auto-save').each(function() {
        const formId = this.id || 'auto-save-form';
        const savedData = localStorage.getItem(`pastel_autosave_${formId}`);
        if (savedData) {
            const data = new URLSearchParams(savedData);
            data.forEach((value, key) => {
                const field = this.querySelector(`[name="${key}"]`);
                if (field) field.value = value;
            });
        }
    });
    
    // Device-specific classes
    $('body').addClass(`device-${PastelUtils.Device.getType()}`);
    if (PastelUtils.Device.isTouch()) {
        $('body').addClass('touch-device');
    }
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    
    console.log('🛠️ Auto-features initialized');
}

/**
 * Show auto-save indicator
 */
function showAutoSaveIndicator() {
    let indicator = $('.auto-save-indicator');
    
    if (indicator.length === 0) {
        indicator = $(`
            <div class="auto-save-indicator position-fixed" style="
                top: 20px; right: 20px; z-index: 9999;
                background: linear-gradient(135deg, var(--pastel-green), var(--success-color));
                color: white; padding: 0.5rem 1rem; border-radius: 20px;
                box-shadow: var(--shadow-soft); backdrop-filter: blur(10px);
            ">
                <i class="fas fa-check text-white me-1"></i>
                บันทึกอัตโนมัติ
            </div>
        `).appendTo('body');
    }
    
    indicator.fadeIn(200).delay(2000).fadeOut(200);
}

/**
 * Initialize the Pastel Reports System
 */
function initializePastelReportsSystem() {
    console.log('🎨 Initializing Pastel Reports System...');
    
    // Show welcome message
    showWelcomeAnimation();
    
    // Initialize components
    initializePastelCharts();
    initializePastelDataTables();
    initializePastelModals();
    initializePastelTooltips();
    initializePastelEventHandlers();
    initializePastelAnimations();
    initializeAutoRefresh();
    
    // Setup responsive handlers
    setupPastelResponsiveHandlers();
    
    // Initialize theme
    initializePastelTheme();
    
    // Initialize special effects
    initializePastelEffects();
    
    console.log('✅ Pastel Reports System initialized successfully');
}

/**
 * Show welcome animation
 */
function showWelcomeAnimation() {
    // Create welcome overlay
    const welcomeHtml = `
        <div id="welcomeOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
             style="background: linear-gradient(135deg, rgba(230, 230, 250, 0.95), rgba(224, 246, 255, 0.95)); z-index: 9999;">
            <div class="text-center">
                <div class="spinner-pastel mb-3"></div>
                <h4 class="animated-title mb-2">ยินดีต้อนรับสู่ระบบรายงาน</h4>
                <p class="text-muted">กำลังโหลดข้อมูล...</p>
            </div>
        </div>
    `;
    
    $('body').append(welcomeHtml);
    
    // Fade out after 2 seconds
    setTimeout(() => {
        $('#welcomeOverlay').fadeOut(800, function() {
            $(this).remove();
        });
    }, 2000);
}

/**
 * Initialize Pastel Chart.js charts
 */
function initializePastelCharts() {
    // Default chart configuration with pastel colors
    Chart.defaults.font.family = 'Kanit, Sarabun, sans-serif';
    Chart.defaults.color = '#718096';
    Chart.defaults.borderColor = 'rgba(135, 206, 235, 0.2)';
    Chart.defaults.backgroundColor = 'rgba(135, 206, 235, 0.1)';
    
    // Pastel color palette
    Chart.defaults.plugins.colors = {
        pastelColors: [
            'rgba(135, 206, 235, 0.8)', // Pastel Blue
            'rgba(221, 160, 221, 0.8)', // Pastel Purple
            'rgba(245, 165, 146, 0.8)', // Pastel Coral
            'rgba(152, 251, 152, 0.8)', // Pastel Green
            'rgba(255, 182, 193, 0.8)', // Pastel Pink
            'rgba(240, 230, 140, 0.8)'  // Pastel Yellow
        ]
    };
    
    // Responsive configuration
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;
    
    // Custom animations
    Chart.defaults.animation = {
        duration: 2000,
        easing: 'easeInOutQuart',
        delay: function(context) {
            return context.dataIndex * 100;
        }
    };
    
    console.log('📊 Pastel Charts initialized');
}

/**
 * Initialize Pastel DataTables
 */
function initializePastelDataTables() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.data-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/th.json'
            },
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: [-1] }
            ],
            drawCallback: function() {
                // Add pastel styling to table elements
                $(this.api().table().container()).find('.paginate_button').addClass('btn-pastel');
                $(this.api().table().container()).find('.dataTables_filter input').addClass('form-control border-pastel');
            }
        });
        
        console.log('📋 Pastel DataTables initialized');
    }
}

/**
 * Initialize Pastel modals
 */
function initializePastelModals() {
    // Auto-focus first input in modals
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('input, select, textarea').first().focus();
        
        // Add entrance animation
        $(this).find('.modal-content').addClass('fade-in-pastel');
    });
    
    // Clear forms when modals close
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0]?.reset();
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();
        $(this).find('.modal-content').removeClass('fade-in-pastel');
    });
    
    console.log('🔲 Pastel Modals initialized');
}

/**
 * Initialize Pastel tooltips and popovers
 */
function initializePastelTooltips() {
    // Initialize tooltips with pastel styling
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            customClass: 'pastel-tooltip'
        });
    });
    
    // Initialize popovers with pastel styling
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            customClass: 'pastel-popover'
        });
    });
    
    console.log('💡 Pastel Tooltips and popovers initialized');
}

/**
 * Initialize Pastel event handlers
 */
function initializePastelEventHandlers() {
    // Enhanced form submission with pastel loading
    $('form.ajax-form').on('submit', handlePastelAjaxForm);
    
    // Pastel confirmation dialogs
    $('[data-confirm]').on('click', handlePastelConfirmAction);
    
    // Auto-save forms with pastel indicators
    $('form.auto-save').on('input change', debounce(handlePastelAutoSave, 1000));
    
    // Print buttons with pastel styling
    $('[data-print]').on('click', handlePastelPrint);
    
    // Export buttons with pastel animations
    $('[data-export]').on('click', handlePastelExport);
    
    // Enhanced search with pastel effects
    $('.search-input').on('input', debounce(handlePastelSearch, 300));
    $('.filter-select').on('change', handlePastelFilter);
    
    // Navigation with pastel transitions
    updatePastelNavigationStates();
    
    // Card hover effects
    initializePastelCardEffects();
    
    console.log('🎯 Pastel Event handlers initialized');
}

/**
 * Initialize Pastel animations
 */
function initializePastelAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                
                // Add appropriate animation class
                if (element.classList.contains('summary-card')) {
                    element.classList.add('scale-in-animation');
                } else if (element.classList.contains('card-wrapper')) {
                    element.classList.add('slide-in-up-animation');
                } else {
                    element.classList.add('fade-in-pastel');
                }
                
                // Unobserve after animation
                setTimeout(() => {
                    observer.unobserve(element);
                }, 1000);
            }
        });
    }, observerOptions);
    
    // Observe animated elements
    document.querySelectorAll('.summary-card, .card-wrapper, .storage-status-item').forEach(el => {
        observer.observe(el);
    });
    
    // Staggered animations for similar elements
    $('.summary-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
    
    $('.card-wrapper').each(function(index) {
        $(this).css('animation-delay', (0.2 + index * 0.2) + 's');
    });
    
    console.log('🎭 Pastel Animations initialized');
}

/**
 * Initialize Pastel card effects
 */
function initializePastelCardEffects() {
    // Enhanced hover effects for summary cards
    $('.summary-card').on('mouseenter', function() {
        $(this).addClass('pastel-hover-effect');
        
        // Add ripple effect
        const ripple = $('<div class="pastel-ripple"></div>');
        $(this).append(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    }).on('mouseleave', function() {
        $(this).removeClass('pastel-hover-effect');
    });
    
    // Icon rotation effects
    $('.summary-icon, .status-icon').on('mouseenter', function() {
        $(this).addClass('rotate-animation');
    }).on('mouseleave', function() {
        $(this).removeClass('rotate-animation');
    });
    
    // Button pulse effects
    $('.btn').on('click', function() {
        $(this).addClass('pulse-animation');
        setTimeout(() => {
            $(this).removeClass('pulse-animation');
        }, 600);
    });
}

/**
 * Initialize auto-refresh functionality
 */
function initializeAutoRefresh() {
    const refreshInterval = 5 * 60 * 1000; // 5 minutes
    
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            refreshPastelDashboardData();
        }
    }, refreshInterval);
    
    // Refresh when page becomes visible
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            // Add subtle animation
            $('.summary-card').addClass('fade-in-pastel');
            setTimeout(() => {
                $('.summary-card').removeClass('fade-in-pastel');
                refreshPastelDashboardData();
            }, 300);
        }
    });
    
    console.log('🔄 Pastel Auto-refresh initialized');
}

/**
 * Setup Pastel responsive handlers
 */
function setupPastelResponsiveHandlers() {
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            handlePastelWindowResize();
        }, 150);
    });
    
    // Handle mobile menu with pastel transitions
    $('.navbar-toggler').on('click', function() {
        $('.navbar-collapse').toggleClass('show');
        $(this).addClass('pulse-animation');
        setTimeout(() => {
            $(this).removeClass('pulse-animation');
        }, 300);
    });
    
    // Close mobile menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.navbar').length) {
            $('.navbar-collapse').removeClass('show');
        }
    });
    
    console.log('📱 Pastel Responsive handlers setup');
}

/**
 * Initialize Pastel theme
 */
function initializePastelTheme() {
    currentTheme = 'pastel';
    $('body').attr('data-theme', 'pastel');
    
    // Add theme class to body
    $('body').addClass('pastel-theme');
    
    // Initialize theme particles (optional)
    initializePastelParticles();
    
    console.log('🎨 Pastel Theme initialized');
}

/**
 * Initialize special pastel effects
 */
function initializePastelEffects() {
    // Gradient animation for titles
    $('.animated-title').each(function() {
        $(this).addClass('gradient-animation');
    });
    
    // Floating animation for icons
    $('.summary-icon, .status-icon').addClass('float-base');
    
    // Shimmer effect for loading elements
    $('.loading-shimmer').addClass('shimmer-effect');
    
    // Add sparkle effect on certain interactions
    $(document).on('click', '.btn-primary, .btn-success', function(e) {
        createSparkleEffect(e.pageX, e.pageY);
    });
    
    console.log('✨ Pastel Effects initialized');
}

/**
 * Initialize pastel particles (optional background effect)
 */
function initializePastelParticles() {
    // Create subtle floating particles
    const particleContainer = $('<div id="pastelParticles" class="position-fixed w-100 h-100" style="top: 0; left: 0; pointer-events: none; z-index: 1;"></div>');
    $('body').prepend(particleContainer);
    
    // Create particles
    for (let i = 0; i < 20; i++) {
        createPastelParticle();
    }
}

/**
 * Create a pastel particle
 */
function createPastelParticle() {
    const colors = ['rgba(135, 206, 235, 0.3)', 'rgba(221, 160, 221, 0.3)', 'rgba(245, 165, 146, 0.3)', 'rgba(152, 251, 152, 0.3)'];
    const particle = $(`
        <div class="pastel-particle" style="
            position: absolute;
            width: ${Math.random() * 10 + 5}px;
            height: ${Math.random() * 10 + 5}px;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            border-radius: 50%;
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100}%;
            animation: float ${Math.random() * 10 + 10}s infinite linear;
        "></div>
    `);
    
    $('#pastelParticles').append(particle);
    
    // Remove particle after animation
    setTimeout(() => {
        particle.remove();
        createPastelParticle(); // Create new particle
    }, (Math.random() * 10 + 10) * 1000);
}

/**
 * Create sparkle effect
 */
function createSparkleEffect(x, y) {
    const sparkle = $(`
        <div class="sparkle-effect" style="
            position: fixed;
            left: ${x}px;
            top: ${y}px;
            width: 10px;
            height: 10px;
            background: radial-gradient(circle, #87CEEB, transparent);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            animation: sparkle 0.6s ease-out forwards;
        "></div>
    `);
    
    $('body').append(sparkle);
    
    setTimeout(() => {
        sparkle.remove();
    }, 600);
}

/**
 * Handle Pastel AJAX form submission
 */
function handlePastelAjaxForm(e) {
    e.preventDefault();
    
    const form = $(this);
    const formData = new FormData(this);
    const url = form.attr('action') || window.location.href;
    const method = form.attr('method') || 'POST';
    
    // Show pastel loading
    const submitBtn = form.find('[type="submit"]');
    const originalHtml = submitBtn.html();
    submitBtn.prop('disabled', true)
              .html('<div class="spinner-pastel me-2" style="width: 1rem; height: 1rem;"></div>กำลังประมวลผล...')
              .addClass('btn-pastel-loading');
    
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
            handlePastelAjaxSuccess(response, form);
        },
        error: function(xhr) {
            handlePastelAjaxError(xhr, form);
        },
        complete: function() {
            // Restore button with animation
            setTimeout(() => {
                submitBtn.prop('disabled', false)
                         .html(originalHtml)
                         .removeClass('btn-pastel-loading')
                         .addClass('pulse-animation');
                
                setTimeout(() => {
                    submitBtn.removeClass('pulse-animation');
                }, 300);
            }, 500);
        }
    });
}

/**
 * Handle Pastel AJAX success response
 */
function handlePastelAjaxSuccess(response, form) {
    if (typeof response === 'string') {
        try {
            response = JSON.parse(response);
        } catch (e) {
            console.error('Invalid JSON response:', response);
            showPastelAlert('เกิดข้อผิดพลาดในการประมวลผลข้อมูล', 'danger');
            return;
        }
    }
    
    if (response.success) {
        showPastelAlert(response.message || 'บันทึกข้อมูลสำเร็จ', 'success');
        
        // Handle redirects with pastel transition
        if (response.redirect) {
            showPastelTransition(() => {
                window.location.href = response.redirect;
            });
        }
        
        // Handle modal close with animation
        if (form.closest('.modal').length) {
            form.closest('.modal').find('.modal-content').addClass('scale-out-animation');
            setTimeout(() => {
                form.closest('.modal').modal('hide');
            }, 300);
        }
        
        // Handle data refresh
        if (response.refresh) {
            refreshPastelCurrentPage();
        }
        
        // Trigger custom event
        $(document).trigger('pastelAjaxSuccess', [response, form]);
        
    } else {
        // Handle validation errors with pastel styling
        if (response.errors) {
            displayPastelValidationErrors(response.errors, form);
        } else {
            showPastelAlert(response.message || 'เกิดข้อผิดพลาด', 'danger');
        }
    }
}

/**
 * Handle Pastel AJAX error response
 */
function handlePastelAjaxError(xhr, form) {
    console.error('AJAX Error:', xhr);
    
    let message = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
    
    if (xhr.status === 422) {
        const response = xhr.responseJSON;
        if (response && response.errors) {
            displayPastelValidationErrors(response.errors, form);
            return;
        }
    } else if (xhr.status === 500) {
        message = 'เกิดข้อผิดพลาดในเซิร์ฟเวอร์';
    } else if (xhr.status === 403) {
        message = 'ไม่มีสิทธิ์ในการดำเนินการนี้';
    } else if (xhr.status === 404) {
        message = 'ไม่พบหน้าที่ร้องขอ';
    }
    
    showPastelAlert(message, 'danger');
}

/**
 * Display Pastel validation errors
 */
function displayPastelValidationErrors(errors, form) {
    Object.keys(errors).forEach(field => {
        const input = form.find(`[name="${field}"]`);
        const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
        
        input.addClass('is-invalid border-pastel-danger');
        
        // Remove existing error message
        input.siblings('.invalid-feedback').remove();
        
        // Add new error message with pastel styling
        const errorHtml = `<div class="invalid-feedback pastel-error-message">${messages.join('<br>')}</div>`;
        input.after(errorHtml);
        
        // Add shake animation
        input.addClass('shake-animation');
        setTimeout(() => {
            input.removeClass('shake-animation');
        }, 600);
    });
    
    // Focus on first error field with smooth scroll
    const firstError = form.find('.is-invalid').first();
    if (firstError.length) {
        $('html, body').animate({
            scrollTop: firstError.offset().top - 100
        }, 500);
        firstError.focus();
    }
}

/**
 * Handle Pastel confirmation actions
 */
function handlePastelConfirmAction(e) {
    e.preventDefault();
    
    const element = $(this);
    const message = element.data('confirm') || 'คุณแน่ใจหรือไม่?';
    const title = element.data('title') || 'ยืนยันการดำเนินการ';
    const confirmText = element.data('confirm-text') || 'ยืนยัน';
    const cancelText = element.data('cancel-text') || 'ยกเลิก';
    
    showPastelConfirmDialog(title, message, confirmText, cancelText, function() {
        // Execute the action with pastel effect
        element.addClass('pulse-animation');
        
        if (element.is('a')) {
            showPastelTransition(() => {
                window.location.href = element.attr('href');
            });
        } else if (element.is('button') && element.closest('form').length) {
            element.closest('form').submit();
        } else {
            element.trigger('confirmed');
        }
    });
}

/**
 * Show Pastel confirmation dialog
 */
function showPastelConfirmDialog(title, message, confirmText, cancelText, callback) {
    const modalHtml = `
        <div class="modal fade" id="pastelConfirmModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-pastel shadow-pastel">
                    <div class="modal-header bg-gradient-pastel text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${title}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-question-circle text-pastel-warning" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p class="mb-0 text-pastel-dark">${message}</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>${cancelText}
                        </button>
                        <button type="button" class="btn btn-danger" id="pastelConfirmAction">
                            <i class="fas fa-check me-1"></i>${confirmText}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal
    $('#pastelConfirmModal').remove();
    
    // Add new modal
    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('pastelConfirmModal'));
    
    // Handle confirm action
    $('#pastelConfirmAction').on('click', function() {
        $(this).addClass('pulse-animation');
        
        setTimeout(() => {
            modal.hide();
            if (typeof callback === 'function') {
                callback();
            }
        }, 300);
    });
    
    // Show modal with animation
    modal.show();
    
    // Add entrance animation
    $('#pastelConfirmModal').on('shown.bs.modal', function() {
        $(this).find('.modal-content').addClass('scale-in-animation');
    });
    
    // Clean up when hidden
    $('#pastelConfirmModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

/**
 * Handle Pastel auto-save
 */
function handlePastelAutoSave() {
    const form = $(this).closest('form');
    if (form.hasClass('auto-save')) {
        const formData = form.serialize();
        
        // Save to localStorage
        const formId = form.attr('id') || 'auto-save-form';
        localStorage.setItem(`pastel-auto-save-${formId}`, formData);
        
        // Show pastel save indicator
        showPastelAutoSaveIndicator();
    }
}

/**
 * Show Pastel auto-save indicator
 */
function showPastelAutoSaveIndicator() {
    let indicator = $('.pastel-auto-save-indicator');
    
    if (indicator.length === 0) {
        indicator = $(`
            <div class="pastel-auto-save-indicator position-fixed" style="
                top: 20px; right: 20px; z-index: 9999;
                background: linear-gradient(135deg, var(--pastel-green), var(--success-color));
                color: white; padding: 0.5rem 1rem; border-radius: 20px;
                box-shadow: var(--shadow-soft); backdrop-filter: blur(10px);
            ">
                <i class="fas fa-check text-white me-1"></i>
                บันทึกอัตโนมัติ
            </div>
        `).appendTo('body');
    }
    
    indicator.fadeIn(200).delay(2000).fadeOut(200);
}

/**
 * Refresh Pastel dashboard data
 */
function refreshPastelDashboardData() {
    // Add loading animation to cards
    $('.summary-card').addClass('shimmer-effect');
    
    if (typeof refreshSummaryData === 'function') {
        refreshSummaryData();
    }
    
    // Refresh charts with animation
    refreshPastelChartData();
    
    // Update timestamp
    updatePastelLastRefreshTime();
    
    // Remove loading animation
    setTimeout(() => {
        $('.summary-card').removeClass('shimmer-effect').addClass('fade-in-pastel');
        setTimeout(() => {
            $('.summary-card').removeClass('fade-in-pastel');
        }, 600);
    }, 1000);
}

/**
 * Refresh Pastel chart data
 */
function refreshPastelChartData() {
    Object.keys(chartInstances).forEach(chartId => {
        const chart = chartInstances[chartId];
        if (chart && chart.config.type) {
            // Add update animation
            chart.update('active');
        }
    });
}

/**
 * Update Pastel last refresh time
 */
function updatePastelLastRefreshTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('th-TH');
    
    $('.last-refresh-time').text(`อัปเดตล่าสุด: ${timeString}`).addClass('text-pastel-success');
}

/**
 * Show Pastel transition effect
 */
function showPastelTransition(callback) {
    const overlay = $(`
        <div class="pastel-transition-overlay position-fixed w-100 h-100" style="
            top: 0; left: 0; z-index: 9999;
            background: linear-gradient(135deg, rgba(230, 230, 250, 0.9), rgba(224, 246, 255, 0.9));
            display: flex; align-items: center; justify-content: center;
        ">
            <div class="text-center">
                <div class="spinner-pastel mb-3"></div>
                <p class="text-pastel-dark">กำลังโหลด...</p>
            </div>
        </div>
    `);
    
    $('body').append(overlay);
    
    setTimeout(() => {
        if (typeof callback === 'function') {
            callback();
        }
    }, 800);
}

/**
 * Utility: Show Pastel alert message
 */
function showPastelAlert(message, type = 'info', duration = 5000) {
    const alertId = 'pastel-alert-' + Date.now();
    const typeClasses = {
        success: 'alert-success border-pastel-success',
        danger: 'alert-danger border-pastel-danger',
        warning: 'alert-warning border-pastel-warning',
        info: 'alert-info border-pastel-info'
    };
    
    const icons = {
        success: 'fa-check-circle',
        danger: 'fa-exclamation-triangle',
        warning: 'fa-exclamation-circle',
        info: 'fa-info-circle'
    };
    
    const alertHtml = `
        <div id="${alertId}" class="alert ${typeClasses[type]} alert-dismissible fade show shadow-pastel" 
             role="alert" style="backdrop-filter: blur(10px);">
            <i class="fas ${icons[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Create alert container if not exists
    if ($('#pastelAlertContainer').length === 0) {
        $('body').append(`
            <div id="pastelAlertContainer" class="position-fixed" style="
                top: 100px; right: 20px; z-index: 1050; max-width: 400px;
            "></div>
        `);
    }
    
    $('#pastelAlertContainer').append(alertHtml);
    
    // Add entrance animation
    $(`#${alertId}`).addClass('slide-in-right-animation');
    
    // Auto remove
    setTimeout(() => {
        $(`#${alertId}`).addClass('slide-out-right-animation');
        setTimeout(() => {
            $(`#${alertId}`).alert('close');
        }, 300);
    }, duration);
}

/**
 * Handle Pastel window resize
 */
function handlePastelWindowResize() {
    // Resize charts with animation
    Object.values(chartInstances).forEach(chart => {
        if (chart && typeof chart.resize === 'function') {
            chart.resize();
        }
    });
    
    // Update responsive elements
    updatePastelResponsiveElements();
    
    // Refresh particle positions
    if ($('#pastelParticles').length) {
        $('#pastelParticles .pastel-particle').each(function() {
            $(this).css({
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%'
            });
        });
    }
}

/**
 * Update Pastel responsive elements
 */
function updatePastelResponsiveElements() {
    const isMobile = window.innerWidth < 768;
    
    // Update data table responsive
    if ($.fn.DataTable) {
        $('.data-table').DataTable().responsive.recalc();
    }
    
    // Update card layouts with animation
    if (isMobile) {
        $('.card-deck').addClass('flex-column').addClass('mobile-transition');
    } else {
        $('.card-deck').removeClass('flex-column').addClass('desktop-transition');
    }
    
    // Update navigation
    if (isMobile) {
        $('.navbar-nav').addClass('mobile-nav');
    } else {
        $('.navbar-nav').removeClass('mobile-nav');
    }
}

/**
 * Update Pastel navigation states
 */
function updatePastelNavigationStates() {
    const currentPath = window.location.pathname;
    
    $('.nav-link').each(function() {
        const link = $(this);
        const href = link.attr('href');
        
        if (href && currentPath.includes(href.split('/').pop())) {
            link.addClass('active pastel-nav-active');
        } else {
            link.removeClass('active pastel-nav-active');
        }
    });
}

/**
 * Utility Functions
 */
window.PastelUtils = {
    /**
     * Animation utilities
     */
    Animation: {
        entrance: function(element, type = 'fadeInUp', delay = 0) {
            const el = $(element);
            el.css({
                'opacity': '0',
                'transform': this.getTransform(type, 'start'),
                'animation-delay': delay + 'ms'
            });
            
            setTimeout(() => {
                el.css({
                    'transition': 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)',
                    'opacity': '1',
                    'transform': this.getTransform(type, 'end')
                });
            }, delay);
        },

        stagger: function(elements, type = 'fadeInUp', staggerDelay = 100) {
            $(elements).each((index, element) => {
                this.entrance(element, type, index * staggerDelay);
            });
        },

        getTransform: function(type, state) {
            const transforms = {
                fadeInUp: { start: 'translateY(30px)', end: 'translateY(0)' },
                fadeInDown: { start: 'translateY(-30px)', end: 'translateY(0)' },
                fadeInLeft: { start: 'translateX(-30px)', end: 'translateX(0)' },
                fadeInRight: { start: 'translateX(30px)', end: 'translateX(0)' },
                scaleIn: { start: 'scale(0.8)', end: 'scale(1)' }
            };
            return transforms[type]?.[state] || 'none';
        },

        ripple: function(element, event) {
            const el = $(element);
            const rect = element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;
            
            const ripple = $(`
                <div class="pastel-ripple" style="
                    position: absolute; width: ${size}px; height: ${size}px;
                    left: ${x}px; top: ${y}px; border-radius: 50%;
                    background: radial-gradient(circle, rgba(255,255,255,0.5) 0%, transparent 70%);
                    transform: scale(0); animation: ripple 0.6s ease-out;
                    pointer-events: none; z-index: 1;
                "></div>
            `);
            
            el.css('position', 'relative').append(ripple);
            setTimeout(() => ripple.remove(), 600);
        },

        sparkle: function(x, y) {
            const sparkle = $(`
                <div style="position: fixed; left: ${x}px; top: ${y}px; width: 10px; height: 10px;
                    background: radial-gradient(circle, #87CEEB, transparent); border-radius: 50%;
                    pointer-events: none; z-index: 9999; animation: sparkle 0.6s ease-out forwards;">
                </div>
            `);
            $('body').append(sparkle);
            setTimeout(() => sparkle.remove(), 600);
        }
    },

    /**
     * Color utilities
     */
    Color: {
        palette: {
            lavender: '#E6E6FA', mint: '#F0FFF0', peach: '#FFEFD5',
            sky: '#E0F6FF', rose: '#FFE4E6', lemon: '#FFFACD',
            purple: '#DDA0DD', green: '#98FB98', coral: '#F5A592',
            blue: '#87CEEB', pink: '#FFB6C1', yellow: '#F0E68C'
        },

        random: function() {
            const colors = Object.values(this.palette);
            return colors[Math.floor(Math.random() * colors.length)];
        },

        hexToRgba: function(hex, alpha = 1) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }
    },

    /**
     * Format utilities
     */
    Format: {
        number: function(number, options = {}) {
            const defaults = { minimumFractionDigits: 0, maximumFractionDigits: 2 };
            return new Intl.NumberFormat('th-TH', { ...defaults, ...options }).format(number);
        },

        currency: function(amount, currency = 'THB') {
            return new Intl.NumberFormat('th-TH', { style: 'currency', currency }).format(amount);
        },

        date: function(date, options = {}) {
            const defaults = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString('th-TH', { ...defaults, ...options });
        },

        fileSize: function(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i];
        }
    },

    /**
     * Notification utilities
     */
    Notification: {
        toast: function(message, type = 'info', duration = 5000) {
            const typeClasses = {
                'success': 'bg-pastel-success',
                'error': 'bg-pastel-danger', 
                'info': 'bg-pastel-info',
                'warning': 'bg-pastel-warning'
            };
            
            const icons = {
                'success': 'fa-check-circle',
                'error': 'fa-exclamation-triangle',
                'info': 'fa-info-circle',
                'warning': 'fa-exclamation-circle'
            };
            
            const toastId = 'toast-' + Date.now();
            const toastHtml = `
                <div id="${toastId}" class="toast pastel-toast align-items-center text-white ${typeClasses[type]} border-0" 
                     role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas ${icons[type]} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            let container = $('#pastelToastContainer');
            if (!container.length) {
                container = $('<div id="pastelToastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
                $('body').append(container);
            }
            
            container.append(toastHtml);
            const toast = new bootstrap.Toast(document.getElementById(toastId), { delay: duration });
            toast.show();
            
            $(`#${toastId}`).on('hidden.bs.toast', function() {
                $(this).remove();
            });
        }
    },

    /**
     * Performance utilities
     */
    Performance: {
        debounce: function(func, wait, immediate = false) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    timeout = null;
                    if (!immediate) func.apply(this, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(this, args);
            };
        },

        throttle: function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }
    },

    /**
     * Device utilities
     */
    Device: {
        isMobile: () => window.innerWidth < 768,
        isTablet: () => window.innerWidth >= 768 && window.innerWidth < 1024,
        isDesktop: () => window.innerWidth >= 1024,
        getType: function() {
            if (this.isMobile()) return 'mobile';
            if (this.isTablet()) return 'tablet';
            return 'desktop';
        },
        isTouch: () => 'ontouchstart' in window || navigator.maxTouchPoints > 0
    },

    /**
     * Validation utilities
     */
    Validation: {
        email: (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email),
        phone: (phone) => /^(\+66|66|0)[0-9]{8,9}$/.test(phone.replace(/[-\s]/g, '')),
        required: (value) => value !== null && value !== undefined && value.toString().trim() !== '',
        minLength: (value, min) => value && value.toString().length >= min,
        maxLength: (value, max) => !value || value.toString().length <= max
    }
};

/**
 * Form Validation System
 */
window.PastelValidation = {
    validateField: function(field, showErrors = true) {
        const rules = field.dataset.validate;
        if (!rules) return { valid: true, errors: [] };

        const value = field.value;
        const errors = [];
        const ruleList = rules.split('|');

        for (const rule of ruleList) {
            const [ruleName, param] = rule.split(':');
            
            switch (ruleName) {
                case 'required':
                    if (!PastelUtils.Validation.required(value)) {
                        errors.push('ฟิลด์นี้จำเป็นต้องกรอก');
                    }
                    break;
                case 'email':
                    if (value && !PastelUtils.Validation.email(value)) {
                        errors.push('รูปแบบอีเมลไม่ถูกต้อง');
                    }
                    break;
                case 'phone':
                    if (value && !PastelUtils.Validation.phone(value)) {
                        errors.push('รูปแบบหมายเลขโทรศัพท์ไม่ถูกต้อง');
                    }
                    break;
                case 'minLength':
                    if (value && !PastelUtils.Validation.minLength(value, parseInt(param))) {
                        errors.push(`ต้องมีความยาวอย่างน้อย ${param} ตัวอักษร`);
                    }
                    break;
                case 'maxLength':
                    if (value && !PastelUtils.Validation.maxLength(value, parseInt(param))) {
                        errors.push(`ความยาวไม่เกิน ${param} ตัวอักษร`);
                    }
                    break;
            }
        }

        const result = { valid: errors.length === 0, errors };

        if (showErrors) {
            this.displayErrors(field, errors);
        }

        return result;
    },

    displayErrors: function(field, errors) {
        this.clearErrors(field);
        
        if (errors.length === 0) return;

        field.classList.add('is-invalid');
        const errorContainer = document.createElement('div');
        errorContainer.className = 'invalid-feedback';
        errorContainer.innerHTML = errors.join('<br>');
        field.parentNode.appendChild(errorContainer);

        field.classList.add('shake-animation');
        setTimeout(() => field.classList.remove('shake-animation'), 600);
    },

    clearErrors: function(field) {
        field.classList.remove('is-invalid');
        const errorMessages = field.parentNode.querySelectorAll('.invalid-feedback');
        errorMessages.forEach(msg => msg.remove());
    },

    showSuccess: function(field) {
        this.clearErrors(field);
        field.classList.add('is-valid');
    }
};

/**
 * Utility: Format numbers with pastel styling
 */
function formatPastelNumber(number, options = {}) {
    const defaults = {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    };
    
    const formatted = new Intl.NumberFormat('th-TH', { ...defaults, ...options }).format(number);
    return `<span class="pastel-number">${formatted}</span>`;
}

/**
 * Global error handler with pastel styling
 */
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    
    if (e.error.name !== 'ChunkLoadError') {
        showPastelAlert('เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง', 'danger');
    }
});

/**
 * Global unhandled promise rejection handler
 */
window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled promise rejection:', e.reason);
    showPastelAlert('เกิดข้อผิดพลาดในการประมวลผล', 'warning');
});

// Export functions for global use
window.PastelReportsSystem = {
    showPastelAlert,
    showPastelConfirmDialog,
    showPastelTransition,
    formatPastelNumber,
    refreshPastelDashboardData,
    createSparkleEffect: PastelUtils.Animation.sparkle,
    debounce: PastelUtils.Performance.debounce,
    
    // Additional utilities
    utils: PastelUtils,
    validation: PastelValidation,
    
    // Quick access functions
    toast: PastelUtils.Notification.toast,
    formatNumber: PastelUtils.Format.number,
    formatCurrency: PastelUtils.Format.currency,
    formatDate: PastelUtils.Format.date,
    formatFileSize: PastelUtils.Format.fileSize,
    
    // Animation helpers
    animateEntrance: PastelUtils.Animation.entrance,
    animateStagger: PastelUtils.Animation.stagger,
    addRipple: PastelUtils.Animation.ripple,
    
    // Device detection
    isMobile: PastelUtils.Device.isMobile,
    isTablet: PastelUtils.Device.isTablet,
    isDesktop: PastelUtils.Device.isDesktop,
    
    // Configuration
    config: {
        animations: {
            enabled: true,
            duration: {
                fast: 200,
                normal: 600,
                slow: 1000
            }
        },
        notifications: {
            position: 'top-right',
            duration: 5000
        },
        validation: {
            showErrorsInstantly: true,
            validateOnBlur: true
        }
    }
};

// Global error handler
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    
    if (e.error.name !== 'ChunkLoadError') {
        if (window.PastelReportsSystem && window.PastelReportsSystem.toast) {
            window.PastelReportsSystem.toast('เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง', 'error');
        }
    }
});

// Global unhandled promise rejection handler
window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled promise rejection:', e.reason);
    if (window.PastelReportsSystem && window.PastelReportsSystem.toast) {
        window.PastelReportsSystem.toast('เกิดข้อผิดพลาดในการประมวลผล', 'warning');
    }
});

// Console welcome message
console.log(`
🎨 Pastel Reports System
========================
✨ สีขาวเป็นหลัก + สีพาสเทล
🎭 พร้อม Advanced Animations  
📱 Enhanced Responsive Design
🚀 Optimized Performance
========================
พัฒนาโดย: Reports System Team
`);

// Performance monitoring
if ('performance' in window) {
    window.addEventListener('load', function() {
        setTimeout(() => {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log(`⚡ Page load time: ${loadTime}ms`);
            
            if (loadTime > 3000) {
                console.warn('⚠️ Slow page load detected. Consider optimizing assets.');
            }
        }, 0);
    });
}

// Initialize page tracking
(function() {
    const startTime = Date.now();
    
    document.addEventListener('DOMContentLoaded', function() {
        const domTime = Date.now() - startTime;
        console.log(`📄 DOM ready in: ${domTime}ms`);
    });
    
    window.addEventListener('load', function() {
        const totalTime = Date.now() - startTime;
        console.log(`🎯 Total load time: ${totalTime}ms`);
    });
})();

// Console welcome message
console.log(`
🎨 Pastel Reports System
========================
✨ สีขาวเป็นหลัก + สีพาสเทล
🎭 พร้อม Advanced Animations  
📱 Enhanced Responsive Design
🚀 Optimized Performance
========================
พัฒนาโดย: Reports System Team
`);

// Initialize performance monitoring
if ('performance' in window) {
    window.addEventListener('load', function() {
        setTimeout(() => {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log(`⚡ Page load time: ${loadTime}ms`);
            
            if (loadTime > 3000) {
                console.warn('⚠️ Slow page load detected. Consider optimizing assets.');
            }
        }, 0);
    });
}