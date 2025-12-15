/* 
 * slider.js
 * ---------
 * ไฟล์สำหรับจัดการ Slider แสดงบริการต่างๆ ในหน้า Login
 */

// ตัวแปรสำหรับ Slider
let slideIndex = 1;
let sliding = false;
let startX;
let startScrollLeft;
let slideContainer;
let slideTrack;
let isDragging = false;

// ฟังก์ชันเลื่อนสไลด์
function plusSlides(n) {
    if (sliding) return;
    sliding = true;
    
    const slides = document.querySelectorAll('.card');
    const slideWidth = slides[0].offsetWidth + 20; // รวมระยะห่าง margin
    const visibleSlides = Math.floor(slideContainer.offsetWidth / slideWidth);
    
    // จำกัดไม่ให้เลื่อนเกินขอบเขต
    const totalSlides = slides.length;
    const maxScrollLeft = slideTrack.scrollWidth - slideContainer.offsetWidth;
    
    let newPos;
    
    if (n > 0) { // เลื่อนไปทางขวา
        newPos = Math.min(slideTrack.scrollLeft + (slideWidth * visibleSlides), maxScrollLeft);
    } else { // เลื่อนไปทางซ้าย
        newPos = Math.max(slideTrack.scrollLeft - (slideWidth * visibleSlides), 0);
    }
    
    // เลื่อนด้วย animation
    smoothScrollTo(slideTrack, newPos, 500, function() {
        sliding = false;
    });
}

// ฟังก์ชัน Smooth Scroll
function smoothScrollTo(element, to, duration, callback) {
    const start = element.scrollLeft;
    const change = to - start;
    const startTime = performance.now();
    
    function animateScroll(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function
        const ease = easeInOutQuad(progress);
        element.scrollLeft = start + change * ease;
        
        if (elapsed < duration) {
            requestAnimationFrame(animateScroll);
        } else {
            if (callback) callback();
        }
    }
    
    requestAnimationFrame(animateScroll);
}

// Easing function
function easeInOutQuad(t) {
    return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
}

// เริ่มต้นการลาก (Mouse/Touch)
function startDrag(e) {
    isDragging = true;
    startX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
    startScrollLeft = slideTrack.scrollLeft;
    
    slideTrack.style.cursor = 'grabbing';
    slideTrack.style.userSelect = 'none';
    
    document.addEventListener('mousemove', drag);
    document.addEventListener('touchmove', drag, { passive: false });
    document.addEventListener('mouseup', endDrag);
    document.addEventListener('touchend', endDrag);
}

// ดำเนินการลาก
function drag(e) {
    if (!isDragging) return;
    
    // ป้องกันการเลื่อนหน้า
    e.preventDefault();
    
    const x = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
    const diff = x - startX;
    
    slideTrack.scrollLeft = startScrollLeft - diff;
}

// จบการลาก
function endDrag() {
    isDragging = false;
    slideTrack.style.cursor = 'grab';
    slideTrack.style.userSelect = '';
    
    document.removeEventListener('mousemove', drag);
    document.removeEventListener('touchmove', drag);
    document.removeEventListener('mouseup', endDrag);
    document.removeEventListener('touchend', endDrag);
}

// เริ่มต้น Slider เมื่อ DOM โหลดเสร็จ
document.addEventListener('DOMContentLoaded', function() {
    slideContainer = document.getElementById('slideshow-container');
    slideTrack = document.getElementById('slide-track');
    
    if (!slideContainer || !slideTrack) return;
    
    // เพิ่ม event listener สำหรับการลาก
    slideTrack.addEventListener('mousedown', startDrag);
    slideTrack.addEventListener('touchstart', startDrag, { passive: true });
    
    // ป้องกันการเลื่อนหน้าเมื่อลากในขณะใช้ touch
    slideTrack.addEventListener('touchmove', function(e) {
        if (isDragging) e.preventDefault();
    }, { passive: false });
    
    // คำนวณความกว้างทั้งหมดของ slide-track
    const updateSlideTrackWidth = function() {
        const slides = document.querySelectorAll('.card');
        const totalWidth = Array.from(slides).reduce(
            (total, slide) => total + slide.offsetWidth + 20, // รวมระยะห่าง margin
            0
        );
        
        // อัพเดตความกว้างของ slide-track
        slideTrack.style.width = totalWidth + 'px';
    };
    
    // รันตอนเริ่มต้นและเมื่อหน้าต่างเปลี่ยนขนาด
    updateSlideTrackWidth();
    window.addEventListener('resize', updateSlideTrackWidth);
});