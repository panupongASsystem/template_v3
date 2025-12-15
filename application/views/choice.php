<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->session->userdata('tenant_name'); ?> - สมาร์ทออฟฟิต</title>
    
    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Kanit:300,400,500,600' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Styles -->
    <style>
        /* 🚨 REQUIRED: Modal z-index fixes - ต้องมีในทุกหน้า */
        .modal {
            z-index: 9999 !important;
        }
        .modal-backdrop {
            z-index: 9998 !important;
        }
        .modal-dialog {
            z-index: 10000 !important;
            position: relative;
        }
        .modal-content {
            position: relative;
            z-index: 10001 !important;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        /* ✅ Email Selection Modal Styles */
        .email-selection-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        .email-selection-modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .email-modal-content {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            padding: 2rem;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: emailModalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }

        @keyframes emailModalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(-50px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes emailModalSlideOut {
            from {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
            to {
                opacity: 0;
                transform: scale(0.8) translateY(50px);
            }
        }

        .email-modal-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .email-modal-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            animation: emailIconPulse 2s infinite;
        }

        @keyframes emailIconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .email-modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .email-modal-subtitle {
            font-size: 0.95rem;
            color: #6b7280;
        }

        .email-options {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .email-option-btn {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
        }

        .email-option-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .email-option-btn:hover::before {
            left: 100%;
        }

        .email-option-btn:hover {
            border-color: #3b82f6;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
            text-decoration: none;
        }

        .email-option-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .email-option-icon.primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        .email-option-icon.secondary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .email-option-text {
            flex: 1;
            text-align: left;
        }

        .email-option-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .email-option-desc {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .email-option-arrow {
            font-size: 1.2rem;
            color: #9ca3af;
            transition: all 0.3s ease;
        }

        .email-option-btn:hover .email-option-arrow {
            color: #3b82f6;
            transform: translateX(5px);
        }

        .email-modal-close {
            background: #f3f4f6;
            border: none;
            color: #6b7280;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 0.5rem;
        }

        .email-modal-close:hover {
            background: #e5e7eb;
            color: #374151;
            transform: translateY(-2px);
        }

        /* ✅ Force 2FA Modal - สำหรับ Super Admin บังคับ */
        .force-2fa-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            z-index: 99999 !important;
        }

        .force-2fa-modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .force-2fa-content {
            background: white;
            border-radius: 1rem;
            padding: 2.5rem;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: force2faSlideIn 0.5s ease;
            position: relative;
        }

        @keyframes force2faSlideIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .force-2fa-icon {
            font-size: 4rem;
            color: #dc2626;
            margin-bottom: 1.5rem;
            animation: warningPulse 2s infinite;
        }

        @keyframes warningPulse {
            0%, 100% { 
                color: #dc2626; 
                transform: scale(1);
            }
            50% { 
                color: #ef4444; 
                transform: scale(1.05);
            }
        }

        .force-2fa-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .force-2fa-message {
            font-size: 1.1rem;
            color: #4b5563;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .force-2fa-highlight {
            background: linear-gradient(135deg, #fef3c7, #fbbf24);
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
            border-left: 4px solid #f59e0b;
            font-weight: 600;
            color: #92400e;
        }

        .force-2fa-setup-btn {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 1rem;
        }

        .force-2fa-setup-btn:hover {
            background: linear-gradient(135deg, #b91c1c, #991b1b);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        }

        .force-2fa-logout-btn {
            background: #6b7280;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .force-2fa-logout-btn:hover {
            background: #4b5563;
            transform: translateY(-1px);
        }

        /* ✅ Overlay to prevent interaction */
        .security-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99998;
            display: none;
        }

        .security-overlay.active {
            display: block;
        }

        /* ✅ Disable all interactions when force modal is shown */
        body.force-2fa-active {
            overflow: hidden;
        }

        body.force-2fa-active .container-custom,
        body.force-2fa-active .grid,
        body.force-2fa-active .user-header {
            pointer-events: none;
            filter: blur(2px);
            opacity: 0.7;
        }

        /* เก็บ CSS เดิมทั้งหมด */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            color: #1e293b;
            line-height: 1.5;
        }

        .container-custom {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* User Profile Header */
        .user-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem 2rem;
            background: rgba(248, 250, 252, 0.8);
            border-radius: 1rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .user-details h5 {
            font-weight: 600;
            color: #334155;
            margin: 0;
        }

        .user-details p {
            color: #64748b;
            margin: 0;
            font-size: 0.9rem;
        }

        .profile-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-profile-view {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        .btn-profile-view:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            color: white;
        }

        .btn-profile-edit {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-profile-edit:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .header {
            text-align: center;
            margin-bottom: 4rem;
            padding-top: 1rem;
        }

        .logo {
            width: 140px;
            height: 140px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            display: block;
            background: white;
            padding: 5px;
        }

        .site-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .site-subtitle {
            font-size: 1.25rem;
            color: #64748b;
            font-weight: 300;
        }

        /* Grid Layout */
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin: 0 auto;
            max-width: 1400px;
            padding: 0 1rem;
        }

        @media (max-width: 1200px) {
            .grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .user-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        @media (max-width: 600px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .email-modal-content {
                padding: 1.5rem;
                max-width: 95%;
            }

            .email-modal-title {
                font-size: 1.3rem;
            }

            .email-option-btn {
                padding: 1rem;
            }

            .email-option-icon {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }

            .email-option-title {
                font-size: 1rem;
            }

            .force-2fa-content {
                padding: 2rem;
                margin: 1rem;
            }

            .force-2fa-title {
                font-size: 1.5rem;
            }

            .force-2fa-message {
                font-size: 1rem;
            }
        }

        /* Card Wrapper */
        .card-wrapper {
            position: relative;
            height: 220px;
            width: 100%;
        }

        /* Card Styles */
        .card {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(248, 250, 252, 0.8);
            border-radius: 1.5rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 1.5rem;
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        .card-blue::before { background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.25)); }
        .card-purple::before { background: linear-gradient(135deg, rgba(147, 51, 234, 0.15), rgba(126, 34, 206, 0.25)); }
        .card-green::before { background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.25)); }
        .card-indigo::before { background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(79, 70, 229, 0.25)); }
        .card-pink::before { background: linear-gradient(135deg, rgba(236, 72, 153, 0.15), rgba(219, 39, 119, 0.25)); }
        .card-yellow::before { background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.25)); }
        .card-red::before { background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.25)); }
        .card-orange::before { background: linear-gradient(135deg, rgba(249, 115, 22, 0.15), rgba(234, 88, 12, 0.25)); }
        .card-teal::before { background: linear-gradient(135deg, rgba(20, 184, 166, 0.15), rgba(13, 148, 136, 0.25)); }
        .card-emerald::before { background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(4, 120, 87, 0.25)); }
        .card-cyan::before { background: linear-gradient(135deg, rgba(6, 182, 212, 0.15), rgba(8, 145, 178, 0.25)); }
        .card-violet::before { background: linear-gradient(135deg, rgba(124, 58, 237, 0.15), rgba(109, 40, 217, 0.25)); }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card:hover .icon-circle {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .card-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        /* Version Badge */
        .version-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            z-index: 10;
        }

        .version-badge.trial {
            background-color: #FEF3C7;
            color: #92400E;
            border: 1px solid #F59E0B;
        }

        .version-badge.full {
            background-color: #D1FAE5;
            color: #065F46;
            border: 1px solid #10B981;
        }

        /* Icon Circle */
        .icon-circle {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        /* Card Title */
        .card-title {
            font-size: 1.25rem;
            font-weight: 500;
            color: #334155;
            line-height: 1.6;
        }

        /* Card Icon */
        .card-icon {
            font-size: 2rem;
            display: block;
            background: linear-gradient(45deg, #4f46e5, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .email-icon {
            background: linear-gradient(45deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 4px rgba(59, 130, 246, 0.2));
        }

        .card-blue .email-icon {
            background: linear-gradient(45deg, #1e40af, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logout-container {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin: 3rem auto 2rem;
            flex-wrap: wrap;
        }

        .home-button {
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 2rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(16, 185, 129, 0.2);
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.1);
        }

        .home-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
            color: white;
            text-decoration: none;
            background: linear-gradient(135deg, #059669, #047857);
        }

        .home-icon-circle {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .home-button:hover .home-icon-circle {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .home-icon {
            font-size: 1.25rem;
            color: white;
        }

        .logout-button {
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            color: #64748b;
            padding: 0.75rem 2rem;
            border-radius: 2rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .logout-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            color: #334155;
            text-decoration: none;
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        }

        .logout-icon-circle {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .logout-button:hover .logout-icon-circle {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .logout-icon {
            font-size: 1.25rem;
            background: linear-gradient(45deg, #64748b, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .support {
            text-align: center;
            margin-top: 2rem;
            padding: 1rem;
        }

        .support-container {
            display: inline-flex;
            align-items: center;
            background: white;
            padding: 1.25rem 2.5rem;
            border-radius: 9999px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .support-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .support-text {
            color: #64748b;
            margin-right: 0.75rem;
            font-weight: 300;
        }

        .line-icon {
            color: #00b900;
            font-size: 1.5rem;
            margin: 0 0.5rem;
        }

        .line-link {
            color: #00b900;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .line-link:hover {
            color: #009900;
            text-decoration: underline;
        }

        /* Error Modal Styles */
        .error-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
        }
        
        .error-modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 25px;
            width: 400px;
            max-width: 90%;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            animation: errorSlideIn 0.3s ease;
        }
        
        @keyframes errorSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes errorSlideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
        
        .error-icon {
            font-size: 3rem;
            color: #EF4444;
            margin-bottom: 15px;
        }
        
        .error-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 10px;
        }
        
        .error-message {
            font-size: 1.1rem;
            color: #4B5563;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .error-close-btn {
            background: #EF4444;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .error-close-btn:hover {
            background: #DC2626;
            transform: translateY(-2px);
        }

        /* Security Badge Styles */
        .security-status {
            margin-top: 8px;
        }

        .security-badge {
            padding: 6px 12px;
            font-size: 0.8rem;
            font-weight: 500;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }

        .security-badge i {
            font-size: 0.9rem;
        }

        .btn-profile-security {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .btn-profile-security:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            color: white;
        }

        .btn-profile-security-active {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
        }

        .btn-profile-security-active:hover {
            background: linear-gradient(135deg, #047857, #065f46);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
            color: white;
        }

        .security-notification-dot {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 12px;
            height: 12px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid white;
            animation: securityBlink 1.5s infinite;
        }

        @keyframes securityBlink {
            0%, 50% { 
                opacity: 1; 
                transform: scale(1);
            }
            51%, 100% { 
                opacity: 0.3;
                transform: scale(0.8);
            }
        }

        .pulsing-warning {
            animation: pulseWarning 2s infinite;
        }

        @keyframes pulseWarning {
            0% { 
                opacity: 1; 
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4);
            }
            50% { 
                opacity: 0.8; 
                transform: scale(1.02);
                box-shadow: 0 0 0 10px rgba(245, 158, 11, 0);
            }
            100% { 
                opacity: 1; 
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            }
        }

        .badge.bg-success {
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
        }

        .smart-office-notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #ff4757, #ff3742);
            color: white;
            border-radius: 50%;
            min-width: 35px;
            height: 35px;
            font-size: 0.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);
            animation: smartOfficePulse 2s infinite;
            z-index: 999;
            line-height: 1;
            letter-spacing: -0.5px;
        }

        @keyframes smartOfficePulse {
            0% { 
                transform: scale(1); 
                box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);
            }
            50% { 
                transform: scale(1.15); 
                box-shadow: 0 6px 20px rgba(255, 71, 87, 0.6);
            }
            100% { 
                transform: scale(1); 
                box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);
            }
        }

        .card-wrapper.has-notification .card:hover {
            box-shadow: 0 15px 35px rgba(255, 71, 87, 0.15);
        }

        @media (max-width: 768px) {
            .user-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .profile-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-profile {
                width: 100%;
                justify-content: center;
                margin-bottom: 0.5rem;
            }
            
            .security-badge {
                font-size: 0.75rem;
                padding: 4px 8px;
            }
            
            .security-notification-dot {
                top: -3px;
                right: -3px;
                width: 10px;
                height: 10px;
            }

            .logout-container {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
            
            .home-button,
            .logout-button {
                width: 100%;
                max-width: 280px;
                justify-content: center;
                padding: 1rem 2rem;
            }

            .smart-office-notification-badge {
                min-width: 20px;
                height: 20px;
                font-size: 0.7rem;
                top: -6px;
                right: -6px;
                border-width: 2px;
            }
        }

        @media (max-width: 480px) {
            .home-button,
            .logout-button {
                max-width: 240px;
                padding: 0.875rem 1.5rem;
                font-size: 0.9rem;
            }
            
            .home-icon-circle,
            .logout-icon-circle {
                width: 32px;
                height: 32px;
                margin-right: 0.5rem;
            }
            
            .home-icon,
            .logout-icon {
                font-size: 1.1rem;
            }

            .smart-office-notification-badge {
                min-width: 18px;
                height: 18px;
                font-size: 0.65rem;
                top: -5px;
                right: -5px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { 
                opacity: 0;
                transform: translateY(30px); 
            }
            to { 
                opacity: 1;
                transform: translateY(0); 
            }
        }
    </style>
	
	<style>
/* ========================================
   DASHBOARD TOUR STYLES
   ======================================== */

/* Tour Button */
.start-dashboard-tour-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 25px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 999;
    font-family: 'Kanit', sans-serif;
}

.start-dashboard-tour-btn:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 12px 28px rgba(102, 126, 234, 0.6);
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.start-dashboard-tour-btn:active {
    transform: translateY(-1px) scale(1.02);
}

.start-dashboard-tour-btn i {
    font-size: 20px;
    animation: tourPulse 2s ease-in-out infinite;
}

@keyframes tourPulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2);
    }
}

