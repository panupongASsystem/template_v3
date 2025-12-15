<?php
/**
 * ✅ Complete Multi-User Safe Google Drive Auto Refresh
 * รวมทุก Feature: Smart Loading + Multi-User Protection + Auto Reconnect
 */
?>
<script>
/**
 * 🛡️ Complete Multi-User Safe Google Drive Auto-Refresh v5.0
 * ✅ Smart Loading - โหลดเฉพาะเมื่อจำเป็น
 * ✅ Multi-User Protection - ป้องกัน Race Condition
 * ✅ Auto Reconnect - เชื่อมต่อใหม่อัตโนมัติ
 * ✅ Resource Optimization - ประหยัด Memory/CPU
 * ✅ Connection Aware - ปรับตามสภาพเน็ต/แบต
 */

// ================================
// 1. SMART LOADER CLASS
// ================================
class SmartAutoRefreshLoader {
    constructor() {
        try {
            this.isEnabled = false;
            this.autoRefreshInstance = null;
            this.checkInterval = null;
            this.currentPath = window.location.pathname;
            this.userId = this.getCurrentUserId();
            
            // ✅ ตั้งค่า Page Priorities ก่อน
            this.pagePriorities = {
                '/google_drive': 100,
                '/member': 80,
                '/System_member': 80,
                '/User/choice': 70,
                '/dashboard': 60,
                '/': 50
            };
            
            // ✅ คำนวณ priority หลังจากตั้งค่าแล้ว
            this.priority = this.calculatePagePriority();
            
            // ✅ Loading Conditions
            this.loadingConditions = {
                enableOnlyMainTab: true,
                enableOnlyActiveWindow: true,
                maxInstancesPerUser: 1,
                minTimeBetweenChecks: 300000, // 5 นาที
                disableOnLowBattery: true,
                disableOnSlowConnection: true
            };
            
            this.init();
            
        } catch (error) {
            console.error('❌ SmartAutoRefreshLoader constructor error:', error);
            
            // ✅ Fallback เมื่อเกิด error
            this.isEnabled = false;
            this.priority = 0;
            this.userId = 'error_user_' + Date.now();
            this.currentPath = window.location.pathname || '/';
            this.pagePriorities = {};
            this.loadingConditions = {};
        }
    }
    
    getCurrentUserId() {
        const userElement = document.querySelector('[data-user-id]');
        if (userElement) return userElement.getAttribute('data-user-id');
        
        if (typeof window.currentUserId !== 'undefined') return window.currentUserId;
        
        // ✅ ดึงจาก PHP session ถ้ามี
        const metaUser = document.querySelector('meta[name="user-id"]');
        if (metaUser) return metaUser.getAttribute('content');
        
        return 'anonymous_' + Math.random().toString(36).substr(2, 9);
    }
    
    calculatePagePriority() {
        // ✅ ป้องกัน Error เมื่อ pagePriorities เป็น undefined/null
        if (!this.pagePriorities || typeof this.pagePriorities !== 'object') {
            console.warn('⚠️ pagePriorities not properly initialized, using default priorities');
            
            // ใช้ค่า default
            const defaultPriorities = {
                '/google_drive': 100,
                '/member': 80,
                '/System_member': 80,
                '/User/choice': 70,
                '/dashboard': 60,
                '/': 50
            };
            
            for (const [path, priority] of Object.entries(defaultPriorities)) {
                if (this.currentPath.includes(path)) return priority;
            }
            return 0;
        }
        
        // ✅ ใช้ค่า pagePriorities ปกติ
        for (const [path, priority] of Object.entries(this.pagePriorities)) {
            if (this.currentPath.includes(path)) return priority;
        }
        return 0;
    }
    
    async init() {
        try {
            console.log(`🛡️ Complete Multi-User Safe Auto-Refresh v5.0`);
            console.log(`📄 Path: ${this.currentPath}`);
            console.log(`👤 User: ${this.userId}`);
            console.log(`⭐ Priority: ${this.priority}`);
            
            // ✅ Validation
            if (!this.currentPath) {
                console.warn('⚠️ currentPath is empty, using fallback');
                this.currentPath = window.location.pathname || '/';
            }
            
            if (!this.userId) {
                console.warn('⚠️ userId is empty, generating fallback');
                this.userId = 'fallback_user_' + Date.now();
            }
            
            if (typeof this.priority !== 'number') {
                console.warn('⚠️ priority is not a number, recalculating');
                this.priority = this.calculatePagePriority();
            }
            
            const shouldEnable = await this.shouldEnableAutoRefresh();
            
            if (shouldEnable) {
                await this.enableAutoRefresh();
            } else {
                console.log('🚫 Auto-refresh conditions not met');
                this.setupConditionalActivation();
            }
            
        } catch (error) {
            console.error('❌ SmartAutoRefreshLoader init error:', error);
            
            // ✅ Fallback behavior
            console.log('🔄 Attempting fallback initialization...');
            this.setupConditionalActivation();
        }
    }
    
