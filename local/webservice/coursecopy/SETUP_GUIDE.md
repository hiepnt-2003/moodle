# Course Copy RESTful API - Setup Guide

## 🎯 Tổng quan
Plugin này cung cấp RESTful API để copy/clone môn học trong Moodle 3.9+ với đầu vào và đầu ra theo yêu cầu:

**Đầu vào:**
- `shortname_clone` (string): Shortname của môn học nguồn cần copy
- `fullname` (string): Tên đầy đủ cho môn học mới
- `shortname` (string): Shortname cho môn học mới (phải unique)
- `startdate` (integer): Ngày bắt đầu (Unix timestamp)
- `enddate` (integer): Ngày kết thúc (Unix timestamp)

**Đầu ra:**
- `status` (string): "success" hoặc "error"
- `id` (integer): ID của môn học mới (0 nếu lỗi)
- `message` (string): Thông báo kết quả hoặc lỗi

## 🚀 Cài đặt

### Bước 1: Upload Plugin
1. Upload thư mục `coursecopy` vào `local/webservice/coursecopy/`
2. Vào **Site Administration → Notifications** để cài đặt plugin

### Bước 2: Tạo Token
1. Vào **Site Administration → Advanced features**
2. Enable **Web services** (để có thể tạo token)
3. Vào **Site Administration → Server → Web services → Manage tokens**
4. Click **Create token**
5. Chọn user có đủ quyền (xem bên dưới)
6. Service để trống hoặc chọn service bất kỳ
7. Copy token để sử dụng

### Bước 3: Cấp quyền cho User
User cần có các capabilities sau:
- `moodle/course:create` - Tạo môn học mới
- `moodle/backup:backupcourse` - Backup môn học
- `moodle/restore:restorecourse` - Restore môn học

**Cách cấp quyền:**
1. Vào **Site Administration → Users → Permissions → Define roles**
2. Edit role của user (ví dụ: Manager, Course creator)
3. Tìm và allow các capabilities trên

## 🔧 API Usage

### Endpoint
```
**URL**: `POST /webservice/restful/server.php/local_coursecopy_copy_course`
```

### Authentication
Có 2 cách truyền token:

**Cách 1: Authorization Header**
```
Authorization: Bearer your_token_here
```

**Cách 2: Request Body**
```json
{
  "token": "your_token_here",
  "shortname_clone": "...",
  ...
}
```

### Request Example
```bash
curl -X POST "http://your-moodle-site/webservice/restful/server.php/local_coursecopy_copy_course" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: abc123def456" \
  -d '{
    "shortname_clone": "MATH101",
    "fullname": "Mathematics 101 - Spring 2025", 
    "shortname": "MATH101_2025",
    "startdate": 1704067200,
    "enddate": 1719792000
  }'
```

### Response Examples

**Success:**
```json
{
  "status": "success",
  "id": 15,
  "message": "Course copied successfully"
}
```

**Error:**
```json
{
  "status": "error", 
  "id": 0,
  "message": "Source course not found with shortname: MATH101"
}
```

## 🧪 Testing

### Sử dụng Test Interface
1. Truy cập: `http://your-site/local/webservice/coursecopy/test_api.php`
2. Nhập token và thông tin môn học
3. Click "Copy Course" để test

### Sử dụng Postman
1. Import file `coursecopy_api.postman_collection.json`
2. Set variables:
   - `base_url`: URL của Moodle site
   - `token`: Token đã tạo
3. Run các test cases

### Tạo Timestamp
```javascript
// JavaScript
Math.floor(new Date('2025-01-01').getTime() / 1000)  // 1735689600

// PHP  
strtotime('2025-01-01')  // 1735689600

// Online tool
https://www.unixtimestamp.com/
```

## ❌ Troubleshooting

### 1. "Invalid token"
- Kiểm tra token có tồn tại trong `mdl_external_tokens`
- Token chưa expired
- User của token còn active

### 2. "Source course not found"
- Kiểm tra `shortname_clone` chính xác
- Course phải visible hoặc user có quyền access
- Course không bị deleted

### 3. "User does not have permission"
- User cần có đủ 3 capabilities đã nêu
- Kiểm tra role assignment của user
- User phải active, not suspended

### 4. "Course with shortname already exists" 
- `shortname` mới phải unique trong hệ thống
- Kiểm tra bảng `mdl_course` field `shortname`

### 5. "Start date must be before end date"
- `startdate < enddate`
- Cả hai đều phải là Unix timestamp (số nguyên)

### 6. Network/CORS errors
- Kiểm tra server có cho phép cross-origin requests
- Đảm bảo Moodle site accessible từ client

## 🔐 Security Notes

1. **Token Security**: 
   - Không hardcode token trong code
   - Sử dụng environment variables
   - Rotate token định kỳ

2. **Permission Check**:
   - Plugin tự động kiểm tra capabilities
   - Validate input parameters
   - Log các activities

3. **Rate Limiting**:
   - Consider implement rate limiting
   - Monitor API usage

## 📁 File Structure
```
local/webservice/coursecopy/
├── restful.php                    # Main RESTful API endpoint  
├── test_api.php                   # Test interface
├── version.php                    # Plugin info
├── README.md                      # Documentation
├── SETUP_GUIDE.md                 # This file
├── coursecopy_api.postman_collection.json   # Postman tests
├── db/
│   ├── access.php                 # Capabilities
│   └── services.php              # Service definitions  
├── lang/
│   └── en/
│       └── local_coursecopy.php   # Language strings
└── integration/
    └── restful_integration.php    # Future integrations
```

## 🎯 Integration Examples

### PHP Client
```php
$data = [
    'shortname_clone' => 'DEMO2024',
    'fullname' => 'Demo Course Copy 2025',
    'shortname' => 'DEMO2025', 
    'startdate' => strtotime('2025-01-01'),
    'enddate' => strtotime('2025-12-31')
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://moodle-site/local/webservice/coursecopy/restful.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$result = json_decode($response, true);

if ($result['status'] === 'success') {
    echo "Course copied! ID: " . $result['id'];
} else {
    echo "Error: " . $result['message'];
}
```

### JavaScript Client
```javascript
async function copyCourse(token, courseData) {
    const response = await fetch('/local/webservice/coursecopy/restful.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(courseData)
    });
    
    const result = await response.json();
    
    if (result.status === 'success') {
        console.log(`Course copied! ID: ${result.id}`);
        return result.id;
    } else {
        throw new Error(result.message);
    }
}

// Usage
const courseData = {
    shortname_clone: 'DEMO2024',
    fullname: 'Demo Course Copy 2025',
    shortname: 'DEMO2025',
    startdate: Math.floor(new Date('2025-01-01').getTime() / 1000),
    enddate: Math.floor(new Date('2025-12-31').getTime() / 1000)
};

copyCourse('your-token', courseData)
    .then(courseId => console.log('Success:', courseId))
    .catch(error => console.error('Error:', error.message));
```

## 📞 Support

- **Plugin Version**: 1.0.0
- **Moodle Version**: 3.9+  
- **Protocol**: RESTful API với JSON
- **License**: GPL v3 or later

Nếu có vấn đề, kiểm tra:
1. Moodle error logs
2. Web server error logs  
3. Browser developer console (cho client-side errors)