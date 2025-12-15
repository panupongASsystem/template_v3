<?php
// ข้อมูลหน่วยงานที่สามารถแก้ไขได้ที่จุดเดียว
$organization = [
    'type' => 'อบต', // ประเภทหน่วยงาน: อบต, เทศบาล, อบจ
    'name' => 'สว่าง', // ชื่อหน่วยงาน
    'full_name' => 'องค์การบริหารส่วนตำบลสว่าง', // ชื่อเต็มของหน่วยงาน
    'short_name' => 'อบต.สว่าง', // ชื่อย่อของหน่วยงาน
    'address' => [
        'number' => '123',
        'moo' => '4',
        'tambon' => 'สว่าง',
        'amphoe' => 'เมือง',
        'province' => 'สว่าง',
        'zipcode' => '12345'
    ],
    'contact' => [
        'phone' => '034-123456',
        'email' => 'info@sawang.go.th',
        'dpo_email' => 'dpo@sawang.go.th',
        'website' => 'www.sawang.go.th'
    ],
    'logo' => 'logo.png', // ตำแหน่งของโลโก้
    'last_updated' => '27 กุมภาพันธ์ 2568' // วันที่ปรับปรุงล่าสุด
];

// สร้างที่อยู่แบบเต็ม
$full_address = $organization['address']['number'] . " หมู่ " . $organization['address']['moo'] . 
                " ตำบล" . $organization['address']['tambon'] . 
                " อำเภอ" . $organization['address']['amphoe'] . 
                " จังหวัด" . $organization['address']['province'] . 
                " " . $organization['address']['zipcode'];

// สร้างคำนำหน้าชื่อหน่วยงานให้ถูกต้อง
switch($organization['type']) {
    case 'อบต':
        $org_prefix = 'องค์การบริหารส่วนตำบล';
        break;
    case 'เทศบาล':
        $org_prefix = 'เทศบาล';
        break;
    case 'อบจ':
        $org_prefix = 'องค์การบริหารส่วนจังหวัด';
        break;
    default:
        $org_prefix = 'องค์กรปกครองส่วนท้องถิ่น';
}

// ถ้าไม่ได้กำหนดชื่อเต็ม ให้สร้างจากคำนำหน้าและชื่อ
if(empty($organization['full_name'])) {
    $organization['full_name'] = $org_prefix . $organization['name'];
}