/* Tour Overlay */
.tour-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(0px);
    z-index: 9990;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.tour-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Spotlight */
.spotlight {
    position: absolute;
    border: 3px solid #667eea;
    border-radius: 12px;
    box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.3),
                0 0 30px rgba(102, 126, 234, 0.8),
                inset 0 0 20px rgba(102, 126, 234, 0.1);
    z-index: 9991;
    pointer-events: none;
    transition: all 0.3s ease;
    background: transparent;
}

.spotlight.pulse {
    animation: spotlightPulse 2s ease-in-out infinite;
}

@keyframes spotlightPulse {
    0%, 100% {
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.3),
                    0 0 30px rgba(102, 126, 234, 0.8),
                    inset 0 0 20px rgba(102, 126, 234, 0.1);
    }
    50% {
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.3),
                    0 0 40px rgba(102, 126, 234, 1),
                    inset 0 0 30px rgba(102, 126, 234, 0.15);
    }
}

/* Label */
.tour-label {
    position: absolute;
    padding: 12px 20px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    white-space: nowrap;
    z-index: 9992;
    pointer-events: none;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.5);
    animation: labelFloat 0.5s ease-out;
    font-family: 'Kanit', sans-serif;
}

@keyframes labelFloat {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Tooltip */
.tour-tooltip {
    position: absolute;
    width: 450px;
    max-width: 90vw;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    z-index: 9993;
    animation: tooltipSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    font-family: 'Kanit', sans-serif;
}

@keyframes tooltipSlideIn {
    from {
        opacity: 0;
        transform: scale(0.8) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.tour-tooltip-header {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px 25px 15px;
    border-bottom: 2px solid #f0f0f0;
}

.tour-tooltip-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.tour-tooltip-icon.primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.tour-tooltip-icon.success {
    background: linear-gradient(135deg, #11998e, #38ef7d);
    color: white;
}

.tour-tooltip-icon.warning {
    background: linear-gradient(135deg, #f093fb, #f5576c);
    color: white;
}

.tour-tooltip-icon.error {
    background: linear-gradient(135deg, #fa709a, #fee140);
    color: white;
}

.tour-tooltip-title {
    font-size: 20px;
    font-weight: 600;
    color: #2d3748;
    flex: 1;
}

.tour-tooltip-content {
    padding: 20px 25px;
    font-size: 15px;
    line-height: 1.8;
    color: #4a5568;
}

.tour-tooltip-content strong {
    color: #2d3748;
    font-weight: 600;
}

.tour-tooltip-footer {
    padding: 15px 25px 20px;
    border-top: 2px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.tour-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.tour-progress-dots {
    display: flex;
    gap: 8px;
}

.tour-progress-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #e2e8f0;
    transition: all 0.3s ease;
}

.tour-progress-dot.active {
    width: 30px;
    border-radius: 5px;
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.tour-progress-text {
    font-size: 14px;
    font-weight: 600;
    color: #667eea;
}

.tour-footer-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.tour-buttons {
    display: flex;
    gap: 10px;
}

.tour-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'Kanit', sans-serif;
}

.dashboard-tour-skip {
    background: #fff;
    color: #e53e3e;
    border: 2px solid #e53e3e;
}

.dashboard-tour-skip:hover {
    background: #fff5f5;
    transform: translateY(-2px);
}

.dashboard-tour-prev,
.dashboard-tour-close {
    background: #e2e8f0;
    color: #4a5568;
}

.dashboard-tour-prev:hover,
.dashboard-tour-close:hover {
    background: #cbd5e0;
    transform: translateY(-2px);
}

.dashboard-tour-next,
.dashboard-tour-finish {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.dashboard-tour-next:hover,
.dashboard-tour-finish:hover {
    background: linear-gradient(135deg, #764ba2, #667eea);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .start-dashboard-tour-btn {
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        padding: 0;
        justify-content: center;
        border-radius: 50%;
    }
    
    .start-dashboard-tour-btn span {
        display: none;
    }
    
    .tour-tooltip {
        width: calc(100vw - 40px);
    }
    
    .tour-tooltip-header {
        padding: 15px 20px 12px;
    }
    
    .tour-tooltip-icon {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
    
    .tour-tooltip-title {
        font-size: 18px;
    }
    
    .tour-tooltip-content {
        padding: 15px 20px;
        font-size: 14px;
    }
    
    .tour-tooltip-footer {
        gap: 12px;
    }
    
    .tour-footer-actions {
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }
    
    .tour-buttons {
        width: 100%;
    }
    
    .tour-btn {
        flex: 1;
        justify-content: center;
    }
    
    .dashboard-tour-skip {
        width: 100%;
    }
}
		
		
		/* ส่วนข้อกำหนดและเงื่อนไข */
.membership-terms-section {
    width: 100%;
    padding: 15px 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.membership-terms-link {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    color: #64748b;
    text-decoration: none;
    border-radius: 50px;
    font-size: 15px;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.membership-terms-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(79, 172, 254, 0.4);
    background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
}

/* ส่วน Support */
.support {
    width: 100%;
    padding: 15px 0;
    background: white;
    border-bottom: 1px solid #e0e7f0;
    display: flex;
    justify-content: center;
}

.support-container {
    display: flex;
    align-items: center;
    gap: 12px;
}
		
</style>

	
	
</head>
<body>
	
	
	<!-- Overlay สำหรับ Tour -->
<div id="dashboardTourOverlay" class="tour-overlay"></div>

<!-- ปุ่ม Start Tour -->
<button class="start-dashboard-tour-btn" onclick="startDashboardTour()" title="เริ่มทัวร์แนะนำการใช้งาน">
    <i class="fas fa-question-circle"></i>
    <span>แนะนำการใช้งาน</span>
</button>
	
	

<?php
// *** 🔔 ตรวจสอบสถานะ 2FA และสิทธิ์ของผู้ใช้ ***
$current_user_id = $this->session->userdata('m_id');
$user_profile = $this->db->select('m.*, p.pname')
                        ->from('tbl_member m')
                        ->join('tbl_position p', 'm.ref_pid = p.pid', 'left')
                        ->where('m.m_id', $current_user_id)
                        ->get()
                        ->row();

// ✅ ตรวจสอบว่าเป็น Super Admin หรือไม่
$is_super_admin = false;
$need_force_2fa = false;

if ($user_profile) {
    if ($user_profile->m_system === 'super_admin') {
        $is_super_admin = true;
    }
    elseif (empty($user_profile->m_system) && $user_profile->ref_pid == 1) {
        $is_super_admin = true;
    }
    
    $has_2fa_secret = isset($user_profile->google2fa_secret) && !empty(trim($user_profile->google2fa_secret));
    $is_2fa_enabled = isset($user_profile->google2fa_enabled) && $user_profile->google2fa_enabled == 1;
    $is_2fa_active = $has_2fa_secret && $is_2fa_enabled;
    
    if ($is_super_admin && !$is_2fa_active) {
        $need_force_2fa = true;
    }
}

// *** 🔔 ดึงข้อมูล e-Service Notification ***
$staff_notifications_count = 0;
$staff_unread_count = 0;

try {
    if ($this->db->table_exists('tbl_notifications') && $this->db->table_exists('tbl_notification_reads')) {
        $this->db->select('COUNT(n.notification_id) as unread_count');
        $this->db->from('tbl_notifications n');
        $this->db->join('tbl_notification_reads nr', 
                       'n.notification_id = nr.notification_id AND nr.user_id = "' . $current_user_id . '" AND nr.user_type = "staff"', 
                       'left');
        $this->db->where('n.target_role', 'staff');
        $this->db->where('n.is_archived', 0);
        $this->db->where('nr.id IS NULL');
        
        $query = $this->db->get();
        
        if ($query && $query->num_rows() > 0) {
            $result = $query->row();
            $staff_unread_count = (int)$result->unread_count;
        }
    }
} catch (Exception $e) {
    $staff_unread_count = 0;
}

$staff_unread_count = max(0, (int)$staff_unread_count);

// *** 🔔 ดึงข้อมูล Webboard Notification ***
$webboard_notifications_count = 0;

try {
    $user_id = $this->session->userdata('m_id');
    $tenant_code = $this->session->userdata('tenant_code');
    
    if (!empty($user_id) && !empty($tenant_code)) {
        $api_url = 'https://webboard.assystem.co.th/api/get_notification_count';
        $api_url .= '?' . http_build_query([
            'user_id' => $user_id,
            'tenant_code' => $tenant_code
        ]);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'X-Requested-With: XMLHttpRequest'
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($http_code === 200 && $response) {
            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status'] === 'success') {
                $webboard_notifications_count = (int)$result['unread_count'];
                log_message('info', 'Webboard notification count: ' . $webboard_notifications_count);
            }
        } else {
            log_message('error', 'Failed to fetch webboard notifications. HTTP Code: ' . $http_code . ', Error: ' . $curl_error);
        }
    }
} catch (Exception $e) {
    log_message('error', 'Webboard notification exception: ' . $e->getMessage());
    $webboard_notifications_count = 0;
}

$webboard_notifications_count = max(0, (int)$webboard_notifications_count);

// *** 🔔 ดึงข้อมูล Back Office Notification ***
$backoffice_notifications_count = 0;

try {
    $user_id = $this->session->userdata('m_id');
    $tenant_code = $this->session->userdata('tenant_code');
    
    if (!empty($user_id) && !empty($tenant_code)) {
        $api_url = 'https://backoffice.assystem.co.th/api/get_notification_count';
        $api_url .= '?' . http_build_query([
            'user_id' => $user_id,
            'tenant_code' => $tenant_code
        ]);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'X-Requested-With: XMLHttpRequest'
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($http_code === 200 && $response) {
            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status'] === 'success') {
                $backoffice_notifications_count = (int)$result['unread_count'];
                log_message('info', 'Back Office notification count: ' . $backoffice_notifications_count);
            }
        } else {
            log_message('error', 'Failed to fetch back office notifications. HTTP Code: ' . $http_code . ', Error: ' . $curl_error);
        }
    }
} catch (Exception $e) {
    log_message('error', 'Back Office notification exception: ' . $e->getMessage());
    $backoffice_notifications_count = 0;
}

$backoffice_notifications_count = max(0, (int)$backoffice_notifications_count);

log_message('info', 'Final back office notification count: ' . $backoffice_notifications_count);

// Helper Functions
function get_card_color_class($module_id) {
    $colors = [
        'card-blue', 'card-purple', 'card-green', 'card-indigo',
        'card-pink', 'card-yellow', 'card-red', 'card-orange',
        'card-teal', 'card-emerald', 'card-cyan', 'card-violet'
    ];
    return $colors[($module_id - 1) % count($colors)];
}

function get_module_icon($module_code) {
    $icons = [
        'member' => 'fa-solid fa-users',
        'web_mgt' => 'fa-solid fa-globe',
        'back_office' => 'fa-regular fa-folder-open',
        'saraban' => 'fa-solid fa-file-lines',
        'qcar' => 'fa-solid fa-car',
        'qmeeting_room' => 'fa-solid fa-building',
        'personnel' => 'fa-solid fa-user-tie',
        'tax' => 'fa-solid fa-money-bill-wave',
        'cctv' => 'fa-solid fa-video',
        'assets' => 'fa-solid fa-boxes-stacked',
        'google_drive' => 'fa-brands fa-google-drive',
        'webboard' => 'fa-solid fa-comments'
    ];
    
    return isset($icons[$module_code]) ? $icons[$module_code] : 'fa-solid fa-cube';
}

function check_module_access($module_id, $is_trial) {
    $CI =& get_instance();
    $member_id = $CI->session->userdata('m_id');
    
    $member = $CI->db->select('ref_pid, grant_system_ref_id, storage_access_granted')
                   ->from('tbl_member')
                   ->where('m_id', $member_id)
                   ->get()
                   ->row();
    
    if (!$member) {
        return false;
    }
    
    if (in_array($member->ref_pid, [1, 2])) {
        return true;
    }
    
    if ($is_trial == 1) {
        return true;
    }
    
    if ($module_id == 11) {
        return $member->storage_access_granted == 1;
    }
    
    $grant_systems = explode(',', $member->grant_system_ref_id);
    return in_array($module_id, $grant_systems);
}

function generate_system_url($module_id, $module_code, $is_trial) {
    $CI =& get_instance();
    
    if ($module_id == 11) {
        if ($is_trial == 1) {
            return site_url('Google_drive_files');
        }
        
        $member_id = $CI->session->userdata('m_id');
        $member = $CI->db->select('storage_access_granted')
                        ->from('tbl_member')
                        ->where('m_id', $member_id)
                        ->get()
                        ->row();
        
        if (!$member) {
            return "javascript:showGoogleDriveAccessError();";
        }
        
        if ((int)$member->storage_access_granted !== 1) {
            return "javascript:showGoogleDriveAccessError();";
        }
        
        return site_url('Google_drive_files');
    }
    
    if (!check_module_access($module_id, $is_trial)) {
        return "javascript:showAccessDeniedError()";
    }
    
    $tenant = $CI->tenant_db->where('domain', $_SERVER['HTTP_HOST'])
                           ->where('is_active', 1)
                           ->where('deleted_at IS NULL')
                           ->get('tenants')
                           ->row();
    
    if (!$tenant) {
        return "javascript:showAccessDeniedError()";
    }
    
    $existing_token = $CI->db->where([
        'user_id' => $CI->session->userdata('m_id'),
        'domain' => $_SERVER['HTTP_HOST'],
        'expires_at >' => date('Y-m-d H:i:s')
    ])->get('auth_tokens')->row();
    
    if ($existing_token) {
        $token = $existing_token->token;
    } else {
        $token = hash('sha256', $CI->session->userdata('m_id') . time() . random_bytes(32));
        
        $token_data = array(
            'token' => $token,
            'user_id' => $CI->session->userdata('m_id'),
            'ipaddress' => get_client_ip(),
            'domain' => $_SERVER['HTTP_HOST'],
            'tenant_id' => $tenant->id,
            'tenant_code' => $tenant->code,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'expires_at' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
            'created_at' => date('Y-m-d H:i:s')
        );

        $CI->db->where([
            'user_id' => $CI->session->userdata('m_id'),
            'domain' => $_SERVER['HTTP_HOST'],
            'expires_at <=' => date('Y-m-d H:i:s')
        ])->delete('auth_tokens');

        $CI->db->insert('auth_tokens', $token_data);
    }

    $user_data = array(
        'token' => $token,
        'm_id' => $CI->session->userdata('m_id'),
        'm_username' => $CI->session->userdata('m_username'),
        'm_fname' => $CI->session->userdata('m_fname'),
        'm_lname' => $CI->session->userdata('m_lname'),
        'tenant_id' => $tenant->id,
        'tenant_code' => $tenant->code
    );

    $base_urls = [
        'assets' => 'https://assetssv1.assystem.co.th/auth/login',
        'saraban' => 'https://saraban.assystem.co.th/',
        'tax' => 'https://localtax.assystem.co.th/auth/login',
        'qcar' => 'https://carbooking.assystem.co.th/auth/login',
        'back_office' => 'https://backoffice.assystem.co.th/auth/login',
        'qmeeting_room' => 'https://smartmeeting.assystem.co.th/auth/login',
        'webboard' => 'https://webboard.assystem.co.th/auth/login',
    ];

    return isset($base_urls[$module_code]) ? 
           $base_urls[$module_code] . '?' . http_build_query($user_data) : 
           "javascript:showAccessDeniedError()";
}

function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

if (!$this->session->userdata('m_id')) {
    redirect('User');
}
?>

<!-- ✅ Email Selection Modal -->
<div id="emailSelectionModal" class="email-selection-modal">
    <div class="email-modal-content">
        <div class="email-modal-header">
            <i class="fas fa-envelope email-modal-icon"></i>
            <h3 class="email-modal-title">เลือกระบบอีเมล</h3>
            <p class="email-modal-subtitle">กรุณาเลือกระบบอีเมลที่ต้องการเข้าใช้งาน</p>
        </div>

        <div class="email-options">
            <a href="#" id="domainEmailBtn" class="email-option-btn" target="_blank">
                <div class="email-option-icon primary">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="email-option-text">
                    <div class="email-option-title" id="domainEmailTitle">อีเมล @domain.com</div>
                    <div class="email-option-desc">เว็บเมลของหน่วยงาน</div>
                </div>
                <i class="fas fa-arrow-right email-option-arrow"></i>
            </a>

            <a href="https://mail.dla.go.th/login" class="email-option-btn" target="_blank">
                <div class="email-option-icon secondary">
                    <i class="fas fa-building"></i>
                </div>
                <div class="email-option-text">
                    <div class="email-option-title">อีเมล @dla.go.th</div>
                    <div class="email-option-desc">ระบบอีเมลกรมส่งเสริมการปกครองท้องถิ่น</div>
                </div>
                <i class="fas fa-arrow-right email-option-arrow"></i>
            </a>
        </div>

        <button type="button" class="email-modal-close" onclick="closeEmailModal()">
            <i class="fas fa-times me-2"></i>ปิด
        </button>
    </div>
</div>

<!-- ✅ Force 2FA Modal สำหรับ Super Admin -->
<?php if ($need_force_2fa): ?>
<div id="force2faModal" class="force-2fa-modal">
    <div class="force-2fa-content">
        <i class="bi bi-shield-exclamation force-2fa-icon"></i>
        <h2 class="force-2fa-title">จำเป็นต้องเปิดใช้งาน 2FA</h2>
        <p class="force-2fa-message">
            ในฐานะ <strong>Super Administrator</strong> คุณจำเป็นต้องเปิดใช้งานการยืนยันตัวตน 2 ขั้นตอน (2FA) เพื่อความปลอดภัยของระบบ
        </p>
        <div class="force-2fa-highlight">
            <i class="bi bi-exclamation-triangle"></i>
            คุณไม่สามารถเข้าใช้งานระบบได้จนกว่าจะตั้งค่า 2FA เรียบร้อยแล้ว
        </div>
        <button type="button" class="force-2fa-setup-btn" onclick="goToSecuritySettings()">
            <i class="bi bi-shield-plus me-2"></i>
            ตั้งค่า 2FA ทันที
        </button>
        <button type="button" class="force-2fa-logout-btn" onclick="window.location.href='<?php echo site_url('User/logout'); ?>'">
            <i class="bi bi-box-arrow-right me-2"></i>
            ออกจากระบบ
        </button>
    </div>
</div>

<div id="securityOverlay" class="security-overlay active"></div>
<?php endif; ?>

<!-- Modal แสดงข้อความแจ้งเตือน -->
<div id="errorModal" class="error-modal">
    <div class="error-modal-content">
        <i class="fas fa-exclamation-circle error-icon"></i>
        <h3 class="error-title">ไม่สามารถเข้าถึงได้</h3>
        <p class="error-message">ท่านไม่มีสิทธิ์เข้าใช้งานระบบนี้ โปรดติดต่อผู้ดูแลระบบของ <span id="tenant-name"><?php echo $this->session->userdata('tenant_name'); ?></span></p>
        <button class="error-close-btn" onclick="closeErrorModal()">ตกลง</button>
    </div>
</div>

<!-- Modal แสดงข้อความแจ้งเตือนสำหรับ Google Drive -->	
<div id="googleDriveErrorModal" class="error-modal">
    <div class="error-modal-content">
        <i class="fab fa-google-drive error-icon" style="color: #4285f4;"></i>
        <h3 class="error-title">Google Drive ยังไม่เปิดใช้งาน</h3>
        <p class="error-message">
            คุณยังไม่ได้รับสิทธิ์ในการเข้าใช้งาน Google Drive<br>
            <strong>กรุณาติดต่อผู้ดูแลระบบเพื่อเปิดใช้งาน</strong>
        </p>
        <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #2196f3;">
            <small style="color: #1565c0;">
                <i class="fas fa-info-circle"></i>
                ผู้ดูแลระบบจะต้องอนุมัติการเข้าใช้งาน Google Drive ก่อนที่คุณจะสามารถเข้าถึงได้
            </small>
        </div>
        <button class="error-close-btn" onclick="closeGoogleDriveErrorModal()" style="background: #4285f4;">
            <i class="fas fa-check me-2"></i>เข้าใจแล้ว
        </button>
    </div>
</div>

<div class="container-custom">
    <header class="header">
        <img src="<?php echo base_url('docs/logo.png'); ?>" alt="Logo" class="logo">
        <h1 class="site-title">สมาร์ทออฟฟิต</h1>
        <p class="site-subtitle"><?php echo $this->session->userdata('tenant_name'); ?></p>
        <p class="site-subtitle">ลดขั้นตอน ประหยัดเวลา และเพิ่มประสิทธิภาพการทำงานของเจ้าหน้าที่ ก้าวไปสู่ระบบราชการ 4.0</p>
    </header>

    <!-- User Profile Header -->
    <div class="user-header">
        <div class="user-info">
            <?php $img_path = !empty($user_profile->m_img) ? 'docs/img/avatar/' . $user_profile->m_img : 'docs/img/avatar/default_user.png'; ?>
            <img src="<?= base_url($img_path); ?>" alt="User Avatar" class="user-avatar">
            <div class="user-details">
                <h5><?php echo $user_profile->m_fname . ' ' . $user_profile->m_lname; ?></h5>
                <p><?php echo $user_profile->pname; ?></p>
                
                <div class="security-status mt-2">
                    <?php 
                    $has_2fa_secret = isset($user_profile->google2fa_secret) && !empty(trim($user_profile->google2fa_secret));
                    $is_2fa_enabled = isset($user_profile->google2fa_enabled) && $user_profile->google2fa_enabled == 1;
                    $is_2fa_active = $has_2fa_secret && $is_2fa_enabled;
                    ?>
                    
                    <?php if ($is_2fa_active): ?>
                        <span class="badge bg-success security-badge">
                            <i class="bi bi-shield-check"></i>
                            ยืนยันตัวตน 2FA เปิดใช้งานแล้ว
                        </span>
                    <?php else: ?>
                        <span class="badge bg-warning security-badge pulsing-warning">
                            <i class="bi bi-shield-exclamation"></i>
                            <?php if ($is_super_admin): ?>
                                <strong>Super Admin: จำเป็นต้องตั้งค่า 2FA</strong>
                            <?php elseif (!$has_2fa_secret): ?>
                                ยังไม่ได้ตั้งค่า 2FA
                            <?php elseif (!$is_2fa_enabled): ?>
                                2FA ถูกปิดใช้งาน
                            <?php else: ?>
                                ยังไม่ได้ยืนยันตัวเปิดใช้งาน 2FA
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="profile-actions">
            <a href="<?php echo site_url('System_admin/user_profile'); ?>" class="btn-profile btn-profile-view">
                <i class="bi bi-person-circle"></i>
                ดูโปรไฟล์
            </a>
            
            <?php if ($need_force_2fa): ?>
                <button type="button" class="btn-profile btn-profile-security" onclick="goToSecuritySettings()">
                    <i class="bi bi-shield-plus"></i>
                    ตั้งค่าความปลอดภัย (จำเป็น)
                    <span class="security-notification-dot"></span>
                </button>
            <?php elseif (!$is_2fa_active): ?>
                <a href="<?php echo site_url('System_admin/user_profile'); ?>#security-section" class="btn-profile btn-profile-security">
                    <i class="bi bi-shield-plus"></i>
                    ตั้งค่าความปลอดภัย
                    <span class="security-notification-dot"></span>
                </a>
            <?php else: ?>
                <a href="<?php echo site_url('System_admin/user_profile'); ?>#security-section" class="btn-profile btn-profile-security-active">
                    <i class="bi bi-shield-check"></i>
                    จัดการความปลอดภัย
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grid Container -->
    <div class="grid">
        <?php
        $member = $this->db->select('m_id, ref_pid, m_status, grant_system_ref_id, m_system')
                   ->from('tbl_member')
                   ->where('m_id', $this->session->userdata('m_id'))
                   ->where('m_status', '1')
                   ->get()
                   ->row();

        $can_access_admin = false;
        $can_access_member_system = false;
        $can_access_web_system = false;
        $can_access_reports = false;

        if ($member) {
            if (in_array($member->m_system, ['system_admin', 'super_admin'])) {
                $can_access_admin = true;
                $can_access_member_system = true;
                $can_access_web_system = true;
                $can_access_reports = true;
            }
            
            if ($member->m_system == 'user_admin') {
                $can_access_reports = true;
                $can_access_admin = true;
            }
            
            if (empty($member->m_system) && in_array($member->ref_pid, [1, 2])) {
                $can_access_admin = true;
                $can_access_member_system = true;
                $can_access_web_system = true;
                $can_access_reports = true;
            }
            
            if (empty($member->m_system) && $member->ref_pid == 3) {
                $can_access_reports = true;
                $can_access_admin = true;
            }
            
            if (!empty($member->grant_system_ref_id)) {
                $granted_systems = explode(',', $member->grant_system_ref_id);
                
                if (in_array('1', $granted_systems)) {
                    $can_access_member_system = true;
                }
                
                if (in_array('2', $granted_systems)) {
                    $can_access_web_system = true;
                }
                
                if (in_array('999', $granted_systems)) {
                    $can_access_reports = true;
                }
                
                if ($can_access_member_system || $can_access_web_system || $can_access_reports) {
                    $can_access_admin = true;
                }
            }
        }

        if ($can_access_admin) {
            $admin_modules = [];
            
            $admin_modules[] = [
                'id' => 'email_system',
                'name' => 'ระบบอีเมล',
                'code' => 'email_system', 
                'icon' => 'fa-solid fa-envelope',
                'url' => 'javascript:showEmailModal();',
                'color' => 'card-blue',
                'target' => '_self'
            ];
            
            if ($can_access_reports) {
                $admin_modules[] = [
                    'id' => 'reports',
                    'name' => 'ระบบ e-Service',
                    'code' => 'reports_system',
                    'icon' => 'fa-solid fa-chart-bar',
                    'url' => site_url('System_reports/index'),
                    'color' => 'card-cyan'
                ];
            }
            
            if ($can_access_member_system) {
                $admin_modules[] = [
                    'id' => 'member_mgt',
                    'name' => 'ระบบจัดการสมาชิก',
                    'code' => 'member_system',
                    'icon' => 'fa-solid fa-users',
                    'url' => site_url('System_member'),
                    'color' => 'card-orange'
                ];
            }
            
            if ($can_access_web_system) {
                $admin_modules[] = [
                    'id' => 'web_mgt',
                    'name' => 'ระบบจัดการเว็บไซต์',
                    'code' => 'web_system',
                    'icon' => 'fa-solid fa-globe',
                    'url' => site_url('System_admin'),
                    'color' => 'card-indigo'
                ];
            }
        }

        $modules = $this->db->select('*')
                           ->from('tbl_member_modules')
                           ->where_not_in('id', [1, 2])
                           ->order_by('display_order', 'asc')
                           ->get()
                           ->result();

        foreach ($modules as $module): 
            if ($module->status):
                // ✅ ตรวจสอบว่าเป็น Back Office Module หรือไม่
                $isBackOfficeModule = ($module->id == 3 || $module->code === 'back_office');
                // ✅ ตรวจสอบว่าเป็น Webboard Module หรือไม่
                $isWebboardModule = ($module->id == 17 || $module->code === 'webboard');
                
                $wrapperClass = '';
                if ($isBackOfficeModule && $backoffice_notifications_count > 0) {
                    $wrapperClass = 'has-notification';
                } elseif ($isWebboardModule && $webboard_notifications_count > 0) {
                    $wrapperClass = 'has-notification';
                }
        ?>
            <div class="card-wrapper <?php echo $wrapperClass; ?>">
                <div class="version-badge <?php echo $module->is_trial ? 'trial' : 'full'; ?>">
                    <?php echo $module->is_trial ? 'เวอร์ชั่นทดลองใช้' : 'เวอร์ชั่นเต็ม'; ?>
                </div>

                <?php if ($isBackOfficeModule && $backoffice_notifications_count > 0): ?>
                    <div class="smart-office-notification-badge" 
                         title="มีการแจ้งเตือน <?php echo $backoffice_notifications_count; ?> รายการ">
                        <?php echo $backoffice_notifications_count > 99 ? '99+' : $backoffice_notifications_count; ?>
                    </div>
                <?php elseif ($isWebboardModule && $webboard_notifications_count > 0): ?>
                    <div class="smart-office-notification-badge" 
                         title="มีการแจ้งเตือน <?php echo $webboard_notifications_count; ?> รายการ">
                        <?php echo $webboard_notifications_count > 99 ? '99+' : $webboard_notifications_count; ?>
                    </div>
                <?php endif; ?>

                <a href="<?php echo $need_force_2fa ? 'javascript:show2FAReminder()' : generate_system_url($module->id, $module->code, $module->is_trial); ?>" 
                   class="card <?php echo get_card_color_class($module->id); ?>"
                   data-tenant="<?php echo $this->session->userdata('tenant_code'); ?>"
                   data-tenant-id="<?php echo $this->session->userdata('tenant_id'); ?>"
                   <?php if ($isBackOfficeModule && $backoffice_notifications_count > 0): ?>
                       title="ระบบ Back Office (มีการแจ้งเตือน <?php echo $backoffice_notifications_count; ?> รายการ)"
                   <?php elseif ($isWebboardModule && $webboard_notifications_count > 0): ?>
                       title="กระดานสนทนา (มีการแจ้งเตือน <?php echo $webboard_notifications_count; ?> รายการ)"
                   <?php endif; ?>>
                    <div class="card-content">
                        <div class="icon-circle">
                            <i class="<?php echo get_module_icon($module->code); ?> card-icon"></i>
                        </div>
                        <h2 class="card-title"><?php echo $module->name; ?></h2>
                    </div>
                </a>
            </div>
        <?php 
            endif;
        endforeach;

        if ($can_access_admin):
            foreach ($admin_modules as $module):
                $isReportsModule = ($module['code'] === 'reports_system');
                $wrapperClass = ($isReportsModule && $staff_unread_count > 0) ? 'has-notification' : '';
        ?>
            <div class="card-wrapper <?php echo $wrapperClass; ?>">
                <?php if ($isReportsModule && $staff_unread_count > 0): ?>
                    <div class="smart-office-notification-badge" 
                         title="มีการแจ้งเตือน <?php echo $staff_unread_count; ?> รายการ">
                        <?php echo $staff_unread_count > 99 ? '99+' : $staff_unread_count; ?>
                    </div>
                <?php endif; ?>
                
                <a href="<?php echo $need_force_2fa ? 'javascript:show2FAReminder()' : $module['url']; ?>" 
                   class="card <?php echo $module['color']; ?>"
                   <?php if ($isReportsModule): ?>
                       title="ระบบ e-Service<?php echo $staff_unread_count > 0 ? ' (มีการแจ้งเตือน ' . $staff_unread_count . ' รายการ)' : ''; ?>"
                   <?php endif; ?>>
                    <div class="card-content">
                        <div class="icon-circle">
                            <i class="<?php echo $module['icon']; ?> card-icon <?php echo $module['code'] === 'email_system' ? 'email-icon' : ''; ?>"></i>
                        </div>
                        <h2 class="card-title"><?php echo $module['name']; ?></h2>
                    </div>
                </a>
            </div>
        <?php 
            endforeach;
        endif;
        ?>
    </div>
    
    <div class="logout-container">
        <a href="<?php echo $need_force_2fa ? 'javascript:show2FAReminder()' : site_url('Home'); ?>" class="home-button">
            <div class="home-icon-circle">
                <i class="fas fa-home home-icon"></i>
            </div>
            <span>กลับหน้าหลัก</span>
        </a>
        
        <a href="<?php echo site_url('User/logout'); ?>" class="logout-button">
            <div class="logout-icon-circle">
                <i class="fas fa-sign-out-alt logout-icon"></i>
            </div>
            <span>ออกจากระบบ</span>
        </a>
    </div>
	
	
	<div class="membership-terms-section">
		
		<div class="membership-terms-container">
        <a href=<?php echo base_url("policy/security"); ?> target="_blank" class="membership-terms-link">
            <i class="fas fa-file-contract"></i>
            <span>นโยบายการรักษาความมั่นคงปลอดภัยเว็บไซต์</span><br>
        </a>
    </div>
		
    <div class="membership-terms-container">
        <a href=<?php echo base_url("policy/privacy"); ?> target="_blank" class="membership-terms-link">
            <i class="fas fa-file-contract"></i>
            <span>ข้อกำหนดและเงื่อนไขความเป็นส่วนตัว</span>
        </a>
    </div>
</div>
	
	

    <div class="support">
        <div class="support-container">
            <span class="support-text">ติดปัญหาการใช้งาน หรือติดต่อฝ่ายขาย</span>
            <i class="fab fa-line line-icon"></i>
            <a href="https://line.me/ti/p/@assystem" target="_blank" class="line-link">@assystem</a>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Session Manager -->
<script src="<?php echo base_url('asset/js/pri-session-manager.js'); ?>"></script>

<script>
// ✅ Global Variables
window.base_url = '<?php echo base_url(); ?>';
const needForce2FA = <?php echo $need_force_2fa ? 'true' : 'false'; ?>;
const isSuperAdmin = <?php echo $is_super_admin ? 'true' : 'false'; ?>;

// ✅ Get current domain for email
const currentDomain = '<?php echo $_SERVER['HTTP_HOST']; ?>';
let cleanDomain = currentDomain;

if (cleanDomain.startsWith('www.')) {
    cleanDomain = cleanDomain.substring(4);
}

console.log('Original Domain:', currentDomain);
console.log('Clean Domain:', cleanDomain);

// ✅ Email Modal Functions
function showEmailModal() {
    if (needForce2FA) {
        show2FAReminder();
        return;
    }
    
    const modal = document.getElementById('emailSelectionModal');
    if (modal) {
        const domainEmailBtn = document.getElementById('domainEmailBtn');
        const domainEmailTitle = document.getElementById('domainEmailTitle');
        
        if (domainEmailBtn && domainEmailTitle) {
            const webmailUrl = 'https://webmail.' + cleanDomain + '/';
            domainEmailBtn.href = webmailUrl;
            domainEmailTitle.textContent = 'อีเมล @' + cleanDomain;
        }
        
        modal.classList.add('show');
        
        setTimeout(() => {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeEmailModal();
                }
            });
        }, 100);
    }
}

function closeEmailModal() {
    const modal = document.getElementById('emailSelectionModal');
    if (modal) {
        const content = modal.querySelector('.email-modal-content');
        if (content) {
            content.style.animation = 'emailModalSlideOut 0.3s ease-in';
            setTimeout(() => {
                modal.classList.remove('show');
                content.style.animation = '';
            }, 300);
        } else {
            modal.classList.remove('show');
        }
    }
}

// ✅ Force 2FA Functions
function showForce2FAModal() {
    console.log('🔒 Showing Force 2FA Modal for Super Admin');
    
    const modal = document.getElementById('force2faModal');
    const overlay = document.getElementById('securityOverlay');
    
    if (modal && overlay) {
        document.body.classList.add('force-2fa-active');
        modal.classList.add('show');
        overlay.classList.add('active');
        
        document.addEventListener('keydown', preventEscape);
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                e.preventDefault();
                e.stopPropagation();
                show2FAReminder();
            }
        });
    }
}

function hideForce2FAModal() {
    console.log('🔓 Hiding Force 2FA Modal');
    
    const modal = document.getElementById('force2faModal');
    const overlay = document.getElementById('securityOverlay');
    
    if (modal && overlay) {
        document.body.classList.remove('force-2fa-active');
        modal.classList.remove('show');
        overlay.classList.remove('active');
        
        document.removeEventListener('keydown', preventEscape);
    }
}

function preventEscape(e) {
    if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        show2FAReminder();
    }
}

