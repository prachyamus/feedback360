# 360 Feedback API - คู่มือการใช้งาน

## วิธีการเรียกใช้งาน API

### 1. การเข้าถึง API
```
Base URL: http://localhost/api/
```

### 2. การทดสอบ API
เปิดไฟล์ `test-api.html` ในเบราว์เซอร์เพื่อทดสอบ API ผ่านเว็บอินเทอร์เฟซ

### 3. ตัวอย่างการเรียกใช้งาน

#### Authentication
```javascript
// เข้าสู่ระบบ
const loginResponse = await fetch('/api/auth', {
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

// สมัครสมาชิก
const registerResponse = await fetch('/api/auth', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        action: 'register',
        emp_id: 'E002',
        password: 'password123',
        emp_fname: 'John',
        emp_lname: 'Doe'
    })
});
```

#### Users Management
```javascript
// ดึงข้อมูลผู้ใช้ทั้งหมด
const usersResponse = await fetch('/api/users?rounded=5');

// สร้างผู้ใช้ใหม่
const createUserResponse = await fetch('/api/users', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        action: 'create',
        emp_id: 'E003',
        rounded: '5',
        emp_fname: 'Jane',
        emp_lname: 'Smith',
        emp_div: 'IT',
        emp_dep: 'Development'
    })
});

// อัปเดตผู้ใช้
const updateUserResponse = await fetch('/api/users', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        action: 'update',
        emp_id: 'E003',
        rounded: '5',
        emp_fname: 'Jane Updated',
        emp_lname: 'Smith Updated'
    })
});
```

#### Surveys Management
```javascript
// ดึงข้อมูลแบบสำรวจทั้งหมด
const surveysResponse = await fetch('/api/surveys?rounded=5');

// สร้างแบบสำรวจใหม่
const createSurveyResponse = await fetch('/api/surveys', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        action: 'save',
        sub_title: 'Employee Satisfaction Survey',
        sub_title_m: 'แบบสำรวจความพึงพอใจพนักงาน',
        sub_discrip: 'Survey about employee satisfaction',
        sub_band: '5',
        type_group: '1'
    })
});
```

#### Questions Management
```javascript
// ดึงข้อมูลคำถามทั้งหมด
const questionsResponse = await fetch('/api/questions?sub_id=1');

// สร้างคำถามใหม่
const createQuestionResponse = await fetch('/api/questions', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        action: 'save',
        sub_id: '1',
        q_title: 'How satisfied are you with your job?',
        q_title_m: 'คุณพึงพอใจกับงานของคุณมากแค่ไหน?',
        q_type: 'rating',
        q_status: '1'
    })
});
```

#### Survey Answers
```javascript
// บันทึกคำตอบแบบสำรวจ
const saveAnswerResponse = await fetch('/api/survey-answers', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        action: 'save',
        emp_id: 'E001',
        q_id: '1',
        answer_score: '4',
        answer_comment: 'Very satisfied',
        rounded: '5'
    })
});

// บันทึกคำตอบหลายข้อพร้อมกัน
const saveBulkAnswersResponse = await fetch('/api/survey-answers', {
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
                answer_comment: 'Good'
            },
            {
                q_id: '2',
                answer_score: '5',
                answer_comment: 'Excellent'
            }
        ]
    })
});
```

#### Reports
```javascript
// ดึงรายงานสรุป
const summaryReportResponse = await fetch('/api/reports?type=summary&rounded=5');

// ดึงรายงานผู้ใช้
const userReportResponse = await fetch('/api/reports?type=user&rounded=5&emp_id=E001');

// ดึงรายงานตามแผนก
const groupReportResponse = await fetch('/api/reports?type=group&rounded=5');
```

#### Admin Functions
```javascript
// ดึงข้อมูล Dashboard
const dashboardResponse = await fetch('/api/admin?action=dashboard&rounded=5');

// ดึงข้อมูลสถิติ
const statisticsResponse = await fetch('/api/admin?action=statistics&rounded=5');
```

### 4. Response Format

#### Success Response
```json
{
    "success": true,
    "data": {
        // ข้อมูลที่ส่งกลับ
    }
}
```

#### Error Response
```json
{
    "success": false,
    "message": "ข้อความแสดงข้อผิดพลาด"
}
```

### 5. HTTP Status Codes
- `200` - สำเร็จ
- `201` - สร้างข้อมูลสำเร็จ
- `400` - ข้อมูลไม่ถูกต้อง
- `401` - ไม่ได้รับอนุญาต
- `404` - ไม่พบข้อมูล
- `405` - Method ไม่ได้รับอนุญาต
- `500` - ข้อผิดพลาดของเซิร์ฟเวอร์

### 6. การใช้ cURL

#### GET Request
```bash
curl -X GET "http://localhost/api/users?rounded=5"
```

#### POST Request
```bash
curl -X POST "http://localhost/api/auth" \
  -H "Content-Type: application/json" \
  -d '{
    "action": "login",
    "emp_id": "E001",
    "password": "password123"
  }'
```

### 7. การใช้ JavaScript Fetch API

```javascript
async function callAPI(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(`/api/${endpoint}`, options);
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message);
        }
        
        return result.data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// ตัวอย่างการใช้งาน
const users = await callAPI('users?rounded=5');
const newUser = await callAPI('users', 'POST', {
    action: 'create',
    emp_id: 'E004',
    rounded: '5',
    emp_fname: 'Test',
    emp_lname: 'User'
});
```

### 8. การจัดการ Error

```javascript
try {
    const response = await fetch('/api/users', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'create',
            emp_id: 'E005',
            rounded: '5'
        })
    });
    
    const result = await response.json();
    
    if (!result.success) {
        console.error('API Error:', result.message);
        // จัดการข้อผิดพลาด
    } else {
        console.log('Success:', result.data);
        // จัดการผลลัพธ์ที่สำเร็จ
    }
} catch (error) {
    console.error('Network Error:', error);
    // จัดการข้อผิดพลาดเครือข่าย
}
```

### 9. การทดสอบ API

1. เปิดไฟล์ `test-api.html` ในเบราว์เซอร์
2. กรอกข้อมูลในฟอร์มต่างๆ
3. กดปุ่มทดสอบเพื่อเรียก API
4. ดูผลลัพธ์ใน Response section

### 10. ข้อควรระวัง

- ตรวจสอบให้แน่ใจว่าฐานข้อมูลเชื่อมต่อได้
- ใช้ HTTPS ในสภาพแวดล้อม production
- ตรวจสอบ CORS settings สำหรับ frontend
- ใช้ prepared statements เพื่อป้องกัน SQL injection
- ตรวจสอบ input validation ก่อนส่งข้อมูล
