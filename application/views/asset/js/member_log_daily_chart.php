<script>
/**
 * member_log_daily_chart.js - สำหรับแสดงกราฟเส้นการเข้าสู่ระบบรายวัน
 */

// ฟังก์ชันสำหรับแปลงเดือนเป็นภาษาไทย
function getThaiMonth(month) {
    const thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    return thaiMonths[month];
}

// ฟังก์ชันสำหรับแปลงวันที่เป็นรูปแบบไทย
function formatThaiDate(date) {
    const day = date.getDate();
    const month = getThaiMonth(date.getMonth());
    const year = date.getFullYear() + 543; // แปลงเป็นปี พ.ศ.
    return `${day} ${month} ${year}`;
}

// กำหนด chartColors เป็น global variable เพื่อให้แน่ใจว่าสามารถเข้าถึงได้ทุกที่
window.chartColors = {
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

// สร้างกราฟเส้นแสดงการเข้าสู่ระบบรายวัน
window.dailyLoginChart = null;

function createDailyLoginChart(month, year) {
    console.log(`Creating chart for month: ${month}, year: ${year}`);
    
    // สร้างข้อมูลวันที่ในเดือนที่เลือก
    const daysInMonth = new Date(year, month, 0).getDate();
    const labels = [];
    
    for (let i = 1; i <= daysInMonth; i++) {
        labels.push(i); // แสดงเฉพาะวันที่ (1-31)
    }
    
    // ใช้ URL แบบเต็มแทนการใช้ baseUrl เพื่อหลีกเลี่ยงปัญหา
    const apiUrl = `${window.baseUrl}User_log_backend/get_daily_login_data?month=${month}&year=${year}`;
    console.log(`Fetching data from: ${apiUrl}`);
    
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            console.log('Response received');
            return response.json();
        })
        .then(data => {
            console.log('Data parsed successfully:', data);
            
            // สร้างข้อมูลสำหรับแสดงในกราฟ
            const successData = Array(daysInMonth).fill(0);
            const failedData = Array(daysInMonth).fill(0);
            
            // นำข้อมูลจาก API มาใส่ในอาร์เรย์ - เพิ่มการตรวจสอบข้อมูล
            if (data.success && Array.isArray(data.success)) {
                data.success.forEach(item => {
                    const day = parseInt(item.day) - 1; // ปรับเป็น index ที่เริ่มจาก 0
                    if (day >= 0 && day < daysInMonth) {
                        successData[day] = parseInt(item.count);
                    }
                });
            }
            
            if (data.failed && Array.isArray(data.failed)) {
                data.failed.forEach(item => {
                    const day = parseInt(item.day) - 1; // ปรับเป็น index ที่เริ่มจาก 0
                    if (day >= 0 && day < daysInMonth) {
                        failedData[day] = parseInt(item.count);
                    }
                });
            }
            
            // ถ้ามีกราฟอยู่แล้ว ให้ทำลายก่อนสร้างใหม่
            if (window.dailyLoginChart instanceof Chart) {
                console.log('Destroying previous chart');
                window.dailyLoginChart.destroy();
            }
            
            // สร้างกราฟใหม่
            const canvas = document.getElementById('dailyLoginChart');
            if (!canvas) {
                console.error('Canvas element not found!');
                return;
            }
            
            const ctx = canvas.getContext('2d');
            console.log('Creating new chart');
            
            window.dailyLoginChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'เข้าสู่ระบบสำเร็จ',
                            data: successData,
                            borderColor: window.chartColors.success,
                            backgroundColor: window.chartColors.successLight,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: window.chartColors.success,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'เข้าสู่ระบบล้มเหลว',
                            data: failedData,
                            borderColor: window.chartColors.danger,
                            backgroundColor: window.chartColors.dangerLight,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: window.chartColors.danger,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                padding: 15
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            padding: 10,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                title: function(context) {
                                    // แสดงวันที่เป็นรูปแบบไทย
                                    const selectedMonth = parseInt(month);
                                    const selectedYear = parseInt(year);
                                    const day = parseInt(context[0].label);
                                    const date = new Date(selectedYear, selectedMonth-1, day);
                                    return formatThaiDate(date);
                                },
                                label: function(context) {
                                    // แสดงจำนวนครั้งการเข้าสู่ระบบ
                                    let label = context.dataset.label || '';
                                    return `${label}: ${context.raw} ครั้ง`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: `${getThaiMonth(month-1)} ${year+543}`,
                                font: {
                                    size: 14,
                                    weight: '500'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            // กำหนดค่า suggestedMax แทนการใช้ฟังก์ชัน max
                            suggestedMax: function() {
                                // หาค่าสูงสุดจากทั้งสอง dataset
                                let maxSuccess = Math.max(...successData);
                                let maxFailed = Math.max(...failedData);
                                let maxValue = Math.max(maxSuccess, maxFailed, 5); // ขั้นต่ำคือ 5
                                
                                // ปัดค่าเป็นจำนวนเต็มที่เหมาะสม
                                if (maxValue <= 10) {
                                    return 10; // ถ้าน้อยกว่า 10 ให้แสดงสูงสุดที่ 10
                                } else if (maxValue <= 20) {
                                    return 20; // ถ้าน้อยกว่า 20 ให้แสดงสูงสุดที่ 20
                                } else if (maxValue <= 50) {
                                    return 50; // ถ้าน้อยกว่า 50 ให้แสดงสูงสุดที่ 50
                                } else if (maxValue <= 100) {
                                    return 100; // ถ้าน้อยกว่า 100 ให้แสดงสูงสุดที่ 100
                                } else {
                                    return Math.ceil(maxValue / 100) * 100; // ปัดขึ้นเป็นหลักร้อยที่ใกล้ที่สุด
                                }
                            }(),
                            ticks: {
                                precision: 0,
                                font: {
                                    weight: '500'
                                },
                                padding: 10,
                                // กำหนดระยะห่างระหว่างขีดบนแกน Y
                                stepSize: function() {
                                    let maxSuccess = Math.max(...successData);
                                    let maxFailed = Math.max(...failedData);
                                    let maxValue = Math.max(maxSuccess, maxFailed, 5);
                                    let suggestedMax;
                                    
                                    // ใช้ logic เดียวกันกับ suggestedMax
                                    if (maxValue <= 10) {
                                        suggestedMax = 10;
                                        return 2; // แบ่งเป็น 5 ขีด (0, 2, 4, 6, 8, 10)
                                    } else if (maxValue <= 20) {
                                        suggestedMax = 20;
                                        return 4; // แบ่งเป็น 5 ขีด (0, 4, 8, 12, 16, 20)
                                    } else if (maxValue <= 50) {
                                        suggestedMax = 50;
                                        return 10; // แบ่งเป็น 5 ขีด (0, 10, 20, 30, 40, 50)
                                    } else if (maxValue <= 100) {
                                        suggestedMax = 100;
                                        return 20; // แบ่งเป็น 5 ขีด (0, 20, 40, 60, 80, 100)
                                    } else {
                                        suggestedMax = Math.ceil(maxValue / 100) * 100;
                                        return suggestedMax / 5; // แบ่งเป็น 5 ขีด
                                    }
                                }()
                            },
                            // ปิดการปรับแต่งอัตโนมัติ
                            grace: '0%',
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)'
                            }
                        }
                    },
                    // เพิ่มการกำหนดค่าที่สำคัญอื่นๆ
                    elements: {
                        line: {
                            tension: 0.3 // ความโค้งของเส้น
                        },
                        point: {
                            radius: 4,
                            hoverRadius: 6,
                            borderWidth: 2
                        }
                    },
                    layout: {
                        padding: {
                            top: 10,
                            right: 10,
                            bottom: 10,
                            left: 10
                        }
                    },
                    animation: {
                        duration: 1000
                    }
                }
            });
            
            console.log('Chart created successfully');
        })
        .catch(error => {
            console.error('Error fetching login data:', error);
            
            // แสดงข้อความเมื่อไม่สามารถโหลดข้อมูลได้
            const canvas = document.getElementById('dailyLoginChart');
            if (!canvas) {
                console.error('Canvas element not found!');
                return;
            }
            
            const ctx = canvas.getContext('2d');
            
            // ล้าง canvas ก่อนวาดข้อความ
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            ctx.font = '16px Prompt';
            ctx.textAlign = 'center';
            ctx.fillStyle = '#FF5C5C'; // สีแดง
            ctx.fillText('ไม่สามารถโหลดข้อมูลกราฟได้', canvas.width / 2, canvas.height / 2);
            ctx.fillStyle = '#637381'; // สีเทา
            ctx.font = '14px Prompt';
            ctx.fillText('โปรดลองโหลดหน้าใหม่อีกครั้ง', canvas.width / 2, canvas.height / 2 + 30);
        });
}

