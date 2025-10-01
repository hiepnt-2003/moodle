# Hướng dẫn cài đặt và sử dụng Course Clone Plugin

## 1. Cài đặt Plugin

### Bước 1: Copy plugin vào Moodle
```bash
# Copy thư mục courseclone vào thư mục local của Moodle
cp -r courseclone /path/to/your/moodle/local/
```

### Bước 2: Cài đặt plugin
1. Truy cập Moodle với quyền administrator
2. Vào **Site Administration → Notifications**
3. Click **Upgrade Moodle database now** để cài đặt plugin

## 2. Cấu hình Web Services

### Bước 1: Kích hoạt Web services
1. Vào **Site Administration → Advanced features**
2. Tick chọn **Enable web services**
3. Click **Save changes**

### Bước 2: Kích hoạt REST protocol
1. Vào **Site Administration → Server → Web services → Manage protocols**
2. Kích hoạt **REST protocol**

### Bước 3: Tạo External service
1. Vào **Site Administration → Server → Web services → External services**
2. Click **Add** để tạo service mới
3. Nhập thông tin:
   - **Name**: Course Clone Service
   - **Short name**: courseclone_service
   - **Enabled**: Yes
4. Click **Add service**

### Bước 4: Thêm function vào service
1. Trong danh sách External services, click **Functions** ở service vừa tạo
2. Click **Add functions**
3. Tìm và chọn: **local_courseclone_clone_course**
4. Click **Add functions**

### Bước 5: Tạo User và Token
1. Tạo user mới hoặc sử dụng user có sẵn
2. Vào **Site Administration → Server → Web services → Manage tokens**
3. Click **Add**
4. Chọn:
   - **User**: User bạn muốn dùng
   - **Service**: Course Clone Service
5. Click **Save changes**
6. **Lưu lại token** được tạo ra

### Bước 6: Phân quyền cho User
User cần có các quyền sau:
- `moodle/course:create`
- `moodle/backup:backupcourse` 
- `moodle/restore:restorecourse`
- `local/courseclone:clone`

## 3. Test với Postman

### Bước 1: Import Collection
1. Mở Postman
2. Import file `Course_Clone_API.postman_collection.json`

### Bước 2: Cấu hình Variables
1. Click vào Collection → Variables
2. Cập nhật các giá trị:
   - `moodle_url`: URL Moodle của bạn (ví dụ: http://localhost/moodle)
   - `wstoken`: Token vừa tạo ở bước 5 trên
   - `source_shortname`: Shortname của khóa học muốn clone
   - `new_fullname`: Tên đầy đủ cho khóa học mới
   - `new_shortname`: Shortname cho khóa học mới (phải unique)

### Bước 3: Test Authentication
1. Chạy request **Test Authentication** trước
2. Nếu thành công, bạn sẽ thấy thông tin site

### Bước 4: Test Clone Course
1. Chạy request **Clone Course**
2. Kiểm tra response:
   - Success: `{"status": "success", "id": 123, "message": "Course cloned successfully"}`
   - Error: `{"status": "error", "id": 0, "message": "Error description"}`

## 4. Các thông số đầu vào

| Tham số | Kiểu | Mô tả | Bắt buộc |
|---------|------|-------|----------|
| `shortname_clone` | string | Shortname của khóa học nguồn | Có |
| `fullname` | string | Tên đầy đủ của khóa học mới | Có |
| `shortname` | string | Shortname của khóa học mới | Có |
| `startdate` | int | Thời gian bắt đầu (timestamp) | Có |
| `enddate` | int | Thời gian kết thúc (timestamp) | Có |

## 5. Các thông số đầu ra

| Tham số | Kiểu | Mô tả |
|---------|------|-------|
| `status` | string | "success" hoặc "error" |
| `id` | int | ID của khóa học mới (0 nếu lỗi) |
| `message` | string | Thông báo thành công hoặc lỗi |

## 6. Ví dụ sử dụng

### Request URL:
```
POST /webservice/rest/server.php
```

### Request Body (form-data):
```
wstoken: your_token_here
wsfunction: local_courseclone_clone_course
moodlewsrestformat: json
shortname_clone: math101
fullname: Mathematics 101 - Spring 2025
shortname: math101_spring2025
startdate: 1704067200
enddate: 1735689600
```

### Response thành công:
```json
{
    "status": "success",
    "id": 125,
    "message": "Course cloned successfully"
}
```

### Response lỗi:
```json
{
    "status": "error",
    "id": 0,
    "message": "Source course with shortname 'math101' not found"
}
```

## 7. Troubleshooting

### Lỗi thường gặp:

1. **"Invalid token"**: Kiểm tra token có đúng không
2. **"Function not found"**: Plugin chưa được cài đặt đúng
3. **"Access denied"**: User không đủ quyền
4. **"Course not found"**: Shortname nguồn không tồn tại
5. **"Shortname already exists"**: Shortname mới đã được sử dụng

### Debug:
1. Kiểm tra log Moodle trong **Site Administration → Reports → Logs**
2. Kiểm tra PHP error log
3. Test trực tiếp bằng file `test.php` trong plugin