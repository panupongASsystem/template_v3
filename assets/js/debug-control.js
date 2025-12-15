/**
 * ไฟล์: asset/js/debug-control.js (แก้ไข path ให้ตรงกับโค้ด)
 * 
 * 🎯 Debug Control ที่ปรับปรุงแล้ว - แก้ปัญหาไม่สามารถปิด console ได้
 */

(function() {
    'use strict';
    
    // ตรวจสอบว่าโหลดซ้ำหรือไม่
    if (window.DebugControl) {
        console.warn('Debug Control already loaded - skipping');
        return;
    }
    
    console.log('🔧 Loading Debug Control...');
    
    // Debug Control Object
    window.DebugControl = {
        version: '2.0.0',
        initialized: false,
        
        // เก็บ console เดิม (ต้องเก็บก่อนที่จะมีการแทนที่)
        originalConsole: {
            log: window.console.log.bind(window.console),
            warn: window.console.warn.bind(window.console),
            error: window.console.error.bind(window.console),
            info: window.console.info.bind(window.console),
            debug: window.console.debug.bind(window.console),
            table: window.console.table.bind(window.console),
            group: window.console.group.bind(window.console),
            groupEnd: window.console.groupEnd.bind(window.console),
            trace: window.console.trace.bind(window.console),
            time: window.console.time.bind(window.console),
            timeEnd: window.console.timeEnd.bind(window.console)
        },
        
        // ตรวจสอบ debug mode
        getDebugMode: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const debugParam = urlParams.get('debug_dump');
            return debugParam === 'true' || debugParam === '1';
        },
        
        // สร้างฟังก์ชันว่างที่ไม่ทำอะไร
        createNoOpFunction: function() {
            return function() { /* ไม่ทำอะไรเลย */ };
        },
        
        // ตั้งค่า debug mode
        setupDebugMode: function() {
            if (this.initialized) {
                this.originalConsole.warn('Debug Control already initialized');
                return;
            }
            
            const isDebugMode = this.getDebugMode();
            
            if (isDebugMode) {
                // เปิด debug - คืนค่า console เดิม
                window.console.log = this.originalConsole.log;
                window.console.warn = this.originalConsole.warn;
                window.console.error = this.originalConsole.error;
                window.console.info = this.originalConsole.info;
                window.console.debug = this.originalConsole.debug;
                window.console.table = this.originalConsole.table;
                window.console.group = this.originalConsole.group;
                window.console.groupEnd = this.originalConsole.groupEnd;
                window.console.trace = this.originalConsole.trace;
                window.console.time = this.originalConsole.time;
                window.console.timeEnd = this.originalConsole.timeEnd;
                
                this.originalConsole.log('🟢 Debug Control v' + this.version + ' - Mode: ON');
                this.originalConsole.log('🌐 Current URL:', window.location.href);
                this.originalConsole.group('🔧 Available Debug Commands:');
                this.originalConsole.log('• debugToggle()  - สลับ debug mode');
                this.originalConsole.log('• debugEnable()  - เปิด debug mode');
                this.originalConsole.log('• debugDisable() - ปิด debug mode');
                this.originalConsole.log('• debugStatus()  - แสดงสถานะ');
                this.originalConsole.log('• debugRemove()  - ลบ debug parameter');
                this.originalConsole.groupEnd();
            } else {
                // ปิด debug - แทนที่ด้วยฟังก์ชันว่าง
                const noOp = this.createNoOpFunction();
                
                window.console.log = noOp;
                window.console.warn = noOp;
                window.console.info = noOp;
                window.console.debug = noOp;
                window.console.table = noOp;
                window.console.group = noOp;
                window.console.groupEnd = noOp;
                window.console.trace = noOp;
                window.console.time = noOp;
                window.console.timeEnd = noOp;
                
                // แสดงข้อความปิดเพียงครั้งเดียว
                this.originalConsole.log('🔴 Debug Control v' + this.version + ' - Mode: OFF');
                //this.originalConsole.log('💡 To enable debug: Add ?debug_dump=true to URL');
            }
            
            this.initialized = true;
        },
        
        // สลับ debug mode
        toggle: function() {
            const currentUrl = new URL(window.location);
            const currentDebug = currentUrl.searchParams.get('debug_dump');
            
            if (currentDebug === 'true' || currentDebug === '1') {
                currentUrl.searchParams.set('debug_dump', 'false');
            } else {
                currentUrl.searchParams.set('debug_dump', 'true');
            }
            
            window.location.href = currentUrl.toString();
        },
        
        // เปิด debug mode
        enable: function() {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('debug_dump', 'true');
            window.location.href = currentUrl.toString();
        },
        
        // ปิด debug mode
        disable: function() {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('debug_dump', 'false');
            window.location.href = currentUrl.toString();
        },
        
        // ลบ debug parameter
        remove: function() {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.delete('debug_dump');
            window.location.href = currentUrl.toString();
        },
        
        // แสดงสถานะ
        status: function() {
            const isDebugMode = this.getDebugMode();
            this.originalConsole.group('📊 Debug Control Status');
            this.originalConsole.log('Version:', this.version);
            this.originalConsole.log('Debug Mode:', isDebugMode ? '🟢 ON' : '🔴 OFF');
            this.originalConsole.log('Initialized:', this.initialized ? '✅ Yes' : '❌ No');
            this.originalConsole.log('Current URL:', window.location.href);
            this.originalConsole.groupEnd();
            return isDebugMode;
        },
        
        // บังคับปิด console (สำหรับกรณีฉุกเฉิน)
        forceDisable: function() {
            const noOp = this.createNoOpFunction();
            
            window.console.log = noOp;
            window.console.warn = noOp;
            window.console.info = noOp;
            window.console.debug = noOp;
            window.console.table = noOp;
            window.console.group = noOp;
            window.console.groupEnd = noOp;
            window.console.trace = noOp;
            window.console.time = noOp;
            window.console.timeEnd = noOp;
            
            this.originalConsole.log('🚫 Console forcefully disabled');
        },
        
        // คืนค่า console เดิม
        restore: function() {
            window.console.log = this.originalConsole.log;
            window.console.warn = this.originalConsole.warn;
            window.console.error = this.originalConsole.error;
            window.console.info = this.originalConsole.info;
            window.console.debug = this.originalConsole.debug;
            window.console.table = this.originalConsole.table;
            window.console.group = this.originalConsole.group;
            window.console.groupEnd = this.originalConsole.groupEnd;
            window.console.trace = this.originalConsole.trace;
            window.console.time = this.originalConsole.time;
            window.console.timeEnd = this.originalConsole.timeEnd;
            
            this.originalConsole.log('🔄 Console restored to original state');
        }
    };
    
    // สร้าง shortcuts สำหรับใช้ใน console
    window.debugToggle = window.DebugControl.toggle.bind(window.DebugControl);
    window.debugEnable = window.DebugControl.enable.bind(window.DebugControl);
    window.debugDisable = window.DebugControl.disable.bind(window.DebugControl);
    window.debugStatus = window.DebugControl.status.bind(window.DebugControl);
    window.debugRemove = window.DebugControl.remove.bind(window.DebugControl);
    window.debugForceOff = window.DebugControl.forceDisable.bind(window.DebugControl);
    window.debugRestore = window.DebugControl.restore.bind(window.DebugControl);
    
    // เรียกใช้ทันทีเมื่อโหลด
    window.DebugControl.setupDebugMode();
    
    console.log('✅ Debug Control v' + window.DebugControl.version + ' loaded successfully');
    
})();

// เพิ่ม event listener สำหรับการโหลดหน้าเสร็จสิ้น
document.addEventListener('DOMContentLoaded', function() {
    if (window.DebugControl && !window.DebugControl.getDebugMode()) {
        // ตรวจสอบและปิด console logs ที่อาจเหลืออยู่
        setTimeout(function() {
            window.DebugControl.forceDisable();
        }, 100);
    }
});

// เพิ่มการตรวจสอบเมื่อหน้าโหลดเสร็จสมบูรณ์
window.addEventListener('load', function() {
    if (window.DebugControl && !window.DebugControl.getDebugMode()) {
        // ปิด console อีกครั้งเพื่อให้แน่ใจ
        setTimeout(function() {
            window.DebugControl.forceDisable();
        }, 500);
    }
});