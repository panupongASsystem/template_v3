<div class="text-center pages-head">
    <span class="font-pages-head">แบบประเมินความพึงพอใจการให้บริการ</span>
</div>
</div>
<img src="<?php echo base_url('docs/welcome-btm-light-other.png'); ?>">

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<style>
/* Apple-inspired Assessment Form Styles */
:root {
    --primary-color: #007AFF;
    --secondary-color: #5856D6;
    --success-color: #34C759;
    --warning-color: #FF9500;
    --error-color: #FF3B30;
    --text-primary: #1D1D1F;
    --text-secondary: #86868B;
    --background-primary: #F5F5F7;
    --background-secondary: #FFFFFF;
    --border-color: #D2D2D7;
    --shadow-light: 0 2px 8px rgba(0, 0, 0, 0.04);
    --shadow-medium: 0 4px 16px rgba(0, 0, 0, 0.08);
    --shadow-heavy: 0 8px 32px rgba(0, 0, 0, 0.12);
    --border-radius: 12px;
    --border-radius-large: 20px;
}

* {
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #F5F5F7 0%, #FAFAFA 100%);
    color: var(--text-primary);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
    line-height: 1.5;
}

.public-notice {
    background: linear-gradient(135deg, #E3F2FD 0%, #F3E5F5 100%);
    border: 1px solid rgba(121, 134, 203, 0.1);
    border-radius: var(--border-radius-large);
    padding: 24px;
    margin-bottom: 32px;
    text-align: center;
    box-shadow: var(--shadow-light);
    backdrop-filter: blur(10px);
}

.public-notice h3 {
    color: var(--primary-color);
    margin-bottom: 12px;
    font-size: 1.25rem;
    font-weight: 600;
}

.public-notice p {
    color: var(--text-secondary);
    margin-bottom: 0;
    font-size: 1rem;
    font-weight: 400;
}

.assessment-header {
    text-align: center;
    padding: 40px 0;
    margin-bottom: 40px;
}

.assessment-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 16px;
    color: var(--text-primary);
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.assessment-header p {
    font-size: 1.125rem;
    color: var(--text-secondary);
    max-width: 600px;
    margin: 0 auto;
    font-weight: 400;
}

<?php if (isset($settings['show_progress_bar']) && $settings['show_progress_bar'] == '1'): ?>
.progress-container {
    background: var(--background-secondary);
    padding: 24px;
    border-radius: var(--border-radius);
    margin-bottom: 32px;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-light);
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: #E5E5EA;
    border-radius: 3px;
    overflow: hidden;
    position: relative;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: 3px;
    transition: width 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    width: 0%;
    position: relative;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-text {
    text-align: center;
    margin-top: 12px;
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
}
<?php endif; ?>

.alert {
    padding: 16px 20px;
    margin-bottom: 24px;
    border-radius: var(--border-radius);
    border: none;
    box-shadow: var(--shadow-light);
    font-weight: 500;
}

.alert-danger {
    background: linear-gradient(135deg, #FFE5E5 0%, #FFF0F0 100%);
    color: var(--error-color);
}

.alert-success {
    background: linear-gradient(135deg, #E8F5E8 0%, #F0FFF0 100%);
    color: var(--success-color);
}

.alert-info {
    background: linear-gradient(135deg, #E3F2FD 0%, #F0F8FF 100%);
    color: var(--primary-color);
}

.section {
    margin-bottom: 48px;
}

.section-header {
    background: var(--background-secondary);
    padding: 28px;
    border-radius: var(--border-radius-large);
    margin-bottom: 32px;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-medium);
    position: relative;
    overflow: hidden;
}

.section-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-title i {
    color: var(--primary-color);
    font-size: 1.25rem;
}

.section-description {
    color: var(--text-secondary);
    font-size: 1rem;
    font-weight: 400;
}

.question-group {
    margin-bottom: 32px;
    padding: 24px;
    background: var(--background-secondary);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-light);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.question-group:hover {
    box-shadow: var(--shadow-medium);
    transform: translateY(-2px);
}

.question-label {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 20px;
    display: block;
    line-height: 1.4;
}

.required {
    color: var(--error-color);
    margin-left: 6px;
    font-weight: 700;
}

.radio-group {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.radio-item {
    position: relative;
}

.radio-item input[type="radio"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    width: 0;
    height: 0;
}

.radio-label {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 20px;
    background: var(--background-secondary);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    font-size: 1rem;
    font-weight: 500;
    min-width: 120px;
    color: var(--text-primary);
    box-shadow: var(--shadow-light);
}

.radio-label:hover {
    border-color: var(--primary-color);
    background: #F0F8FF;
    box-shadow: var(--shadow-medium);
    transform: translateY(-1px);
}

.radio-item input[type="radio"]:checked + .radio-label {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow-heavy);
    font-weight: 600;
}

.radio-item input[type="radio"]:checked + .radio-label::before {
    content: '✓';
    margin-right: 8px;
    font-weight: bold;
    font-size: 1.1rem;
}

.rating-scale {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.9) 100%);
    border-radius: var(--border-radius-large);
    padding: 32px;
    margin: 32px 0;
    border: 1px solid rgba(226, 232, 240, 0.5);
    box-shadow: 
        0 10px 25px rgba(0, 0, 0, 0.05),
        0 4px 10px rgba(0, 0, 0, 0.03),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
    position: relative;
    overflow: hidden;
}

.rating-scale::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), #34C759, var(--warning-color), var(--error-color));
    border-radius: var(--border-radius-large) var(--border-radius-large) 0 0;
}

