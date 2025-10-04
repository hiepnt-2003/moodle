# Course Copier Plugin - Moodle RESTful WebService

Plugin Moodle để copy/clone khóa học thông qua **RESTful WebService** chuẩn của Moodle với endpoint `/webservice/rest/server.php`.

## 🎯 Tính năng

- **Clone Course**: Sao chép một khóa học từ shortname sang khóa học mới
- **Get Available Courses**: Lấy danh sách các khóa học có thể clone
- **RESTful Protocol**: Sử dụng endpoint chuẩn `/webservice/rest/server.php` của Moodle
- **JSON Format**: Input/output dạng JSON với `moodlewsrestformat=json`
- **Token Authentication**: Bảo mật với web service token
- **CORS Support**: Hỗ trợ cross-origin requests

## 🚀 API Endpoints

### Base URL
```
POST /webservice/rest/server.php
```

### 1. Copy Course

**Method:** POST  
**Endpoint:** `/webservice/rest/server.php`

**Headers:**
- `Content-Type`: application/json

**JSON Body:**
```json
{
  "wstoken": "your_web_service_token",
  "wsfunction": "local_coursecopier_copy_course",
  "moodlewsrestformat": "json",
  "shortname_clone": "ORIGINAL123",
  "fullname": "New Course Name",
  "shortname": "NEWCOURSE2025",
  "startdate": 1704067200,
  "enddate": 1719792000
}
```

**Parameters:**
- `wstoken`: Web service token (bắt buộc)
- `wsfunction`: `local_coursecopier_copy_course`
- `moodlewsrestformat`: `json` (để nhận JSON response)
- `shortname_clone`: Shortname của khóa học nguồn cần copy
- `fullname`: Tên đầy đủ cho khóa học mới
- `shortname`: Shortname cho khóa học mới (phải unique)
- `startdate`: Ngày bắt đầu (Unix timestamp)
- `enddate`: Ngày kết thúc (Unix timestamp)

**Success Response:**
```json
{
  "status": "success",
  "id": 25,
  "message": "Copy môn học thành công! Đã sao chép toàn bộ nội dung từ môn học gốc."
}
```

**Error Response:**
```json
{
  "status": "error",
  "id": 0,
  "message": "Không tìm thấy môn học với shortname: NOTEXIST"
}
```

### 2. Get Available Courses

**JSON Body:**
```json
{
  "wstoken": "your_web_service_token",
  "wsfunction": "local_coursecopier_get_available_courses",
  "moodlewsrestformat": "json",
  "categoryid": 0
}
```

**Response:**
```json
{
  "courses": [
    {
      "id": 2,
      "fullname": "Sample Course",
      "shortname": "COURSE123",
      "category": 1,
      "startdate": 1609459200,
      "enddate": 1617235200,
      "visible": true
    }
  ],
  "total": 1,
  "status": "success",
  "message": "Lấy danh sách môn học thành công"
}
```

## 🔧 Cài đặt Plugin

### 1. Upload Plugin
```bash
# Copy plugin vào thư mục local/
cp -r coursecopier /path/to/moodle/local/
```

### 2. Cài đặt từ Moodle Admin
1. Đăng nhập với tài khoản Admin
2. Vào **Site Administration → Notifications**
3. Click **Upgrade Moodle database now**

### 3. Cấu hình Web Services

#### 3.1. Enable Web Services
- Vào **Site Administration → Advanced features**
- Check **Enable web services**

#### 3.2. Enable REST Protocol
- Vào **Site Administration → Server → Web services → Manage protocols**
- Enable **REST protocol**

#### 3.3. Create Service and Functions
1. Vào **Site Administration → Server → Web services → External services**
2. Click **Add** để tạo service mới
3. Add functions:
   - `local_coursecopier_copy_course`
   - `local_coursecopier_get_available_courses`

#### 3.4. Create Token
1. Vào **Site Administration → Server → Web services → Manage tokens**
2. Click **Create token**
3. Chọn user và service
4. Copy token để sử dụng trong API calls

