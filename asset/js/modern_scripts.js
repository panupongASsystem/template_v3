/**
 * Core JavaScript Functions
 * For improving UI interactions in the intranet system
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize menu popup triggers
    initPopups();
    
    // Initialize back to top button
    initBackToTop();
    
    // Add smooth scrolling
    initSmoothScroll();
    
    // File validation
    initFileValidation();
    
    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Initialize dropdown animations
    initDropdownAnimations();
});

/**
 * Initialize popup functionality
 */
function initPopups() {
    // Open popups
    const buttons = document.querySelectorAll('[data-target]');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('data-target');
            document.querySelector(target).style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        });
    });
    
    // Close popups
    const closeButtons = document.querySelectorAll('.close-button');
    closeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('data-target');
            document.querySelector(target).style.display = 'none';
            document.body.style.overflow = ''; // Enable scrolling
        });
    });
    
    // Close on overlay click
    const popups = document.querySelectorAll('.popup');
    popups.forEach(popup => {
        popup.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                document.body.style.overflow = ''; // Enable scrolling
            }
        });
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const visiblePopups = document.querySelectorAll('.popup[style*="display: block"]');
            visiblePopups.forEach(popup => {
                popup.style.display = 'none';
            });
            document.body.style.overflow = ''; // Enable scrolling
        }
    });
}

/**
 * Initialize back to top button functionality
 */
function initBackToTop() {
    const scrollToTopBtn = document.getElementById('scroll-to-top');
    if (!scrollToTopBtn) return;
    
    // Show button after scrolling 300px
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.classList.add('visible');
        } else {
            scrollToTopBtn.classList.remove('visible');
        }
    });
    
    // Scroll to top when clicked
    scrollToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/**
 * Initialize smooth scrolling for all internal links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * File upload validation
 */
function initFileValidation() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        if (input.hasAttribute('accept')) {
            input.addEventListener('change', function() {
                const fileName = this.value;
                if (fileName) {
                    const extension = fileName.substring(fileName.lastIndexOf('.')).toLowerCase();
                    const acceptedFormats = this.getAttribute('accept').split(',').map(format => format.trim().toLowerCase());
                    
                    if (!acceptedFormats.some(format => extension.match(format.replace('*', '')))) {
                        alert('ประเภทไฟล์ไม่ถูกต้อง กรุณาอัพโหลดไฟล์ตามที่ระบุเท่านั้น');
                        this.value = '';
                    }
                }
            });
        }
    });
}

/**
 * Initialize animations for dropdown menus
 */
function initDropdownAnimations() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        if (!menu) return;
        
        // Add animation classes
        menu.classList.add('animate__animated', 'animate__faster');
        
        dropdown.addEventListener('show.bs.dropdown', function () {
            menu.classList.remove('animate__fadeOut');
            menu.classList.add('animate__fadeIn');
        });
        
        dropdown.addEventListener('hide.bs.dropdown', function () {
            menu.classList.remove('animate__fadeIn');
            menu.classList.add('animate__fadeOut');
        });
    });
}

/**
 * Function to validate file uploads before form submission
 * @returns {boolean} Whether the form should be submitted
 */
function validateFile() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    let isValid = true;
    
    fileInputs.forEach(input => {
        if (input.required && !input.files.length) {
            alert('กรุณาเลือกไฟล์ที่ต้องการอัพโหลด');
            isValid = false;
        }
        
        if (input.files.length && input.hasAttribute('accept')) {
            const fileName = input.value;
            const extension = fileName.substring(fileName.lastIndexOf('.')).toLowerCase();
            const acceptedFormats = input.getAttribute('accept').split(',').map(format => format.trim().toLowerCase());
            
            if (!acceptedFormats.some(format => extension.match(format.replace('*', '')))) {
                alert('ประเภทไฟล์ไม่ถูกต้อง กรุณาอัพโหลดไฟล์ตามที่ระบุเท่านั้น');
                isValid = false;
            }
        }
    });
    
    return isValid;
}

/**
 * Function to handle file download tracking
 * @param {Event} event - The click event
 * @param {number} fileId - The ID of the file being downloaded
 */
function downloadFile(event, fileId) {
    // Send AJAX request to track the download
    const xhr = new XMLHttpRequest();
    xhr.open('GET', base_url + 'increment_download/' + fileId, true);
    xhr.send();
}

/**
 * Helper function to scroll to top of an element
 * @param {string} elementId - The ID of the element to scroll to
 */
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

/**
 * Confirm deletion with SweetAlert if available
 * @param {number} id - The ID of the item to delete
 * @param {string} url - The URL to redirect to after confirmation
 * @param {string} itemType - The type of item being deleted (optional)
 */
function confirmDelete(id, url, itemType = 'รายการ') {
    if (typeof Swal !== 'undefined') {
        // Use SweetAlert if available
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: `คุณต้องการลบ${itemType}นี้ใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url + id;
            }
        });
    } else {
        // Use standard confirmation if SweetAlert is not available
        if (confirm(`คุณต้องการลบ${itemType}นี้ใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้`)) {
            window.location.href = url + id;
        }
    }
}

/**
 * Toggle sidebar on mobile devices
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.main-sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

/**
 * Function to set the base URL for the JavaScript functions
 * This should be called from the PHP code with the correct base URL
 * @param {string} url - The base URL of the application
 */
function setBaseUrl(url) {
    window.base_url = url;
}