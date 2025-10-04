# Course Copy RESTful API Plugin

Plugin Moodle cho phép copy môn học thông qua RESTful API sử dụng plugin webservice_restful.

## Thông tin Plugin

- **Tên**: Course Copy
- **Loại**: local plugin
- **Phiên bản**: 1.0.0
- **Yêu cầu**: Moodle 3.8+ (tương thích với Moodle 3.9)
- **Plugin phụ thuộc**: webservice_restful

## Tính năng

- Copy môn học với các thông tin mới (tên đầy đủ, tên viết tắt, ngày bắt đầu, ngày kết thúc)
- Giữ nguyên cấu trúc, định dạng và cài đặt của môn học gốc
- RESTful API endpoint với xác thực token
- Hỗ trợ cả Authorization Bearer token và wstoken trong request body

## Cài đặt

### 1. Cài đặt plugin webservice_restful (nếu chưa có)

Plugin này yêu cầu webservice_restful đã được cài đặt và kích hoạt.

### 2. Cài đặt plugin coursecopy

```bash
cd /path/to/moodle/local/
git clone <repository-url> coursecopy
cd coursecopy
```

Hoặc giải nén plugin vào thư mục `local/coursecopy`

### 3. Cập nhật database

Truy cập: **Site administration → Notifications** để cài đặt plugin.

### 4. Kích hoạt Web Services

1. **Kích hoạt Web services**:
   - Site administration → Advanced features
   - Tích chọn "Enable web services"

2. **Kích hoạt RESTful protocol**:
   - Site administration → Plugins → Web services → Manage protocols
   - Kích hoạt "RESTful protocol"

3. **Tạo user cho web service**:
   - Tạo user mới hoặc sử dụng user hiện có
   - Cấp quyền: `moodle/course:create`, `moodle/course:view`

4. **Tạo token**:
   - Site administration → Plugins → Web services → Manage tokens
   - Tạo token mới cho user
   - Chọn service: "Course Copy Service" hoặc "All services"
   - Lưu token để sử dụng trong API requests

## Sử dụng API

### Endpoint

```
POST /local/coursecopy/restful_api.php
```

### Xác thực

Có 3 cách cung cấp token:

#### Cách 1: Authorization Bearer Header (khuyến nghị)
```
Authorization: Bearer YOUR_TOKEN_HERE
```

#### Cách 2: Trong request body
```json
{
  "wstoken": "YOUR_TOKEN_HERE",
  "wsfunction": "local_coursecopy_copy_course",
  ...
}
```

#### Cách 3: Query parameter
```
/local/coursecopy/restful_api.php?wstoken=YOUR_TOKEN_HERE
```

### Request Format

#### Copy Course

**Method**: POST  
**Content-Type**: application/json

**Parameters**:

| Tham số | Kiểu | Bắt buộc | Mô tả |
|---------|------|----------|-------|
| wsfunction | string | Không | Tên function (mặc định: local_coursecopy_copy_course) |
| shortname_clone | string | Có | Shortname của môn học nguồn cần copy |
| fullname | string | Có | Tên đầy đủ cho môn học mới |
| shortname | string | Có | Tên viết tắt cho môn học mới |
| startdate | integer | Có | Ngày bắt đầu (Unix timestamp) |
| enddate | integer | Có | Ngày kết thúc (Unix timestamp) |

**Example Request**:

```bash
curl -X POST https://your-moodle-site.com/local/coursecopy/restful_api.php \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer abc123def456ghi789" \
  -d '{
    "wsfunction": "local_coursecopy_copy_course",
    "shortname_clone": "COURSE2024",
    "fullname": "Course Copy 2025",
    "shortname": "COURSE2025",
    "startdate": 1704067200,
    "enddate": 1735689600
  }'
```

### Response Format

#### Success Response

```json
{
  "status": "success",
  "id": 123,
  "message": "Copy môn học thành công! ID môn học mới: 123"
}
```

#### Error Response

```json
{
  "status": "error",
  "id": 0,
  "message": "Mô tả lỗi chi tiết"
}
```

### Response Fields

| Field | Kiểu | Mô tả |
|-------|------|-------|
| status | string | "success" hoặc "error" |
| id | integer | ID của môn học mới (0 nếu có lỗi) |
| message | string | Thông báo thành công hoặc mô tả lỗi |

## Ví dụ sử dụng

### JavaScript (Fetch API)