function goToSecuritySettings() {
    console.log('🔧 Redirecting to Security Settings...');
    
    Swal.fire({
        title: 'กำลังเปิดหน้าตั้งค่าความปลอดภัย...',
        text: 'โปรดรอสักครู่',
        icon: 'info',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(function() {
        window.location.href = '<?php echo site_url("System_admin/user_profile"); ?>#security-section';
    }, 1000);
}

function forceLogout() {
    console.log('🚪 Force logout initiated by Super Admin');
    
    Swal.fire({
        title: 'ออกจากระบบ?',
        text: 'คุณจะต้องเข้าสู่ระบบใหม่และตั้งค่า 2FA ก่อนใช้งาน',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'ออกจากระบบ',
        cancelButtonText: 'ยกเลิก',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'กำลังออกจากระบบ...',
                text: 'โปรดรอสักครู่',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(function() {
                window.location.href = '<?php echo site_url("User/logout"); ?>';
            }, 1000);
        }
    });
}

function show2FAReminder() {
    if (isSuperAdmin) {
        Swal.fire({
            title: 'ไม่สามารถเข้าถึงได้!',
            html: `
                <div style="text-align: center;">
                    <i class="bi bi-shield-exclamation" style="font-size: 3rem; color: #dc2626; margin-bottom: 1rem;"></i>
                    <p style="font-size: 1.1rem; margin-bottom: 1rem;">
                        ในฐานะ <strong>Super Administrator</strong><br>
                        คุณจำเป็นต้องเปิดใช้งาน <strong>2FA</strong> ก่อนเข้าใช้งานระบบ
                    </p>
                    <div style="background: #fef3c7; padding: 1rem; border-radius: 0.5rem; border-left: 4px solid #f59e0b; margin: 1rem 0;">
                        <strong style="color: #92400e;">
                            <i class="bi bi-exclamation-triangle"></i>
                            นี่เป็นข้อกำหนดด้านความปลอดภัยที่จำเป็น
                        </strong>
                    </div>
                </div>
            `,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bi bi-shield-plus"></i> ตั้งค่า 2FA ทันที',
            cancelButtonText: '<i class="bi bi-box-arrow-right"></i> ออกจากระบบ',
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'animated bounceIn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                goToSecuritySettings();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                forceLogout();
            }
        });
    } else {
        Swal.fire({
            title: 'แนะนำให้ตั้งค่า 2FA',
            html: `
                <div style="text-align: center;">
                    <i class="bi bi-shield-plus" style="font-size: 3rem; color: #f59e0b; margin-bottom: 1rem;"></i>
                    <p>เพื่อความปลอดภัยของบัญชี แนะนำให้เปิดใช้งานการยืนยันตัวตน 2 ขั้นตอน</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ตั้งค่า 2FA',
            cancelButtonText: 'ข้ามไปก่อน'
        }).then((result) => {
            if (result.isConfirmed) {
                goToSecuritySettings();
            }
        });
    }
}

// ✅ Document Ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('📚 Document ready, initializing ADMIN session system...');
    console.log('🔒 Need Force 2FA:', needForce2FA);
    console.log('👑 Is Super Admin:', isSuperAdmin);
    console.log('📊 Initial e-Service unread count:', <?php echo $staff_unread_count; ?>);
    console.log('💬 Initial Webboard unread count:', <?php echo $webboard_notifications_count; ?>);
    console.log('📁 Initial Back Office unread count:', <?php echo $backoffice_notifications_count; ?>);
    
    if (needForce2FA) {
        setTimeout(function() {
            showForce2FAModal();
        }, 500);
    }
    
    if (typeof window.createAdminSessionModalsIfNeeded === 'function') {
        window.createAdminSessionModalsIfNeeded();
    }
    
    const sessionVars = {
        m_id: '<?php echo $this->session->userdata('m_id'); ?>',
        tenant_id: '<?php echo $this->session->userdata('tenant_id'); ?>',
        admin_id: '<?php echo $this->session->userdata('admin_id'); ?>',
        user_id: '<?php echo $this->session->userdata('user_id'); ?>',
        mp_id: '<?php echo $this->session->userdata('mp_id'); ?>',
        logged_in: '<?php echo $this->session->userdata('logged_in'); ?>',
        username: '<?php echo $this->session->userdata('username'); ?>'
    };
    
    const hasAdminSession = sessionVars.m_id || sessionVars.admin_id || sessionVars.user_id || 
                           (sessionVars.logged_in && !sessionVars.mp_id);
    
    if (typeof window.initializeAdminSessionManager === 'function') {
        window.initializeAdminSessionManager(hasAdminSession);
    }
    
    if (typeof window.setupAdminModalEventListeners === 'function') {
        window.setupAdminModalEventListeners();
    }

    setupErrorPrevention();
    
    if (!needForce2FA && !isSuperAdmin) {
        const warningBadge = document.querySelector('.pulsing-warning');
        if (warningBadge) {
            setTimeout(function() {
                showSecurityReminder();
            }, 15000);
        }
    }
    
    const googleDriveModal = document.getElementById('googleDriveErrorModal');
    if (googleDriveModal) {
        googleDriveModal.addEventListener('click', function(e) {
            if (e.target === googleDriveModal) {
                closeGoogleDriveErrorModal();
            }
        });
    }

    // ✅ Start Auto-Refresh for Notifications
    setInterval(function() {
        if (!needForce2FA) {
            refreshSmartOfficeNotificationCount(); // e-Service
            refreshWebboardNotificationCount(); // Webboard
            refreshBackOfficeNotificationCount(); // Back Office
        }
    }, 120000); // ทุก 2 นาที
});

// ✅ Error Modal Functions
function showAccessDeniedError() {
    if (needForce2FA) {
        show2FAReminder();
        return;
    }
    
    const modal = document.getElementById('errorModal');
    if (modal) {
        modal.style.display = 'block';
    }
}

function closeErrorModal() {
    const modal = document.getElementById('errorModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function showGoogleDriveAccessError() {
    if (needForce2FA) {
        show2FAReminder();
        return;
    }
    
    const modal = document.getElementById('googleDriveErrorModal');
    if (modal) {
        modal.style.display = 'block';
        
        const content = modal.querySelector('.error-modal-content');
        if (content) {
            content.style.animation = 'none';
            setTimeout(() => {
                content.style.animation = 'errorSlideIn 0.4s ease-out';
            }, 10);
        }
    }
}

function closeGoogleDriveErrorModal() {
    const modal = document.getElementById('googleDriveErrorModal');
    if (modal) {
        const content = modal.querySelector('.error-modal-content');
        if (content) {
            content.style.animation = 'errorSlideOut 0.3s ease-in';
            setTimeout(() => {
                modal.style.display = 'none';
                content.style.animation = '';
            }, 300);
        } else {
            modal.style.display = 'none';
        }
    }
}

function setupErrorPrevention() {
    console.log('Setting up error prevention...');
    
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            console.log('Image not found:', this.src);
            this.style.display = 'none';
        });
    });
    
    const elementsWithBg = document.querySelectorAll('[style*="background"]');
    elementsWithBg.forEach(element => {
        const bgImage = getComputedStyle(element).backgroundImage;
        if (bgImage && bgImage.includes('welcome-btm-light-other.png')) {
            console.log('Removing broken background image from:', element);
            element.style.backgroundImage = 'none';
        }
    });
}

function showSecurityReminder() {
    if (needForce2FA || isSuperAdmin) {
        return;
    }
    
    const reminder = document.createElement('div');
    reminder.className = 'alert alert-warning alert-dismissible fade show position-fixed';
    reminder.style.cssText = `
        top: 20px; 
        right: 20px; 
        z-index: 9999; 
        max-width: 350px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-left: 4px solid #f59e0b;
    `;
    reminder.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-exclamation me-2" style="font-size: 1.2rem;"></i>
            <div>
                <strong>เตือนความปลอดภัย!</strong><br>
                <small>เพื่อความปลอดภัยของบัญชี แนะนำให้เปิดใช้งาน 2FA</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(reminder);
    
    setTimeout(() => {
        if (reminder.parentNode) {
            const bsAlert = new bootstrap.Alert(reminder);
            bsAlert.close();
        }
    }, 8000);
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-profile-security, .btn-profile-security-active')) {
        console.log('Security settings accessed by user:', '<?php echo $this->session->userdata("m_username"); ?>');
    }
});

if (window.location.hash === '#security-section') {
    setTimeout(function() {
        const securitySection = document.querySelector('.twofa-status, [id*="security"], [class*="section-card"]');
        if (securitySection) {
            securitySection.scrollIntoView({ 
                behavior: 'smooth',
                block: 'center'
            });
            
            securitySection.style.border = '3px solid #f59e0b';
            securitySection.style.borderRadius = '10px';
            securitySection.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                securitySection.style.border = '';
                securitySection.style.borderRadius = '';
            }, 3000);
        }
    }, 500);
}

// ✅ Refresh e-Service Notification Count
function refreshSmartOfficeNotificationCount() {
    fetch('<?php echo site_url("User/get_notification_count"); ?>', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('e-Service notification response:', data);
        if (data.status === 'success') {
            updateSmartOfficeBadge(data.unread_count);
        }
    })
    .catch(error => {
        console.log('e-Service notification error:', error);
        console.log('Using current e-Service count:', <?php echo $staff_unread_count; ?>);
    });
}

// ✅ Refresh Webboard Notification Count
function refreshWebboardNotificationCount() {
    const userId = '<?php echo $this->session->userdata("m_id"); ?>';
    const tenantCode = '<?php echo $this->session->userdata("tenant_code"); ?>';
    
    if (!userId || !tenantCode) {
        console.log('Missing user_id or tenant_code for webboard notification');
        return;
    }
    
    const apiUrl = `https://webboard.assystem.co.th/api/get_notification_count?user_id=${userId}&tenant_code=${tenantCode}`;
    
    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Webboard notification response:', data);
        if (data.status === 'success') {
            updateWebboardBadge(data.unread_count);
        }
    })
    .catch(error => {
        console.log('Webboard notification error:', error);
        console.log('Using current webboard count:', <?php echo $webboard_notifications_count; ?>);
    });
}

// ✅ Refresh Back Office Notification Count
function refreshBackOfficeNotificationCount() {
    const userId = '<?php echo $this->session->userdata("m_id"); ?>';
    const tenantCode = '<?php echo $this->session->userdata("tenant_code"); ?>';
    
    if (!userId || !tenantCode) {
        console.log('Missing user_id or tenant_code for back office notification');
        return;
    }
    
    const apiUrl = `https://backoffice.assystem.co.th/api/get_notification_count?user_id=${userId}&tenant_code=${tenantCode}`;
    
    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Back Office notification response:', data);
        if (data.status === 'success') {
            updateBackOfficeBadge(data.unread_count);
        }
    })
    .catch(error => {
        console.log('Back Office notification error:', error);
        console.log('Using current back office count:', <?php echo $backoffice_notifications_count; ?>);
    });
}

