# 360 Feedback REST API

REST API สำหรับระบบ 360 Feedback ที่สร้างด้วย PHP

## โครงสร้างโฟลเดอร์

```
Backend/
├── config/
│   ├── database.php    # การตั้งค่าฐานข้อมูล
│   └── cors.php        # การตั้งค่า CORS
├── utils/
│   └── helpers.php     # ฟังก์ชันช่วยเหลือ
├── api/
│   ├── index.php       # ข้อมูล API
│   ├── auth.php        # ระบบ Authentication
│   ├── users.php       # จัดการผู้ใช้
│   ├── surveys.php     # จัดการแบบสำรวจ
│   ├── questions.php   # จัดการคำถาม
│   ├── employees.php   # จัดการพนักงาน
│   ├── reports.php     # รายงาน
│   ├── survey-answers.php # คำตอบแบบสำรวจ
│   └── admin.php       # ระบบ Admin
└── README.md
```

## การติดตั้ง

1. วางไฟล์ทั้งหมดในโฟลเดอร์ `Backend/`
2. ตั้งค่าฐานข้อมูลใน `config/database.php`
3. ตรวจสอบการตั้งค่า CORS ใน `config/cors.php`

## การใช้งาน

### Base URL
```
http://localhost/api/
```

### Authentication
ใช้ Cookie-based authentication หรือ Authorization header

### Endpoints

#### Authentication
- `GET /api/auth` - ตรวจสอบสถานะการเข้าสู่ระบบ
- `POST /api/auth` - เข้าสู่ระบบ/ออกจากระบบ/สมัครสมาชิก

#### Users
- `GET /api/users?rounded=5` - ดึงข้อมูลผู้ใช้ทั้งหมด
- `POST /api/users` - สร้าง/แก้ไข/ลบผู้ใช้

#### Surveys
- `GET /api/surveys?rounded=5` - ดึงข้อมูลแบบสำรวจทั้งหมด
- `POST /api/surveys` - สร้าง/แก้ไข/ลบแบบสำรวจ

#### Questions
- `GET /api/questions?sub_id=1` - ดึงข้อมูลคำถามทั้งหมด
- `POST /api/questions` - สร้าง/แก้ไข/ลบคำถาม

#### Employees
- `GET /api/employees?rounded=5&search=keyword` - ดึงข้อมูลพนักงาน
- `POST /api/employees` - สร้าง/แก้ไข/ลบพนักงาน

#### Reports
- `GET /api/reports?type=summary&rounded=5` - ดึงรายงาน
- `POST /api/reports` - ส่งออกรายงาน

#### Survey Answers
- `GET /api/survey-answers?emp_id=E001&rounded=5` - ดึงคำตอบแบบสำรวจ
- `POST /api/survey-answers` - บันทึกคำตอบแบบสำรวจ

#### Admin
- `GET /api/admin?action=dashboard&rounded=5` - ข้อมูล Dashboard
- `POST /api/admin` - การดำเนินการ Admin

## ตัวอย่างการใช้งาน

### เข้าสู่ระบบ
```javascript
const response = await fetch('/api/auth', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        action: 'login',
        emp_id: 'E001',
        password: 'password123'
    })
});
```

### ดึงข้อมูลผู้ใช้
```javascript
const response = await fetch('/api/users?rounded=5');
const data = await response.json();
```

### บันทึกคำตอบแบบสำรวจ
```javascript
const response = await fetch('/api/survey-answers', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        action: 'bulk_save',
        emp_id: 'E001',
        rounded: '5',
        answers: [
            {
                q_id: '1',
                answer_score: '4',
                answer_comment: 'ดีมาก'
            }
        ]
    })
});
```

## Response Format

### Success Response
```json
{
    "success": true,
    "data": {
        // ข้อมูลที่ส่งกลับ
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "ข้อความแสดงข้อผิดพลาด"
}
```

## การตั้งค่า CORS

API รองรับ CORS สำหรับการพัฒนา โดยอนุญาต origin ต่อไปนี้:
- http://localhost:3000
- http://localhost:3001
- http://127.0.0.1:3000
- http://127.0.0.1:3001

## ข้อกำหนดระบบ

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server
