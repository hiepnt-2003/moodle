# Course Clone Plugin

Plugin Moodle để copy môn học với webservice API.

## Tính năng

- Copy môn học với thông tin mới
- Giữ nguyên category của môn học gốc
- Hỗ trợ webservice API
- Validation đầy đủ các tham số đầu vào

## Cài đặt

1. Copy thư mục plugin vào `local/courseclone`
2. Truy cập Site Administration để cài đặt plugin
3. Cấu hình webservice trong Site Administration > Server > Web services

## Sử dụng Webservice

### Endpoint
`local_courseclone_clone_course`

### Tham số đầu vào
- `shortname_clone`: Shortname của môn học nguồn cần copy
- `fullname`: Tên đầy đủ cho môn học mới  
- `shortname`: Tên viết tắt cho môn học mới
- `startdate`: Ngày bắt đầu (timestamp)
- `enddate`: Ngày kết thúc (timestamp)

### Tham số đầu ra
- `status`: "success" hoặc "error"
- `id`: ID của môn học mới (0 nếu có lỗi)
- `message`: Thông báo kết quả hoặc lỗi

### Ví dụ sử dụng với Postman

**URL:** `http://yourmoodle.com/webservice/rest/server.php`

**Method:** POST

**Parameters:**
- `wstoken`: Your webservice token
- `wsfunction`: local_courseclone_clone_course
- `moodlewsrestformat`: json
- `shortname_clone`: MATH101
- `fullname`: Toán học cơ bản - Lớp 2
- `shortname`: MATH101_2
- `startdate`: 1640995200
- `enddate`: 1672531200

## Yêu cầu hệ thống

- Moodle 4.0+
- Quyền tạo môn học, backup và restore

## Bảo mật

Plugin yêu cầu các quyền sau:
- `moodle/course:create`
- `moodle/backup:backupcourse` 
- `moodle/restore:restorecourse`