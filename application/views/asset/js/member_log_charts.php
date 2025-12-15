<script>
/**
 * member_log_charts.js - สำหรับการสร้างกราฟหลักในหน้าแสดงผลกิจกรรมผู้ใช้
 */

document.addEventListener('DOMContentLoaded', function() {
    // ตั้งค่าสีสำหรับกราฟ
    const chartColors = {
        primary: '#6C63FF',
        primaryLight: 'rgba(108, 99, 255, 0.2)',
        success: '#36B37E',
        successLight: 'rgba(54, 179, 126, 0.2)',
        danger: '#FF5C5C',
        dangerLight: 'rgba(255, 92, 92, 0.2)',
        info: '#5E6A84',
        infoLight: 'rgba(94, 106, 132, 0.2)',
        warning: '#FFAB2B',
        warningLight: 'rgba(255, 171, 43, 0.2)',
        gray: ['#F9FAFC', '#EBEEF5', '#DFE3E8', '#C4CDD5', '#919EAB', '#637381', '#454F5B', '#212B36']
    };
    
    // กำหนดค่าเริ่มต้นสำหรับ Chart.js
    Chart.defaults.font.family = "'Poppins', 'Prompt', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#637381';
    Chart.defaults.plugins.tooltip.padding = 15;
    Chart.defaults.plugins.tooltip.cornerRadius = 12;
    Chart.defaults.plugins.tooltip.titleFont = {
        size: 14,
        weight: 'bold'
    };
    Chart.defaults.plugins.tooltip.bodyFont = {
        size: 13
    };
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(255, 255, 255, 0.95)';
    Chart.defaults.plugins.tooltip.borderColor = 'rgba(222, 226, 230, 0.9)';
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.usePointStyle = true;
    Chart.defaults.plugins.tooltip.boxPadding = 6;
    Chart.defaults.plugins.tooltip.caretSize = 6;
    Chart.defaults.plugins.tooltip.caretPadding = 10;
    Chart.defaults.plugins.tooltip.displayColors = true;
    Chart.defaults.plugins.tooltip.callbacks.labelTextColor = function(context) {
        return '#212B36';
    }
    
    // สร้างกราฟวงกลมแสดงประเภทกิจกรรม
    function createActivityTypeChart() {
        const activityTypeData = {
            labels: ['เข้าสู่ระบบล้มเหลว', 'เข้าสู่ระบบ', 'ออกจากระบบ'],
            datasets: [{
                data: [
                    failed_count, // ตัวแปรที่ได้จาก PHP
                    login_count,  // ตัวแปรที่ได้จาก PHP
                    logout_count  // ตัวแปรที่ได้จาก PHP
                ],
                backgroundColor: [
                    chartColors.danger,      // สีแดง - failed
                    chartColors.success,     // สีเขียว - login
                    chartColors.info         // สีเทา - logout
                ],
                borderWidth: 0,
                hoverOffset: 20,
                borderRadius: 4
            }]
        };
        
        new Chart(document.getElementById('activityTypeChart'), {
            type: 'doughnut',
            data: activityTypeData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }
    
    // สร้างกราฟแท่งแสดงจำนวนการเข้าสู่ระบบของผู้ใช้
    function createUserLoginChart() {
        new Chart(document.getElementById('userLoginChart'), {
            type: 'bar',
            data: {
                labels: userLabels, // ตัวแปรที่ได้จาก PHP
                datasets: [{
                    label: 'จำนวนครั้งเข้าสู่ระบบ',
                    data: userLoginData, // ตัวแปรที่ได้จาก PHP
                    backgroundColor: chartColors.primaryLight,
                    borderColor: chartColors.primary,
                    borderWidth: 2,
                    borderRadius: 8,
                    maxBarThickness: 50,
                    hoverBackgroundColor: chartColors.primary
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `จำนวนครั้งเข้าสู่ระบบ: ${context.raw} ครั้ง`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                weight: '500'
                            },
                            padding: 10
                        },
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.03)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                weight: '500'
                            },
                            padding: 10,
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    }
                },
                animation: {
                    duration: 1000
                }
            }
        });
    }
    
    // เพิ่มเอฟเฟกต์ animation สำหรับค่าสถิติ
    function setupStatCards() {
        // เพิ่มเอฟเฟกต์ hover ให้กับการ์ดสถิติ
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                const icon = this.querySelector('.stats-icon');
                if (icon) {
                    icon.style.transform = 'scale(1.15) translateY(-5px)';
                    icon.style.transition = 'all 0.3s ease';
                }
                
                const value = this.querySelector('.stats-value');
                if (value) {
                    value.style.transform = 'scale(1.05)';
                    value.style.transition = 'all 0.3s ease';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.stats-icon');
                if (icon) {
                    icon.style.transform = 'scale(1) translateY(0)';
                }
                
                const value = this.querySelector('.stats-value');
                if (value) {
                    value.style.transform = 'scale(1)';
                }
            });
        });
        
        // เพิ่ม animation เมื่อโหลดเพจ
        const animateStats = function() {
            const statsValues = document.querySelectorAll('.stats-value');
            statsValues.forEach((element, index) => {
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            });
        };
        
        // Set initial state for animation
        const statsValues = document.querySelectorAll('.stats-value');
        statsValues.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(15px)';
            element.style.transition = 'all 0.5s ease-out';
        });
        
        // Trigger animation
        setTimeout(animateStats, 300);
    }
    
    // สร้างกราฟและตั้งค่าเอฟเฟกต์
    if (document.getElementById('activityTypeChart')) {
        createActivityTypeChart();
    }
    
    if (document.getElementById('userLoginChart')) {
        createUserLoginChart();
    }
    
    setupStatCards();
    
    // ปุ่มส่งออก Excel
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var currentUrl = window.location.search;
            window.location.href = baseUrl + 'User_log_backend/export_csv' + currentUrl;
        });
    }
});
</script>