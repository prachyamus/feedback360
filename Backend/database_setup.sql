-- สร้างตาราง rounds สำหรับจัดการรอบการประเมิน
-- คัดลอกและรันใน phpMyAdmin หรือ MySQL client

CREATE TABLE IF NOT EXISTS `rounds` (
  `r_id` int(11) NOT NULL AUTO_INCREMENT,
  `r_title` varchar(255) NOT NULL COMMENT 'ชื่อรอบการประเมิน',
  `r_start` date DEFAULT NULL COMMENT 'วันที่เริ่มรอบการประเมิน',
  `r_end` date DEFAULT NULL COMMENT 'วันที่สิ้นสุดรอบการประเมิน',
  `r_status` enum('0','1') DEFAULT '1' COMMENT 'สถานะ: 0=ปิด, 1=เปิด',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่อัปเดตล่าสุด',
  PRIMARY KEY (`r_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางจัดการรอบการประเมิน';

-- ข้อมูลตัวอย่าง (ถ้าต้องการ)
INSERT INTO `rounds` (`r_title`, `r_start`, `r_end`, `r_status`) VALUES
('รอบการประเมินครั้งที่ 1/2567', '2024-01-01', '2024-03-31', '1'),
('รอบการประเมินครั้งที่ 2/2567', '2024-04-01', '2024-06-30', '1'),
('รอบการประเมินครั้งที่ 3/2567', '2024-07-01', '2024-09-30', '0');