// ✅ Update e-Service Badge
function updateSmartOfficeBadge(count) {
    const reportsWrapper = document.querySelector('a[href*="System_reports"]')?.closest('.card-wrapper');
    
    if (!reportsWrapper) return;
    
    let badge = reportsWrapper.querySelector('.smart-office-notification-badge');
    
    if (count > 0) {
        if (!badge) {
            badge = document.createElement('div');
            badge.className = 'smart-office-notification-badge';
            reportsWrapper.appendChild(badge);
        }
        
        badge.textContent = count > 99 ? '99+' : count;
        badge.title = `มีการแจ้งเตือน ${count} รายการ`;
        badge.style.display = 'flex';
        reportsWrapper.classList.add('has-notification');
        
        console.log('e-Service badge updated:', count);
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
        reportsWrapper.classList.remove('has-notification');
        
        console.log('e-Service badge hidden (count is 0)');
    }
}

// ✅ Update Webboard Badge
function updateWebboardBadge(count) {
    console.log('Updating webboard badge with count:', count);
    
    const allCards = document.querySelectorAll('.card-wrapper');
    let webboardWrapper = null;
    
    allCards.forEach(wrapper => {
        const card = wrapper.querySelector('.card');
        const titleElement = card?.querySelector('.card-title');
        if (titleElement && (titleElement.textContent.includes('กระดานสนทนา') || titleElement.textContent.includes('Webboard'))) {
            webboardWrapper = wrapper;
        }
    });
    
    if (!webboardWrapper) {
        console.log('Webboard wrapper not found');
        return;
    }
    
    let badge = webboardWrapper.querySelector('.smart-office-notification-badge');
    
    if (count > 0) {
        if (!badge) {
            badge = document.createElement('div');
            badge.className = 'smart-office-notification-badge';
            webboardWrapper.appendChild(badge);
            console.log('Created new webboard badge');
        }
        
        badge.textContent = count > 99 ? '99+' : count;
        badge.title = `มีการแจ้งเตือน ${count} รายการ`;
        badge.style.display = 'flex';
        webboardWrapper.classList.add('has-notification');
        
        console.log('Webboard badge updated:', count);
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
        webboardWrapper.classList.remove('has-notification');
        
        console.log('Webboard badge hidden (count is 0)');
    }
}