```javascript
const copyData = {
  wsfunction: "local_coursecopy_copy_course",
  shortname_clone: "MATH2024",
  fullname: "Mathematics 2025",
  shortname: "MATH2025",
  startdate: 1704067200,  // 2024-01-01
  enddate: 1735689600     // 2025-01-01
};

fetch('https://your-moodle-site.com/local/coursecopy/restful_api.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer YOUR_TOKEN_HERE'
  },
  body: JSON.stringify(copyData)
})
.then(response => response.json())
.then(data => {
  if (data.status === 'success') {
    console.log('Course copied successfully! New course ID:', data.id);
  } else {
    console.error('Error:', data.message);
  }
})
.catch(error => console.error('Request failed:', error));
```

### Python

```python
import requests
import json
from datetime import datetime

url = "https://your-moodle-site.com/local/coursecopy/restful_api.php"
token = "YOUR_TOKEN_HERE"

headers = {
    "Content-Type": "application/json",
    "Authorization": f"Bearer {token}"
}

data = {
    "wsfunction": "local_coursecopy_copy_course",
    "shortname_clone": "COURSE2024",
    "fullname": "Course Copy 2025",
    "shortname": "COURSE2025",
    "startdate": int(datetime(2024, 1, 1).timestamp()),
    "enddate": int(datetime(2025, 1, 1).timestamp())
}

response = requests.post(url, headers=headers, json=data)
result = response.json()

if result['status'] == 'success':
    print(f"Course copied successfully! New course ID: {result['id']}")
else:
    print(f"Error: {result['message']}")
```

### PHP

```php
<?php
$url = "https://your-moodle-site.com/local/coursecopy/restful_api.php";
$token = "YOUR_TOKEN_HERE";

$data = [
    'wsfunction' => 'local_coursecopy_copy_course',
    'shortname_clone' => 'COURSE2024',
    'fullname' => 'Course Copy 2025',
    'shortname' => 'COURSE2025',
    'startdate' => strtotime('2024-01-01'),
    'enddate' => strtotime('2025-01-01')
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n" .
                   "Authorization: Bearer $token\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$response = json_decode($result, true);

if ($response['status'] === 'success') {
    echo "Course copied successfully! New course ID: " . $response['id'];
} else {
    echo "Error: " . $response['message'];
}
?>
```

## Xử lý lỗi thường gặp

| Lỗi | Nguyên nhân | Giải pháp |
|-----|------------|-----------|
| "Authorization token required" | Không có token trong request | Thêm token vào Authorization header hoặc request body |
| "Invalid token" | Token không đúng hoặc không tồn tại | Kiểm tra lại token trong Moodle admin |
| "Token expired" | Token đã hết hạn | Tạo token mới hoặc gia hạn token |
| "Không tìm thấy môn học với shortname: ..." | Shortname nguồn không tồn tại | Kiểm tra lại shortname của môn học nguồn |
| "Shortname đã tồn tại: ..." | Shortname mới đã được sử dụng | Chọn shortname khác cho môn học mới |
| "Ngày kết thúc phải sau ngày bắt đầu" | startdate >= enddate | Đảm bảo enddate > startdate |
| "Missing required parameters" | Thiếu tham số bắt buộc | Kiểm tra lại tất cả các tham số required |

## Kiểm tra timestamp

Để chuyển đổi ngày thành Unix timestamp:

### Online tool
- https://www.unixtimestamp.com/

### JavaScript
```javascript
const timestamp = new Date('2024-01-01').getTime() / 1000;
```

### PHP
```php
$timestamp = strtotime('2024-01-01');
```

### Python
```python
from datetime import datetime
timestamp = int(datetime(2024, 1, 1).timestamp())
```

## Quyền truy cập

Plugin này yêu cầu các quyền sau:
- `moodle/course:create` - Tạo môn học mới
- `moodle/course:view` - Xem thông tin môn học

## Bảo mật

- Plugin sử dụng xác thực token của Moodle
- Token phải được giữ bí mật và không chia sẻ
- Nên sử dụng HTTPS khi gọi API
- Token có thể đặt thời gian hết hạn trong Moodle admin
- Validate tất cả input parameters

## Troubleshooting

### Debug mode

Để bật debug mode trong Moodle:
1. Site administration → Development → Debugging
2. Set "Debug messages" to "DEVELOPER"
3. Check lại response để xem error details

### Kiểm tra token

```sql
SELECT * FROM mdl_external_tokens WHERE token = 'YOUR_TOKEN';
```

### Kiểm tra web services

```sql
SELECT * FROM mdl_external_services WHERE shortname = 'coursecopy_service';
```

## Hỗ trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra Moodle error logs
2. Kiểm tra web server error logs
3. Bật debug mode để xem chi tiết lỗi

## License

GNU GPL v3 or later

## Tác giả

Course Copy Team - 2025