## 📋 Test với Postman

### 1. Import Collection
Import file `Course_Copier_API.postman_collection.json` vào Postman

### 2. Setup Environment Variables
- `moodle_url`: URL của Moodle site (vd: http://localhost)
- `ws_token`: Web service token từ Moodle admin

### 3. Test Requests
- **Clone Course (RESTful Protocol)**: Test chính để clone khóa học
- **Get Available Courses (RESTful)**: Lấy danh sách khóa học có thể clone
- **Test Invalid Dates (RESTful)**: Test validation với ngày không hợp lệ
- **Test với URL-encoded (Alternative)**: Fallback với form data

## 🔐 Permissions Required

User cần có các quyền sau:
- `moodle/course:create`: Tạo khóa học mới
- `moodle/course:view`: Xem danh sách khóa học
- `moodle/backup:backupcourse`: Backup khóa học nguồn
- `moodle/restore:restorecourse`: Restore vào khóa học mới

## 🧪 Testing Examples

### cURL Example
```bash
curl -X POST "http://localhost/webservice/rest/server.php" \
  -H "Content-Type: application/json" \
  -d '{
    "wstoken": "your_token_here",
    "wsfunction": "local_coursecopier_copy_course",
    "moodlewsrestformat": "json",
    "shortname_clone": "ORIGINAL123",
    "fullname": "New Course 2025",
    "shortname": "NEW2025",
    "startdate": 1704067200,
    "enddate": 1719792000
  }'
```

### JavaScript/Fetch Example
```javascript
const response = await fetch('/webservice/rest/server.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    wstoken: 'your_token_here',
    wsfunction: 'local_coursecopier_copy_course',
    moodlewsrestformat: 'json',
    shortname_clone: 'ORIGINAL123',
    fullname: 'New Course 2025',
    shortname: 'NEW2025',
    startdate: 1704067200,
    enddate: 1719792000
  })
});

const result = await response.json();
console.log(result);
```

## 🐛 Troubleshooting

### Lỗi thường gặp:

1. **"Web service not enabled"**: 
   - Kích hoạt web services trong **Site Administration → Advanced features**

2. **"Invalid token"**: 
   - Kiểm tra token có tồn tại và chưa hết hạn
   - Verify token permissions

3. **"Function does not exist"**: 
   - Đảm bảo functions đã được add vào service
   - Check plugin đã được cài đặt

4. **"Course not found"**: 
   - Kiểm tra `shortname_clone` có tồn tại không
   - User có permission truy cập course nguồn

5. **"Capability required"**: 
   - User cần có đủ permissions để backup/restore courses

### Debug Mode
Enable debugging trong Moodle:
`Administration → Site administration → Development → Debugging`

## 📁 File Structure

```
local/coursecopier/
├── externallib.php                  # External web service functions
├── version.php                      # Plugin version info
├── Course_Copier_API.postman_collection.json # Postman test collection
├── README.md                        # Documentation
├── db/
│   ├── access.php                   # Capabilities definition
│   └── services.php                 # Web service functions & services
└── lang/
    └── en/
        └── local_coursecopier.php   # English language strings
```

## 🏗️ Technical Details

- **Moodle Version**: 3.10+
- **PHP Version**: 7.4+
- **Plugin Type**: Local plugin
- **Protocol**: RESTful Web Services (/webservice/rest/server.php)
- **Format**: JSON với moodlewsrestformat=json
- **Security**: Token authentication, capability checks, input validation

## 📞 Support

Nếu có vấn đề với plugin:

1. Kiểm tra Moodle logs tại **Site Administration → Reports → Logs**
2. Kiểm tra Web service logs tại **Site Administration → Development → Web service test client**
3. Ensure RESTful protocol đã được enable
4. Verify token và service configuration

---

**Plugin Version**: v1.0  
**Compatible**: Moodle 3.10+  
**Protocol**: RESTful (/webservice/rest/server.php)  
**License**: GPL v3 or later