// ✅ Update Back Office Badge
function updateBackOfficeBadge(count) {
    console.log('Updating back office badge with count:', count);
    
    const allCards = document.querySelectorAll('.card-wrapper');
    let backOfficeWrapper = null;
    
    allCards.forEach(wrapper => {
        const card = wrapper.querySelector('.card');
        const titleElement = card?.querySelector('.card-title');
        if (titleElement && (
            titleElement.textContent.includes('Back Office') || 
            titleElement.textContent.includes('แบ็กออฟฟิศ') ||
            titleElement.textContent.includes('back office')
        )) {
            backOfficeWrapper = wrapper;
        }
    });
    
    if (!backOfficeWrapper) {
        console.log('Back Office wrapper not found');
        return;
    }
    
    let badge = backOfficeWrapper.querySelector('.smart-office-notification-badge');
    
    if (count > 0) {
        if (!badge) {
            badge = document.createElement('div');
            badge.className = 'smart-office-notification-badge';
            backOfficeWrapper.appendChild(badge);
            console.log('Created new back office badge');
        }
        
        badge.textContent = count > 99 ? '99+' : count;
        badge.title = `มีการแจ้งเตือน ${count} รายการ`;
        badge.style.display = 'flex';
        backOfficeWrapper.classList.add('has-notification');
        
        console.log('Back Office badge updated:', count);
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
        backOfficeWrapper.classList.remove('has-notification');
        
        console.log('Back Office badge hidden (count is 0)');
    }
}

