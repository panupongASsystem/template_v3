<style>
    :root {
        /* สีจากภาพที่ส่งมา */
        --white: #FFFFFF;
        --snow: #FFFAFA;
        --honeydew: #F0FFF0;
        --mintcream: #F5FFFA;
        --azure: #F0FFFF;
        --aliceblue: #F0F8FF;
        --ghostwhite: #F8F8FF;
        --whitesmoke: #F5F5F5;
        --seashell: #FFF5EE;
        --beige: #F5F5DC;
        --oldlace: #FDF5E6;
        --floralwhite: #FFFAF0;
        --ivory: #FFFFF0;
        --antiquewhite: #FAEBD7;
        --linen: #FAF0E6;
        --lavenderblush: #FFF0F5;
        --mistyrose: #FFE4E1;

        /* สีเพิ่มเติมสำหรับองค์ประกอบต่างๆ */
        --primary: #F0F8FF;
        /* AliceBlue */
        --primary-dark: #90A4AE;
        --secondary: #FFF0F5;
        /* LavenderBlush */
        --accent: #F5F5DC;
        /* Beige */
        --text-color: #455A64;
        --form-bg: #FFFFFF;
        --input-bg: #F5F5F5;
        /* WhiteSmoke */
        --border-color: #E0E0E0;
        --form-shadow: rgba(0, 0, 0, 0.05);
        --error: #FF5252;
        --success: #4CAF50;

        /* สีสำหรับการ์ด */
        --tax-card-bg: #F0FFF0;
        /* Honeydew */
        --queue-card-bg: #F0F8FF;
        /* AliceBlue */
        --tax-accent: #66BB6A;
        --queue-accent: #64B5F6;

        /* สีสำหรับ header และ footer */
        --header-bg: #FFFFFF;
        --header-border: #E0E0E0;
        --nav-link-color: #546E7A;
        --nav-link-hover: #37474F;
        --nav-link-active: #263238;
        --footer-bg: #F5F5F5;
        --footer-text: #78909C;
        --footer-link: #546E7A;
        --footer-border: #E0E0E0;
    }

    body {
        font-family: 'Prompt', sans-serif;
        background-color: var(--whitesmoke);
        color: var(--text-color);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    main {
        flex: 1;
    }

    /* Header Styles */
    .header {
        background-color: var(--header-bg);
    }

    .header-logo {
        transition: transform 0.3s;
    }

    .header-logo-link:hover .header-logo {
        transform: scale(1.05);
    }

    .header-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0;
    }

    .header-subtitle {
        font-size: 0.9rem;
        color: var(--primary-dark);
    }

    /* Navbar Styles */
    .navbar-light .navbar-nav .nav-link {
        color: var(--nav-link-color);
        font-weight: 500;
        padding: 0.8rem 1rem;
        transition: color 0.3s, background-color 0.3s;
        border-bottom: 3px solid transparent;
    }

    .navbar-light .navbar-nav .nav-link:hover {
        color: var(--nav-link-hover);
        background-color: rgba(144, 164, 174, 0.1);
    }

    .navbar-light .navbar-nav .nav-link.active {
        color: var(--nav-link-active);
        border-bottom-color: var(--primary-dark);
    }

    .navbar-light .navbar-nav .nav-link i {
        margin-right: 0.3rem;
    }

    /* Utilities */
    .bg-light-custom {
        background-color: var(--whitesmoke);
    }

    .text-primary-custom {
        color: var(--primary-dark) !important;
    }

    .btn-primary-custom {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        color: white;
    }

    .btn-primary-custom:hover {
        background-color: #607D8B;
        border-color: #607D8B;
        color: white;
    }

    /* Content Styles */
    .bg-pages {
        background-color: var(--form-bg);
        border-radius: 10px;
        box-shadow: 0 5px 20px var(--form-shadow);
        padding: 2rem;
        margin-top: 1.5rem;
        margin-bottom: 2rem;
        border-top: 5px solid var(--primary);
    }

    .container-pages-news {
        max-width: 1200px;
        margin: 0 auto;
    }

    .font-pages-head {
        font-size: 2rem;
        font-weight: 500;
        color: var(--text-color);
        padding-bottom: 5px;
        border-bottom: 3px solid var(--secondary);
        display: inline-block;
    }

    /* ส่วนข้อความต้อนรับ */
    .welcome-section {
        text-align: center;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background-color: var(--ghostwhite);
        border-radius: 10px;
    }

    .user-greeting {
        background-color: var(--primary-dark);
        color: white;
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 50px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        font-weight: 500;
    }

    .user-greeting i {
        margin-right: 8px;
        font-size: 1.2rem;
    }

    .service-header {
        font-size: 1.75rem;
        font-weight: 500;
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }

    .service-subheader {
        color: #78909C;
        max-width: 700px;
        margin: 0 auto;
    }

    /* แจ้งเตือนหลัง Login สำเร็จ */
    .login-alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        background-color: #E8F5E9;
        color: var(--success);
        border-left: 4px solid var(--success);
    }

    .login-alert i {
        font-size: 1.2rem;
        margin-right: 8px;
    }

    .login-text {
        color: var(--primary-dark);
        font-weight: 500;
        text-decoration: none;
        padding: 2px 5px;
        border-radius: 3px;
        background-color: var(--primary);
        transition: all 0.3s;
    }

    .login-text:hover {
        color: white;
        background-color: var(--primary-dark);
    }

    /* ส่วนของการ์ดระบบบริการ */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .service-card {
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
        border-top: 5px solid;
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }

    .tax-card {
        border-color: var(--tax-accent);
        background-color: var(--tax-card-bg);
    }

    .queue-card {
        border-color: var(--queue-accent);
        background-color: var(--queue-card-bg);
    }

    .service-icon {
        font-size: 2.5rem;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .tax-card .service-icon {
        color: var(--tax-accent);
    }

    .queue-card .service-icon {
        color: var(--queue-accent);
    }

    .service-content {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .service-content h4 {
        margin-top: 0;
        font-size: 1.5rem;
        font-weight: 500;
        color: var(--text-color);
        margin-bottom: 0.75rem;
    }

    .service-content p {
        color: #78909C;
        margin-bottom: 1.5rem;
    }

    .service-features {
        margin-bottom: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .service-features span {
        display: flex;
        align-items: center;
        color: #546E7A;
    }

    .service-features i {
        margin-right: 8px;
        font-size: 0.9rem;
    }

    .tax-card .service-features i {
        color: var(--tax-accent);
    }

    .queue-card .service-features i {
        color: var(--queue-accent);
    }

    .btn-service {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 20px;
        font-weight: 500;
        border-radius: 6px;
        color: white;
        transition: all 0.3s;
        margin-top: auto;
    }

    .tax-btn {
        background-color: var(--tax-accent);
        border-color: var(--tax-accent);
    }

    .tax-btn:hover {
        background-color: #43A047;
        border-color: #43A047;
    }

    .queue-btn {
        background-color: var(--queue-accent);
        border-color: var(--queue-accent);
    }

    .queue-btn:hover {
        background-color: #42A5F5;
        border-color: #42A5F5;
    }

    /* Footer Styles */
    .footer {
        background-color: var(--footer-bg);
        color: var(--footer-text);
    }

    .footer-title {
        color: var(--text-color);
        font-weight: 600;
        font-size: 1.1rem;
    }

    .footer-subtitle {
        color: var(--primary-dark);
    }

    .footer-heading {
        color: var(--text-color);
        font-weight: 600;
        font-size: 1.1rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .footer-heading:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background-color: var(--primary-dark);
        border-radius: 3px;
    }

    .footer-links li {
        margin-bottom: 0.5rem;
    }

    .footer-links a {
        color: var(--footer-link);
        text-decoration: none;
        transition: color 0.3s;
        display: inline-block;
    }

    .footer-links a:hover {
        color: var(--text-color);
        transform: translateX(5px);
    }

    .social-links {
        display: flex;
        gap: 1rem;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background-color: var(--primary);
        color: var(--primary-dark);
        border-radius: 50%;
        transition: all 0.3s;
        text-decoration: none;
    }

    .social-link:hover {
        background-color: var(--primary-dark);
        color: white;
        transform: translateY(-3px);
    }

    .policy-links a {
        color: var(--footer-link);
        text-decoration: none;
        transition: color 0.3s;
    }

    .policy-links a:hover {
        color: var(--text-color);
        text-decoration: underline;
    }

    /* Back to Top Button */
    .back-to-top {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: none;
        z-index: 999;
        width: 40px;
        height: 40px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .header-title {
            font-size: 1.2rem;
        }

        .header-subtitle {
            font-size: 0.8rem;
        }

        .navbar-light .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
            border-bottom: none;
            border-left: 3px solid transparent;
        }

        .navbar-light .navbar-nav .nav-link.active {
            border-bottom-color: transparent;
            border-left-color: var(--primary-dark);
            background-color: rgba(144, 164, 174, 0.1);
        }

        .services-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .service-card {
            max-width: 100%;
        }
    }

    /* สไตล์ทั่วไป (คงไว้เหมือนเดิม) */
    .bg-pages {
        background-color: #f8f9fa;
        padding: 3rem 0;
    }

    .container-pages-news {
        max-width: 1140px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* สไตล์ส่วนต้อนรับ */
    .welcome-section {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .user-greeting {
        background-color: #e8f4fe;
        display: inline-block;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        margin-bottom: 1.5rem;
        color: rgb(29, 30, 32);
        font-weight: 500;
    }

    .user-greeting i {
        margin-right: 0.5rem;
    }

    .service-header {
        font-size: 1.8rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .service-subheader {
        color: #6c757d;
        max-width: 600px;
        margin: 0 auto;
    }

    .login-text {
        color: #0d6efd;
        font-weight: 500;
        text-decoration: none;
    }

    .login-text:hover {
        text-decoration: underline;
    }

    .login-alert {
        max-width: 600px;
        margin: 0 auto 2rem;
        text-align: center;
    }

    /* สไตล์กริดบริการ */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        max-width: 800px;
        margin: 0 auto;
    }

    .service-card {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .service-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 2rem;
    }

    .tax-card .service-icon {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .queue-card .service-icon {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .service-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .service-content h4 {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .service-content p {
        color: #6c757d;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .btn-service {
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .tax-btn {
        background-color: #198754;
        color: white;
    }

    .tax-btn:hover {
        background-color: #146c43;
        color: white;
    }

    .queue-btn {
        background-color: #0d6efd;
        color: white;
    }

    .queue-btn:hover {
        background-color: #0a58ca;
        color: white;
    }

    /* สไตล์สำหรับ Modal Timeout Warning */
    .timeout-icon {
        font-size: 3.5rem;
        color: #fd7e14;
        animation: pulse 1.5s infinite;
    }

    .timeout-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 1rem;
    }

    .timeout-message {
        font-size: 1.1rem;
        color: #495057;
        line-height: 1.6;
    }

    #countdown {
        font-weight: bold;
        color: #fd7e14;
        font-size: 1.2rem;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    #timeoutProgressBar {
        transition: width 1s linear;
    }

    /* สไตล์สำหรับปุ่มในโมดัล */
    #stayLoggedInBtn {
        background-color: #0d6efd;
        border-color: #0d6efd;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    #stayLoggedInBtn:hover {
        background-color: #0a58ca;
        border-color: #0a58ca;
        transform: translateY(-2px);
    }

    /* สไตล์เรสพอนซีฟ */
    @media (max-width: 768px) {
        .service-header {
            font-size: 1.5rem;
        }

        .services-grid {
            grid-template-columns: 1fr;
            max-width: 400px;
        }

        .service-card {
            padding: 1.2rem;
        }
    }
	
	
	
	
	
	
	
	/* CSS สำหรับ Notification System */

/* 🔔 Notification Bell Styles */
.notification-bell-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.notification-bell {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, rgba(139, 157, 195, 0.95), rgba(139, 157, 195, 0.85));
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 12px 40px rgba(139, 157, 195, 0.2);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.notification-bell:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 20px 60px rgba(139, 157, 195, 0.25);
    background: linear-gradient(135deg, #8B9DC3, #6B7FA3);
}

.notification-bell i {
    font-size: 1.5rem;
    color: white;
    transition: all 0.3s ease;
}

.notification-bell:hover i {
    animation: ringBell 0.5s ease-in-out;
}

@keyframes ringBell {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-10deg); }
    75% { transform: rotate(10deg); }
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: linear-gradient(135deg, #ff4757, #ff3742);
    color: white;
    border-radius: 50%;
    min-width: 22px;
    height: 22px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(255, 71, 87, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Notification Dropdown */
.notification-dropdown {
    position: absolute;
    top: 65px;
    right: 0;
    width: 380px;
    max-height: 500px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(139, 157, 195, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.3);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.notification-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.notification-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(232, 236, 244, 0.5);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, rgba(250, 251, 255, 0.8), rgba(240, 244, 248, 0.8));
}

.notification-header h6 {
    margin: 0;
    font-weight: 600;
    color: #5A6C7D;
    font-size: 1.1rem;
}

.notification-count {
    background: linear-gradient(135deg, #8B9DC3, #6B7FA3);
    color: white;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.notification-list {
    max-height: 320px;
    overflow-y: auto;
    padding: 8px 0;
}

.notification-item {
    padding: 16px 24px;
    border-bottom: 1px solid rgba(232, 236, 244, 0.3);
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.notification-item:hover {
    background: linear-gradient(135deg, rgba(139, 157, 195, 0.05), rgba(139, 157, 195, 0.08));
    transform: translateX(3px);
}

.notification-item.unread {
    background: linear-gradient(135deg, rgba(139, 157, 195, 0.08), rgba(139, 157, 195, 0.05));
    border-left: 4px solid #8B9DC3;
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    top: 20px;
    right: 20px;
    width: 8px;
    height: 8px;
    background: linear-gradient(135deg, #ff4757, #ff3742);
    border-radius: 50%;
    box-shadow: 0 0 10px rgba(255, 71, 87, 0.5);
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(139, 157, 195, 0.15), rgba(139, 157, 195, 0.25));
    display: flex;
    align-items: center;
    justify-content: center;
    color: #8B9DC3;
    flex-shrink: 0;
    margin-top: 2px;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 600;
    color: #5A6C7D;
    font-size: 0.95rem;
    margin-bottom: 4px;
    line-height: 1.3;
}

.notification-message {
    color: rgba(90, 108, 125, 0.8);
    font-size: 0.85rem;
    line-height: 1.4;
    margin-bottom: 6px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.notification-time {
    color: rgba(90, 108, 125, 0.6);
    font-size: 0.75rem;
    font-weight: 400;
}

.notification-empty {
    text-align: center;
    padding: 40px 20px;
    color: rgba(90, 108, 125, 0.6);
}

.notification-empty i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}

.notification-footer {
    padding: 16px 24px;
    border-top: 1px solid rgba(232, 236, 244, 0.5);
    text-align: center;
    background: linear-gradient(135deg, rgba(250, 251, 255, 0.8), rgba(240, 244, 248, 0.8));
}

.view-all-link {
    color: #8B9DC3;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.view-all-link:hover {
    color: #6B7FA3;
    text-decoration: none;
    transform: translateX(3px);
}

/* Responsive */
@media (max-width: 768px) {
    .notification-bell-container {
        top: 15px;
        right: 15px;
    }
    
    .notification-bell {
        width: 50px;
        height: 50px;
    }
    
    .notification-bell i {
        font-size: 1.3rem;
    }
    
    .notification-dropdown {
        width: 320px;
        right: -10px;
    }
}

@media (max-width: 480px) {
    .notification-dropdown {
        width: 280px;
        right: -20px;
    }
}
</style>