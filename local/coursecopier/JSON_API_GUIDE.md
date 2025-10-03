# Course Copier JSON API

## Tổng quan

API này cung cấp endpoints RESTful với định dạng JSON body và token authentication để copy các môn học trong Moodle.

## 🚀 Endpoints

### Base URL
```
POST /local/coursecopier/api.php
```

## 🔑 Authentication

API hỗ trợ 2 cách authentication:

### 1. Authorization Header (Recommended)
```http
Authorization: Bearer YOUR_WEB_SERVICE_TOKEN
```

### 2. Token trong JSON body
```json
{
  "wstoken": "YOUR_WEB_SERVICE_TOKEN",
  ...
}
```

## 📋 Available Functions

### 1. Get Available Courses

Lấy danh sách các môn học có thể copy.

**Request:**
```http
POST /local/coursecopier/api.php
Content-Type: application/json
Authorization: Bearer YOUR_TOKEN

{
  "wsfunction": "local_coursecopier_get_available_courses",
  "categoryid": 0
}
```

**Response Success:**
```json
{
  "courses": [
    {
      "id": 2,
      "fullname": "Sample Course",
      "shortname": "SAMPLE123",
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

### 2. Copy Course

Copy một môn học với thông tin mới.

**Request:**
```http
POST /local/coursecopier/api.php
Content-Type: application/json
Authorization: Bearer YOUR_TOKEN

{
  "wsfunction": "local_coursecopier_copy_course",
  "shortname_clone": "ORIGINAL123",
  "fullname": "New Course Name 2025",
  "shortname": "NEWCOURSE2025",
  "startdate": 1704067200,
  "enddate": 1719792000
}
```

**Response Success:**
```json
{
  "status": "success",
  "id": 15,
  "message": "Copy môn học thành công! Đã sao chép toàn bộ nội dung từ môn học gốc."
}
```

**Response Error:**
```json
{
  "status": "error",
  "id": 0,
  "message": "Không tìm thấy môn học với shortname: NOTEXIST"
}
```

## 🔧 Parameters

### Get Available Courses
- `wsfunction`: "local_coursecopier_get_available_courses"
- `categoryid` (optional): ID của category, 0 = tất cả categories

### Copy Course
- `wsfunction`: "local_coursecopier_copy_course"
- `shortname_clone`: Shortname của môn học gốc cần copy
- `fullname`: Tên đầy đủ của môn học mới
- `shortname`: Shortname của môn học mới (phải unique)
- `startdate`: Ngày bắt đầu (Unix timestamp)
- `enddate`: Ngày kết thúc (Unix timestamp)

## 🧪 Testing

### Sử dụng Test Interface
Truy cập: `/local/coursecopier/test_json_api.php` để test API trực tiếp trong browser.

### Sử dụng cURL

**Get Available Courses:**
```bash
curl -X POST "https://yourmoodle.com/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "wsfunction": "local_coursecopier_get_available_courses",
    "categoryid": 0
  }'
```

**Copy Course:**
```bash
curl -X POST "https://yourmoodle.com/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "wsfunction": "local_coursecopier_copy_course",
    "shortname_clone": "ORIGINAL123",
    "fullname": "New Course Name 2025",
    "shortname": "NEWCOURSE2025",
    "startdate": 1704067200,
    "enddate": 1719792000
  }'
```

### Sử dụng Postman

Import collection: `Course_Copier_API.postman_collection.json`

**Environment Variables:**
- `moodle_url`: URL của Moodle site (không có trailing slash)
- `ws_token`: Web service token

## ⚠️ Error Handling

API trả về các HTTP status codes:

- `200`: Success
- `400`: Bad Request (JSON invalid, missing parameters)
- `401`: Unauthorized (token invalid/expired)
- `405`: Method Not Allowed (chỉ cho phép POST)
- `500`: Internal Server Error

**Error Response Format:**
```json
{
  "status": "error",
  "id": 0,
  "message": "Error description"
}
```

## 🔐 Security Features

1. **Token Validation**: Kiểm tra token trong database
2. **User Authentication**: Xác thực user từ token
3. **Capability Check**: Kiểm tra quyền tạo môn học
4. **CORS Support**: Cho phép cross-origin requests
5. **Input Validation**: Validate tất cả parameters
6. **Error Handling**: Không expose sensitive information

## 📝 Setup Web Service Token

1. Đăng nhập Moodle với tài khoản admin
2. Đi tới **Site Administration → Server → Web Services → Overview**
3. Enable web services nếu chưa enable
4. Đi tới **Site Administration → Server → Web Services → Manage tokens**
5. Click **Create token**
6. Chọn user và service (hoặc tất cả services)
7. Copy token và sử dụng trong API calls

## 🚦 Rate Limiting

- Không có rate limiting built-in
- Recommend implement rate limiting ở web server level (nginx, apache)

## 📚 Examples

### JavaScript/Fetch
```javascript
const response = await fetch('/local/coursecopier/api.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + token
  },
  body: JSON.stringify({
    wsfunction: 'local_coursecopier_get_available_courses',
    categoryid: 0
  })
});

const data = await response.json();
console.log(data);
```

### PHP
```php
$data = [
  'wsfunction' => 'local_coursecopier_copy_course',
  'shortname_clone' => 'ORIGINAL123',
  'fullname' => 'New Course 2025',
  'shortname' => 'NEW2025',
  'startdate' => 1704067200,
  'enddate' => 1719792000
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://yourmoodle.com/local/coursecopier/api.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$result = json_decode($response, true);

curl_close($ch);
```

### Python
```python
import requests
import json

url = 'https://yourmoodle.com/local/coursecopier/api.php'
headers = {
    'Content-Type': 'application/json',
    'Authorization': f'Bearer {token}'
}

data = {
    'wsfunction': 'local_coursecopier_get_available_courses',
    'categoryid': 0
}

response = requests.post(url, headers=headers, data=json.dumps(data))
result = response.json()

print(result)
```

## 🐛 Troubleshooting

### Common Issues:

1. **"Token is required"**: Đảm bảo token được gửi trong Authorization header hoặc JSON body
2. **"Invalid token"**: Kiểm tra token có tồn tại trong database và chưa expired
3. **"JSON body is required"**: Đảm bảo gửi POST request với valid JSON
4. **"Unknown function"**: Kiểm tra `wsfunction` parameter đúng spelling
5. **"Permission denied"**: User cần có capability `moodle/course:create`

### Debug Mode:
Thêm parameter `debug=1` để xem thêm thông tin debug (chỉ cho admin).