// ✅ Prevent all navigation for Super Admin without 2FA
document.addEventListener('click', function(e) {
    if (needForce2FA) {
        const link = e.target.closest('a[href]');
        if (link && !link.href.includes('logout') && !link.href.includes('javascript:')) {
            e.preventDefault();
            e.stopPropagation();
            show2FAReminder();
            return false;
        }
    }
});

console.log('✅ Admin Dashboard Script Loaded Successfully');
console.log('💬 Webboard Notification System Initialized');
console.log('📁 Back Office Notification System Initialized');
</script>
	
	
	
<script>
// ======================== DASHBOARD TOUR GUIDE SYSTEM ========================

const dashboardTourSteps = [
    {
        target: '.user-details',
        title: 'ข้อมูลผู้ใช้งาน',
        content: '<strong>👤 รายละเอียดผู้ใช้งาน</strong><br><br>แสดงข้อมูลของคุณที่กำลังเข้าระบบอยู่<br>✅ รูปโปรไฟล์<br>✅ ชื่อ-นามสกุล<br>✅ ตำแหน่งงาน<br>✅ สถานะการเปิดใช้งานความปลอดภัย 2FA',
        icon: 'fa-user-circle',
        iconType: 'primary',
        label: 'ข้อมูลผู้ใช้',
        labelPosition: 'top',
        arrowDirection: 'down'
    },
    {
        target: '.btn-profile-view',
        title: 'ดูและแก้ไขโปรไฟล์',
        content: '<strong>📝 จัดการข้อมูลส่วนตัว</strong><br><br>คลิกเพื่อดูและแก้ไขโปรไฟล์ของคุณ:<br>✅ แก้ไขชื่อ-นามสกุล<br>✅ เปลี่ยนรหัสผ่าน<br>✅ อัปเดตอีเมล<br>✅ แก้ไขเบอร์โทรศัพท์<br>✅ แก้ไขรูปโปรไฟล์<br>✅ เปิดใช้งานความปลอดภัย 2FA',
        icon: 'fa-id-card',
        iconType: 'primary',
        label: 'ดูโปรไฟล์',
        labelPosition: 'bottom',
        arrowDirection: 'up'
    },
    {
        target: '.btn-profile-security, .btn-profile-security-active',
        title: 'ตั้งค่าความปลอดภัย 2FA',
        content: '<strong>🔐 ระบบยืนยันตัวตน 2 ชั้น (2FA)</strong><br><br>เปิดใช้งาน Google Authenticator เพื่อ:<br>✅ เพิ่มความปลอดภัยให้บัญชี<br>✅ ป้องกันการเข้าถึงโดยไม่ได้รับอนุญาต<br>✅ ยืนยันตัวตนด้วย OTP ผ่าน แอป Google Authenticator<br><br><span style="color: #e74c3c;">⚠️ ผู้ใช้งานทุกท่านควรเปิดใช้งาน 2FA เพื่อความปลอดภัย</span>',
        icon: 'fa-shield-halved',
        iconType: 'warning',
        label: 'ความปลอดภัย 2FA',
        labelPosition: 'bottom',
        arrowDirection: 'up'
    },
    {
        target: '.grid',
        title: 'โครงสร้างระบบสมาร์ทออฟฟิต',
        content: '<strong>🎯 ระบบงานทั้งหมด</strong><br><br>เมนูหลักที่รวมระบบงานต่างๆ ไว้ที่นี่ เช่น:<br>📊 ระบบ Back Office<br>📋 ระบบสารบรรณ<br>💬 กระดานสนทนาในเครือข่าย<br>✉️ ระบบอีเมล<br>📝 ระบบ e-Service<br>⚙️ และระบบอื่นๆ<br><br>คลิกที่การ์ดเพื่อเข้าใช้งานแต่ละระบบต่างๆ',
        icon: 'fa-th-large',
        iconType: 'success',
        label: 'เมนูระบบทั้งหมด',
        labelPosition: 'top',
        arrowDirection: 'down'
    },
    {
        target: 'a[href*="backoffice.assystem.co.th"]',
        title: 'ระบบ Back Office',
        content: '<strong>📊 ระบบบริหารจัดการภายใน</strong><br><br>ระบบสำหรับจัดการงานภายในองค์กร:<br>📁 จัดการและแชร์เอกสาร<br>🖼️ คลังรูปภาพและวิดีโอภายใน<br>📢 แจ้งข่าวสารองค์กร<br>📝 แบบฟอร์มภายในต่างๆ<br>📊 รายงานและสถิติต่างๆ<br><br>🔔 มีไอคอนแจ้งเตือนเมื่อมีงานใหม่',
        icon: 'fa-briefcase',
        iconType: 'primary',
        label: 'แบคออฟฟิศ',
        labelPosition: 'right',
        arrowDirection: 'left'
    },
    {
        target: 'a[href*="webboard.assystem.co.th"]',
        title: 'กระดานสนทนาภายในเครือข่าย',
        content: '<strong>💬 กระดานสนทนาภายในเครือข่าย</strong><br><br>พื้นที่สำหรับสื่อสารและแลกเปลี่ยนความรู้:<br>💡 ถามตอบปัญหา<br>📢 ประกาศข่าวสาร<br>🤝 แชร์ประสบการณ์<br>📚 แลกเปลี่ยนความรู้<br><br>🔔 มีการแจ้งเตือนโพสต์ใหม่และการตอบกลับ',
        icon: 'fa-comments',
        iconType: 'success',
        label: 'กระดานสนทนา',
        labelPosition: 'left',
        arrowDirection: 'right'
    },
    {
        target: 'a[href="javascript:showEmailModal();"], a[onclick*="showEmailModal"]',
        title: 'ระบบอีเมล',
        content: '<strong>✉️ ระบบจัดการอีเมล</strong><br><br>เข้าถึงระบบอีเมลขององค์กร:<br>📧 เว็บเมล์ภายในองค์กร<br>📨 เว็บเมล์กรมส่งเสริมการปกครองท้องถิ่น<br>📬 จัดการกล่องจดหมาย<br>📤 ส่ง-รับอีเมล<br><br>💡 เลือกระบบอีเมลที่ต้องการใช้งาน',
        icon: 'fa-envelope',
        iconType: 'primary',
        label: 'ระบบอีเมล',
        labelPosition: 'left',
        arrowDirection: 'right'
    },
    {
        target: 'a[href*="System_reports"]',
        title: 'ระบบ e-Service',
        content: '<strong>📝 ระบบบริการอิเล็กทรอนิกส์</strong><br><br>ระบบให้บริการประชาชนออนไลน์:<br>📋 จัดการคำร้องต่างๆ<br>📊 จัดการสถานะต่างคำร้องต่างๆ<br>📈 รายงานข้อมูล<br>🔍 ติดตามสถานะ<br><br>🔔 แจ้งเตือนเมื่อมีคำร้องใหม่',
        icon: 'fa-file-alt',
        iconType: 'warning',
        label: 'อีเซอร์วิส',
        labelPosition: 'left',
        arrowDirection: 'right'
    },
    {
        target: '.logout-container',
        title: 'กลับหน้าหลักและออกจากระบบ',
        content: '<strong>🏠 ปุ่มควบคุมระบบ</strong><br><br>ใช้เมื่อต้องการ:<br>🏠 <strong>กลับหน้าหลัก</strong><br>กลับไปยังหน้าแรกของระบบ<br><br>🚪 <strong>ออกจากระบบ</strong><br>Logout เพื่อความปลอดภัย<br><br>💡 <strong>Tips:</strong> อย่าลืม Logout เมื่อใช้งานเสร็จ!',
        icon: 'fa-door-open',
        iconType: 'error',
        label: 'กลับหน้าหลักและออกจากระบบ',
        labelPosition: 'top',
        arrowDirection: 'down'
    }
];

