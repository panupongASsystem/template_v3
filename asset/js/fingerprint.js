/* 
 * fingerprint.js
 * -------------
 * ไฟล์สำหรับการสร้าง Device Fingerprint เพื่อระบุตัวตนอุปกรณ์
 * โดยรวบรวมข้อมูลพื้นฐานของเบราว์เซอร์และอุปกรณ์
 */

// ฟังก์ชันสร้าง Canvas Fingerprint
async function getCanvasFingerprint() {
    try {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 200;
        canvas.height = 50;
        
        // วาดข้อความและรูปร่างต่างๆ
        ctx.textBaseline = "top";
        ctx.font = "14px 'Arial'";
        ctx.textBaseline = "alphabetic";
        ctx.fillStyle = "#f60";
        ctx.fillRect(125, 1, 62, 20);
        ctx.fillStyle = "#069";
        ctx.fillText("Hello, world!", 2, 15);
        ctx.fillStyle = "rgba(102, 204, 0, 0.7)";
        ctx.fillText("Hello, world!", 4, 17);
        
        return canvas.toDataURL().replace('data:image/png;base64,', '').substring(0, 32);
    } catch (e) {
        console.error('Canvas fingerprint error:', e);
        return '';
    }
}

// ฟังก์ชันสร้าง WebGL Fingerprint
async function getWebGLFingerprint() {
    try {
        const canvas = document.createElement('canvas');
        const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
        if (!gl) return '';
        
        const info = {
            vendor: gl.getParameter(gl.VENDOR),
            renderer: gl.getParameter(gl.RENDERER),
            webglVersion: gl.getParameter(gl.VERSION)
        };
        
        return JSON.stringify(info).substring(0, 64);
    } catch (e) {
        console.error('WebGL fingerprint error:', e);
        return '';
    }
}

// ฟังก์ชันทำ SHA-256
async function sha256(str) {
    try {
        // แปลง string เป็น ArrayBuffer
        const buffer = new TextEncoder().encode(str);
        // สร้าง hash
        const hashBuffer = await crypto.subtle.digest('SHA-256', buffer);
        // แปลง ArrayBuffer เป็น Array
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        // แปลง Array ของ bytes เป็น hex string
        const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
        return hashHex;
    } catch (e) {
        console.error("SHA-256 error:", e);
        // Fallback สร้าง hash อย่างง่าย
        return "fallback-" + Date.now() + "-" + Math.random().toString(36).substring(2);
    }
}

// ฟังก์ชันสร้าง fingerprint
async function generateFingerprint() {
    const fingerData = {
        userAgent: navigator.userAgent,
        language: navigator.language,
        screenResolution: `${window.screen.width}x${window.screen.height}`,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        platform: navigator.platform,
        doNotTrack: navigator.doNotTrack,
        colorDepth: window.screen.colorDepth,
    };
    
    // แปลงข้อมูลเป็น JSON string
    const dataString = JSON.stringify(fingerData);
    
    // สร้าง hash จากข้อมูล
    return sha256(dataString);
}

// Export ฟังก์ชันสำหรับให้ไฟล์อื่นใช้งาน
window.FingerprintJS = {
    getCanvasFingerprint: getCanvasFingerprint,
    getWebGLFingerprint: getWebGLFingerprint,
    sha256: sha256,
    generate: generateFingerprint,
    
    // ฟังก์ชัน collectDeviceInfo
    collectDeviceInfo: async function() {
        try {
            // ข้อมูลพื้นฐานสำหรับส่งเป็น form fields
            const basicData = {
                'screen_resolution': String(window.screen.width + 'x' + window.screen.height),
                'color_depth': String(window.screen.colorDepth),
                'timezone': String(Intl.DateTimeFormat().resolvedOptions().timeZone),
                'language': String(navigator.language || navigator.userLanguage || ''),
                'dnt': String(navigator.doNotTrack || window.doNotTrack || navigator.msDoNotTrack || 'unknown')
            };
            
            // สร้าง fingerprint สำหรับเชื่อมโยงอุปกรณ์
            const fingerprint = await generateFingerprint(); // ใช้ชื่อฟังก์ชันโดยตรงแทน this.generate()
            basicData['fingerprint'] = String(fingerprint);
            
            // ข้อมูลเพิ่มเติมสำหรับวิเคราะห์อุปกรณ์
            const canvasFp = await getCanvasFingerprint(); // ใช้ชื่อฟังก์ชันโดยตรงแทน this.getCanvasFingerprint()
            const webglFp = await getWebGLFingerprint(); // ใช้ชื่อฟังก์ชันโดยตรงแทน this.getWebGLFingerprint()
            
            // ข้อมูลปลั๊กอิน
            let plugins = '';
            if (navigator.plugins && navigator.plugins.length) {
                const pluginsArray = Array.from(navigator.plugins).map(p => p.name);
                plugins = JSON.stringify(pluginsArray);
            }
            
            basicData['canvas_fp'] = String(canvasFp);
            basicData['webgl_fp'] = String(webglFp);
            basicData['plugins'] = String(plugins);
            
            // ข้อมูลอุปกรณ์แบบละเอียด
            const fullInfo = {
                type: String(navigator.userAgent.indexOf('Mobile') !== -1 ? 'Mobile' : 'Desktop'),
                device: String(navigator.platform),
                browser: String((function() {
                    const ua = navigator.userAgent;
                    if (ua.indexOf('Firefox') !== -1) return 'Firefox';
                    if (ua.indexOf('Chrome') !== -1) return 'Chrome';
                    if (ua.indexOf('Safari') !== -1) return 'Safari';
                    if (ua.indexOf('Edge') !== -1) return 'Edge';
                    if (ua.indexOf('MSIE') !== -1 || ua.indexOf('Trident/') !== -1) return 'IE';
                    return 'Unknown';
                })()),
                browserVersion: String((function() {
                    const ua = navigator.userAgent;
                    let match;
                    if ((match = ua.match(/(Firefox|Chrome|Safari|Edge|MSIE|Trident)\/?(\d+)/))) {
                        return match[2];
                    }
                    return '';
                })()),
                os: String(navigator.platform),
                screenResolution: String(basicData.screen_resolution),
                colorDepth: String(basicData.color_depth),
                timezone: String(basicData.timezone),
                language: String(basicData.language),
                plugins: String(plugins),
                doNotTrack: String(basicData.dnt),
                canvasFingerprint: String(canvasFp),
                webglFingerprint: String(webglFp),
                userAgent: String(navigator.userAgent),
                fingerprint: String(fingerprint)
            };
            
            console.log('Collected device info - basic data types:', 
                Object.keys(basicData).map(k => `${k}: ${typeof basicData[k]}`));
            
            return { basicData, fullInfo };
        } catch (error) {
            console.error('Error collecting device info:', error);
            // ส่งค่า default กลับไปหากเกิดข้อผิดพลาด
            return {
                basicData: { 'fingerprint': String(await generateFingerprint()) }, // ใช้ชื่อฟังก์ชันโดยตรง
                fullInfo: { error: 'Failed to collect device info' }
            };
        }
    }
};