    async shouldEnableAutoRefresh() {
        // 1. Page Priority Check
        if (this.priority === 0) {
            console.log('❌ Page not in allowed list');
            return false;
        }
        
        // 2. Active Instance Count Check
        if (this.loadingConditions.maxInstancesPerUser > 0) {
            const activeInstances = await this.countActiveInstances();
            if (activeInstances >= this.loadingConditions.maxInstancesPerUser) {
                console.log(`❌ Too many instances: ${activeInstances}/${this.loadingConditions.maxInstancesPerUser}`);
                return false;
            }
        }
        
        // 3. Main Tab Check
        if (this.loadingConditions.enableOnlyMainTab) {
            const isMainTab = await this.checkIfMainTab();
            if (!isMainTab) {
                console.log('❌ Not main tab');
                return false;
            }
        }
        
        // 4. Active Window Check
        if (this.loadingConditions.enableOnlyActiveWindow && document.hidden) {
            console.log('❌ Window not active');
            return false;
        }
        
        // 5. Connection Speed Check
        if (this.loadingConditions.disableOnSlowConnection) {
            const connectionSpeed = await this.checkConnectionSpeed();
            if (connectionSpeed === 'slow') {
                console.log('❌ Slow connection detected');
                return false;
            }
        }
        
        // 6. Battery Check
        if (this.loadingConditions.disableOnLowBattery) {
            const batteryStatus = await this.checkBatteryStatus();
            if (batteryStatus === 'low') {
                console.log('❌ Low battery detected');
                return false;
            }
        }
        
        // 7. Time Between Checks
        const lastRefresh = localStorage.getItem(`gdrive_last_refresh_${this.userId}`);
        if (lastRefresh) {
            const timeSinceLastRefresh = Date.now() - parseInt(lastRefresh);
            if (timeSinceLastRefresh < this.loadingConditions.minTimeBetweenChecks) {
                const remainingTime = Math.round((this.loadingConditions.minTimeBetweenChecks - timeSinceLastRefresh) / 1000);
                console.log(`❌ Too soon since last refresh: ${remainingTime}s remaining`);
                return false;
            }
        }
        
        console.log('✅ All conditions met for auto-refresh');
        return true;
    }
    
    async countActiveInstances() {
        try {
            const keys = Object.keys(localStorage);
            let activeCount = 0;
            const now = Date.now();
            const timeout = 60000; // 1 นาที
            
            keys.forEach(key => {
                if (key.startsWith(`gdrive_main_tab_${this.userId}`)) {
                    try {
                        const data = JSON.parse(localStorage.getItem(key));
                        if (data && data.lastPing && (now - data.lastPing) < timeout) {
                            activeCount++;
                        }
                    } catch (e) {}
                }
            });
            
            return activeCount;
        } catch (error) {
            console.error('Error counting active instances:', error);
            return 0;
        }
    }
    
    async checkIfMainTab() {
        const mainTabData = localStorage.getItem(`gdrive_main_tab_${this.userId}`);
        if (!mainTabData) return true;
        
        try {
            const data = JSON.parse(mainTabData);
            const timeSinceLastPing = Date.now() - data.lastPing;
            return timeSinceLastPing > 30000; // 30 วินาที
        } catch (e) {
            return true;
        }
    }
    
