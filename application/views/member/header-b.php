<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการสมาชิก</title>
    <link rel="icon" href="<?php echo base_url("docs/logo.png"); ?>" type="image/x-icon">
    
    <!-- Tailwind CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.31/dist/sweetalert2.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    


    <style>
        /* เพิ่ม transition เพื่อความสวยงาม */
        .menu-grid {
            transition: opacity 0.3s ease;
        }
        
        /* ปรับแต่ง checkbox ให้สวยงาม */
        .form-checkbox {
            cursor: pointer;
        }
        
        /* ปรับขนาดและสีของ label เมื่อ disabled */
        .form-checkbox:disabled + label {
            color: #9CA3AF;
            cursor: not-allowed;
        }

        /* 🚨 REQUIRED: Session Warning Modals Styles - สำหรับ Tailwind */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.75);
            z-index: 9998;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-content {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 28rem;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.95) translateY(-10px);
            transition: transform 0.3s ease;
            z-index: 9999;
            position: relative;
        }
        
        .modal-overlay.show .modal-content {
            transform: scale(1) translateY(0);
        }
        
        .timeout-icon, .logout-icon {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .timeout-title, .logout-title {
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .timeout-message, .logout-message {
            line-height: 1.6;
            color: #6B7280;
        }
        
        /* ปรับสำหรับ responsive */
        @media (max-width: 640px) {
            .timeout-icon i, .logout-icon i {
                font-size: 3rem !important;
            }
            .timeout-title, .logout-title {
                font-size: 1.2rem;
            }
            .modal-content {
                margin: 1rem;
                width: calc(100% - 2rem);
            }
        }
        
        /* Alert styles สำหรับ notification */
        .alert-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            min-width: 300px;
            max-width: 500px;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .alert-success {
            background-color: #D1FAE5;
            border: 1px solid #A7F3D0;
            color: #065F46;
        }
        
        .alert-danger {
            background-color: #FEE2E2;
            border: 1px solid #FECACA;
            color: #991B1B;
        }
        
        .alert-warning {
            background-color: #FEF3C7;
            border: 1px solid #FDE68A;
            color: #92400E;
        }
        
        .alert-info {
            background-color: #DBEAFE;
            border: 1px solid #BFDBFE;
            color: #1E40AF;
        }

        /* Back to top button */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
            z-index: 1000;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3B82F6, #1D4ED8);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .back-to-top:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
        }
    </style>
</head>

<body class="bg-gray-50">