.rating-header {
    text-align: center;
    margin-bottom: 28px;
    position: relative;
}

.rating-header h3 {
    color: var(--text-primary);
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 8px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.rating-header h3::before {
    content: '📊';
    font-size: 1.25rem;
    background: none;
    -webkit-text-fill-color: initial;
}

.rating-legend {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.rating-item {
    background: linear-gradient(135deg, rgba(255,255,255,0.8) 0%, rgba(248,250,252,0.8) 100%);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 16px 12px;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
}

.rating-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    transition: all 0.3s ease;
}

.rating-item:nth-child(1)::before { background: var(--error-color); }
.rating-item:nth-child(2)::before { background: var(--warning-color); }
.rating-item:nth-child(3)::before { background: #8E8E93; }
.rating-item:nth-child(4)::before { background: var(--primary-color); }
.rating-item:nth-child(5)::before { background: var(--success-color); }

.rating-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
    border-color: var(--primary-color);
}

.rating-label-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
    display: block;
}

.rating-score {
    font-size: 1.1rem;
    font-weight: 700;
    display: block;
}

.rating-item:nth-child(1) .rating-score { color: var(--error-color); }
.rating-item:nth-child(2) .rating-score { color: var(--warning-color); }
.rating-item:nth-child(3) .rating-score { color: #8E8E93; }
.rating-item:nth-child(4) .rating-score { color: var(--primary-color); }
.rating-item:nth-child(5) .rating-score { color: var(--success-color); }

.rating-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 20px;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-light);
}

.rating-table th,
.rating-table td {
    padding: 16px 12px;
    text-align: center;
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.2s ease;
}

.rating-table th {
    background: var(--background-primary);
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.9rem;
}

.rating-table .question-cell {
    text-align: left;
    font-weight: 500;
    background: var(--background-secondary);
    color: var(--text-primary);
}

.rating-table .category-row {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    font-weight: 700;
}

.rating-table tbody tr:hover {
    background: var(--background-primary);
}

.rating-table .category-row:hover {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
}

.rating-radio {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: var(--primary-color);
    transform: scale(1.2);
}

.text-input {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-size: 1rem;
    font-family: inherit;
    transition: all 0.3s ease;
    background: var(--background-secondary);
    color: var(--text-primary);
    box-shadow: var(--shadow-light);
}

.text-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
    background: #FAFAFA;
}

.text-input:disabled {
    background: var(--background-primary);
    color: var(--text-secondary);
    cursor: not-allowed;
}

.text-group {
    margin-top: 16px;
}

.textarea-field {
    width: 100%;
    min-height: 120px;
    padding: 16px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-size: 1rem;
    font-family: inherit;
    resize: vertical;
    transition: all 0.3s ease;
    background: var(--background-secondary);
    color: var(--text-primary);
    box-shadow: var(--shadow-light);
}