let dashboardCurrentStep = 0;
let dashboardIsActive = false;

function createDashboardSafeClickHandler() {
    return function(e) {
        if (!dashboardIsActive) return;
        
        const tooltip = document.querySelector('.dashboard-tour-tooltip');
        if (!tooltip || !document.body.contains(tooltip)) return;
        
        try {
            if (!tooltip.contains(e.target)) {
                const closeBtn = e.target.closest('.dashboard-tour-close');
                const nextBtn = e.target.closest('.dashboard-tour-next');
                const prevBtn = e.target.closest('.dashboard-tour-prev');
                const finishBtn = e.target.closest('.dashboard-tour-finish');
                const skipBtn = e.target.closest('.dashboard-tour-skip');
                
                if (!closeBtn && !nextBtn && !prevBtn && !finishBtn && !skipBtn) {
                    console.log('Clicked outside dashboard tooltip');
                }
            }
        } catch (error) {
            console.error('Error in dashboard click handler:', error);
            if (dashboardIsActive) endDashboardTour();
        }
    };
}

function createDashboardSafeKeyHandler() {
    return function(e) {
        if (!dashboardIsActive) return;
        
        try {
            if (e.key === 'Escape') {
                console.log('ESC pressed - ending dashboard tour');
                endDashboardTour();
            } else if (e.key === 'ArrowRight') {
                const nextBtn = document.querySelector('.dashboard-tour-next');
                if (nextBtn) nextBtn.click();
            } else if (e.key === 'ArrowLeft') {
                const prevBtn = document.querySelector('.dashboard-tour-prev');
                if (prevBtn) prevBtn.click();
            }
        } catch (error) {
            console.error('Error in dashboard key handler:', error);
        }
    };
}