// ตั้งค่าตัวเลือกเดือนและปี
function setupMonthYearSelector() {
    console.log('Setting up month/year selectors');
    
    const monthSelector = document.getElementById('monthSelector');
    const yearSelector = document.getElementById('yearSelector');
    const todayBtn = document.getElementById('todayBtn');
    
    if (!monthSelector || !yearSelector || !todayBtn) {
        console.error('Month/year selectors or today button not found!');
        return;
    }
    
    // ตั้งค่าเดือนและปีปัจจุบัน
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth() + 1; // เดือนใน JavaScript เริ่มจาก 0
    const currentYear = currentDate.getFullYear();
    
    console.log(`Current month: ${currentMonth}, year: ${currentYear}`);
    
    // ตั้งค่าค่าเริ่มต้นสำหรับตัวเลือก
    monthSelector.value = currentMonth;
    yearSelector.value = currentYear;
    
    // สร้างกราฟเริ่มต้น
    createDailyLoginChart(currentMonth, currentYear);
    
    // เพิ่ม event listener สำหรับการเปลี่ยนเดือนหรือปี
    monthSelector.addEventListener('change', updateChart);
    yearSelector.addEventListener('change', updateChart);
    
    // ปุ่มกลับไปยังวันนี้
    todayBtn.addEventListener('click', function() {
        monthSelector.value = currentMonth;
        yearSelector.value = currentYear;
        createDailyLoginChart(currentMonth, currentYear);
    });
    
    // ฟังก์ชันอัปเดตกราฟเมื่อเปลี่ยนเดือนหรือปี
    function updateChart() {
        const selectedMonth = parseInt(monthSelector.value);
        const selectedYear = parseInt(yearSelector.value);
        console.log(`Updating chart to month: ${selectedMonth}, year: ${selectedYear}`);
        createDailyLoginChart(selectedMonth, selectedYear);
    }
    
    console.log('Month/year selectors setup complete');
}

// เริ่มต้นเมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM content loaded');
    
    // ตรวจสอบว่า Canvas element มีอยู่จริงหรือไม่
    const canvas = document.getElementById('dailyLoginChart');
    if (!canvas) {
        console.error('Canvas element "dailyLoginChart" not found!');
        return;
    }
    
    // ตั้งค่าตัวเลือกเดือนและปี
    setupMonthYearSelector();
});
</script>