    async checkConnectionSpeed() {
        try {
            // Navigator Connection API
            if ('connection' in navigator) {
                const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
                if (connection) {
                    if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                        return 'slow';
                    }
                    if (connection.downlink < 1) return 'slow';
                }
            }
            
            // Fallback: Ping Test
            const startTime = performance.now();
            try {
                await fetch(window.location.origin + '/favicon.ico', {
                    method: 'HEAD',
                    cache: 'no-cache',
                    mode: 'no-cors'
                });
                const endTime = performance.now();
                return (endTime - startTime) > 2000 ? 'slow' : 'fast';
            } catch (e) {
                return 'slow';
            }
        } catch (error) {
            return 'unknown';
        }
    }
    
    async checkBatteryStatus() {
        try {
            if ('getBattery' in navigator) {
                const battery = await navigator.getBattery();
                if (battery.level < 0.2 && !battery.charging) return 'low';
                if (battery.level < 0.1) return 'low';
            }
            return 'normal';
        } catch (error) {
            return 'unknown';
        }
    }
    
    async enableAutoRefresh() {
        if (this.isEnabled) {
            console.log('⚠️ Auto-refresh already enabled');
            return;
        }
        
        console.log('🚀 Enabling Multi-User Safe Auto-Refresh...');
        
        try {
            // รอให้ Class โหลด
            let retries = 0;
            while (typeof MultiUserSafeGoogleDriveAutoRefresh === 'undefined' && retries < 50) {
                await new Promise(resolve => setTimeout(resolve, 100));
                retries++;
            }
            
            if (typeof MultiUserSafeGoogleDriveAutoRefresh === 'undefined') {
                throw new Error('Failed to load MultiUserSafeGoogleDriveAutoRefresh class');
            }
            
            this.autoRefreshInstance = new MultiUserSafeGoogleDriveAutoRefresh();
            this.isEnabled = true;
            this.registerInstance();
            
            console.log('✅ Multi-User Safe Auto-Refresh enabled successfully');
        } catch (error) {
            console.error('❌ Failed to enable Auto-Refresh:', error);
            this.isEnabled = false;
        }
    }
    
    setupConditionalActivation() {
        console.log('⏳ Setting up conditional activation...');
        
        // Window Visibility Changes
        document.addEventListener('visibilitychange', async () => {
            if (!document.hidden && !this.isEnabled) {
                console.log('👁️ Window became active, re-checking conditions...');
                const shouldEnable = await this.shouldEnableAutoRefresh();
                if (shouldEnable) await this.enableAutoRefresh();
            } else if (document.hidden && this.isEnabled) {
                setTimeout(() => {
                    if (document.hidden && this.isEnabled) {
                        console.log('😴 Window inactive too long, disabling auto-refresh...');
                        this.disableAutoRefresh();
                    }
                }, 300000); // 5 นาที
            }
        });
        
        // Periodic Check
        this.checkInterval = setInterval(async () => {
            if (!this.isEnabled) {
                const shouldEnable = await this.shouldEnableAutoRefresh();
                if (shouldEnable) {
                    await this.enableAutoRefresh();
                    clearInterval(this.checkInterval);
                }
            }
        }, 30000);
        
        // Storage Changes
        window.addEventListener('storage', (event) => {
            if (event.key === `gdrive_main_tab_${this.userId}` && !this.isEnabled) {
                setTimeout(async () => {
                    const shouldEnable = await this.shouldEnableAutoRefresh();
                    if (shouldEnable) await this.enableAutoRefresh();
                }, 1000);
            }
        });
    }
    
    disableAutoRefresh() {
        if (!this.isEnabled) return;
        
        console.log('🛑 Disabling Auto-Refresh...');
        
        if (this.autoRefreshInstance && typeof this.autoRefreshInstance.destroy === 'function') {
            this.autoRefreshInstance.destroy();
        }
        
        this.autoRefreshInstance = null;
        this.isEnabled = false;
        this.unregisterInstance();
        
        console.log('✅ Auto-Refresh disabled');
    }
    
    registerInstance() {
        const registry = JSON.parse(localStorage.getItem('gdrive_instance_registry') || '{}');
        registry[this.userId] = {
            path: this.currentPath,
            priority: this.priority,
            timestamp: Date.now(),
            tabId: `tab_${Date.now()}`
        };
        localStorage.setItem('gdrive_instance_registry', JSON.stringify(registry));
    }
    
    unregisterInstance() {
        const registry = JSON.parse(localStorage.getItem('gdrive_instance_registry') || '{}');
        delete registry[this.userId];
        localStorage.setItem('gdrive_instance_registry', JSON.stringify(registry));
    }
    
    getStatus() {
        return {
            isEnabled: this.isEnabled,
            currentPath: this.currentPath,
            userId: this.userId,
            priority: this.priority,
            hasInstance: !!this.autoRefreshInstance,
            conditions: this.loadingConditions
        };
    }
    
    destroy() {
        if (this.checkInterval) clearInterval(this.checkInterval);
        this.disableAutoRefresh();
        console.log('🗑️ Smart Auto-Refresh Loader destroyed');
    }
}

// ================================
// 2. MULTI-USER SAFE AUTO-REFRESH CLASS
// ================================
class MultiUserSafeGoogleDriveAutoRefresh {
    constructor() {
        this.refreshInterval = null;
        this.checkInterval = 5 * 60 * 1000; // 5 นาที
        this.isRefreshing = false;
        this.lastCheck = 0;
        this.retryCount = 0;
        this.maxRetries = 3;
        this.tabId = this.generateTabId();
        this.userId = this.getCurrentUserId();
        this.sessionId = this.getSessionId();
        this.isMainTab = false;
        this.autoReconnectEnabled = true;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 1;
        
        // Multi-User Protection
        this.lockTimeout = 60000; // 1 นาที
        this.backoffDelay = 2000; // 2 วินาที
        this.maxBackoffDelay = 30000; // 30 วินาที
        
        // Storage Keys (User-specific)
        this.storageKeys = {
            lastRefresh: `gdrive_last_refresh_${this.userId}`,
            isRefreshing: `gdrive_is_refreshing_${this.userId}`,
            mainTab: `gdrive_main_tab_${this.userId}`,
            tokenStatus: `gdrive_token_status_${this.userId}`,
            globalLock: 'gdrive_global_refresh_lock',
            userLock: `gdrive_user_lock_${this.userId}`
        };
        
        this.init();
    }
    