function startDashboardTour() {
    console.log('🎯 Starting dashboard tour...');
    
    if (window.dashboardTourClickHandler) {
        document.removeEventListener('click', window.dashboardTourClickHandler);
        window.dashboardTourClickHandler = null;
    }
    if (window.dashboardTourKeyHandler) {
        document.removeEventListener('keydown', window.dashboardTourKeyHandler);
        window.dashboardTourKeyHandler = null;
    }
    
    dashboardIsActive = true;
    dashboardCurrentStep = 0;
    
    const startBtn = document.querySelector('.start-dashboard-tour-btn');
    if (startBtn) startBtn.style.display = 'none';
    
    let overlay = document.getElementById('dashboardTourOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'dashboardTourOverlay';
        overlay.className = 'tour-overlay';
        document.body.appendChild(overlay);
    }
    overlay.classList.add('active');
    
    window.dashboardTourClickHandler = createDashboardSafeClickHandler();
    window.dashboardTourKeyHandler = createDashboardSafeKeyHandler();
    
    document.addEventListener('click', window.dashboardTourClickHandler);
    document.addEventListener('keydown', window.dashboardTourKeyHandler);
    
    showDashboardStep(0);
    console.log('✅ Dashboard tour started successfully');
}

function showDashboardStep(stepIndex) {
    console.log(`📍 Showing dashboard step ${stepIndex + 1}/${dashboardTourSteps.length}`);
    
    if (stepIndex < 0 || stepIndex >= dashboardTourSteps.length) return;
    
    dashboardCurrentStep = stepIndex;
    const step = dashboardTourSteps[stepIndex];
    
    const selectors = step.target.split(',').map(s => s.trim());
    let targetElement = null;
    
    for (const selector of selectors) {
        targetElement = document.querySelector(selector);
        if (targetElement) break;
    }
    
    if (!targetElement) {
        console.error('Target element not found:', step.target);
        return;
    }
    
    removeDashboardTourElements();
    
    targetElement.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
    
    setTimeout(() => {
        if (dashboardIsActive) {
            createDashboardSpotlight(targetElement);
            createDashboardLabel(targetElement, step.label, step.labelPosition);
            createDashboardTooltip(step, stepIndex);
            console.log(`✅ Dashboard step ${stepIndex + 1} displayed`);
        }
    }, 300);
}

function createDashboardSpotlight(element) {
    const rect = element.getBoundingClientRect();
    const spotlight = document.createElement('div');
    spotlight.className = 'spotlight pulse';
    spotlight.id = 'dashboardTourSpotlight';
    
    const padding = 8;
    
    spotlight.style.top = (rect.top + window.scrollY - padding) + 'px';
    spotlight.style.left = (rect.left + window.scrollX - padding) + 'px';
    spotlight.style.width = (rect.width + padding * 2) + 'px';
    spotlight.style.height = (rect.height + padding * 2) + 'px';
    
    document.body.appendChild(spotlight);
}

function createDashboardLabel(element, text, position) {
    const rect = element.getBoundingClientRect();
    const label = document.createElement('div');
    label.className = `tour-label ${position}`;
    label.id = 'dashboardTourLabel';
    label.textContent = text;
    
    document.body.appendChild(label);
    
    const labelRect = label.getBoundingClientRect();
    let top, left;
    
    const extraPadding = 30;
    
    switch(position) {
        case 'top':
            top = rect.top + window.scrollY - labelRect.height - 20 - extraPadding;
            left = rect.left + window.scrollX + (rect.width / 2) - (labelRect.width / 2);
            break;
        case 'bottom':
            top = rect.bottom + window.scrollY + 20;
            left = rect.left + window.scrollX + (rect.width / 2) - (labelRect.width / 2);
            break;
        case 'left':
            top = rect.top + window.scrollY + (rect.height / 2) - (labelRect.height / 2);
            left = rect.left + window.scrollX - labelRect.width - 20;
            break;
        case 'right':
            top = rect.top + window.scrollY + (rect.height / 2) - (labelRect.height / 2);
            left = rect.right + window.scrollX + 20;
            break;
    }
    
    label.style.top = top + 'px';
    label.style.left = left + 'px';
}

function createDashboardTooltip(step, stepIndex) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tour-tooltip dashboard-tour-tooltip';
    tooltip.id = 'dashboardTourTooltip';
    
    const iconClass = step.iconType || 'primary';
    
    tooltip.innerHTML = `
        <div class="tour-tooltip-header">
            <div class="tour-tooltip-icon ${iconClass}">
                <i class="fas ${step.icon}"></i>
            </div>
            <div class="tour-tooltip-title">${step.title}</div>
        </div>
        <div class="tour-tooltip-content">${step.content}</div>
        <div class="tour-tooltip-footer">
            <div class="tour-progress">
                <div class="tour-progress-dots">
                    ${dashboardTourSteps.map((_, i) => `
                        <div class="tour-progress-dot ${i === stepIndex ? 'active' : ''}"></div>
                    `).join('')}
                </div>
                <div class="tour-progress-text">${stepIndex + 1}/${dashboardTourSteps.length}</div>
            </div>
            <div class="tour-footer-actions">
                <button class="tour-btn dashboard-tour-skip">
                    <i class="fas fa-times-circle"></i> ปิดแนะนำ
                </button>
                <div class="tour-buttons">
                    ${stepIndex > 0 ? 
                        '<button class="tour-btn dashboard-tour-prev"><i class="fas fa-arrow-left"></i> ก่อนหน้า</button>' : 
                        '<button class="tour-btn dashboard-tour-close" style="background: #E74C3C;">ปิดทัวร์</button>'}
                    ${stepIndex < dashboardTourSteps.length - 1 ? 
                        '<button class="tour-btn dashboard-tour-next">ถัดไป <i class="fas fa-arrow-right"></i></button>' : 
                        '<button class="tour-btn dashboard-tour-finish" style="background: #27AE60;">เสร็จสิ้น <i class="fas fa-check"></i></button>'}
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(tooltip);
    
    setTimeout(() => {
        positionDashboardTooltip(tooltip);
        attachDashboardTooltipEventListeners(tooltip, stepIndex);
    }, 50);
}

function positionDashboardTooltip(tooltip) {
    const rect = tooltip.getBoundingClientRect();
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    
    let top = (viewportHeight - rect.height) / 2 + window.scrollY;
    let left = (viewportWidth - rect.width) / 2;
    
    // ปรับตำแหน่งตาม step (9 steps)
    if (dashboardCurrentStep === 8) {  // Step 9 (logout) อยู่ล่างสุด
        top = window.scrollY + 80;
    } else if (dashboardCurrentStep === 6) {  // Step 7 (อีเมล) ขยับขวา
        left = viewportWidth - rect.width - 400;  // ชิดขวา
        top = window.scrollY + 120;
    } else if (dashboardCurrentStep === 7) {  // Step 8 (e-Service) ขยับซ้าย
        left = 350;  // ชิดซ้าย
        top = window.scrollY + 120;
    } else if (dashboardCurrentStep >= 4 && dashboardCurrentStep <= 5) {  // Step 5-6 (Back Office, Webboard)
        top = window.scrollY + 120;
    }
    
    top = Math.max(20, Math.min(top, window.scrollY + viewportHeight - rect.height - 20));
    left = Math.max(20, Math.min(left, viewportWidth - rect.width - 20));
    
    tooltip.style.top = top + 'px';
    tooltip.style.left = left + 'px';
}

function attachDashboardTooltipEventListeners(tooltip, stepIndex) {
    const closeBtn = tooltip.querySelector('.dashboard-tour-close');
    const nextBtn = tooltip.querySelector('.dashboard-tour-next');
    const prevBtn = tooltip.querySelector('.dashboard-tour-prev');
    const finishBtn = tooltip.querySelector('.dashboard-tour-finish');
    const skipBtn = tooltip.querySelector('.dashboard-tour-skip');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            endDashboardTour();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (stepIndex < dashboardTourSteps.length - 1) {
                showDashboardStep(stepIndex + 1);
            }
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (stepIndex > 0) {
                showDashboardStep(stepIndex - 1);
            }
        });
    }
    
    if (finishBtn) {
        finishBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            endDashboardTour();
        });
    }
    
    if (skipBtn) {
        skipBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            // ปิดและไม่แสดงอีก
            localStorage.setItem('dashboard_tour_skipped', 'true');
            endDashboardTour();
        });
    }
}

function removeDashboardTourElements() {
    const spotlight = document.getElementById('dashboardTourSpotlight');
    if (spotlight) spotlight.remove();
    
    const label = document.getElementById('dashboardTourLabel');
    if (label) label.remove();
    
    const tooltip = document.getElementById('dashboardTourTooltip');
    if (tooltip) tooltip.remove();
}

function endDashboardTour() {
    console.log('🛑 Ending dashboard tour...');
    
    dashboardIsActive = false;
    
    if (window.dashboardTourClickHandler) {
        document.removeEventListener('click', window.dashboardTourClickHandler);
        window.dashboardTourClickHandler = null;
    }
    if (window.dashboardTourKeyHandler) {
        document.removeEventListener('keydown', window.dashboardTourKeyHandler);
        window.dashboardTourKeyHandler = null;
    }
    
    removeDashboardTourElements();
    
    const overlay = document.getElementById('dashboardTourOverlay');
    if (overlay) overlay.classList.remove('active');
    
    setTimeout(() => {
        const startBtn = document.querySelector('.start-dashboard-tour-btn');
        if (startBtn) startBtn.style.display = 'flex';
    }, 100);
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
    localStorage.setItem('dashboard_tour_completed', 'true');
    
    console.log('✅ Dashboard tour ended successfully');
}

let dashboardResizeTimeout;
window.addEventListener('resize', () => {
    if (!dashboardIsActive) return;
    clearTimeout(dashboardResizeTimeout);
    dashboardResizeTimeout = setTimeout(() => {
        showDashboardStep(dashboardCurrentStep);
    }, 250);
});

window.addEventListener('load', () => {
    const autoStart = false;
    
    if (autoStart && !localStorage.getItem('dashboard_tour_completed')) {
        setTimeout(() => {
            console.log('🚀 Auto-starting dashboard tour...');
            startDashboardTour();
        }, 2000);
    }
});

console.log('✅ Dashboard Tour Guide System loaded successfully!');
</script>
	

</body>
</html>