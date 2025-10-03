# Course Copier Plugin for Moodle

Plugin này cung cấp RESTful API để copy môn học trong Moodle với các thông tin tùy chỉnh.

## Tính năng

- Copy môn học từ một course hiện có
- Tùy chỉnh tên đầy đủ, tên viết tắt, ngày bắt đầu và ngày kết thúc cho course mới
- RESTful API có thể test bằng Postman
- Sao chép toàn bộ nội dung course (activities, resources, settings)

## Cài đặt

1. Copy thư mục `coursecopier` vào `moodle/local/`
2. Truy cập Administration > Site administration > Notifications để cài đặt plugin
3. Cấu hình Web Services:
   - Administration > Site administration > Plugins > Web services > Overview
   - Enable web services
   - Enable protocols (REST protocol)
   - Create a service và add functions:
     - `local_coursecopier_copy_course`
     - `local_coursecopier_get_available_courses`

## API Endpoints

### 1. Copy Course (JSON REST API - Recommended)

**Endpoint:** `/local/coursecopier/api.php`

**Method:** POST

**Headers:**
- `Content-Type`: application/json
- `Authorization`: Bearer {token} (optional, token can also be in body)

**JSON Body:**
```json
{
  "wsfunction": "local_coursecopier_copy_course",
  "wstoken": "your_token_here", 
  "shortname_clone": "ORIGINAL123",
  "fullname": "New Course Name",
  "shortname": "NEWCOURSE2025",
  "startdate": 1704067200,
  "enddate": 1719792000
}
```

### 1.1. Copy Course (Traditional Moodle Web Service)

**Endpoint:** `/webservice/rest/server.php`

**Method:** POST

**Parameters:**
- `wstoken`: Web service token
- `wsfunction`: `local_coursecopier_copy_course`
- `moodlewsrestformat`: json
- `shortname_clone`: Shortname của môn học nguồn cần copy
- `fullname`: Tên đầy đủ cho môn học mới
- `shortname`: Tên viết tắt cho môn học mới
- `startdate`: Ngày bắt đầu (timestamp Unix)
- `enddate`: Ngày kết thúc (timestamp Unix)

**Response:**
```json
{
  "status": "success",
  "id": 123,
  "message": "Copy môn học thành công! Đã sao chép toàn bộ nội dung từ môn học gốc."
}
```

**Error Response:**
```json
{
  "status": "error",
  "id": 0,
  "message": "Không tìm thấy môn học với shortname: ABC123"
}
```

### 2. Get Available Courses (JSON REST API - Recommended)

**Endpoint:** `/local/coursecopier/api.php`

**Method:** POST

**Headers:**
- `Content-Type`: application/json
- `Authorization`: Bearer {token}

**JSON Body:**
```json
{
  "wsfunction": "local_coursecopier_get_available_courses",
  "wstoken": "your_token_here",
  "categoryid": 0
}
```

### 2.1. Get Available Courses (Traditional Moodle Web Service)

**Endpoint:** `/webservice/rest/server.php`

**Method:** POST

**Parameters:**
- `wstoken`: Web service token
- `wsfunction`: `local_coursecopier_get_available_courses`
- `moodlewsrestformat`: json
- `categoryid`: ID danh mục (0 = tất cả)

**Response:**
```json
{
  "courses": [
    {
      "id": 2,
      "fullname": "Course Example",
      "shortname": "EXAMPLE123",
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

## Postman Collection

### Setup Postman Environment
Tạo environment với variables:
- `moodle_url`: URL của Moodle site (ví dụ: https://yourmoodle.com)
- `ws_token`: Web service token

### Test Cases

#### Test 1: Get Available Courses (JSON API)
```json
POST {{moodle_url}}/local/coursecopier/api.php
Content-Type: application/json
Authorization: Bearer {{ws_token}}

{
  "wsfunction": "local_coursecopier_get_available_courses",
  "categoryid": 0
}
```

#### Test 2: Copy Course (JSON API)
```json
POST {{moodle_url}}/local/coursecopier/api.php
Content-Type: application/json
Authorization: Bearer {{ws_token}}

{
  "wsfunction": "local_coursecopier_copy_course",
  "shortname_clone": "ORIGINAL123",
  "fullname": "New Course Name",
  "shortname": "NEWCOURSE123",
  "startdate": 1609459200,
  "enddate": 1617235200
}
```

#### Test 3: Traditional Web Service (Fallback)
```
POST {{moodle_url}}/webservice/rest/server.php
Content-Type: application/x-www-form-urlencoded

wstoken={{ws_token}}
&wsfunction=local_coursecopier_copy_course
&moodlewsrestformat=json
&shortname_clone=ORIGINAL123
&fullname=New Course Name
&shortname=NEWCOURSE123
&startdate=1609459200
&enddate=1617235200
```

## Permissions

Plugin yêu cầu các permissions sau:
- `moodle/course:create` - Tạo course mới
- `moodle/backup:backupcourse` - Backup course
- `moodle/restore:restorecourse` - Restore course
- `moodle/course:view` - Xem danh sách course

## Lưu ý

1. **Timestamps**: Sử dụng Unix timestamps cho startdate và enddate
2. **Shortname**: Phải unique trong hệ thống
3. **Permissions**: User gọi API phải có đủ quyền trên course nguồn và system
4. **Backup/Restore**: Plugin sử dụng Moodle backup/restore API để copy toàn bộ nội dung

## Troubleshooting

### Lỗi thường gặp:

1. **"Web service not enabled"**: Kích hoạt web services trong Administration
2. **"Invalid token"**: Kiểm tra token và user permissions
3. **"Capability required"**: User cần có đủ permissions
4. **"Course not found"**: Kiểm tra shortname_clone có tồn tại không

### Debug mode:
Enable debugging trong Moodle để xem chi tiết lỗi:
`Administration > Site administration > Development > Debugging`

## Tác giả

Plugin được phát triển cho Moodle 3.10+

## License

GNU GPL v3 or later