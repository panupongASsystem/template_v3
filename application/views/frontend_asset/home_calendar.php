<script>
    document.addEventListener('DOMContentLoaded', function() {
        // เลือกองค์ประกอบที่เกี่ยวข้อง
        const detailBox = document.querySelector('.calender-detail-content');
        const popup = document.getElementById('calendarPopup');
        const overlay = document.getElementById('calendarOverlay');
        const closeBtn = popup.querySelector('.popup-close');
        const popupContent = document.getElementById('popupContent');

        // เพิ่ม event listener เมื่อคลิกที่กล่องรายละเอียด
        detailBox.addEventListener('click', function(e) {
            e.stopPropagation(); // ป้องกันการขึ้นบับเบิลของเหตุการณ์

            // ดึงเนื้อหาจากกล่องเดิม
            const originalContent = document.getElementById('qCalender').innerHTML;

            // ใส่เนื้อหาลงในกล่อง popup
            popupContent.innerHTML = originalContent;

            // แสดง popup และ overlay
            popup.style.display = 'block';
            overlay.style.display = 'block';

            // เพิ่มการเช็คว่าทำงานถูกต้อง
            console.log('Showing popup');
        });

        // เพิ่ม event listener สำหรับปุ่มปิด
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // ป้องกันการขึ้นบับเบิลของเหตุการณ์
            closePopup();
        });

        // เมื่อคลิกที่ overlay ให้ปิด popup
        overlay.addEventListener('click', function() {
            closePopup();
        });

        // ปิด popup เมื่อกด ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && popup.style.display === 'block') {
                closePopup();
            }
        });

        // ฟังก์ชันสำหรับปิด popup
        function closePopup() {
            popup.style.display = 'none';
            overlay.style.display = 'none';
            console.log('Closing popup');
        }
    });
    // ปฏิทิน ทั้งหมด ********************************************************************************
    $(document).ready(function() {
        let currentDate = new Date();
        let events = <?= json_encode($events) ?>;

        // Debug: แสดงข้อมูล events ทั้งหมด
        console.log("All events:", events);

        function renderCalendar(date) {
            const year = date.getFullYear();
            const month = date.getMonth();
            const firstDayOfMonth = new Date(year, month, 1).getDay();
            const lastDateOfMonth = new Date(year, month + 1, 0).getDate();

            $('#monthYear').text(date.toLocaleDateString('th-TH', {
                month: 'long'
            }));
            $('#days').empty();

            for (let i = 0; i < firstDayOfMonth; i++) {
                $('#days').append('<div class="day"></div>');
            }

            for (let i = 1; i <= lastDateOfMonth; i++) {
                const day = String(i).padStart(2, '0');
                const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${day}`;
                const isToday = (new Date().toDateString() === new Date(dateString).toDateString());
                const dayClass = isToday ? 'day current-day' : 'day';
                const hasEvent = events.some(event => {
                    const eventStartTime = new Date(event.calender_date).getTime();
                    const eventEndTime = new Date(event.calender_date_end).getTime();
                    const currentTime = new Date(dateString).getTime();
                    return currentTime >= eventStartTime && currentTime <= eventEndTime;
                });
                const eventDot = hasEvent ? '<span class="event-dot"></span>' : '';
                $('#days').append(`<div class="${dayClass}" data-date="${dateString}"><span>${i}</span>${eventDot}</div>`);
            }

            $('.day').on('click', function() {
                const clickedDate = $(this).data('date');
                if (clickedDate) {
                    const selectedDate = new Date(clickedDate);
                    updateQCalenderDisplay(selectedDate);

                    $('.day').removeClass('selected-day');
                    $(this).addClass('selected-day');
                }
            });

            // แสดงข้อมูลกิจกรรมของวันปัจจุบันทันทีหลังจาก render ปฏิทิน
            updateQCalenderDisplay(new Date());
        }

        function updateQCalenderDisplay(selectedDate) {
            const $qCalenderContainer = $('#qCalender');
            $qCalenderContainer.empty();

            const day_th = selectedDate.getDate();
            const month_th = selectedDate.toLocaleString('th-TH', {
                month: 'long'
            });
            const year_th = selectedDate.getFullYear() + 543;
            const formattedDate = `${day_th} ${month_th} ${year_th}`;

            // ปรับปรุงการกรองกิจกรรม
            const filteredEvents = events.filter(event => {
                const eventStart = new Date(event.calender_date);
                const eventEnd = new Date(event.calender_date_end);
                // ตัดเวลาออกเพื่อเปรียบเทียบเฉพาะวัน
                const selectedDateTime = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate());
                const eventStartDateTime = new Date(eventStart.getFullYear(), eventStart.getMonth(), eventStart.getDate());
                const eventEndDateTime = new Date(eventEnd.getFullYear(), eventEnd.getMonth(), eventEnd.getDate());

                return selectedDateTime >= eventStartDateTime && selectedDateTime <= eventEndDateTime;
            });

            // Debug: แสดงกิจกรรมที่กรองแล้วสำหรับวันที่เลือก
            console.log("Filtered events for", formattedDate, ":", filteredEvents);

            if (filteredEvents.length > 0) {
                filteredEvents.forEach(event => {
                    $qCalenderContainer.append(`
                    <span class="font-calender2">วันที่ ${formattedDate}</span><br>
                    <span class="font-calender2 detail-text">${event.calender_detail}</span><br><br>
                `);
                });
            } else {
                $qCalenderContainer.append(`<span class="font-calender2">วันที่ ${formattedDate}</span><br><span class="font-calender2">ไม่มีกิจกรรมในวันนี้</span>`);
            }
        }

        $('#prevMonth').on('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });

        $('#nextMonth').on('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });

        renderCalendar(currentDate);
    });
    //   ********************************************************************************
</script>