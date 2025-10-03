# Course Clone RESTful API Documentation

## Overview
Course Clone API cung cấp các endpoints RESTful để clone khóa học trong Moodle.

## Base URL
```
https://your-moodle-site.com/webservice/rest/server.php
```

## Authentication
Tất cả API calls cần token xác thực. Thêm vào URL hoặc header:
```
?wstoken=YOUR_TOKEN_HERE
```

## Available Endpoints

### 1. Clone Course
**Endpoint:** `clone_course`  
**Method:** POST  
**Description:** Clone một khóa học với thông tin mới

**Parameters:**
- `wsfunction`: `local_courseclone_clone_course`
- `shortname_clone` (string): Shortname của môn học nguồn cần copy
- `fullname` (string): Tên đầy đủ cho môn học mới
- `shortname` (string): Tên viết tắt cho môn học mới
- `startdate` (int): Ngày bắt đầu (Unix timestamp)
- `enddate` (int): Ngày kết thúc (Unix timestamp)

**Example Request:**
```bash
curl -X POST "https://your-moodle-site.com/webservice/rest/server.php" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=local_courseclone_clone_course" \
  -d "moodlewsrestformat=json" \
  -d "shortname_clone=MATH101" \
  -d "fullname=Mathematics 101 - Fall 2024" \
  -d "shortname=MATH101_FALL2024" \
  -d "startdate=1693526400" \
  -d "enddate=1701302400"
```

**Example Response:**
```json
{
    "status": "success",
    "id": 15,
    "message": "Copy môn học thành công! (Đã tạo môn học mới với cấu trúc cơ bản từ môn học gốc)"
}
```

### 2. Get Course List
**Endpoint:** `get_course_list`  
**Method:** GET  
**Description:** Lấy danh sách các khóa học có thể clone

**Parameters:**
- `wsfunction`: `local_courseclone_get_course_list`
- `categoryid` (int, optional): ID danh mục (0 = tất cả, default: 0)
- `visible` (bool, optional): Chỉ lấy course hiển thị (default: true)

**Example Request:**
```bash
curl "https://your-moodle-site.com/webservice/rest/server.php?wstoken=YOUR_TOKEN&wsfunction=local_courseclone_get_course_list&moodlewsrestformat=json&categoryid=1&visible=1"
```

**Example Response:**
```json
{
    "courses": [
        {
            "id": 5,
            "fullname": "Mathematics 101",
            "shortname": "MATH101",
            "category": 1,
            "startdate": 1693526400,
            "enddate": 1701302400,
            "visible": true
        }
    ],
    "total": 1
}
```

### 3. Get Clone Status
**Endpoint:** `get_clone_status`  
**Method:** GET  
**Description:** Lấy thông tin chi tiết của một khóa học để clone

**Parameters:**
- `wsfunction`: `local_courseclone_get_clone_status`
- `courseid` (int): Course ID cần kiểm tra

**Example Request:**
```bash
curl "https://your-moodle-site.com/webservice/rest/server.php?wstoken=YOUR_TOKEN&wsfunction=local_courseclone_get_clone_status&moodlewsrestformat=json&courseid=5"
```

**Example Response:**
```json
{
    "id": 5,
    "fullname": "Mathematics 101",
    "shortname": "MATH101",
    "category": 1,
    "startdate": 1693526400,
    "enddate": 1701302400,
    "visible": true,
    "format": "topics",
    "enrolled_users": 25,
    "sections_count": 12,
    "activities_count": 48,
    "can_clone": true,
    "status": "ready_for_clone"
}
```

## Error Handling

### Error Response Format:
```json
{
    "status": "error",
    "id": 0,
    "message": "Mô tả lỗi chi tiết"
}
```

### Common Error Codes:
- **400 Bad Request**: Thiếu parameters bắt buộc
- **401 Unauthorized**: Token không hợp lệ
- **403 Forbidden**: Không có quyền thực hiện action
- **404 Not Found**: Course không tồn tại
- **500 Internal Server Error**: Lỗi server

## Setup Instructions

### 1. Enable Web Services
1. Vào **Site Administration > Server > Web services > Overview**
2. Follow steps 1-9 để enable web services

### 2. Create Service
1. Vào **Site Administration > Server > Web services > External services**
2. Add service "Course Clone Service" 
3. Add functions:
   - `local_courseclone_clone_course`
   - `local_courseclone_get_course_list`
   - `local_courseclone_get_clone_status`

### 3. Create Token
1. Vào **Site Administration > Server > Web services > Manage tokens**
2. Create token cho user có quyền tạo course
3. Assign service "Course Clone Service"

### 4. Test API
```bash
# Test with Postman or curl
curl "https://your-moodle-site.com/webservice/rest/server.php?wstoken=YOUR_TOKEN&wsfunction=local_courseclone_get_course_list&moodlewsrestformat=json"
```

## Security Notes
- Chỉ user có capability `moodle/course:create` mới có thể clone course
- Token cần được bảo mật và không share công khai
- Sử dụng HTTPS cho production environment
- Regularly rotate tokens

## Rate Limiting
- Không có built-in rate limiting
- Recommend implement rate limiting ở web server level
- Monitor API usage through Moodle logs