<?php
/**
 * Apple-inspired CSS Styles for Google Drive Member Interface
 * รวมจาก 2 ไฟล์เป็นไฟล์เดียว
 */
?>
<style>
    * {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .apple-blur {
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
    }
    
    .apple-shadow {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .apple-shadow-lg {
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: saturate(180%) blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3);
    }
    
    /* File Grid */
    .file-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
    }
    
    .file-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        padding: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .file-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
        background: rgba(255, 255, 255, 1);
    }
    
    .folder-card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.08);
    }
    
    .folder-card:hover {
        background: rgba(255, 255, 255, 1);
        box-shadow: 0 16px 40px rgba(59, 130, 246, 0.15);
    }

    /* Folder Tree Styles */
    .folder-tree {
        background: rgba(248, 250, 252, 0.8);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .folder-tree-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin: 2px 0;
    }

    .folder-tree-item:hover {
        background: rgba(59, 130, 246, 0.1);
        transform: translateX(4px);
    }

    .folder-tree-item.active {
        background: rgba(59, 130, 246, 0.15);
        border-left: 3px solid #3b82f6;
    }

    .folder-tree-children {
        margin-left: 20px;
        padding-left: 12px;
        border-left: 1px solid rgba(148, 163, 184, 0.3);
    }

    .folder-tree-toggle {
        width: 16px;
        height: 16px;
        margin-right: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #64748b;
        transition: transform 0.2s ease;
    }

    .folder-tree-toggle.expanded {
        transform: rotate(90deg);
    }

    /* Trial Badge */
    .trial-badge {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    }

    /* Trial Warning */
    .trial-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.1) 100%);
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    /* Stats Cards */
    .stats-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
        background: rgba(255, 255, 255, 1);
    }

    /* Button Styles */
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
        transform: translateY(0);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(59, 130, 246, 0.4);
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(139, 92, 246, 0.3);
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(139, 92, 246, 0.4);
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 16px rgba(239, 68, 68, 0.3);
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(239, 68, 68, 0.4);
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }

    /* Input Styles */
    .input-field {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(209, 213, 219, 0.5);
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .input-field:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: rgba(255, 255, 255, 1);
    }

    /* Modal Styles */
    .modal-content {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: saturate(180%) blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    /* Progress Bar */
    .progress-bar {
        background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
        height: 8px;
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    /* Loading Spinner */
    .loading-spinner {
        border: 4px solid rgba(59, 130, 246, 0.1);
        border-left: 4px solid #3b82f6;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* View Mode & Sort Button Styles */
    .view-mode-btn, .sort-btn {
        color: #6b7280;
        background: transparent;
        border: none;
        transition: all 0.2s ease;
    }

    .view-mode-btn:hover, .sort-btn:hover {
        color: #374151;
        background: rgba(59, 130, 246, 0.1);
        transform: translateY(-1px);
    }

    .view-mode-btn.active, .sort-btn.active {
        color: #3b82f6;
        background: rgba(59, 130, 246, 0.15);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
    }

    .view-mode-btn.active:hover, .sort-btn.active:hover {
        color: #2563eb;
        background: rgba(59, 130, 246, 0.2);
    }

    /* Share Modal Components */
.share-type-card {
    transition: all 0.2s ease;
}

.share-type-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.share-type-card.selected {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-color: #3b82f6;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.2);
}
	
	.permission-btn, .access-btn {
    transition: all 0.2s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.permission-btn:hover, .access-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
	

    .email-permission-btn, .link-permission-btn, .link-access-btn {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .email-permission-btn:hover, .link-permission-btn:hover, .link-access-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .email-permission-btn.active {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
    color: white !important;
}

.link-permission-btn.active {
    background: linear-gradient(135deg, #10b981, #059669) !important;
    color: white !important;
}

.link-access-btn.active {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
    color: white !important;
}


    /* Responsive Design */
    @media (max-width: 768px) {
        .file-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }
        
        .file-card {
            padding: 16px;
        }
        
        .glass-card {
            margin: 8px;
            border-radius: 16px;
        }
    }

    @media (max-width: 640px) {
        .file-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.75rem;
        }
        
        .stats-card {
            padding: 16px;
        }
    }
</style>


<style>
.modal-content {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.modal-header.bg-danger {
    border-bottom: none;
}

.btn-contact-granter {
    width: 30px;
    height: 30px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.permission-granter-item {
    transition: all 0.2s ease;
}

.permission-granter-item:hover {
    background-color: #f8f9fa !important;
    transform: translateY(-1px);
}

#permission-granters-list {
    max-height: 300px;
    overflow-y: auto;
}

.alert {
    border-radius: 8px;
}

.modal-body {
    padding: 1.5rem;
}

.card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.card-header {
    border-bottom: 1px solid #dee2e6;
    border-radius: 8px 8px 0 0 !important;
}

/* Custom scrollbar */
#permission-granters-list::-webkit-scrollbar {
    width: 6px;
}

#permission-granters-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#permission-granters-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#permission-granters-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>