// ถ้าไม่ได้กำหนดชื่อย่อ ให้สร้างจากประเภทและชื่อ
if(empty($organization['short_name'])) {
    $organization['short_name'] = $organization['type'] . '.' . $organization['name'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบฟอร์มขอความยินยอมในการเก็บข้อมูลส่วนบุคคล - <?php echo $organization['full_name']; ?></title>
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #006838;
            margin-bottom: 5px;
        }
        .section {
            background-color: #f9f9f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #006838;
        }
        .section h2 {
            color: #006838;
            margin-top: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .consent-item {
            margin-bottom: 15px;
        }
        .consent-group {
            margin-bottom: 25px;
        }
        .consent-check {
            margin-bottom: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="checkbox"] {
            margin-right: 10px;
        }
        .data-list {
            background-color: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .data-list h3 {
            margin-top: 0;
            color: #0056b3;
        }
        .data-list ul {
            padding-left: 20px;
        }
        .buttons {
            text-align: center;
            margin-top: 30px;
        }
        .buttons button {
            padding: 12px 24px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .accept-btn {
            background-color: #006838;
            color: white;
        }
        .accept-btn:hover {
            background-color: #004d2b;
        }
        .cancel-btn {
            background-color: #f0f0f0;
            color: #333;
        }
        .cancel-btn:hover {
            background-color: #e0e0e0;
        }
        .footer {
            margin-top: 40px;
            font-size: 14px;
            text-align: center;
            color: #666;
        }
        a {
            color: #0056b3;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="logo">
        <!-- ใส่โลโก้ หน่วยงาน -->
        <img src="<?php echo $organization['logo']; ?>" alt="โลโก้ <?php echo $organization['full_name']; ?>">
    </div>
    
    <div class="header">
        <h1>แบบฟอร์มขอความยินยอมในการเก็บข้อมูลส่วนบุคคล</h1>
        <p><?php echo $organization['full_name']; ?></p>
    </div>
    
    <div class="section">
        <h2>คำชี้แจง</h2>
        <p>ตามพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 <?php echo $organization['full_name']; ?> ("<?php echo $organization['short_name']; ?>") มีความจำเป็นต้องขอความยินยอมจากท่านในการเก็บรวบรวม ใช้ และเปิดเผยข้อมูลส่วนบุคคลของท่าน เพื่อประโยชน์ในการให้บริการแก่ท่านอย่างมีประสิทธิภาพ</p>
        <p>กรุณาอ่านรายละเอียดต่อไปนี้โดยละเอียดก่อนตัดสินใจให้ความยินยอม</p>
    </div>
    
    <div class="section">
        <h2>1. วัตถุประสงค์ในการเก็บข้อมูล</h2>
        <p><?php echo $organization['short_name']; ?> จะเก็บรวบรวม ใช้ และเปิดเผยข้อมูลส่วนบุคคลของท่านเพื่อวัตถุประสงค์ดังต่อไปนี้:</p>
        <ul>
            <li>เพื่อการลงทะเบียนและยืนยันตัวตนในการใช้บริการระบบออนไลน์ของ <?php echo $organization['short_name']; ?></li>
            <li>เพื่อการให้บริการและอำนวยความสะดวกในการติดต่อราชการ</li>
            <li>เพื่อการแจ้งข้อมูลข่าวสาร ประกาศ หรือกิจกรรมที่เป็นประโยชน์แก่ประชาชน</li>
            <li>เพื่อการวิเคราะห์และพัฒนาการให้บริการที่ตรงกับความต้องการของประชาชน</li>
            <li>เพื่อการจัดทำฐานข้อมูลสำหรับการวางแผนพัฒนาท้องถิ่น</li>
            <li>เพื่อปฏิบัติตามกฎหมายที่เกี่ยวข้อง</li>
        </ul>
    </div>
    
    <div class="section">
        <h2>2. ข้อมูลที่จะทำการจัดเก็บ</h2>
        
        <div class="data-list">
            <h3>ข้อมูลส่วนบุคคลพื้นฐาน</h3>
            <ul>
                <li>คำนำหน้าชื่อ</li>
                <li>ชื่อ-นามสกุล</li>
                <li>เลขประจำตัวประชาชน 13 หลัก</li>
                <li>วัน/เดือน/ปีเกิด</li>
                <li>อายุ</li>
                <li>เพศ</li>
                <li>สัญชาติ</li>
                <li>ศาสนา</li>
            </ul>
        </div>
        
        <div class="data-list">
            <h3>ข้อมูลการติดต่อ</h3>
            <ul>
                <li>ที่อยู่ตามทะเบียนบ้าน (บ้านเลขที่, หมู่, ซอย, ถนน, ตำบล/แขวง, อำเภอ/เขต, จังหวัด, รหัสไปรษณีย์)</li>
                <li>ที่อยู่ปัจจุบัน (กรณีไม่ตรงกับทะเบียนบ้าน)</li>
                <li>หมายเลขโทรศัพท์มือถือ</li>
                <li>อีเมล</li>
            </ul>
        </div>
        
        <div class="data-list">
            <h3>ข้อมูลเพิ่มเติมสำหรับการใช้งานระบบ</h3>
            <ul>
                <li>ชื่อผู้ใช้งาน (Username)</li>
                <li>รหัสผ่าน (จัดเก็บในรูปแบบเข้ารหัส)</li>
                <li>คำถามเพื่อความปลอดภัย/การกู้คืนรหัสผ่าน</li>
                <li>รูปถ่ายสำหรับโปรไฟล์ (ถ้ามี)</li>
            </ul>
        </div>
        
        <div class="data-list">
            <h3>ข้อมูลอื่นๆ ที่อาจเป็นประโยชน์</h3>
            <ul>
                <li>สถานภาพสมรส</li>
                <li>อาชีพ</li>
                <li>ระดับการศึกษา</li>
                <li>สถานะในครัวเรือน (หัวหน้าครอบครัว, สมาชิกในครอบครัว)</li>
                <li>ข้อมูลความพิการ (ถ้ามี)</li>
                <li>ข้อมูลสิทธิสวัสดิการที่ได้รับ (ถ้ามี)</li>
            </ul>
        </div>
    </div>
    
    <div class="section">
        <h2>3. ระยะเวลาในการเก็บรักษาข้อมูล</h2>
        <p><?php echo $organization['short_name']; ?> จะเก็บรักษาข้อมูลส่วนบุคคลของท่านไว้ตราบเท่าที่จำเป็นต่อการบรรลุวัตถุประสงค์ที่เก็บรวบรวมข้อมูลส่วนบุคคลนั้น หรือตามระยะเวลาที่กฎหมายกำหนด แล้วแต่ระยะเวลาใดจะนานกว่า โดยปกติจะเก็บไว้ตลอดระยะเวลาที่ท่านเป็นผู้ใช้บริการของ <?php echo $organization['short_name']; ?> และอาจเก็บต่อไปอีกเป็นเวลา 10 ปีหลังจากท่านยุติการใช้บริการ เพื่อวัตถุประสงค์ในการพิสูจน์ตรวจสอบกรณีอาจเกิดข้อพิพาทภายในอายุความตามที่กฎหมายกำหนด</p>
    </div>
    
    <div class="section">
        <h2>4. การเปิดเผยข้อมูลต่อบุคคลที่สาม</h2>
        <p><?php echo $organization['short_name']; ?> อาจมีความจำเป็นต้องเปิดเผยข้อมูลส่วนบุคคลของท่านแก่บุคคลหรือหน่วยงานดังต่อไปนี้:</p>
        <ul>
            <li>หน่วยงานราชการที่เกี่ยวข้อง เช่น กรมการปกครอง กระทรวงมหาดไทย เพื่อการตรวจสอบข้อมูลหรือการดำเนินงานตามกฎหมาย</li>
            <li>ผู้ให้บริการระบบเทคโนโลยีสารสนเทศที่เกี่ยวข้องกับการให้บริการของ <?php echo $organization['short_name']; ?> ภายใต้ข้อกำหนดให้ผู้ให้บริการเหล่านั้นต้องรักษาความลับของข้อมูล</li>
        </ul>
        <p><?php echo $organization['short_name']; ?> จะไม่จำหน่าย โอน หรือแบ่งปันข้อมูลส่วนบุคคลของท่านให้แก่บุคคลที่สามเพื่อวัตถุประสงค์ทางการตลาด</p>
    </div>
    
    <div class="section">
        <h2>5. สิทธิของเจ้าของข้อมูลส่วนบุคคล</h2>
        <p>ท่านมีสิทธิเกี่ยวกับข้อมูลส่วนบุคคลของท่านดังต่อไปนี้:</p>
        <ul>
            <li>สิทธิในการเข้าถึงและขอรับสำเนาข้อมูลส่วนบุคคล</li>
            <li>สิทธิในการแก้ไขข้อมูลส่วนบุคคลให้ถูกต้อง</li>
            <li>สิทธิในการลบข้อมูลส่วนบุคคล (ภายใต้เงื่อนไขตามกฎหมาย)</li>
            <li>สิทธิในการจำกัดการใช้ข้อมูลส่วนบุคคล</li>
            <li>สิทธิในการคัดค้านการประมวลผลข้อมูลส่วนบุคคล</li>
            <li>สิทธิในการถอนความยินยอม</li>
            <li>สิทธิในการยื่นข้อร้องเรียนต่อคณะกรรมการคุ้มครองข้อมูลส่วนบุคคล</li>
        </ul>
        <p>ท่านสามารถใช้สิทธิดังกล่าวได้โดยติดต่อเจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคลของ <?php echo $organization['short_name']; ?> ตามช่องทางที่ระบุไว้ในข้อ 7</p>
    </div>
    
    <div class="section">
        <h2>6. ความปลอดภัยของข้อมูล</h2>
        <p><?php echo $organization['short_name']; ?> จัดให้มีมาตรการในการรักษาความมั่นคงปลอดภัยที่เหมาะสม เพื่อป้องกันการสูญหาย เข้าถึง ใช้ เปลี่ยนแปลง แก้ไข หรือเปิดเผยข้อมูลส่วนบุคคลโดยไม่ได้รับอนุญาต โดยสอดคล้องกับกฎหมายและระเบียบที่เกี่ยวข้อง</p>
    </div>
    
    <div class="section">
        <h2>7. ช่องทางการติดต่อเจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล</h2>
        <p>หากท่านมีข้อสงสัยหรือต้องการใช้สิทธิเกี่ยวกับข้อมูลส่วนบุคคลของท่าน สามารถติดต่อได้ที่:</p>
        <p><strong>เจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล</strong><br>
        <?php echo $organization['full_name']; ?><br>
        ที่อยู่: <?php echo $full_address; ?><br>
        โทรศัพท์: <?php echo $organization['contact']['phone']; ?><br>
        อีเมล: <?php echo $organization['contact']['dpo_email']; ?></p>
    </div>
    
    <div class="section">
        <h2>8. ความยินยอม</h2>
        
        <div class="consent-group">
            <div class="consent-check">
                <input type="checkbox" id="consent-basic" name="consent-basic">
                <label for="consent-basic">ข้าพเจ้ายินยอมให้ <?php echo $organization['short_name']; ?> เก็บรวบรวม ใช้ และเปิดเผยข้อมูลส่วนบุคคลพื้นฐานและข้อมูลการติดต่อของข้าพเจ้า ตามวัตถุประสงค์ที่ระบุไว้ข้างต้น</label>
            </div>
            
            <div class="consent-check">
                <input type="checkbox" id="consent-sensitive" name="consent-sensitive">
                <label for="consent-sensitive">ข้าพเจ้ายินยอมให้ <?php echo $organization['short_name']; ?> เก็บรวบรวม ใช้ และเปิดเผยข้อมูลส่วนบุคคลที่มีความอ่อนไหว เช่น ศาสนา ข้อมูลสุขภาพ และข้อมูลความพิการ (ถ้ามี) ตามวัตถุประสงค์ที่ระบุไว้ข้างต้น</label>
            </div>
            
            <div class="consent-check">
                <input type="checkbox" id="consent-notification" name="consent-notification">
                <label for="consent-notification">ข้าพเจ้ายินยอมให้ <?php echo $organization['short_name']; ?> ส่งข้อมูลข่าวสาร ประกาศ หรือแจ้งเตือนเกี่ยวกับกิจกรรมและบริการต่างๆ ผ่านช่องทางการติดต่อที่ข้าพเจ้าให้ไว้</label>
            </div>
        </div>
        
        <p><em>หมายเหตุ: ท่านสามารถเพิกถอนความยินยอมได้ตลอดเวลา โดยการถอนความยินยอมจะไม่กระทบต่อความชอบด้วยกฎหมายของการเก็บรวบรวม ใช้ หรือเปิดเผยข้อมูลส่วนบุคคลที่ได้กระทำก่อนการถอนความยินยอม อย่างไรก็ตาม การไม่ให้ความยินยอมในข้อมูลพื้นฐานบางประเภทอาจทำให้ท่านไม่สามารถใช้บริการบางอย่างของ <?php echo $organization['short_name']; ?> ได้</em></p>
    </div>
    
    <div class="buttons">
        <button class="accept-btn">ยินยอม</button>
        <button class="cancel-btn">ไม่ยินยอม</button>
    </div>
    
    <div class="footer">
        <p>สามารถอ่านนโยบายคุ้มครองข้อมูลส่วนบุคคลฉบับเต็มได้ที่ <a href="#"><?php echo $organization['contact']['website']; ?>/privacy-policy</a></p>
        <p>ปรับปรุงล่าสุด: <?php echo $organization['last_updated']; ?></p>
    </div>
    
    <script>
        // สคริปต์จัดการการคลิกปุ่ม
        document.addEventListener('DOMContentLoaded', function() {
            const acceptBtn = document.querySelector('.accept-btn');
            const cancelBtn = document.querySelector('.cancel-btn');
            
            acceptBtn.addEventListener('click', function() {
                // ตรวจสอบว่ากล่องเลือกยินยอมพื้นฐานถูกเลือกหรือไม่
                if(!document.getElementById('consent-basic').checked) {
                    alert('กรุณาให้ความยินยอมในการเก็บรวบรวมข้อมูลพื้นฐานเพื่อดำเนินการต่อ');
                    return;
                }
                
                // เก็บค่าความยินยอม
                const consents = {
                    basic: document.getElementById('consent-basic').checked,
                    sensitive: document.getElementById('consent-sensitive').checked,
                    notification: document.getElementById('consent-notification').checked
                };
                
                // ในสภาพแวดล้อมจริงควรส่งข้อมูลไปยังเซิร์ฟเวอร์ด้วย AJAX
                console.log('ความยินยอมที่ได้รับ:', consents);
                
                // ตัวอย่าง - แสดงข้อความยืนยัน
                alert('ขอบคุณสำหรับความยินยอม');
                
                // ตัวอย่าง - เปลี่ยนเส้นทางไปยังหน้าลงทะเบียน
                // window.location.href = 'register.php';
            });
            
            cancelBtn.addEventListener('click', function() {
                if(confirm('หากท่านไม่ให้ความยินยอม ท่านจะไม่สามารถใช้บริการบางอย่างของ <?php echo $organization['short_name']; ?> ได้ ต้องการดำเนินการต่อหรือไม่?')) {
                    // ตัวอย่าง - กลับไปยังหน้าหลัก
                    // window.location.href = 'index.php';
                    alert('ท่านได้ปฏิเสธการให้ความยินยอม');
                }
            });
        });
    </script>
</body>
</html>