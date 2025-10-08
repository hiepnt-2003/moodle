# API Services - Moodle Web Services Plugin

## Overview

Plugin **API Services** cung cấp các web services API để quản lý Course và User trong Moodle. Plugin này gộp chức năng của 2 services:
- **Course Copy Service** - Copy môn học với thông tin mới
- **User Creation Service** - Tạo người dùng mới

## Features

### 1. Course Copy API
- Copy môn học từ một môn học nguồn với các thông tin mới
- Giữ nguyên cấu trúc, cài đặt và format của môn học nguồn
- Tự động sao chép các course format options

### 2. User Creation API
- Tạo người dùng mới với đầy đủ thông tin
- Hỗ trợ tự động tạo password hoặc tự định nghĩa
- Kiểm tra tính hợp lệ của username, email và password

## Installation

1. Copy thư mục `apiservices` vào thư mục `local/` của Moodle
2. Truy cập Site Administration → Notifications để cài đặt plugin
3. Cấu hình Web Services theo hướng dẫn trong [SETUP_GUIDE.md](SETUP_GUIDE.md)

## API Endpoints

### 1. Copy Course
**Function:** `local_apiservices_copy_course`

**Parameters:**
- `shortname_clone` (string) - Shortname của môn học nguồn cần copy
- `fullname` (string) - Tên đầy đủ cho môn học mới
- `shortname` (string) - Tên viết tắt cho môn học mới
- `startdate` (int) - Ngày bắt đầu (Unix timestamp)
- `enddate` (int) - Ngày kết thúc (Unix timestamp)

**Returns:**
```json
{
    "status": "success",
    "id": 123,
    "message": "Copy môn học thành công! ID môn học mới: 123"
}
```

### 2. Create User
**Function:** `local_apiservices_create_user`

**Parameters:**
- `username` (string) - Username cho người dùng mới
- `firstname` (string) - Tên
- `lastname` (string) - Họ
- `email` (string) - Địa chỉ email
- `createpassword` (boolean) - Tự động tạo password hay không
- `password` (string) - Password (bắt buộc nếu createpassword = false)

**Returns:**
```json
{
    "status": "success",
    "id": 456,
    "message": "User has been successfully created"
}
```

## Testing

Sử dụng Postman collection đã được cung cấp trong file `API_Services.postman_collection.json` để test các API endpoints.

### RESTful Protocol

Plugin này sử dụng **webservice_restful** protocol của Moodle:

**Endpoint Format**: `{{moodle_url}}/webservice/restful/server.php/{function_name}`

**Headers bắt buộc**:
- `Authorization`: YOUR_TOKEN (không cần 'Bearer' prefix)
- `Content-Type`: application/json
- `Accept`: application/json

**Body**: JSON format với các parameters

## Documentation

- [Setup Guide](SETUP_GUIDE.md) - Hướng dẫn cài đặt và cấu hình chi tiết
- [API Overview](OVERVIEW.md) - Tổng quan về các API
- [RESTful Protocol Guide](RESTFUL_GUIDE.md) - Hướng dẫn sử dụng RESTful protocol
- [Postman Collection](API_Services.postman_collection.json) - Collection để test APIs

## Requirements

- Moodle 3.8 trở lên
- Web Services được kích hoạt
- RESTful protocol được kích hoạt (webservice_restful plugin)

## License

This plugin is licensed under the GNU GPL v3 or later.

## Support

Để được hỗ trợ, vui lòng tạo issue hoặc liên hệ với đội phát triển.