    generateTabId() {
        return 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    getCurrentUserId() {
        const userElement = document.querySelector('[data-user-id]');
        if (userElement) return userElement.getAttribute('data-user-id');
        
        if (typeof window.currentUserId !== 'undefined') return window.currentUserId;
        
        const metaUser = document.querySelector('meta[name="user-id"]');
        if (metaUser) return metaUser.getAttribute('content');
        
        return 'anonymous_' + Math.random().toString(36).substr(2, 9);
    }
    
    getSessionId() {
        const sessionElement = document.querySelector('[data-session-id]');
        if (sessionElement) return sessionElement.getAttribute('data-session-id');
        
        if (typeof window.session_id !== 'undefined') return window.session_id;
        
        return 'session_' + Date.now();
    }
    
    init() {
        console.log(`🛡️ Multi-User Safe Auto-Refresh initialized`);
        console.log(`👤 User: ${this.userId}`);
        console.log(`🆔 Session: ${this.sessionId}`);
        console.log(`📱 Tab: ${this.tabId}`);
        
        this.checkMainTab();
        
        window.addEventListener('storage', this.handleStorageChange.bind(this));
        window.addEventListener('visibilitychange', this.handleVisibilityChange.bind(this));
        window.addEventListener('beforeunload', this.handleBeforeUnload.bind(this));
        
        if (this.isMainTab) {
            this.startMainTabOperations();
        } else {
            this.startSlaveTabOperations();
        }
    }
    
    checkMainTab() {
        const currentMainTab = localStorage.getItem(this.storageKeys.mainTab);
        const now = Date.now();
        
        if (!currentMainTab) {
            this.becomeMainTab();
        } else {
            try {
                const mainTabData = JSON.parse(currentMainTab);
                const timeSinceLastPing = now - mainTabData.lastPing;
                
                if (timeSinceLastPing > 30000) {
                    console.log(`🔄 Previous main tab inactive (${timeSinceLastPing}ms), becoming new main tab`);
                    this.becomeMainTab();
                } else {
                    this.isMainTab = false;
                    console.log(`👥 Operating as slave tab (Main: ${mainTabData.tabId})`);
                }
            } catch (e) {
                this.becomeMainTab();
            }
        }
    }
    
    becomeMainTab() {
        this.isMainTab = true;
        this.updateMainTabStatus();
        console.log(`👑 User ${this.userId} - This tab is now the main tab (${this.tabId})`);
    }
    
    updateMainTabStatus() {
        if (this.isMainTab) {
            localStorage.setItem(this.storageKeys.mainTab, JSON.stringify({
                userId: this.userId,
                sessionId: this.sessionId,
                tabId: this.tabId,
                lastPing: Date.now()
            }));
        }
    }
    
    startMainTabOperations() {
        console.log(`🚀 Starting main tab operations for User ${this.userId}`);
        
        // Random delay ป้องกัน multiple users เริ่มพร้อมกัน
        const randomDelay = Math.random() * 10000; // 0-10 วินาที
        setTimeout(() => {
            this.performSafeAutoRefresh();
        }, randomDelay);
        
        // User-specific interval
        const userOffset = parseInt(this.userId.replace(/\D/g, '')) * 1000 || 0;
        const adjustedInterval = this.checkInterval + userOffset;
        
        this.refreshInterval = setInterval(() => {
            this.performSafeAutoRefresh();
        }, adjustedInterval);
        
        this.mainTabPingInterval = setInterval(() => {
            this.updateMainTabStatus();
        }, 15000);
        
        console.log(`⏰ Main tab: User ${this.userId} - Periodic check started (every ${Math.round(adjustedInterval/60000)} minutes)`);
    }
    
    startSlaveTabOperations() {
        console.log(`👥 Starting slave tab operations for User ${this.userId}`);
        
        this.checkTokenStatusFromStorage();
        
        this.mainTabCheckInterval = setInterval(() => {
            this.checkMainTab();
            if (this.isMainTab) {
                this.promoteToMainTab();
            }
        }, 30000);
    }
    
    promoteToMainTab() {
        console.log(`📈 Promoting slave tab to main tab for User ${this.userId}`);
        
        if (this.mainTabCheckInterval) {
            clearInterval(this.mainTabCheckInterval);
        }
        
        this.startMainTabOperations();
    }
    
    async performSafeAutoRefresh() {
        if (!this.isMainTab) {
            console.log(`⚠️ User ${this.userId} - Not main tab, skipping refresh`);
            return;
        }
        
        // Global Lock Check
        const globalLockStatus = await this.checkGlobalLock();
        if (globalLockStatus.isLocked && globalLockStatus.lockedBy !== this.userId) {
            console.log(`🔒 User ${this.userId} - Another user (${globalLockStatus.lockedBy}) is refreshing, waiting...`);
            
            const waitTime = this.getBackoffDelay();
            setTimeout(() => {
                this.performSafeAutoRefresh();
            }, waitTime);
            return;
        }
        
        // User Lock
        const lockAcquired = await this.acquireUserLock();
        if (!lockAcquired) {
            console.log(`🔒 User ${this.userId} - Failed to acquire user lock, backing off...`);
            
            const waitTime = this.getBackoffDelay();
            setTimeout(() => {
                this.performSafeAutoRefresh();
            }, waitTime);
            return;
        }
        
        try {
            console.log(`🔍 User ${this.userId} - Starting safe token refresh check...`);
            
            await this.setGlobalLock();
            
            // API Call with User Context
            const response = await fetch('<?php echo site_url("google_drive_system/refresh_system_token"); ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Cache-Control': 'no-cache',
                    'X-Tab-ID': this.tabId,
                    'X-User-ID': this.userId,
                    'X-Session-ID': this.sessionId
                },
                body: JSON.stringify({
                    safe_mode: true,
                    user_id: this.userId,
                    tab_id: this.tabId
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            // Store Results
            const statusUpdate = {
                timestamp: Date.now(),
                success: data.success,
                message: data.message,
                refreshedBy: this.userId,
                tabId: this.tabId,
                sessionId: this.sessionId
            };
            
            localStorage.setItem(this.storageKeys.tokenStatus, JSON.stringify(statusUpdate));
            localStorage.setItem(this.storageKeys.lastRefresh, Date.now().toString());
            
            if (data.success) {
                console.log(`✅ User ${this.userId} - Token refresh successful!`, data.message);
                this.broadcastToAllTabs('token_refreshed', data.message);
                this.retryCount = 0;
            } else {
                console.warn(`⚠️ User ${this.userId} - Auto-refresh issue:`, data.message);
                this.broadcastToAllTabs('refresh_failed', data.message);
                
                if (data.error_type === 'no_refresh_token' || data.requires_reconnect) {
                    this.broadcastToAllTabs('reconnect_required', data.message);
                    
                    // Auto Reconnect
                    if (this.autoReconnectEnabled && this.reconnectAttempts < this.maxReconnectAttempts) {
                        await this.performAutoReconnect();
                    }
                } else {
                    this.handleRetry(data.message);
                }
            }
            
        } catch (error) {
            console.error(`❌ User ${this.userId} - Auto-refresh error:`, error);
            this.broadcastToAllTabs('refresh_error', error.message);
            this.handleRetry(error.message);
        } finally {
            await this.releaseUserLock();
            await this.releaseGlobalLock();
            this.isRefreshing = false;
        }
    }
    
    // Lock Management Methods
    async checkGlobalLock() {
        try {
            // Simple localStorage-based lock for demo
            // In production, use server-side locks
            const lockData = localStorage.getItem(this.storageKeys.globalLock);
            if (!lockData) return { isLocked: false };
            
            const lock = JSON.parse(lockData);
            const now = Date.now();
            
            if (now > lock.expiresAt) {
                localStorage.removeItem(this.storageKeys.globalLock);
                return { isLocked: false };
            }
            
            return {
                isLocked: true,
                lockedBy: lock.userId,
                lockedAt: lock.timestamp,
                expiresAt: lock.expiresAt
            };
        } catch (error) {
            return { isLocked: false };
        }
    }
    
    async acquireUserLock() {
        try {
            const lockKey = this.storageKeys.userLock;
            const existingLock = localStorage.getItem(lockKey);
            
            if (existingLock) {
                const lock = JSON.parse(existingLock);
                if (Date.now() < lock.expiresAt) {
                    return lock.tabId === this.tabId; // Already own the lock
                }
            }
            
            // Acquire lock
            const lockData = {
                userId: this.userId,
                tabId: this.tabId,
                sessionId: this.sessionId,
                timestamp: Date.now(),
                expiresAt: Date.now() + this.lockTimeout
            };
            
            localStorage.setItem(lockKey, JSON.stringify(lockData));
            return true;
        } catch (error) {
            return false;
        }
    }
    
    async releaseUserLock() {
        try {
            localStorage.removeItem(this.storageKeys.userLock);
        } catch (error) {
            console.error('Release user lock error:', error);
        }
    }
    
    async setGlobalLock() {
        try {
            const lockData = {
                userId: this.userId,
                tabId: this.tabId,
                sessionId: this.sessionId,
                timestamp: Date.now(),
                expiresAt: Date.now() + this.lockTimeout
            };
            
            localStorage.setItem(this.storageKeys.globalLock, JSON.stringify(lockData));
        } catch (error) {
            console.error('Set global lock error:', error);
        }
    }
    
    async releaseGlobalLock() {
        try {
            const lockData = localStorage.getItem(this.storageKeys.globalLock);
            if (lockData) {
                const lock = JSON.parse(lockData);
                if (lock.userId === this.userId && lock.tabId === this.tabId) {
                    localStorage.removeItem(this.storageKeys.globalLock);
                }
            }
        } catch (error) {
            console.error('Release global lock error:', error);
        }
    }
    
    getBackoffDelay() {
        const baseDelay = this.backoffDelay;
        const jitter = Math.random() * 1000;
        const exponentialDelay = Math.min(
            baseDelay * Math.pow(2, this.retryCount),
            this.maxBackoffDelay
        );
        
        return exponentialDelay + jitter;
    }
    
    async performAutoReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.log(`🚫 User ${this.userId} - Max reconnect attempts reached`);
            return;
        }
        
        this.reconnectAttempts++;
        console.log(`🔄 User ${this.userId} - Attempting auto reconnect (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
        
        try {
            localStorage.setItem(`gdrive_last_reconnect_${this.userId}`, Date.now().toString());
            
            const reconnectUrl = '<?php echo site_url("google_drive_system/connect_system_account?force_reconnect=1&auto=1"); ?>';
            const reconnectWindow = window.open(
                reconnectUrl,
                'google_drive_reconnect',
                'width=600,height=700,scrollbars=yes,resizable=yes'
            );
            
            const checkClosed = setInterval(() => {
                if (reconnectWindow.closed) {
                    clearInterval(checkClosed);
                    console.log(`✅ User ${this.userId} - Reconnect window closed, checking token status...`);
                    
                    setTimeout(() => {
                        this.performSafeAutoRefresh();
                    }, 5000);
                }
            }, 1000);
            
            setTimeout(() => {
                if (!reconnectWindow.closed) {
                    reconnectWindow.close();
                    clearInterval(checkClosed);
                    console.log(`⏰ User ${this.userId} - Reconnect timeout`);
                }
            }, 300000); // 5 นาที
            
        } catch (error) {
            console.error(`❌ User ${this.userId} - Auto reconnect error:`, error);
        }
    }
    
    broadcastToAllTabs(type, message) {
        const broadcast = {
            type: type,
            message: message,
            timestamp: Date.now(),
            fromUserId: this.userId,
            fromTabId: this.tabId,
            fromSessionId: this.sessionId
        };
        
        localStorage.setItem(`gdrive_broadcast_${this.userId}`, JSON.stringify(broadcast));
        
        setTimeout(() => {
            localStorage.removeItem(`gdrive_broadcast_${this.userId}`);
        }, 100);
    }
    
    handleStorageChange(event) {
        if (event.key === `gdrive_broadcast_${this.userId}` && event.newValue) {
            try {
                const broadcast = JSON.parse(event.newValue);
                
                if (broadcast.fromTabId === this.tabId) return;
                
                console.log(`📨 User ${this.userId} - Received broadcast: ${broadcast.type} from Tab ${broadcast.fromTabId}`);
                
                switch (broadcast.type) {
                    case 'token_refreshed':
                        this.showSuccessNotification(`🔄 Token refreshed by another tab`);
                        break;
                    case 'refresh_failed':
                        this.showWarningNotification(`⚠️ Refresh failed: ${broadcast.message}`);
                        break;
                    case 'reconnect_required':
                        this.showReconnectNotification();
                        break;
                    case 'refresh_error':
                        this.showErrorNotification(`❌ Refresh error: ${broadcast.message}`);
                        break;
                }
            } catch (e) {
                console.error('Error parsing broadcast:', e);
            }
        }
        
        if (event.key === this.storageKeys.mainTab) {
            setTimeout(() => {
                this.checkMainTab();
            }, 1000);
        }
    }
    
    handleVisibilityChange() {
        if (!document.hidden && this.isMainTab) {
            const lastRefresh = parseInt(localStorage.getItem(this.storageKeys.lastRefresh) || '0');
            const timeSinceLastRefresh = Date.now() - lastRefresh;
            
            if (timeSinceLastRefresh > 2 * 60 * 1000) {
                console.log(`👁️ User ${this.userId} - Main tab visible, checking token status`);
                this.performSafeAutoRefresh();
            }
        }
    }
    
    handleBeforeUnload() {
        if (this.isMainTab) {
            localStorage.removeItem(this.storageKeys.mainTab);
            console.log(`👋 User ${this.userId} - Main tab closing, removing main tab status`);
        }
    }
    
    handleRetry(errorMessage) {
        if (!this.isMainTab) return;
        
        this.retryCount++;
        
        if (this.retryCount <= this.maxRetries) {
            const waitTime = this.getBackoffDelay();
            console.log(`🔄 User ${this.userId} - Retry ${this.retryCount}/${this.maxRetries} in ${Math.round(waitTime/1000)} seconds...`);
            
            setTimeout(() => {
                this.performSafeAutoRefresh();
            }, waitTime);
        } else {
            console.error(`💥 User ${this.userId} - Max retries reached. Manual intervention required.`);
            this.broadcastToAllTabs('max_retries_reached', errorMessage);
            this.retryCount = 0;
        }
    }
    
    checkTokenStatusFromStorage() {
        const tokenStatus = localStorage.getItem(this.storageKeys.tokenStatus);
        if (tokenStatus) {
            try {
                const status = JSON.parse(tokenStatus);
                const timeSinceUpdate = Date.now() - status.timestamp;
                
                if (timeSinceUpdate < 60000) {
                    console.log(`ℹ️ User ${this.userId} - Token status from storage: ${status.message}`);
                }
            } catch (e) {
                console.error('Error parsing token status:', e);
            }
        }
    }
    
    // Notification Methods
    showSuccessNotification(message) {
        if (document.hidden) return;
        console.log(`📢 User ${this.userId} - Success:`, message);
        this.showToast(message, 'success', 3000);
    }
    
    showWarningNotification(message) {
        if (document.hidden) return;
        console.warn(`📢 User ${this.userId} - Warning:`, message);
        this.showToast(message, 'warning', 5000);
    }
    
    showErrorNotification(message) {
        if (document.hidden) return;
        console.error(`📢 User ${this.userId} - Error:`, message);
        this.showToast(message, 'error', 10000);
    }
    
    showReconnectNotification() {
        if (document.hidden) return;
        
        const message = `⚠️ User ${this.userId} - Google Drive requires reconnection`;
        console.warn(`🔗 User ${this.userId} - Reconnection required`);
        
        this.showToast(message + ' - Click to reconnect', 'warning', 0, () => {
            window.open('<?php echo site_url("google_drive_system/connect_system_account?force_reconnect=1"); ?>', '_blank');
        });
    }
    
    showToast(message, type = 'info', duration = 5000, clickHandler = null) {
        const existingToast = document.getElementById(`google-drive-toast-${this.userId}`);
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.id = `google-drive-toast-${this.userId}`;
        toast.style.cssText = `
            position: fixed;
            top: ${20 + (parseInt(this.userId.replace(/\D/g, '')) || 0) * 80}px;
            right: 20px;
            padding: 12px 16px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            max-width: 300px;
            cursor: ${clickHandler ? 'pointer' : 'default'};
            transition: all 0.3s ease;
            background-color: ${
                type === 'success' ? '#10B981' : 
                type === 'warning' ? '#F59E0B' : 
                type === 'error' ? '#EF4444' : '#3B82F6'
            };
        `;
        toast.textContent = message;
        
        if (clickHandler) {
            toast.onclick = clickHandler;
        }
        
        document.body.appendChild(toast);
        
        if (duration > 0) {
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                }
            }, duration);
        }
    }
    
    // Debug Methods
    getStatus() {
        return {
            userId: this.userId,
            sessionId: this.sessionId,
            tabId: this.tabId,
            isMainTab: this.isMainTab,
            isRefreshing: this.isRefreshing,
            lastCheck: new Date(this.lastCheck).toLocaleString(),
            retryCount: this.retryCount,
            reconnectAttempts: this.reconnectAttempts,
            nextCheck: this.isMainTab ? new Date(this.lastCheck + this.checkInterval).toLocaleString() : 'N/A (Slave tab)'
        };
    }
    
    async triggerManualRefresh() {
        if (!this.isMainTab) {
            console.log(`🚫 User ${this.userId} - Only main tab can trigger manual refresh`);
            return false;
        }
        
        console.log(`👆 User ${this.userId} - Manual refresh triggered`);
        await this.performSafeAutoRefresh();
        return true;
    }
    
    destroy() {
        if (this.refreshInterval) clearInterval(this.refreshInterval);
        if (this.mainTabPingInterval) clearInterval(this.mainTabPingInterval);
        if (this.mainTabCheckInterval) clearInterval(this.mainTabCheckInterval);
        
        if (this.isMainTab) {
            localStorage.removeItem(this.storageKeys.mainTab);
        }
        
        const existingToast = document.getElementById(`google-drive-toast-${this.userId}`);
        if (existingToast) {
            existingToast.remove();
        }
        
        console.log(`🛑 User ${this.userId} - Multi-User Safe Auto-Refresh stopped (Tab: ${this.tabId})`);
    }
}

// ================================
// 3. GLOBAL ERROR HANDLER
// ================================
window.addEventListener('error', function(event) {
    if (event.error && event.error.message && 
        (event.error.message.includes('SmartAutoRefreshLoader') || 
         event.error.message.includes('MultiUserSafeGoogleDriveAutoRefresh') ||
         event.error.message.includes('pagePriorities'))) {
        
        console.error('💥 JavaScript Error:', event.error.message);
        console.error('Serious JavaScript error detected:', event.error.message);
        
        // ป้องกันไม่ให้ error ทำให้ระบบหยุดทำงาน
        event.preventDefault();
        return false;
    }
});

// ================================
// 4. INITIALIZATION
// ================================
document.addEventListener('DOMContentLoaded', function() {
    // ✅ Add User ID to DOM for detection
    if (!document.querySelector('[data-user-id]')) {
        const userIdMeta = document.createElement('meta');
        userIdMeta.setAttribute('name', 'user-id');
        
        // Try to get User ID from various sources
        let userId = null;
        
        // From PHP session (if available in JavaScript)
        if (typeof window.currentUserId !== 'undefined') {
            userId = window.currentUserId;
        } else if (typeof window.user_id !== 'undefined') {
            userId = window.user_id;
        } else {
            // Generate anonymous ID
            userId = 'anonymous_' + Math.random().toString(36).substr(2, 9);
        }
        
        userIdMeta.setAttribute('content', userId);
        document.head.appendChild(userIdMeta);
    }
    
    // ✅ Check if page should have auto-refresh
    const currentPath = window.location.pathname;
    const shouldAutoRefresh = (
        currentPath.includes('/google_drive') || 
        currentPath.includes('/member') ||
        currentPath === '/' ||
        currentPath.includes('/dashboard') ||
        currentPath.includes('/System_member') ||
		currentPath.includes('/Google_drive_files') ||
        currentPath.includes('/User/choice')
    );
    
    if (!shouldAutoRefresh) {
        console.log('🚫 Auto-refresh disabled for this page: ' + currentPath);
        return;
    }
    
    console.log('✅ Complete Multi-User Safe Auto-refresh enabled for page: ' + currentPath);
    
    // ✅ Initialize Smart Loader
    window.smartAutoRefreshLoader = new SmartAutoRefreshLoader();
    
    // ✅ Global Debug Functions
    window.checkSmartLoaderStatus = function() {
        if (window.smartAutoRefreshLoader) {
            return window.smartAutoRefreshLoader.getStatus();
        }
        return { error: 'Smart Loader not initialized' };
    };
    
    window.forceEnableAutoRefresh = function() {
        if (window.smartAutoRefreshLoader) {
            return window.smartAutoRefreshLoader.enableAutoRefresh();
        }
        return false;
    };
    
    window.forceDisableAutoRefresh = function() {
        if (window.smartAutoRefreshLoader) {
            return window.smartAutoRefreshLoader.disableAutoRefresh();
        }
        return false;
    };
    
    window.checkGoogleDriveStatus = function() {
        if (window.smartAutoRefreshLoader && 
            window.smartAutoRefreshLoader.autoRefreshInstance &&
            window.smartAutoRefreshLoader.autoRefreshInstance.getStatus) {
            return window.smartAutoRefreshLoader.autoRefreshInstance.getStatus();
        }
        return { error: 'Auto-Refresh not active' };
    };
    
    window.triggerGoogleDriveRefresh = function() {
        if (window.smartAutoRefreshLoader && 
            window.smartAutoRefreshLoader.autoRefreshInstance &&
            window.smartAutoRefreshLoader.autoRefreshInstance.triggerManualRefresh) {
            return window.smartAutoRefreshLoader.autoRefreshInstance.triggerManualRefresh();
        }
        return false;
    };
    
    console.log('🎯 Complete Debug Commands:');
    console.log('  checkSmartLoaderStatus() - ดูสถานะ Smart Loader');
    console.log('  forceEnableAutoRefresh() - บังคับเปิด Auto-Refresh');
    console.log('  forceDisableAutoRefresh() - บังคับปิด Auto-Refresh');
    console.log('  checkGoogleDriveStatus() - ดูสถานะ Auto-Refresh');
    console.log('  triggerGoogleDriveRefresh() - เรียก refresh manual');
});

// ✅ Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.smartAutoRefreshLoader) {
        window.smartAutoRefreshLoader.destroy();
    }
});
</script>