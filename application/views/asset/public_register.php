<style>
    :root {
        /* Modern Soft Color Palette */
        --primary: #667eea;
        --primary-light: #7c3aed;
        --primary-dark: #5b21b6;
        --secondary: #10b981;
        --secondary-light: #34d399;
        --accent: #f59e0b;
        --accent-light: #fbbf24;

        /* Neutral Colors */
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;

        /* Semantic Colors */
        --success: #10b981;
        --warning: #f59e0b;
        --error: #ef4444;
        --info: #3b82f6;

        /* UI Colors */
        --bg-primary: #ffffff;
        --bg-secondary: var(--gray-50);
        --bg-tertiary: var(--gray-100);
        --text-primary: var(--gray-900);
        --text-secondary: var(--gray-600);
        --text-muted: var(--gray-500);
        --border-color: var(--gray-200);
        --border-focus: var(--primary);

        /* Shadow System */
        --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);

        /* Spacing */
        --space-xs: 0.25rem;
        --space-sm: 0.5rem;
        --space-md: 1rem;
        --space-lg: 1.5rem;
        --space-xl: 2rem;
        --space-2xl: 3rem;

        /* Border Radius */
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --radius-xl: 1rem;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Noto Sans Thai', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		font-size: 1.5rem;
        min-height: 100vh;
        background: var(--gray-100);
        color: var(--text-primary);
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .register-container {
        width: 100%;
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
        position: relative;
        z-index: 10;
    }

    .register-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .register-logo {
        width: 150px;
        height: 150px;
        margin-bottom: 1rem;
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        transition: transform 0.3s ease;
        background: var(--bg-primary);
        padding: 1rem;
    }

    .register-logo:hover {
        transform: translateY(-2px);
    }

    .register-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .register-subtitle {
        font-size: 1.125rem;
        color: var(--text-secondary);
        font-weight: 400;
    }

    .register-card {
        background: var(--bg-primary);
        border-radius: var(--radius-xl);
        padding: 4rem;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border-color);
        backdrop-filter: blur(10px);
    }

    .form-title {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 3rem;
        color: var(--text-primary);
        text-align: center;
        position: relative;
    }

    .form-title::after {
        content: '';
        position: absolute;
        bottom: -0.5rem;
        left: 50%;
        transform: translateX(-50%);
        width: 3rem;
        height: 2px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        border-radius: 1px;
    }

    .form-section {
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        padding: 2.5rem;
        margin-bottom: 2.5rem;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .form-section:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-sm);
    }

    .section-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .section-header h5 {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1.375rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-header i {
        color: var(--primary);
        font-size: 1.375rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        color: var(--text-primary);
        font-size: 1rem;
        letter-spacing: 0.025em;
    }

    .required-star {
        color: var(--error);
        margin-left: 0.25rem;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        color: var(--text-muted);
        font-size: 1.125rem;
        z-index: 2;
        transition: color 0.3s ease;
    }

    .input-field {
        width: 100%;
        padding: 1.25rem 1.25rem 1.25rem 3rem;
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--bg-primary);
        color: var(--text-primary);
    }

    .input-field:focus {
        border-color: var(--border-focus);
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .input-field:focus+.input-icon,
    .input-wrapper:focus-within .input-icon {
        color: var(--primary);
    }

    .input-field::placeholder {
        color: var(--text-muted);
    }

    .input-field[readonly] {
        background: var(--bg-tertiary);
        color: var(--text-secondary);
        cursor: not-allowed;
    }

    .form-select-wrapper {
        position: relative;
    }

    .form-select-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1.125rem;
        z-index: 2;
        transition: color 0.3s ease;
    }

    .form-select {
        width: 100%;
        padding: 1.25rem 2.5rem 1.25rem 3rem;
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 1rem;
        transition: all 0.3s ease;
        appearance: none;
        background: var(--bg-primary);
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1.125rem;
        color: var(--text-primary);
    }

    .form-select:focus {
        border-color: var(--border-focus);
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-select:focus+.form-select-icon {
        color: var(--primary);
    }

    .register-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 2rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border: none;
        border-radius: var(--radius-md);
        color: white;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
        letter-spacing: 0.025em;
    }

    .register-btn:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
    }

    .register-btn:active {
        transform: translateY(0);
    }

    .login-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        color: var(--primary);
        background: var(--bg-primary);
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-md);
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .login-link:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        border-color: var(--primary);
        color: var(--primary-dark);
    }

    .error-feedback {
        color: var(--error);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
        font-weight: 500;
    }

    .form-text {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .loading-icon {
        display: none;
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: translateY(-50%) rotate(0deg);
        }

        to {
            transform: translateY(-50%) rotate(360deg);
        }
    }

    /* Avatar Selection */
    .avatar-selection {
        background: var(--bg-primary);
        border-radius: var(--radius-lg);
        padding: 2rem;
        border: 1.5px solid var(--border-color);
    }

    .avatar-label {
        font-weight: 500;
        margin-bottom: 1rem;
        color: var(--text-primary);
        font-size: 1rem;
    }

    .avatar-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .avatar-option {
        position: relative;
        text-align: center;
    }

    .avatar-radio {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .avatar-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .avatar-radio:checked+.avatar-label .avatar-img {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        transform: scale(1.05);
    }

    .avatar-img:hover {
        transform: scale(1.05);
        border-color: var(--primary);
    }

    .upload-option {
        border-top: 1px solid var(--border-color);
        padding-top: 1.5rem;
        margin-top: 1.5rem;
    }

    .file-upload-wrapper {
        position: relative;
    }

    .file-upload-input {
        width: 100%;
        padding: 1.25rem 1.25rem 1.25rem 3rem;
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--bg-primary);
    }

    .file-upload-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1.125rem;
    }

    .footer-text {
        text-align: center;
        margin-top: 2rem;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .footer-text a {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 500;
        transition: opacity 0.3s ease;
    }

    .footer-text a:hover {
        opacity: 0.8;
    }

    .as-highlight {
        color: var(--accent-light);
        font-weight: 700;
    }

    /* Address Preview */
    .alert {
        border-radius: var(--radius-md);
        border: 1px solid var(--info);
        background: rgba(59, 130, 246, 0.05);
        color: var(--info);
        padding: 1rem;
        font-size: 0.875rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .register-container {
            margin: 1rem auto;
            padding: 0 1rem;
            max-width: 100%;
        }

        .register-card {
            padding: 2rem;
        }

        .register-title {
            font-size: 1.5rem;
        }

        .register-logo {
            width: 60px;
            height: 60px;
        }

        .form-section {
            padding: 1.5rem;
        }

        .avatar-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .avatar-img {
            width: 60px;
            height: 60px;
        }
    }

    @media (max-width: 576px) {
        .register-title {
            font-size: 1.25rem;
        }

        .register-subtitle {
            font-size: 1rem;
        }

        .form-title {
            font-size: 1.25rem;
        }

        .avatar-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .form-section {
            padding: 1rem;
        }

        .register-card {
            padding: 1.5rem;
        }
    }

    /* Modern Floating Particles */
    .floating-particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
    }

    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(100, 116, 139, 0.2);
        border-radius: 50%;
        animation: modernFloat 20s infinite linear;
    }

    @keyframes modernFloat {
        0% {
            transform: translateY(100vh) scale(0);
            opacity: 1;
        }

        10% {
            opacity: 1;
            transform: translateY(90vh) scale(1);
        }

        90% {
            opacity: 1;
            transform: translateY(10vh) scale(1);
        }

        100% {
            transform: translateY(-10vh) scale(0);
            opacity: 0;
        }
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: var(--gray-100);
    }

    ::-webkit-scrollbar-thumb {
        background: var(--gray-300);
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--gray-400);
    }

    /* Focus Styles */
    .form-section:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Enhanced Button States */
    .register-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Modern Toggle Styles */
    .input-field:disabled {
        background: var(--gray-100);
        color: var(--gray-500);
        border-color: var(--gray-200);
    }
</style>