.textarea-field:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
    background: #FAFAFA;
}

.other-input {
    margin-top: 12px;
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--background-secondary);
    color: var(--text-primary);
    box-shadow: var(--shadow-light);
}

.other-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
}

.other-input:disabled {
    background: var(--background-primary);
    color: var(--text-secondary);
    cursor: not-allowed;
}

.submit-container {
    text-align: center;
    padding: 40px 0;
    border-top: 1px solid var(--border-color);
    margin-top: 48px;
}

.submit-btn {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    padding: 16px 48px;
    border: none;
    border-radius: 50px;
    font-size: 1.125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    box-shadow: var(--shadow-medium);
    position: relative;
    overflow: hidden;
}

.submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.submit-btn:hover::before {
    left: 100%;
}

.submit-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-heavy);
}

.submit-btn:active {
    transform: translateY(-1px);
    box-shadow: var(--shadow-medium);
}

.submit-btn:disabled {
    background: #8E8E93;
    cursor: not-allowed;
    transform: none;
    box-shadow: var(--shadow-light);
}

.submit-btn i {
    margin-right: 10px;
    font-size: 1rem;
}

.preview-notice {
    background: linear-gradient(135deg, #FFF3CD 0%, #FFF8E1 100%);
    color: var(--warning-color);
    padding: 16px 20px;
    border-radius: var(--border-radius);
    font-weight: 600;
    box-shadow: var(--shadow-light);
    border: 1px solid rgba(255, 149, 0, 0.2);
}

.preview-notice i {
    margin-right: 8px;
}

/* reCAPTCHA Notice */
.recaptcha-notice {
    background: linear-gradient(135deg, #E8F5E8 0%, #F0FFF0 100%);
    border: 1px solid rgba(52, 199, 89, 0.2);
    border-radius: var(--border-radius);
    padding: 16px 20px;
    margin: 20px 0;
    text-align: center;
    color: var(--success-color);
    font-size: 0.9rem;
    font-weight: 500;
    box-shadow: var(--shadow-light);
}

.recaptcha-notice i {
    margin-right: 8px;
    color: var(--success-color);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    :root {
        --border-radius: 10px;
        --border-radius-large: 16px;
    }

    .assessment-header h1 {
        font-size: 2rem;
    }

    .assessment-header p {
        font-size: 1rem;
    }

    .section-header {
        padding: 20px;
    }

    .section-title {
        font-size: 1.25rem;
    }

    .question-group {
        padding: 20px;
    }

    .radio-group {
        flex-direction: column;
    }

    .radio-label {
        min-width: auto;
        justify-content: flex-start;
        padding: 14px 18px;
    }

    .rating-scale {
        padding: 20px;
    }

    .rating-legend {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .rating-item {
        padding: 12px;
    }

    .rating-table {
        font-size: 0.85rem;
    }

    .rating-table th,
    .rating-table td {
        padding: 12px 8px;
    }

    .submit-btn {
        padding: 14px 32px;
        font-size: 1rem;
        width: 100%;
        max-width: 300px;
    }
}

/* Smooth animations */
* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Glassmorphism effects */
.glass-effect {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    background: rgba(255, 255, 255, 0.7);
}
</style>

<!-- แสดง Flash Messages -->
<?php if (!empty($success_message)): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<?php if (!empty($info_message)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <?php echo $info_message; ?>
    </div>
<?php endif; ?>

<div class="container-pages-news" style="margin-top: -200px;">
<!-- Notice สำหรับการเข้าถึงสาธารณะ -->
<div class="public-notice">
    <h2><i class="fas fa-users"></i> แบบประเมินความพึงพอใจการให้บริการ</h2>
    <p>กรุณาทำเครื่องหมาย ✓ ในข้อที่ตรงกับความเป็นจริงและในช่องที่ตรงกับความคิดเห็นของท่านมากที่สุด</p>
</div>

<?php if (isset($settings['show_progress_bar']) && $settings['show_progress_bar'] == '1'): ?>
<div class="progress-container">
    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>
    <div class="progress-text">
        <span id="progressText">ความคืบหน้า: 0%</span>
    </div>
</div>
<?php endif; ?>

<?php echo form_open('assessment/submit', ['id' => 'assessmentForm']); ?>
    <?php foreach ($assessment as $category): ?>
        <div class="section">
            <div class="section-header">
                <div class="section-title">
                    <?php if ($category->category_name == 'ข้อมูลทั่วไปของผู้ตอบ'): ?>
                        <i class="fas fa-user"></i>
                    <?php elseif (strpos($category->category_name, 'การให้บริการ') !== false): ?>
                        <i class="fas fa-handshake"></i>
                    <?php elseif (strpos($category->category_name, 'บุคลากร') !== false): ?>
                        <i class="fas fa-users"></i>
                    <?php elseif (strpos($category->category_name, 'สถานที่') !== false): ?>
                        <i class="fas fa-building"></i>
                    <?php elseif (strpos($category->category_name, 'ข้อเสนอแนะ') !== false): ?>
                        <i class="fas fa-comment-dots"></i>
                    <?php else: ?>
                        <i class="fas fa-star"></i>
                    <?php endif; ?>
                    <?php echo $category->category_name; ?>
                </div>
                <div class="section-description">
                    <?php if ($category->category_name == 'ข้อมูลทั่วไปของผู้ตอบ'): ?>
                        กรุณากรอกข้อมูลพื้นฐานของท่าน
                    <?php elseif (strpos($category->category_name, 'การให้บริการ') !== false || strpos($category->category_name, 'บุคลากร') !== false || strpos($category->category_name, 'สถานที่') !== false): ?>
                        กรุณาให้คะแนนในแต่ละหัวข้อ
                    <?php elseif (strpos($category->category_name, 'ข้อเสนอแนะ') !== false): ?>
                        กรุณาแสดงความคิดเห็นและข้อเสนอแนะ
                    <?php endif; ?>
                </div>
            </div>

            <?php if ((strpos($category->category_name, 'การให้บริการ') !== false || strpos($category->category_name, 'บุคลากร') !== false || strpos($category->category_name, 'สถานที่') !== false) && strpos($category->category_name, 'ข้อเสนอแนะ') === false): ?>
                <!-- Rating Scale Legend -->
                <div class="rating-scale">
                    <div class="rating-header">
                        <h3>เกณฑ์การให้คะแนน</h3>
                    </div>
                    <div class="rating-legend">
                        <div class="rating-item">
                            <span class="rating-label-text">ควรปรับปรุง</span>
                            <span class="rating-score">1 คะแนน</span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label-text">พอใช้</span>
                            <span class="rating-score">2 คะแนน</span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label-text">ปานกลาง</span>
                            <span class="rating-score">3 คะแนน</span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label-text">ดี</span>
                            <span class="rating-score">4 คะแนน</span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label-text">ดีมาก</span>
                            <span class="rating-score">5 คะแนน</span>
                        </div>
                    </div>
                </div>

                <!-- Rating Table -->
                <table class="rating-table">
                    <thead>
                        <tr>
                            <th style="width: 60%;">คุณภาพการบริการ</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="category-row">
                            <td colspan="6"><?php echo $category->category_name; ?></td>
                        </tr>
                        <?php foreach ($category->questions as $question): ?>
                            <tr>
                                <td class="question-cell">
                                    <?php echo $question->question_order . '. ' . $question->question_text; ?>
                                    <?php if ($question->is_required): ?><span class="required">*</span><?php endif; ?>
                                </td>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <td>
                                        <input type="radio" 
                                               name="question_<?php echo $question->id; ?>" 
                                               value="<?php echo $i; ?>" 
                                               class="rating-radio" 
                                               <?php echo $question->is_required ? 'required' : ''; ?>>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <!-- Regular Questions -->
                <?php foreach ($category->questions as $question): ?>
                    <div class="question-group">
                        <label class="question-label">
                            <?php echo $question->question_order . '. ' . $question->question_text; ?>
                            <?php if ($question->is_required): ?><span class="required">*</span><?php endif; ?>
                        </label>

                        <?php if ($question->question_type === 'textarea'): ?>
                            <!-- Textarea Question -->
                            <div class="text-group">
                                <textarea name="question_<?php echo $question->id; ?>" 
                                         class="textarea-field" 
                                         placeholder="กรุณาใส่ข้อเสนอแนะของท่าน..."
                                         rows="5"
                                         <?php echo $question->is_required ? 'required' : ''; ?>></textarea>
                            </div>

                        <?php elseif ($question->question_type === 'radio' && isset($question->options) && !empty($question->options)): ?>
                            <!-- Radio Question -->
                            <div class="radio-group">
                                <?php foreach ($question->options as $option): ?>
                                    <div class="radio-item">
                                        <input type="radio" 
                                               id="q<?php echo $question->id; ?>_opt<?php echo $option->id; ?>" 
                                               name="question_<?php echo $question->id; ?>" 
                                               value="<?php echo $option->option_value; ?>" 
                                               <?php echo $question->is_required ? 'required' : ''; ?>
                                               data-other="<?php echo ($option->option_value == 'อื่นๆ') ? 'true' : 'false'; ?>">
                                        <label for="q<?php echo $question->id; ?>_opt<?php echo $option->id; ?>" class="radio-label">
                                            <?php echo $option->option_text; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Other input field (for occupation) -->
                            <?php if (strpos($question->question_text, 'อาชีพ') !== false): ?>
                                <input type="text" 
                                       id="question_<?php echo $question->id; ?>_other" 
                                       name="question_<?php echo $question->id; ?>_other" 
                                       class="other-input" 
                                       placeholder="โปรดระบุ..." 
                                       disabled>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- Text input for other question types -->
                            <div class="text-group">
                                <input type="text" 
                                       name="question_<?php echo $question->id; ?>" 
                                       class="text-input" 
                                       placeholder="กรุณาใส่คำตอบของท่าน..."
                                       <?php echo $question->is_required ? 'required' : ''; ?>>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="submit-container">
        <?php if (!isset($is_preview) || !$is_preview): ?>
            <!-- reCAPTCHA Notice -->
            <div class="recaptcha-notice">
                <i class="fas fa-shield-alt"></i>
                เว็บไซต์นี้ได้รับการคุ้มครองโดย reCAPTCHA และมีการใช้
                <a href="https://policies.google.com/privacy" target="_blank" style="color: var(--primary-color);">นโยบายความเป็นส่วนตัว</a>
                และ
                <a href="https://policies.google.com/terms" target="_blank" style="color: var(--primary-color);">เงื่อนไขการใช้บริการ</a>
                ของ Google
            </div>
            
            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fas fa-paper-plane"></i> ส่งแบบประเมิน
            </button>
        <?php else: ?>
            <div class="preview-notice">
                <i class="fas fa-eye"></i> นี่คือหน้าตัวอย่างแบบประเมิน
            </div>
        <?php endif; ?>
    </div>
<?php echo form_close(); ?>
</div>
<!-- jQuery Library (เพิ่มก่อน script หลัก) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- reCAPTCHA v3 Script -->
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo get_config_value('recaptcha'); ?>" async defer></script>

<script>
// *** ตั้งค่า reCAPTCHA Key จาก Database ***
window.RECAPTCHA_KEY = '<?php echo get_config_value("recaptcha"); ?>';

document.addEventListener('DOMContentLoaded', function() {
    console.log('Assessment form with reCAPTCHA loaded');
    console.log('reCAPTCHA Key:', window.RECAPTCHA_KEY ? window.RECAPTCHA_KEY.substring(0, 20) + '...' : 'Not found');

    // ===== HELPER FUNCTIONS (ประกาศก่อนใช้งาน) =====
    
    /**
     * ตรวจสอบความถูกต้องของฟอร์ม
     */
    function validateForm(form) {
        let isValid = true;
        let errorMessages = [];

        // ตรวจสอบ required radio groups
        const requiredRadioGroups = {};
        form.querySelectorAll('input[type="radio"][required]').forEach(input => {
            requiredRadioGroups[input.name] = true;
        });

        Object.keys(requiredRadioGroups).forEach(groupName => {
            if (!form.querySelector(`input[name="${groupName}"]:checked`)) {
                isValid = false;
                errorMessages.push('กรุณาเลือกคำตอบสำหรับคำถามที่จำเป็น');
            }
        });

        // ตรวจสอบ required textareas และ text inputs
        form.querySelectorAll('textarea[required], input[type="text"][required]:not(.other-input)').forEach(field => {
            if (field.value.trim() === '') {
                isValid = false;
                errorMessages.push('กรุณากรอกข้อมูลในช่องที่จำเป็น');
            }
        });

        // ตรวจสอบ other inputs ที่เปิดใช้งาน
        form.querySelectorAll('.other-input:not(:disabled)').forEach(input => {
            if (input.value.trim() === '') {
                isValid = false;
                errorMessages.push('กรุณาระบุข้อมูลในช่อง "อื่นๆ"');
                input.focus();
            }
        });

        if (!isValid) {
            alert('กรุณากรอกข้อมูลให้ครบถ้วน:\n• ' + [...new Set(errorMessages)].join('\n• '));
        }

        return isValid;
    }

    /**
     * จัดการข้อผิดพลาดในการส่งฟอร์ม
     */
    function handleSubmissionError(submitBtn, originalText, errorMessage) {
        console.error('Submission error:', errorMessage);
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        alert('เกิดข้อผิดพลาด: ' + errorMessage);
    }

    /**
     * เพิ่ม hidden field ลงในฟอร์ม
     */
    function addHiddenField(form, name, value) {
        // ลบ field เดิมถ้ามี
        const existingField = form.querySelector(`input[name="${name}"]`);
        if (existingField) {
            existingField.remove();
        }
        
        // เพิ่ม field ใหม่
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = name;
        hiddenField.value = value;
        form.appendChild(hiddenField);
    }

    /**
     * ส่งฟอร์มผ่าน AJAX (รวม reCAPTCHA และ fallback)
     */
    function submitFormAjax(form, submitBtn, originalText) {
        console.log('📤 Submitting assessment form...');
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังส่งแบบประเมิน...';
        
        const formData = new FormData(form);
        
        $.ajax({
            url: form.action || 'assessment/submit',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            timeout: 30000,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            success: function(response) {
                console.log('✅ Assessment submission response:', response);
                
                if (response && response.status === 'success') {
                    submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> ส่งสำเร็จ';
                    
                    // Redirect ไปหน้า thank you
                    setTimeout(() => {
                        if (response.redirect_url) {
                            window.location.href = response.redirect_url;
                        } else {
                            window.location.href = 'assessment/thank_you';
                        }
                    }, 800);
                    
                } else {
                    // แสดงข้อผิดพลาด
                    const errorMessage = (response && response.message) ? response.message : 'เกิดข้อผิดพลาดในการส่งข้อมูล';
                    handleSubmissionError(submitBtn, originalText, errorMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Assessment submission error:', {status, error, httpStatus: xhr.status});
                
                let errorMessage = 'เกิดข้อผิดพลาดในการส่งข้อมูล';
                
                // จัดการข้อผิดพลาดแบบละเอียด
                if (status === 'timeout') {
                    errorMessage = 'การเชื่อมต่อใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง';
                } else if (status === 'parsererror') {
                    // ถ้าเป็น parsererror และ status 200 แสดงว่าส่งสำเร็จแล้ว
                    if (xhr.status === 200) {
                        submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> ส่งสำเร็จ';
                        setTimeout(() => {
                            window.location.href = 'assessment/thank_you';
                        }, 800);
                        return;
                    }
                    errorMessage = 'เกิดข้อผิดพลาดในการประมวลผลข้อมูล';
                } else if (xhr.status === 500) {
                    errorMessage = 'เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์ กรุณาลองใหม่อีกครั้ง';
                } else if (xhr.status === 0) {
                    errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้';
                }
                
                handleSubmissionError(submitBtn, originalText, errorMessage);
            }
        });
    }

    // ===== PROGRESS TRACKING (เก็บการทำงานเดิม) =====
    
    function updateProgress() {
        <?php if (isset($settings['show_progress_bar']) && $settings['show_progress_bar'] == '1'): ?>
        const form = document.getElementById('assessmentForm');
        const requiredInputs = form.querySelectorAll('input[required], textarea[required]');
        const radioGroups = {};
        
        // Group radio inputs by name
        requiredInputs.forEach(input => {
            if (input.type === 'radio') {
                radioGroups[input.name] = radioGroups[input.name] || [];
                radioGroups[input.name].push(input);
            }
        });

        let filledCount = 0;
        const totalGroups = Object.keys(radioGroups).length + 
                           form.querySelectorAll('textarea[required], input[type="text"][required]:not(.other-input)').length;

        // Check radio groups
        Object.keys(radioGroups).forEach(groupName => {
            if (form.querySelector(`input[name="${groupName}"]:checked`)) {
                filledCount++;
            }
        });

        // Check textareas and text inputs
        form.querySelectorAll('textarea[required], input[type="text"][required]:not(.other-input)').forEach(field => {
            if (field.value.trim() !== '') {
                filledCount++;
            }
        });

        const progress = Math.round((filledCount / totalGroups) * 100);
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');

        if (progressFill && progressText) {
            progressFill.style.width = progress + '%';
            progressText.textContent = `ความคืบหน้า: ${progress}%`;
        }
        <?php endif; ?>
    }

    // ===== OTHER OCCUPATION TOGGLE (เก็บการทำงานเดิม) =====
    
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const questionId = this.name.replace('question_', '');
            const otherInput = document.getElementById('question_' + questionId + '_other');
            
            if (otherInput) {
                if (this.dataset.other === 'true' && this.checked) {
                    // ถ้าเลือก "อื่นๆ" ให้เปิดช่องกรอก
                    otherInput.disabled = false;
                    otherInput.style.display = 'block';
                    otherInput.focus();
                } else if (this.dataset.other !== 'true') {
                    // ถ้าเลือกตัวเลือกอื่น ให้ปิดช่องกรอก
                    otherInput.disabled = true;
                    otherInput.style.display = 'none';
                    otherInput.value = '';
                }
            }
            updateProgress();
        });
    });

    // ซ่อนช่อง other-input ตั้งแต่เริ่มต้น
    document.querySelectorAll('.other-input').forEach(input => {
        input.style.display = 'none';
    });

    // ===== FORM EVENT LISTENERS (เก็บการทำงานเดิม) =====
    
    document.getElementById('assessmentForm').addEventListener('change', updateProgress);
    document.getElementById('assessmentForm').addEventListener('input', updateProgress);

    // ===== FORM SUBMISSION WITH RECAPTCHA =====
    
    <?php if (!isset($is_preview) || !$is_preview): ?>
    document.getElementById('assessmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('=== ASSESSMENT FORM SUBMISSION WITH RECAPTCHA ===');
        
        const form = this;
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        
        // ตรวจสอบความถูกต้องของฟอร์มก่อน
        if (!validateForm(form)) {
            return false;
        }
        
        // ปิดปุ่มและแสดงสถานะ loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังตรวจสอบความปลอดภัย...';
        
        // ตรวจสอบ reCAPTCHA ถ้ามี
        if (typeof grecaptcha !== 'undefined' && window.RECAPTCHA_KEY && window.RECAPTCHA_KEY.length > 10) {
            console.log('📋 Executing reCAPTCHA for assessment...');
            
            grecaptcha.ready(function() {
                grecaptcha.execute(window.RECAPTCHA_KEY, {action: 'assessment_submit'})
                .then(function(token) {
                    console.log('✅ reCAPTCHA token generated:', token.substring(0, 20) + '...');
                    
                    // เพิ่ม reCAPTCHA token ลงในฟอร์ม
                    addHiddenField(form, 'recaptcha_token', token);
                    addHiddenField(form, 'recaptcha_action', 'assessment_submit');
                    
                    // ส่งฟอร์มพร้อม reCAPTCHA
                    submitFormAjax(form, submitBtn, originalText);
                })
                .catch(function(error) {
                    console.error('❌ reCAPTCHA error:', error);
                    console.log('🔄 Submitting without reCAPTCHA...');
                    
                    // ส่งแบบไม่มี reCAPTCHA
                    submitFormAjax(form, submitBtn, originalText);
                });
            });
        } else {
            console.warn('⚠️ reCAPTCHA not available, submitting without verification');
            // ส่งแบบไม่มี reCAPTCHA
            submitFormAjax(form, submitBtn, originalText);
        }
    });
    <?php endif; ?>

    // ===== INITIALIZATION =====
    
    // Initialize progress
    updateProgress();
    
    console.log('✅ Assessment form initialization complete');
});
</script>