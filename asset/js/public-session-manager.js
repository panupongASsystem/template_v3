/**
 * Complete Public Session Manager - สำหรับประชาชน (Auth_public_mem)
 * ✅ Keep Alive แยกจาก User Activity 
 * ✅ Warning System = แจ้งเตือน 2 ระดับ (5 นาที และ 1 นาที)
 * ✅ JSON parsing error handling + Cross-Tab Sync
 * ✅ Auto close modal เมื่อมีการเคลื่อนไหว + Toast notifications
 * ✅ Cross-Tab Activity Synchronization เหมือน Complete Session Manager
 * ✅ broadcastActivity() + handleRemoteActivity() + Tab ID system
 * ✅ PublicCrossTabSessionManager + Global Functions + Testing Functions
 * 🆕 FULL CODE ครบครันทุกส่วน
 */

// ✅ ป้องกันการโหลดซ้ำของ core functions
if (typeof window.PublicSessionManagerLoaded !== 'undefined') {
    console.warn('⚠️ complete-public-session-manager.js core already loaded, skipping...');
} else {
    window.PublicSessionManagerLoaded = true;
    console.log('📚 Loading Complete Public Session Manager...');

    // 🆕 Toast Notification System
    window.showToast = window.showToast || function(message, type = 'info', timeout = 3000) {
        try {
            const toastId = 'toast_' + Date.now();
            const toastHTML = `
                <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 position-fixed" 
                     style="top: 20px; right: 20px; z-index: 99999; min-width: 300px; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);" 
                     role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-${getToastIcon(type)} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                                data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', toastHTML);
            
            const toastElement = document.getElementById(toastId);
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const toast = new bootstrap.Toast(toastElement, { delay: timeout });
                toast.show();
                
                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            } else {
                setTimeout(() => {
                    toastElement.style.animation = 'slideOutRight 0.3s ease-in forwards';
                    setTimeout(() => toastElement.remove(), 300);
                }, timeout);
            }
            
            return toastElement;
        } catch (error) {
            console.error('❌ Error showing toast:', error);
            console.log(`📢 ${message}`);
        }
    };

    function getToastIcon(type) {
        switch(type) {
            case 'success': return 'check-circle-fill';
            case 'danger': return 'exclamation-triangle-fill';
            case 'warning': return 'exclamation-triangle-fill';
            case 'info': return 'info-circle-fill';
            default: return 'info-circle-fill';
        }
    }

    // 🆕 Toast CSS (เพิ่มเฉพาะถ้ายังไม่มี)
    if (!document.getElementById('public-toast-animations-css')) {
        const toastCSS = `
            <style id="public-toast-animations-css">
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
                .toast {
                    animation: slideInRight 0.3s ease-out !important;
                }
                .toast.bg-success { background: linear-gradient(135deg, #88d8c0, #6bb6ff) !important; }
                .toast.bg-warning { background: linear-gradient(135deg, #ffeaa7, #fab1a0) !important; }
                .toast.bg-danger { background: linear-gradient(135deg, #fd79a8, #fdcb6e) !important; }
                .toast.bg-info { background: linear-gradient(135deg, #74b9ff, #0984e3) !important; }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', toastCSS);
    }

    // 🔧 Public User Session Manager (แก้ไขใหม่ + Cross-Tab Sync)
    window.PublicSessionManager = (function() {
        'use strict';
        
        let config = {
            sessionTimeout: 6 * 60 * 1000,      // 6 นาที (production) / 3 นาที (ทดสอบ)
            warningTime5Min: 5 * 60 * 1000,     // แจ้งเตือนเหลือ 5 นาที
            warningTime1Min: 1 * 60 * 1000,     // แจ้งเตือนเหลือ 1 นาที
            keepAliveInterval: 2 * 60 * 1000,   // keep alive ทุก 2 นาที
            maxIdleTime: 6 * 60 * 1000,         // idle สูงสุด 6 นาที
            debugMode: true,                    // เปิด debug
            keepAliveRetries: 3,                // จำนวนครั้งที่ลองใหม่
            baseUrl: window.base_url || ''      // Base URL สำหรับประชาชน
        };
        
        let timers = {
            logout: null,
            warning5Min: null,
            warning1Min: null,
            keepAlive: null
        };
        
        let state = {
            lastUserActivity: Date.now(),        // 🔑 เวลา activity จริงจากผู้ใช้
            lastKeepAlive: Date.now(),          // เวลา keep alive ล่าสุด
            warning5MinShown: false,
            warning1MinShown: false,
            isInitialized: false,
            userIsActive: true,
            keepAliveFailCount: 0,               // นับจำนวนครั้งที่ keep alive ล้มเหลว
            tabId: null                         // 🆕 ID ของ tab นี้
        };
        
        let callbacks = {
            onWarning5Min: null,
            onWarning1Min: null,
            onLogout: null,
            onError: null
        };
        
        function log(message, level = 'info') {
            if (config.debugMode) {
                const timestamp = new Date().toLocaleTimeString();
                console[level](`[PublicSessionManager ${timestamp}] ${message}`);
            }
        }
        
        function clearAllTimers() {
            Object.keys(timers).forEach(key => {
                if (timers[key]) {
                    if (key === 'keepAlive') {
                        clearInterval(timers[key]);
                    } else {
                        clearTimeout(timers[key]);
                    }
                    timers[key] = null;
                }
            });
        }

        // 🆕 สร้าง Tab ID
        function getTabId() {
            if (!state.tabId) {
                state.tabId = 'public_tab_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
            }
            return state.tabId;
        }

        // 🆕 Broadcast activity ไปยัง tabs อื่น
        function broadcastActivity(activityTime = null) {
            const now = activityTime || Date.now();
            const message = {
                type: 'user_activity',
                timestamp: now,
                tabId: getTabId(),
                userType: 'public',
                lastActivity: now
            };
            
            try {
                // ใช้ PublicCrossTabSync ถ้ามี
                if (window.PublicCrossTabSync && typeof window.PublicCrossTabSync.broadcast === 'function') {
                    window.PublicCrossTabSync.broadcast(message);
                    log(`📡 Broadcasted public activity to other tabs (time: ${new Date(now).toLocaleTimeString()})`);
                } else {
                    // Fallback: ใช้ localStorage
                    localStorage.setItem('public_session_activity', JSON.stringify(message));
                    log(`📦 Stored public activity in localStorage (fallback)`);
                }
            } catch (error) {
                log(`❌ Error broadcasting public activity: ${error.message}`, 'warn');
            }
        }

        // 🆕 รับ activity จาก tabs อื่น
        function handleRemoteActivity(data) {
            if (!data || data.tabId === getTabId() || data.userType !== 'public') {
                return; // ข้ามถ้าเป็น message จาก tab นี้เองหรือไม่ใช่ public
            }
            
            const now = Date.now();
            const remoteActivityTime = data.lastActivity || data.timestamp;
            
            // ตรวจสอบว่า remote activity ใหม่กว่า local activity หรือไม่
            if (remoteActivityTime > state.lastUserActivity) {
                log(`🔄 Syncing public activity from another tab (${new Date(remoteActivityTime).toLocaleTimeString()})`);
                
                // อัปเดต local activity โดยไม่ broadcast ซ้ำ
                state.lastUserActivity = remoteActivityTime;
                state.userIsActive = true;
                
                // ปิด modal ที่แสดงอยู่
                closeActiveSessionModals();
                
                // รีเซ็ต timers
                resetActivityTimers();
                
                // แสดงการแจ้งเตือน
                if (typeof window.showToast === 'function') {
                    window.showToast('🔄 Session ถูกต่ออายุจาก Tab อื่น', 'info', 2000);
                }
                
                log(`✅ Public session synced from remote tab successfully`);
            }
        }

        // 🆕 ปิด modal ที่แสดงอยู่เมื่อมีการเคลื่อนไหว
        function closeActiveSessionModals() {
            const modalsToClose = ['sessionWarning5Min', 'sessionWarning1Min'];
            let modalClosed = false;
            
            modalsToClose.forEach(modalId => {
                const modalElement = document.getElementById(modalId);
                if (modalElement && modalElement.classList.contains('show')) {
                    try {
                        // ใช้ Bootstrap API ปิด modal
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                            log(`📴 Auto-closed PUBLIC modal: ${modalId} due to user activity`);
                            modalClosed = true;
                        } else {
                            // Fallback: สร้าง instance ใหม่แล้วปิด
                            const newModalInstance = new bootstrap.Modal(modalElement);
                            newModalInstance.hide();
                            log(`📴 Auto-closed PUBLIC modal: ${modalId} via new instance`);
                            modalClosed = true;
                        }
                    } catch (error) {
                        log(`⚠️ Error auto-closing PUBLIC modal ${modalId}: ${error.message}`, 'warn');
                        
                        // Ultimate fallback: ซ่อนด้วย CSS
                        modalElement.style.display = 'none';
                        modalElement.classList.remove('show');
                        modalElement.setAttribute('aria-hidden', 'true');
                        modalElement.removeAttribute('aria-modal');
                        
                        // ล้าง backdrop
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(backdrop => backdrop.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('padding-right');
                        
                        modalClosed = true;
                    }
                }
            });
            
            if (modalClosed) {
                // รีเซ็ต warning flags
                state.warning5MinShown = false;
                state.warning1MinShown = false;
                
                // แสดงข้อความแจ้งเตือนเบาๆ
                showAutoExtendNotification();
            }
        }

        // 🆕 แสดงการแจ้งเตือนเบาๆ ว่าต่ออายุแล้ว
        function showAutoExtendNotification() {
            if (typeof window.showToast === 'function') {
                window.showToast('✅ ต่ออายุ Session อัตโนมัติ', 'success', 2000);
            } else {
                log('✅ Public session extended automatically by user activity');
            }
        }
        
        // 🔄 แก้ไข: เพิ่ม broadcast เมื่อมี user activity
        function updateUserActivity() {
            const now = Date.now();
            state.lastUserActivity = now;
            state.userIsActive = true;
            
            log(`👤 Public user activity updated at ${new Date(now).toLocaleTimeString()}`);
            
            // 🆕 Broadcast activity ไปยัง tabs อื่น
            broadcastActivity(now);
            
            // 🆕 ปิด modal ที่กำลังแสดงอยู่เมื่อมีการเคลื่อนไหว
            closeActiveSessionModals();
            
            resetActivityTimers();
        }

        function updateUserActivityManual() {
            if (!config.baseUrl) {
                console.error('base_url not defined for public users');
                return;
            }
            
            $.ajax({
                url: config.baseUrl + 'Auth_public_mem/update_user_activity',
                type: 'POST',
                dataType: 'json',
                timeout: 5000,
                success: function(response) {
                    if (response.success) {
                        log('✅ Public user activity updated manually via server');
                        updateUserActivity();
                    }
                },
                error: function(xhr, status, error) {
                    log('⚠️ Failed to update public user activity on server: ' + error, 'warn');
                    updateUserActivity();
                }
            });
        }
        
        function resetActivityTimers() {
            if (timers.warning5Min) clearTimeout(timers.warning5Min);
            if (timers.warning1Min) clearTimeout(timers.warning1Min);
            if (timers.logout) clearTimeout(timers.logout);
            
            state.warning5MinShown = false;
            state.warning1MinShown = false;
            
            const timeSinceActivity = Date.now() - state.lastUserActivity;
            const warning5MinTimeLeft = Math.max(0, (config.sessionTimeout - config.warningTime5Min) - timeSinceActivity);
            const warning1MinTimeLeft = Math.max(0, (config.sessionTimeout - config.warningTime1Min) - timeSinceActivity);
            const logoutTimeLeft = Math.max(0, config.sessionTimeout - timeSinceActivity);
            
            timers.warning5Min = setTimeout(() => {
                show5MinWarning();
            }, warning5MinTimeLeft);
            
            timers.warning1Min = setTimeout(() => {
                show1MinWarning();
            }, warning1MinTimeLeft);
            
            timers.logout = setTimeout(() => {
                forceLogout('Public user inactivity timeout');
            }, logoutTimeLeft);
            
            log(`⏰ Public activity timers reset - 5Min warning in ${Math.round(warning5MinTimeLeft/1000)}s, 1Min warning in ${Math.round(warning1MinTimeLeft/1000)}s, Logout in ${Math.round(logoutTimeLeft/1000)}s`);
        }
        
        function startKeepAlive() {
            sendKeepAlive();
            timers.keepAlive = setInterval(() => {
                sendKeepAlive();
            }, config.keepAliveInterval);
            log('🔄 Public keep alive started (every 2 minutes)');
        }
        
        async function parseJsonResponse(response) {
            try {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    log(`⚠️ Response is not JSON (content-type: ${contentType})`, 'warn');
                    const textResponse = await response.text();
                    log(`📄 Response text: ${textResponse.substring(0, 200)}...`, 'warn');
                    
                    return {
                        status: 'alive',
                        message: 'Non-JSON response received, assuming alive',
                        raw_response: textResponse
                    };
                }
                
                const responseText = await response.text();
                
                if (!responseText || responseText.trim().length === 0) {
                    log('⚠️ Empty response received', 'warn');
                    return {
                        status: 'alive',
                        message: 'Empty response received, assuming alive'
                    };
                }
                
                try {
                    const jsonData = JSON.parse(responseText);
                    log(`📋 JSON parsed successfully: ${JSON.stringify(jsonData)}`);
                    return jsonData;
                } catch (parseError) {
                    log(`❌ JSON parse error: ${parseError.message}`, 'error');
                    log(`📄 Raw response: ${responseText}`, 'error');
                    
                    return {
                        status: 'alive',
                        message: 'Invalid JSON response, assuming alive',
                        error: parseError.message,
                        raw_response: responseText
                    };
                }
            } catch (error) {
                log(`❌ Error processing response: ${error.message}`, 'error');
                return {
                    status: 'error',
                    message: 'Failed to process response',
                    error: error.message
                };
            }
        }
        
        async function sendKeepAlive() {
            try {
                const baseUrl = config.baseUrl || window.base_url || window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/') + '/';
                
                if (!baseUrl) {
                    throw new Error('base_url is not defined and cannot be determined for public users');
                }
                
                const now = Date.now();
                const timeSinceUserActivity = now - state.lastUserActivity;
                
                log(`🔄 Sending public keep alive request to: ${baseUrl}Auth_public_mem/keep_alive`);
                
                const response = await fetch(baseUrl + 'Auth_public_mem/keep_alive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        last_user_activity: state.lastUserActivity,
                        time_since_activity: timeSinceUserActivity,
                        max_idle_time: config.maxIdleTime,
                        user_type: 'public'
                    }),
                    cache: 'no-cache'
                });
                
                log(`📡 Public keep alive response status: ${response.status} ${response.statusText}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const result = await parseJsonResponse(response);
                
                state.keepAliveFailCount = 0;
                state.lastKeepAlive = now;
                
                if (result.status === 'expired') {
                    log('❌ Public session expired from server', 'warn');
                    forceLogout('Public session expired on server');
                    return;
                }
                
                if (result.status === 'idle_timeout') {
                    log('😴 Server detected public user idle timeout', 'warn');
                    forceLogout('Public server idle timeout');
                    return;
                }
                
                if (result.status === 'error') {
                    log(`⚠️ Server error: ${result.message}`, 'warn');
                    state.keepAliveFailCount++;
                    
                    if (state.keepAliveFailCount >= config.keepAliveRetries) {
                        log('❌ Too many keep alive failures, forcing public logout', 'error');
                        forceLogout('Multiple public keep alive failures');
                        return;
                    }
                }
                
                if (result.status === 'alive') {
                    log(`✅ Public keep alive OK (user idle: ${Math.round(timeSinceUserActivity/1000)}s)`);
                } else {
                    log(`ℹ️ Public keep alive response: ${result.status} - ${result.message || 'No message'}`);
                }
                
            } catch (error) {
                state.keepAliveFailCount++;
                log(`⚠️ Public keep alive failed (${state.keepAliveFailCount}/${config.keepAliveRetries}): ${error.message}`, 'warn');
                
                if (state.keepAliveFailCount >= config.keepAliveRetries) {
                    log('❌ Too many public keep alive failures, forcing logout', 'error');
                    forceLogout('Multiple public keep alive network failures');
                    return;
                }
                
                if (callbacks.onError) {
                    callbacks.onError('Public keep alive failed', error);
                }
            }
        }
        
        function show5MinWarning() {
            if (state.warning5MinShown) return;
            state.warning5MinShown = true;
            log(`⚠️ Showing public 5-minute warning`);
            
            if (callbacks.onWarning5Min) {
                callbacks.onWarning5Min();
                return;
            }
        }
        
        function show1MinWarning() {
            if (state.warning1MinShown) return;
            state.warning1MinShown = true;
            state.userIsActive = false;
            log(`🚨 Showing public 1-minute urgent warning`);
            
            if (callbacks.onWarning1Min) {
                callbacks.onWarning1Min();
                return;
            }
        }
        
        function extendSession() {
            log('🔄 Public user manually extended session');
            updateUserActivity();
            
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
            
            state.warning5MinShown = false;
            state.warning1MinShown = false;
            log('✅ Public session extended successfully');
        }
        
        function forceLogout(reason = 'Unknown') {
            log(`🚪 Public force logout: ${reason}`, 'warn');
            
            if (callbacks.onLogout) {
                callbacks.onLogout(reason);
                return;
            }
            
            clearAllTimers();
            window.location.href = config.baseUrl + 'Auth_public_mem/logout';
        }
        
        function bindActivityEvents() {
            const events = [
                'click', 'keydown', 'scroll', 'mousemove', 
                'touchstart', 'touchend', 'focus'
            ];
            
            const throttle = (func, limit) => {
                let inThrottle;
                return function() {
                    if (!inThrottle) {
                        func.apply(this, arguments);
                        inThrottle = true;
                        setTimeout(() => inThrottle = false, limit);
                    }
                }
            };
            
            const handleActivity = throttle(() => {
                updateUserActivity();
            }, 5000);
            
            events.forEach(event => {
                document.addEventListener(event, handleActivity, { 
                    passive: true 
                });
            });
            
            log('👂 Public activity event listeners bound');
        }

        // 🆕 ตั้งค่า listener สำหรับ remote activity
        function setupCrossTabActivitySync() {
            // Listen to localStorage changes
            window.addEventListener('storage', (event) => {
                if (event.key === 'public_session_activity' && event.newValue) {
                    try {
                        const data = JSON.parse(event.newValue);
                        if (data.type === 'user_activity') {
                            handleRemoteActivity(data);
                        }
                    } catch (error) {
                        log(`❌ Error parsing public activity storage: ${error.message}`, 'warn');
                    }
                }
            });
            
            // Listen to PublicCrossTabSync broadcasts if available
            if (window.PublicCrossTabSync && window.PublicCrossTabSync.broadcastChannel) {
                const originalHandler = window.PublicCrossTabSync.handleBroadcastMessage;
                window.PublicCrossTabSync.handleBroadcastMessage = function(data) {
                    // Call original handler first
                    if (originalHandler) {
                        originalHandler.call(this, data);
                    }
                    
                    // Handle user activity specifically
                    if (data && data.type === 'user_activity') {
                        handleRemoteActivity(data);
                    }
                };
            }
            
            log('🔗 Public cross-tab activity sync setup complete');
        }
        
        // Public API for Public Users
        return {
            init: function(options = {}) {
                if (state.isInitialized) {
                    log('Public SessionManager already initialized', 'warn');
                    return this;
                }
                
                Object.assign(config, options);
                
                state.lastUserActivity = Date.now();
                state.keepAliveFailCount = 0;
                resetActivityTimers();
                startKeepAlive();
                bindActivityEvents();
                
                // 🆕 ตั้งค่า cross-tab sync
                setupCrossTabActivitySync();
                
                state.isInitialized = true;
                log('🚀 Public SessionManager initialized successfully');
                
                return this;
            },
            
            setCallbacks: function(newCallbacks) {
                Object.assign(callbacks, newCallbacks);
                return this;
            },
            
            configure: function(newConfig) {
                Object.assign(config, newConfig);
                if (state.isInitialized) {
                    this.restart();
                }
                return this;
            },
            
            recordActivity: function() {
                updateUserActivity();
                return this;
            },
            
            sendKeepAlive: function() {
                return sendKeepAlive();
            },
            
            extend: function() {
                extendSession();
                return this;
            },
            
            logout: function(reason = 'Manual logout') {
                forceLogout(reason);
                return this;
            },

            // 🆕 เพิ่ม method สำหรับปิด modal จากภายนอก
            closeSessionModals: function() {
                closeActiveSessionModals();
                return this;
            },
            
            restart: function() {
                if (!state.isInitialized) return this;
                
                clearAllTimers();
                state.lastUserActivity = Date.now();
                state.warning5MinShown = false;
                state.warning1MinShown = false;
                state.keepAliveFailCount = 0;
                resetActivityTimers();
                startKeepAlive();
                
                log('🔄 Public SessionManager restarted');
                return this;
            },
            
            destroy: function() {
                clearAllTimers();
                state.isInitialized = false;
                state.keepAliveFailCount = 0;
                log('💥 Public SessionManager destroyed');
                return this;
            },

            // 🆕 เพิ่ม method สำหรับปิด modal จากภายนอก
            closeSessionModals: function() {
                closeActiveSessionModals();
                return this;
            },
            
            getState: function() {
                const now = Date.now();
                const timeSinceActivity = now - state.lastUserActivity;
                const timeSinceKeepAlive = now - state.lastKeepAlive;
                
                return {
                    lastUserActivity: state.lastUserActivity,
                    lastKeepAlive: state.lastKeepAlive,
                    timeSinceUserActivity: timeSinceActivity,
                    timeSinceKeepAlive: timeSinceKeepAlive,
                    remainingTime: Math.max(0, config.sessionTimeout - timeSinceActivity),
                    timeUntil5MinWarning: Math.max(0, (config.sessionTimeout - config.warningTime5Min) - timeSinceActivity),
                    timeUntil1MinWarning: Math.max(0, (config.sessionTimeout - config.warningTime1Min) - timeSinceActivity),
                    userIsActive: state.userIsActive,
                    warning5MinShown: state.warning5MinShown,
                    warning1MinShown: state.warning1MinShown,
                    isInitialized: state.isInitialized,
                    keepAliveFailCount: state.keepAliveFailCount,
                    userType: 'public',
                    tabId: state.tabId
                };
            },
            
            setDebugMode: function(enabled) {
                config.debugMode = enabled;
                return this;
            },
            
            resetFailCounter: function() {
                state.keepAliveFailCount = 0;
                log('🔄 Public keep alive fail counter reset');
                return this;
            },

            // 🆕 เพิ่ม method สำหรับ sync activity จาก external
            syncActivityFromRemote: function(activityTime) {
                if (activityTime > state.lastUserActivity) {
                    handleRemoteActivity({
                        type: 'user_activity',
                        lastActivity: activityTime,
                        userType: 'public',
                        tabId: 'external'
                    });
                }
                return this;
            }
        };
    })();

    // 🌟 Public Cross-Tab Session Manager (สำหรับประชาชน)
    class PublicCrossTabSessionManager {
        constructor() {
            this.storageKey = 'public_session_status';
            this.heartbeatKey = 'public_session_heartbeat';
            this.broadcastChannel = null;
            this.heartbeatInterval = null;
            this.sessionCheckInterval = null;
            this.isInitialized = false;
            this.currentSessionId = null;
            this.userType = 'public';
            
            this.config = {
                heartbeatInterval: 5000,
                sessionCheckInterval: 2000,
                maxHeartbeatAge: 15000
            };
            
            this.init();
        }
        
        init() {
            if (this.isInitialized) return;
            
            console.log('🔄 Initializing Public Cross-Tab Session Manager');
            
            this.setupBroadcastChannel();
            this.setupStorageListener();
            this.startHeartbeat();
            this.startSessionCheck();
            this.updateSessionStatus();
            
            this.isInitialized = true;
            console.log('✅ Public Cross-Tab Session Manager initialized');
        }
        
        setupBroadcastChannel() {
            if ('BroadcastChannel' in window) {
                this.broadcastChannel = new BroadcastChannel('public_session_sync');
                
                this.broadcastChannel.addEventListener('message', (event) => {
                    console.log('📨 Received public broadcast message:', event.data);
                    this.handleBroadcastMessage(event.data);
                });
                
                console.log('📡 Public BroadcastChannel setup complete');
            } else {
                console.log('⚠️ BroadcastChannel not supported, using LocalStorage fallback');
            }
        }
        
        setupStorageListener() {
            window.addEventListener('storage', (event) => {
                if (event.key === this.storageKey) {
                    console.log('📦 Public storage change detected:', event.newValue);
                    this.handleStorageChange(event.newValue);
                }
            });
        }
        
        startHeartbeat() {
            this.heartbeatInterval = setInterval(() => {
                if (this.isLoggedIn()) {
                    this.sendHeartbeat();
                }
            }, this.config.heartbeatInterval);
        }
        
        startSessionCheck() {
            this.sessionCheckInterval = setInterval(() => {
                this.checkSessionStatus();
            }, this.config.sessionCheckInterval);
        }
        
        broadcast(message) {
            const data = {
                timestamp: Date.now(),
                tabId: this.getTabId(),
                userType: this.userType,
                ...message
            };
            
            if (this.broadcastChannel) {
                this.broadcastChannel.postMessage(data);
            }
            
            localStorage.setItem(this.storageKey, JSON.stringify(data));
            console.log('📤 Public broadcast sent:', data);
        }
        
        handleBroadcastMessage(data) {
            if (!data || data.tabId === this.getTabId() || data.userType !== 'public') return;
            
            switch (data.type) {
                case 'logout':
                    this.handleRemoteLogout(data);
                    break;
                case 'login':
                    this.handleRemoteLogin(data);
                    break;
                case 'session_expired':
                    this.handleRemoteSessionExpired(data);
                    break;
                case 'heartbeat':
                    this.handleRemoteHeartbeat(data);
                    break;
                case 'user_activity':
                    this.handleRemoteUserActivity(data);
                    break;
            }
        }

        // 🆕 จัดการ user activity จาก tabs อื่น
        handleRemoteUserActivity(data) {
            if (!data || data.tabId === this.getTabId()) return;
            
            console.log('🔄 Public remote user activity detected:', data);
            
            // Sync กับ PublicSessionManager
            if (data.userType === 'public' && window.PublicSessionManager && window.PublicSessionManager.syncActivityFromRemote) {
                window.PublicSessionManager.syncActivityFromRemote(data.lastActivity || data.timestamp);
            }
        }
        
        handleStorageChange(newValue) {
            if (!newValue) return;
            
            try {
                const data = JSON.parse(newValue);
                this.handleBroadcastMessage(data);
            } catch (error) {
                console.error('Error parsing public storage data:', error);
            }
        }
        
        handleRemoteLogout(data) {
            console.log('🚪 Public remote logout detected');
            
            if (this.isLoggedIn()) {
                this.performLocalLogout();
                this.showLogoutNotification('คุณได้ออกจากระบบในแท็บอื่น');
            }
        }
        
        handleRemoteLogin(data) {
            console.log('🔐 Public remote login detected');
            
            if (!this.isLoggedIn() && data.sessionId) {
                this.currentSessionId = data.sessionId;
                this.updateSessionStatus();
                
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        }
        
        handleRemoteSessionExpired(data) {
            console.log('⏰ Public remote session expired detected');
            
            if (this.isLoggedIn()) {
                this.performLocalLogout();
                this.showSessionExpiredNotification();
            }
        }
        
        handleRemoteHeartbeat(data) {
            localStorage.setItem(this.heartbeatKey, JSON.stringify({
                timestamp: data.timestamp,
                tabId: data.tabId,
                userType: 'public'
            }));
        }
        
        checkSessionStatus() {
            const lastHeartbeat = this.getLastHeartbeat();
            const now = Date.now();
            
            if (lastHeartbeat && (now - lastHeartbeat.timestamp) > this.config.maxHeartbeatAge) {
                console.log('💀 No heartbeat from other public tabs, checking server session');
                this.verifyServerSession();
            }
        }
        
        async verifyServerSession() {
            try {
                const baseUrl = window.base_url || window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/') + '/';
                
                const response = await fetch(baseUrl + 'Auth_public_mem/verify_session', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                const result = await response.json();
                
                if (!result.valid) {
                    console.log('❌ Public server session invalid');
                    this.broadcast({ type: 'session_expired' });
                    this.performLocalLogout();
                }
            } catch (error) {
                console.error('Error verifying public session:', error);
            }
        }
        
        sendHeartbeat() {
            this.broadcast({ 
                type: 'heartbeat',
                sessionId: this.getCurrentSessionId()
            });
        }
        
        getTabId() {
            if (!this.tabId) {
                this.tabId = 'public_tab_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
            }
            return this.tabId;
        }
        
        isLoggedIn() {
            return !!(document.cookie.includes('ci_session') || 
                     window.sessionStorage.getItem('public_user_logged_in') ||
                     this.currentSessionId);
        }
        
        getCurrentSessionId() {
            if (!this.currentSessionId) {
                const matches = document.cookie.match(/ci_session=([^;]+)/);
                this.currentSessionId = matches ? matches[1] : null;
            }
            return this.currentSessionId;
        }
        
        updateSessionStatus() {
            const status = {
                isLoggedIn: this.isLoggedIn(),
                sessionId: this.getCurrentSessionId(),
                timestamp: Date.now(),
                tabId: this.getTabId(),
                userType: 'public'
            };
            
            localStorage.setItem(this.storageKey, JSON.stringify(status));
        }
        
        getLastHeartbeat() {
            try {
                const data = localStorage.getItem(this.heartbeatKey);
                return data ? JSON.parse(data) : null;
            } catch (error) {
                return null;
            }
        }
        
        performLocalLogout() {
            console.log('🚪 Performing public local logout');
            
            this.currentSessionId = null;
            localStorage.removeItem(this.storageKey);
            localStorage.removeItem(this.heartbeatKey);
            sessionStorage.clear();
            
            this.updateSessionStatus();
            
            if (this.heartbeatInterval) {
                clearInterval(this.heartbeatInterval);
            }
            
            const baseUrl = window.base_url || window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/') + '/';
            
            setTimeout(() => {
                window.location.href = baseUrl + 'Auth_public_mem/logout';
            }, 1500);
        }
        
        showLogoutNotification(message = 'คุณได้ออกจากระบบ') {
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'info', 3000);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'ออกจากระบบ',
                    text: message,
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            } else {
                alert(message);
            }
        }
        
        showSessionExpiredNotification() {
            const baseUrl = window.base_url || window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/') + '/';
            
            if (typeof window.showToast === 'function') {
                window.showToast('Session หมดอายุ กรุณาเข้าสู่ระบบใหม่', 'warning', 5000);
                setTimeout(() => {
                    window.location.href = baseUrl + 'Auth_public_mem/logout';
                }, 3000);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Session หมดอายุ',
                    text: 'กรุณาเข้าสู่ระบบใหม่',
                    confirmButtonText: 'ตกลง',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = baseUrl + 'Auth_public_mem/logout';
                });
            } else {
                alert('Session หมดอายุ กรุณาเข้าสู่ระบบใหม่');
                window.location.href = baseUrl + 'Auth_public_mem/logout';
            }
        }
        
        logout() {
            console.log('🚪 Public logout initiated from this tab');
            
            this.broadcast({ 
                type: 'logout',
                sessionId: this.getCurrentSessionId()
            });
            
            this.performLocalLogout();
        }
        
        login(sessionId) {
            console.log('🔐 Public login initiated from this tab');
            
            this.currentSessionId = sessionId;
            this.updateSessionStatus();
            
            this.broadcast({ 
                type: 'login',
                sessionId: sessionId
            });
        }
        
        destroy() {
            console.log('🛑 Destroying Public Cross-Tab Session Manager');
            
            if (this.heartbeatInterval) {
                clearInterval(this.heartbeatInterval);
            }
            
            if (this.sessionCheckInterval) {
                clearInterval(this.sessionCheckInterval);
            }
            
            if (this.broadcastChannel) {
                this.broadcastChannel.close();
            }
            
            this.isInitialized = false;
        }
    }

    // 🌟 สร้าง instance สำหรับประชาชน
    window.PublicCrossTabSync = new PublicCrossTabSessionManager();

    // 🔗 เชื่อมต่อกับ PublicSessionManager
    if (window.PublicSessionManager) {
        const originalLogout = window.PublicSessionManager.logout;
        window.PublicSessionManager.logout = function(reason) {
            console.log('PublicSessionManager logout called, syncing to other tabs');
            window.PublicCrossTabSync.logout();
            if (originalLogout) {
                originalLogout.call(this, reason);
            }
        };
        
        console.log('🔗 PublicSessionManager integration complete');
    }

    // 🔧 Global functions สำหรับประชาชน
    window.publicSyncLogout = function() {
        window.PublicCrossTabSync.logout();
    };

    window.publicSyncLogin = function(sessionId) {
        window.PublicCrossTabSync.login(sessionId);
    };

    // 🧪 Enhanced Testing Functions สำหรับ Public
    window.testPublicAutoCloseModal = function() {
        console.log('🧪 Testing PUBLIC auto-close modal functionality...');
        
        if (typeof window.PublicSessionManager !== 'undefined' && window.PublicSessionManager.getState().isInitialized) {
            console.log('📱 Testing with PublicSessionManager...');
            
            setTimeout(() => {
                if (typeof showSessionWarning === 'function') {
                    showSessionWarning('5min');
                }
                
                setTimeout(() => {
                    console.log('🎭 Simulating mouse movement...');
                    
                    const mouseEvent = new MouseEvent('mousemove', {
                        clientX: Math.random() * window.innerWidth,
                        clientY: Math.random() * window.innerHeight,
                        bubbles: true
                    });
                    
                    document.dispatchEvent(mouseEvent);
                    console.log('✅ Mouse event dispatched - modal should close automatically');
                }, 3000);
            }, 1000);
        } else {
            console.log('⚠️ PublicSessionManager not initialized - cannot test');
        }
    };

    // 🆕 ทดสอบ Cross-Tab Activity Sync สำหรับ Public
    window.testPublicCrossTabActivitySync = function() {
        console.log('🧪 Testing Public Cross-Tab Activity Sync...');
        
        if (window.PublicSessionManager && window.PublicSessionManager.getState().isInitialized) {
            console.log('📱 Broadcasting public activity...');
            window.PublicSessionManager.recordActivity();
            console.log('✅ Public activity broadcasted - check other tabs');
        }
        
        setTimeout(() => {
            console.log('=== PUBLIC SESSION STATES ===');
            if (window.PublicSessionManager) {
                console.log('Public State:', window.PublicSessionManager.getState());
            }
            console.log('=== END STATES ===');
        }, 1000);
    };

    window.testPublicToastNotification = function() {
        if (typeof window.showToast === 'function') {
            window.showToast('🧪 ทดสอบ Public Toast Notification', 'success', 5000);
        } else {
            console.log('❌ showToast function not available');
        }
    };

    console.log('✅ Complete Public Session Management System loaded with Cross-Tab Activity Sync');
    console.log('🎯 Test functions: testPublicAutoCloseModal(), testPublicCrossTabActivitySync(), testPublicToastNotification()');
}

// 🧪 TESTING FUNCTIONS - โหลดทุกครั้ง (ไม่ติด condition)
if (typeof window.testPublicCrossTabActivitySync === 'undefined') {
    console.log('🧪 Loading Public Testing Functions...');

    // 🆕 ทดสอบ Cross-Tab Activity Sync สำหรับ Public
    window.testPublicCrossTabActivitySync = function() {
        console.log('🧪 Testing Public Cross-Tab Activity Sync...');
        
        if (window.PublicSessionManager && window.PublicSessionManager.getState().isInitialized) {
            console.log('📱 Broadcasting public activity...');
            window.PublicSessionManager.recordActivity();
            console.log('✅ Public activity broadcasted - check other tabs');
        } else {
            console.log('⚠️ PublicSessionManager not initialized - cannot test');
        }
        
        setTimeout(() => {
            console.log('=== PUBLIC SESSION STATES ===');
            if (window.PublicSessionManager) {
                console.log('Public State:', window.PublicSessionManager.getState());
            }
            if (window.PublicCrossTabSync) {
                console.log('PublicCrossTabSync State:', {
                    isInitialized: window.PublicCrossTabSync.isInitialized,
                    currentSessionId: window.PublicCrossTabSync.getCurrentSessionId(),
                    isLoggedIn: window.PublicCrossTabSync.isLoggedIn()
                });
            }
            console.log('=== END STATES ===');
        }, 1000);
    };

    window.testPublicToastNotification = function() {
        if (typeof window.showToast === 'function') {
            window.showToast('🧪 ทดสอบ Public Toast Notification', 'success', 5000);
        } else {
            console.log('❌ showToast function not available');
        }
    };

    window.testPublicKeepAlive = function() {
        if (window.PublicSessionManager && window.PublicSessionManager.sendKeepAlive) {
            console.log('🧪 Testing Public Keep Alive...');
            window.PublicSessionManager.sendKeepAlive();
        } else {
            console.log('⚠️ PublicSessionManager not available');
        }
    };

    window.testPublicSessionState = function() {
        if (window.PublicSessionManager) {
            const state = window.PublicSessionManager.getState();
            console.log('🧪 Public Session State:', state);
            
            const now = Date.now();
            const lastActivity = new Date(state.lastUserActivity);
            const timeSinceActivity = Math.round((now - state.lastUserActivity) / 1000);
            const remainingTime = Math.round(state.remainingTime / 1000);
            
            console.log(`📊 Last Activity: ${lastActivity.toLocaleTimeString()}`);
            console.log(`⏱️ Time Since Activity: ${timeSinceActivity} seconds`);
            console.log(`⏰ Remaining Time: ${remainingTime} seconds`);
            console.log(`🔄 Keep Alive Fail Count: ${state.keepAliveFailCount}`);
            console.log(`🏷️ Tab ID: ${state.tabId}`);
        } else {
            console.log('⚠️ PublicSessionManager not available');
        }
    };

    // 🔍 Debug function (โหลดทุกครั้ง)
    window.debugPublicSessionManager = function() {
        console.log('=== PUBLIC SESSION MANAGER DEBUG ===');
        console.log('PublicSessionManager available:', typeof window.PublicSessionManager !== 'undefined');
        console.log('PublicCrossTabSync available:', typeof window.PublicCrossTabSync !== 'undefined');
        console.log('Public functions available:', typeof window.initializePublicSessionManager !== 'undefined');
        console.log('showToast available:', typeof window.showToast !== 'undefined');
        console.log('Base URL:', window.base_url);
        console.log('jQuery available:', typeof $ !== 'undefined');
        console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
        console.log('SweetAlert available:', typeof Swal !== 'undefined');
        
        // ตรวจสอบ modal elements
        const modals = ['sessionWarning5Min', 'sessionWarning1Min', 'sessionLogoutModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            console.log(`Modal ${modalId}:`, modal ? 'EXISTS' : 'NOT FOUND');
        });
        
        if (window.PublicSessionManager) {
            console.log('Public SessionManager state:', window.PublicSessionManager.getState());
        }
        if (window.PublicCrossTabSync) {
            console.log('PublicCrossTabSync initialized:', window.PublicCrossTabSync.isInitialized);
            console.log('PublicCrossTabSync sessionId:', window.PublicCrossTabSync.getCurrentSessionId());
        }
        
        // ตรวจสอบ localStorage
        const publicKeys = ['public_session_status', 'public_session_heartbeat', 'public_session_activity'];
        publicKeys.forEach(key => {
            const value = localStorage.getItem(key);
            console.log(`localStorage[${key}]:`, value ? JSON.parse(value) : 'NULL');
        });
        
        console.log('=== END DEBUG ===');
    };

    // 🧪 Test All Functions
    window.testAllPublicFunctions = function() {
        console.log('🧪 Testing All Public Functions...');
        
        console.log('1. Testing Toast...');
        window.testPublicToastNotification();
        
        setTimeout(() => {
            console.log('2. Testing Keep Alive...');
            window.testPublicKeepAlive();
        }, 1000);
        
        setTimeout(() => {
            console.log('3. Testing Session State...');
            window.testPublicSessionState();
        }, 2000);
        
        setTimeout(() => {
            console.log('4. Testing Cross-Tab Sync...');
            window.testPublicCrossTabActivitySync();
        }, 3000);
        
        setTimeout(() => {
            console.log('5. Running Debug...');
            window.debugPublicSessionManager();
        }, 4000);
    };

    console.log('🧪 Public Testing Functions loaded');
}

// 🚀 PUBLIC FUNCTIONS - โหลดทุกครั้ง (นอก condition)
if (typeof window.initializePublicSessionManager === 'undefined') {
    console.log('📚 Loading Public Session Management functions...');
    
    /**
     * ฟังก์ชันเริ่มต้น PUBLIC Session Manager
     */
    window.initializePublicSessionManager = function(hasPublicSession = false) {
        if (!hasPublicSession) {
            console.log('ℹ️ Public user not logged in, PublicSessionManager not initialized');
            return;
        }

        if (typeof window.PublicSessionManager !== 'undefined') {
            window.PublicSessionManager.init({
                sessionTimeout: 6 * 60 * 1000,      // 6 นาที
                warningTime5Min: 5 * 60 * 1000,     // แจ้งเตือนเหลือ 5 นาที
                warningTime1Min: 1 * 60 * 1000,     // แจ้งเตือนเหลือ 1 นาที
                keepAliveInterval: 2 * 60 * 1000,   // keep alive ทุก 2 นาที
                maxIdleTime: 6 * 60 * 1000,         // idle สูงสุด 6 นาที
                debugMode: true,
                baseUrl: window.base_url
            }).setCallbacks({
                onWarning5Min: function(minutesIdle) {
                    console.log('📢 PUBLIC: 5-minute warning triggered');
                    window.showPublicSessionWarning('5min');
                },
                onWarning1Min: function(minutesIdle) {
                    console.log('🚨 PUBLIC: 1-minute warning triggered');
                    window.showPublicSessionWarning('1min');
                },
                onLogout: function(reason) {
                    console.log('🚪 PUBLIC: Session logout reason:', reason);
                    window.showPublicLogoutModal();
                },
                onError: function(message, error) {
                    console.error('❌ PUBLIC SessionManager error:', message, error);
                }
            });
            
            console.log('✅ PublicSessionManager initialized successfully');
            console.log('🌐 Base URL set to:', window.base_url);
        } else {
            console.error('❌ PublicSessionManager not found! Using fallback...');
            window.startPublicFallbackSessionManager();
        }
    };

    /**
     * Fallback Session Manager สำหรับ Public
     */
    window.startPublicFallbackSessionManager = function() {
        let sessionTimeout = 6 * 60 * 1000; // 6 นาที
        let warningTimeout5Min = 5 * 60 * 1000; // 5 นาที
        let warningTimeout1Min = 1 * 60 * 1000; // 1 นาที
        
        console.log('🔄 Starting public fallback session manager');
        
        setTimeout(() => {
            window.showPublicSessionWarning('5min');
        }, sessionTimeout - warningTimeout5Min);
        
        setTimeout(() => {
            window.showPublicSessionWarning('1min');
        }, sessionTimeout - warningTimeout1Min);
        
        setTimeout(() => {
            window.location.href = window.base_url + 'Auth_public_mem/logout';
        }, sessionTimeout);
    };

    /**
     * สร้าง Public Session Modals ถ้ายังไม่มี
     */
    window.createPublicSessionModalsIfNeeded = function() {
        if (document.getElementById('sessionWarning5Min')) {
            console.log('ℹ️ Public session modals already exist');
            return;
        }
        
        console.log('🏗️ Creating public session modals dynamically...');
        
        const modalCSS = `
            <style id="public-session-modal-css">
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                    100% { transform: scale(1); }
                }
                .public-timeout-icon i, .public-logout-icon i { animation: pulse 2s infinite; }
                .public-timeout-title, .public-logout-title { font-weight: 600; margin-bottom: 15px; }
                .public-timeout-message, .public-logout-message { line-height: 1.6; color: #666; }
                .modal { z-index: 9999 !important; }
                .modal-backdrop { z-index: 9998 !important; }
                .modal-dialog { z-index: 10000 !important; position: relative; }
                .modal-content { position: relative; z-index: 10001 !important; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
            </style>
        `;
        
        if (!document.getElementById('public-session-modal-css')) {
            document.head.insertAdjacentHTML('beforeend', modalCSS);
        }
        
        const modalHTML = `
            <!-- Public Session Warning Modals -->
            <div class="modal fade" id="sessionWarning5Min" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
                        <div class="modal-header" style="background: linear-gradient(135deg, #ffeaa7, #fab1a0); color: #2d3748; border-radius: 20px 20px 0 0; border-bottom: none;">
                            <h5 class="modal-title" style="font-weight: 600;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                แจ้งเตือนการหมดเวลา (ประชาชน)
                            </h5>
                        </div>
                        <div class="modal-body text-center py-4">
                            <div class="public-timeout-icon mb-3">
                                <i class="fas fa-clock" style="font-size: 4rem; color: #ffeaa7;"></i>
                            </div>
                            <h4 class="public-timeout-title font-weight-bold mb-3" style="color: #2d3748;">ระบบจะหมดเวลาใช้งานในอีก 5 นาที</h4>
                            <p class="public-timeout-message mb-4" style="color: #4a5568; line-height: 1.6;">
                                ระบบตรวจพบว่าคุณไม่มีการใช้งานเป็นเวลานาน<br>
                                หากไม่มีการใช้งาน ระบบจะออกจากระบบอัตโนมัติ
                            </p>
                            <div class="alert alert-info" style="background: rgba(116, 185, 255, 0.1); border: 1px solid rgba(116, 185, 255, 0.3); border-radius: 12px; color: #2d3748;">
                                <i class="fas fa-info-circle me-2"></i>การเคลื่อนไหวเมาส์หรือพิมพ์คีย์บอร์ดจะต่ออายุ Session อัตโนมัติ
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center border-0 pb-4">
                            <button type="button" class="btn btn-primary btn-lg me-3" id="extend5MinBtn" style="background: linear-gradient(135deg, #88d8c0, #6bb6ff); border: none; border-radius: 12px; padding: 12px 30px; font-weight: 500;">
                                <i class="fas fa-redo me-2"></i>ใช้งานต่อ
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="logout5MinBtn" style="border-radius: 12px; padding: 12px 30px; font-weight: 500;">
                                <i class="fas fa-sign-out-alt me-1"></i>ออกจากระบบ
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="sessionWarning1Min" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
                        <div class="modal-header" style="background: linear-gradient(135deg, #fd79a8, #fdcb6e); color: #fff; border-radius: 20px 20px 0 0; border-bottom: none;">
                            <h5 class="modal-title" style="font-weight: 600;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                แจ้งเตือนด่วน! (ประชาชน)
                            </h5>
                        </div>
                        <div class="modal-body text-center py-4">
                            <div class="public-timeout-icon mb-3">
                                <i class="fas fa-clock" style="font-size: 4rem; color: #fd79a8;"></i>
                            </div>
                            <h4 class="public-timeout-title font-weight-bold mb-3" style="color: #e53e3e;">ระบบจะหมดเวลาใช้งานในอีก 1 นาที!</h4>
                            <p class="public-timeout-message mb-4" style="color: #4a5568; line-height: 1.6;">
                                <strong>คำเตือน:</strong> หากไม่กดปุ่ม "ใช้งานต่อ" ทันที<br>
                                ระบบจะออกจากระบบอัตโนมัติในอีกไม่ช้า
                            </p>
                            <div class="alert alert-danger" style="background: rgba(253, 121, 168, 0.1); border: 1px solid rgba(253, 121, 168, 0.3); border-radius: 12px; color: #e53e3e;">
                                <i class="fas fa-exclamation-triangle me-2"></i>การเคลื่อนไหวเมาส์หรือพิมพ์คีย์บอร์ดจะต่ออายุ Session อัตโนมัติ
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center border-0 pb-4">
                            <button type="button" class="btn btn-success btn-lg me-3" id="extend1MinBtn" style="background: linear-gradient(135deg, #88d8c0, #6bb6ff); border: none; border-radius: 12px; padding: 12px 30px; font-weight: 500;">
                                <i class="fas fa-redo me-2"></i>ใช้งานต่อ
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="logout1MinBtn" style="border-radius: 12px; padding: 12px 30px; font-weight: 500;">
                                <i class="fas fa-sign-out-alt me-1"></i>ออกจากระบบ
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="sessionLogoutModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
                        <div class="modal-header" style="background: linear-gradient(135deg, #74b9ff, #0984e3); color: #fff; border-radius: 20px 20px 0 0; border-bottom: none;">
                            <h5 class="modal-title" style="font-weight: 600;">
                                <i class="fas fa-info-circle me-2"></i>
                                กำลังนำท่านกลับสู่ระบบล็อกอิน
                            </h5>
                        </div>
                        <div class="modal-body text-center py-4">
                            <div class="public-logout-icon mb-3">
                                <i class="fas fa-sign-out-alt" style="font-size: 4rem; color: #74b9ff;"></i>
                            </div>
                            <h4 class="public-logout-title font-weight-bold mb-3" style="color: #2d3748;">Session หมดอายุแล้ว</h4>
                            <p class="public-logout-message mb-4" style="color: #4a5568; line-height: 1.6;">
                                เรากำลังนำท่านกลับไปสู่ระบบล็อกอินใหม่<br>
                                กรุณารอสักครู่...
                            </p>
                            <div class="progress mt-3 mb-3" style="height: 8px; border-radius: 12px; background: rgba(0,0,0,0.05);">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%; background: linear-gradient(135deg, #74b9ff, #0984e3); border-radius: 12px;"></div>
                            </div>
                            <small class="text-muted">หน้านี้จะถูกเปลี่ยนเส้นทางอัตโนมัติ</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        console.log('✅ Public session modals created dynamically');
    };

    /**
     * ฟังก์ชันแสดง Public Session Warning Modal
     */
    window.showPublicSessionWarning = function(type) {
        const modalId = type === '1min' ? 'sessionWarning1Min' : 'sessionWarning5Min';
        
        if (type === '1min') {
            window.closePublicModal('sessionWarning5Min');
        }
        
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            try {
                if (!modalElement.classList.contains('show')) {
                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: true
                    });
                    modal.show();
                    console.log(`✅ PUBLIC Session warning modal shown: ${type}`);
                }
            } catch (error) {
                console.error(`❌ Error showing public modal ${modalId}:`, error);
                window.showPublicSweetAlertWarning(type);
            }
        } else {
            console.error(`❌ Public modal element ${modalId} not found`);
            window.showPublicSweetAlertWarning(type);
        }
    };

    /**
     * แสดง Public Logout Modal
     */
    window.showPublicLogoutModal = function() {
        window.closePublicModal('sessionWarning5Min');
        window.closePublicModal('sessionWarning1Min');
        
        const modalElement = document.getElementById('sessionLogoutModal');
        if (modalElement) {
            try {
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
                
                setTimeout(() => {
                    window.location.href = window.base_url + 'Auth_public_mem/logout';
                }, 3000);
                
                console.log('✅ PUBLIC Logout modal shown, redirecting in 3 seconds...');
            } catch (error) {
                console.error('❌ Error showing public logout modal:', error);
                window.location.href = window.base_url + 'Auth_public_mem/logout';
            }
        } else {
            console.error('❌ Public logout modal element not found');
            window.location.href = window.base_url + 'Auth_public_mem/logout';
        }
    };

    /**
     * ตั้งค่า Event Listeners สำหรับ Public Modal Buttons
     */
    window.setupPublicModalEventListeners = function() {
        document.addEventListener('click', function(e) {
            const target = e.target;
            
            if (target.id === 'extend5MinBtn' || target.id === 'extend1MinBtn') {
                e.preventDefault();
                console.log(`${target.id} clicked (PUBLIC)`);
                
                if (window.PublicSessionManager) {
                    window.PublicSessionManager.extend();
                }
                
                const modalId = target.id === 'extend5MinBtn' ? 'sessionWarning5Min' : 'sessionWarning1Min';
                window.closePublicModal(modalId);
                window.showPublicAlert('✅ ต่ออายุ Session สำเร็จ', 'success');
            }
            
            if (target.id === 'logout5MinBtn' || target.id === 'logout1MinBtn') {
                e.preventDefault();
                console.log(`${target.id} clicked (PUBLIC)`);
                
                if (window.PublicSessionManager) {
                    window.PublicSessionManager.logout('Public user chose to logout from modal');
                } else {
                    window.location.href = window.base_url + 'Auth_public_mem/logout';
                }
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal5Min = document.getElementById('sessionWarning5Min');
                const modal1Min = document.getElementById('sessionWarning1Min');
                
                if (modal5Min && modal5Min.classList.contains('show')) {
                    console.log('ESC pressed on public 5min modal - extending session');
                    if (window.PublicSessionManager) {
                        window.PublicSessionManager.extend();
                    }
                    window.closePublicModal('sessionWarning5Min');
                    window.showPublicAlert('✅ ต่ออายุ Session สำเร็จ', 'success');
                } else if (modal1Min && modal1Min.classList.contains('show')) {
                    console.log('ESC pressed on public 1min modal - extending session');
                    if (window.PublicSessionManager) {
                        window.PublicSessionManager.extend();
                    }
                    window.closePublicModal('sessionWarning1Min');
                    window.showPublicAlert('✅ ต่ออายุ Session สำเร็จ', 'success');
                }
            }
        });
        
        console.log('✅ PUBLIC Modal event listeners setup complete');
    };

    /**
     * Helper Functions สำหรับ Public
     */
    window.closePublicModal = function(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            try {
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    const newModalInstance = new bootstrap.Modal(modalElement);
                    newModalInstance.hide();
                }
                
                setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('padding-right');
                    document.body.style.removeProperty('overflow');
                }, 300);
            } catch (error) {
                console.error(`❌ Error closing public modal ${modalId}:`, error);
            }
        }
    };

    window.showPublicAlert = function(message, type = 'info', timeout = 5000) {
        try {
            const alertId = 'alert_' + Date.now();
            const alertHTML = `
                <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                     id="${alertId}" 
                     style="top: 20px; right: 20px; z-index: 99999; min-width: 300px; max-width: 500px;" 
                     role="alert">
                    <i class="fas fa-${getPublicAlertIcon(type)} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', alertHTML);
            
            if (timeout > 0) {
                setTimeout(() => {
                    const alert = document.getElementById(alertId);
                    if (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, timeout);
            }
        } catch (error) {
            console.error('❌ Error showing public alert:', error);
            console.log(`📢 ${message}`);
        }
    };

    window.showPublicSweetAlertWarning = function(type) {
        if (typeof Swal === 'undefined') return;
        
        const config = type === '1min' ? {
            title: '🚨 แจ้งเตือนด่วน! (ประชาชน)',
            text: 'ระบบจะหมดเวลาใช้งานในอีก 1 นาที!',
            icon: 'error'
        } : {
            title: '⚠️ แจ้งเตือนการหมดเวลา (ประชาชน)',
            text: 'ระบบจะหมดเวลาใช้งานในอีก 5 นาที',
            icon: 'warning'
        };
        
        Swal.fire({
            ...config,
            showCancelButton: true,
            confirmButtonText: '🔄 ใช้งานต่อ',
            cancelButtonText: '🚪 ออกจากระบบ',
            allowOutsideClick: type !== '1min',
            allowEscapeKey: true
        }).then((result) => {
            if (result.isConfirmed || result.dismiss === Swal.DismissReason.esc) {
                window.showPublicAlert('✅ ต่ออายุ Session สำเร็จ', 'success');
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = window.base_url + 'Auth_public_mem/logout';
            }
        });
    };

    function getPublicAlertIcon(type) {
        switch(type) {
            case 'success': return 'check-circle';
            case 'danger': return 'exclamation-triangle';
            case 'warning': return 'exclamation-triangle';
            case 'info': return 'info-circle';
            default: return 'info-circle';
        }
    }

    console.log('🚀 Public Session Management functions loaded');
}

// 🧪 Test functions (โหลดทุกครั้ง)
if (typeof window.testPublicSessionWarning === 'undefined') {
    window.testPublicSessionWarning = function(type = '5min') {
        console.log(`🧪 Testing PUBLIC ${type} warning...`);
        if (typeof window.showPublicSessionWarning === 'function') {
            window.showPublicSessionWarning(type);
        } else {
            console.log('⚠️ showPublicSessionWarning function not available');
        }
    };
}

if (typeof window.testPublicLogoutModal === 'undefined') {
    window.testPublicLogoutModal = function() {
        console.log('🧪 Testing PUBLIC logout modal...');
        if (typeof window.showPublicLogoutModal === 'function') {
            window.showPublicLogoutModal();
        } else {
            console.log('⚠️ showPublicLogoutModal function not available');
        }
    };
}

console.log('🚀 All Public Session Test Functions loaded');

// 🔍 Debug function (โหลดทุกครั้ง)
window.debugPublicSessionManager = function() {
    console.log('=== PUBLIC SESSION MANAGER DEBUG ===');
    console.log('PublicSessionManager available:', typeof window.PublicSessionManager !== 'undefined');
    console.log('PublicCrossTabSync available:', typeof window.PublicCrossTabSync !== 'undefined');
    console.log('Public functions available:', typeof window.initializePublicSessionManager !== 'undefined');
    console.log('Base URL:', window.base_url);
    
    if (window.PublicSessionManager) {
        console.log('Public SessionManager state:', window.PublicSessionManager.getState());
    }
    if (window.PublicCrossTabSync) {
        console.log('PublicCrossTabSync initialized:', window.PublicCrossTabSync.isInitialized);
    }
    
    console.log('=== END DEBUG ===');
};

console.log('✅ Complete Public Session Manager with Cross-Tab Activity Sync loaded completely');
console.log('🎯 Available Functions:');
console.log('   📋 Main: initializePublicSessionManager(), debugPublicSessionManager()');
console.log('   🧪 Tests: testPublicCrossTabActivitySync(), testPublicAutoCloseModal()');
console.log('   🔧 Utils: testPublicToastNotification(), testAllPublicFunctions()');
console.log('   🎨 UI: testPublicSessionWarning(), testPublicLogoutModal()');
console.log('💡 Quick test: testAllPublicFunctions()');