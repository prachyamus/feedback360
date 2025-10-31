# API Setup Guide for PHP 5.5

## การแก้ไขปัญหา CORS และ Axios

### 1. ปัญหาที่พบ
- PHP 5.5 ไม่รองรับ null coalescing operator (`??`)
- PHP 5.5 ไม่รองรับ short array syntax (`[]`)
- PHP 5.5 ไม่รองรับ `random_bytes()` function
- Axios configuration ไม่ถูกต้อง

### 2. การแก้ไขที่ทำแล้ว

#### A. แก้ไข PHP Syntax ให้รองรับ PHP 5.5
```php
// แทนที่ null coalescing operator
$value = $data['key'] ?? 'default';
// เป็น
$value = isset($data['key']) ? $data['key'] : 'default';

// แทนที่ short array syntax
$array = ['key' => 'value'];
// เป็น
$array = array('key' => 'value');

// แทนที่ random_bytes()
$bytes = random_bytes(16);
// เป็น
$bytes = function_exists('openssl_random_pseudo_bytes') 
    ? openssl_random_pseudo_bytes(16) 
    : md5(uniqid(mt_rand(), true));
```

#### B. แก้ไข Axios Configuration
```javascript
// ใน nuxt.config.js
runtimeConfig: {
  public: {
    apiBaseUrl: 'http://localhost/360'  // กำหนด base URL
  }
}

// เพิ่ม proxy สำหรับ API
nitro: {
  devProxy: {
    '/api': {
      target: 'http://localhost/360',
      changeOrigin: true,
      prependPath: true
    }
  }
}
```

### 3. วิธีทดสอบ API

#### A. ทดสอบโดยตรง
```bash
# ทดสอบไฟล์ PHP โดยตรง
curl http://localhost/360/Backend/api/test.php

# ทดสอบผ่าน router
curl http://localhost/360/api/test
```

#### B. ทดสอบผ่าน Frontend
```javascript
// ใช้ composable
const { testApi, getUsers, login } = useApi()

// ทดสอบ API
const response = await testApi()
console.log(response)

// ทดสอบ Users API
const users = await getUsers('5')
console.log(users)

// ทดสอบ Auth API
const auth = await login('E001', 'password123')
console.log(auth)
```

#### C. ทดสอบผ่านหน้าเว็บ
```
http://localhost:3000/api-test
```

### 4. API Endpoints ที่พร้อมใช้งาน

#### Test API
- `GET /Backend/api/test.php` - ทดสอบการเชื่อมต่อ API

#### Users API
- `GET /Backend/api/users.php?rounded=5` - ดึงข้อมูลผู้ใช้
- `POST /Backend/api/users.php` - สร้าง/แก้ไข/ลบผู้ใช้

#### Auth API
- `GET /Backend/api/auth.php` - ตรวจสอบสถานะการเข้าสู่ระบบ
- `POST /Backend/api/auth.php` - เข้าสู่ระบบ/ออกจากระบบ/สมัครสมาชิก

#### Surveys API
- `GET /Backend/api/surveys.php?rounded=5` - ดึงข้อมูลแบบสำรวจ
- `POST /Backend/api/surveys.php` - สร้าง/แก้ไข/ลบแบบสำรวจ

#### Questions API
- `GET /Backend/api/questions.php?sub_id=1` - ดึงข้อมูลคำถาม
- `POST /Backend/api/questions.php` - สร้าง/แก้ไข/ลบคำถาม

#### Reports API
- `GET /Backend/api/reports.php?type=summary&rounded=5` - ดึงรายงาน
- `POST /Backend/api/reports.php` - ส่งออกรายงาน

### 5. การแก้ไขปัญหา CORS

#### A. ตรวจสอบ CORS Headers
```php
// ใน cors.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
```

#### B. ตรวจสอบ Axios Configuration
```javascript
// ใน axios.js
const api = axios.create({
  baseURL: config.public.apiBaseUrl,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  withCredentials: false
})
```

### 6. ไฟล์ที่สำคัญ

#### Backend Files
- `Backend/api/test.php` - API endpoint สำหรับทดสอบ
- `Backend/config/cors.php` - การตั้งค่า CORS
- `Backend/config/database.php` - การตั้งค่าฐานข้อมูล
- `Backend/utils/helpers.php` - ฟังก์ชันช่วยเหลือ

#### Frontend Files
- `frontend/plugins/axios.js` - การตั้งค่า Axios
- `frontend/nuxt.config.js` - การตั้งค่า Nuxt
- `frontend/composables/useApi.js` - Composable สำหรับเรียก API
- `frontend/pages/api-test.vue` - หน้าทดสอบ API

### 7. การ Debug

#### A. ตรวจสอบ Console Logs
```javascript
// เปิด Developer Tools และดู Console
// ตรวจสอบ Network tab สำหรับ API calls
```

#### B. ตรวจสอบ PHP Errors
```php
// เปิด error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

#### C. ตรวจสอบ CORS
```javascript
// ตรวจสอบ Response Headers
// ดู Access-Control-Allow-Origin
```

### 8. ข้อควรระวัง

1. **PHP Version**: ต้องใช้ PHP 5.5 หรือสูงกว่า
2. **Database**: ตรวจสอบการเชื่อมต่อฐานข้อมูล
3. **File Permissions**: ตรวจสอบสิทธิ์การเข้าถึงไฟล์
4. **CORS**: ตรวจสอบการตั้งค่า CORS headers
5. **Axios**: ตรวจสอบ baseURL และ proxy settings

### 9. การทดสอบที่แนะนำ

1. ทดสอบ API โดยตรงก่อน
2. ทดสอบผ่าน router
3. ทดสอบผ่าน frontend
4. ตรวจสอบ CORS headers
5. ตรวจสอบ error logs

### 10. การแก้ไขปัญหาเพิ่มเติม

หากยังมีปัญหา:
1. ตรวจสอบ PHP error logs
2. ตรวจสอบ Apache/Nginx error logs
3. ตรวจสอบ browser console
4. ตรวจสอบ network requests
5. ตรวจสอบ CORS preflight requests
