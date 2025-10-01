# Hướng dẫn cài đặt và sử dụng User Creation Web Service

## 1. Cài đặt Plugin

### Bước 1: Upload Plugin
1. Sao chép thư mục `usercreation` vào `{moodle_root}/local/`
2. Đảm bảo cấu trúc thư mục như sau:
```
local/
  usercreation/
    version.php
    externallib.php
    README.md
    test.php
    User_Creation_API.postman_collection.json
    db/
      services.php
      access.php
    lang/
      en/
        local_usercreation.php
```

### Bước 2: Cài đặt Plugin
1. Truy cập `Site administration` → `Notifications`
2. Moodle sẽ phát hiện plugin mới và yêu cầu cài đặt
3. Nhấn `Upgrade Moodle database now`

## 2. Cấu hình Web Service

### Bước 1: Kích hoạt Web Services
1. Truy cập `Site administration` → `Advanced features`
2. Tích chọn `Enable web services`
3. Lưu thay đổi

### Bước 2: Kích hoạt REST Protocol
1. Truy cập `Site administration` → `Server` → `Web services` → `Manage protocols`
2. Kích hoạt `REST protocol`

### Bước 3: Tạo Web Service
1. Truy cập `Site administration` → `Server` → `Web services` → `External services`
2. Nhấn `Add` để tạo service mới:
   - **Name**: User Creation Service
   - **Short name**: user_creation_service  
   - **Enabled**: ✓
   - **Authorised users only**: Tùy chọn (khuyến nghị ✓)
3. Lưu service

### Bước 4: Thêm Function vào Service
1. Trong danh sách External services, nhấn `Functions` cho service vừa tạo
2. Nhấn `Add functions`
3. Tìm và thêm function: `local_usercreation_create_user`

### Bước 5: Tạo User cho Web Service
1. Truy cập `Site administration` → `Users` → `Accounts` → `Add a new user`
2. Tạo user mới (ví dụ: `webservice_user`)
3. Gán role `Manager` hoặc tạo role tùy chỉnh với quyền `moodle/user:create`

### Bước 6: Cấp quyền cho User
1. Truy cập `Site administration` → `Server` → `Web services` → `External services`
2. Nhấn `Authorised users` cho service
3. Thêm user vừa tạo vào danh sách

### Bước 7: Tạo Token
1. Truy cập `Site administration` → `Server` → `Web services` → `Manage tokens`
2. Nhấn `Add` để tạo token mới:
   - **User**: Chọn user webservice vừa tạo
   - **Service**: User Creation Service
   - **Valid until**: Để trống hoặc đặt thời gian hết hạn
3. Lưu và copy token để sử dụng

## 3. Kiểm tra với Postman

### Bước 1: Import Collection
1. Mở Postman
2. Import file `User_Creation_API.postman_collection.json`
3. Collection sẽ xuất hiện với 4 request mẫu

### Bước 2: Cấu hình Environment
1. Tạo environment mới trong Postman
2. Thêm biến:
   - `moodle_url`: URL của Moodle (ví dụ: `http://localhost/moodle`)
3. Trong mỗi request, thay `YOUR_TOKEN_HERE` bằng token thực tế

### Bước 3: Test các Request

#### Request 1: Create User - Auto Password
- **Mô tả**: Tạo user với mật khẩu tự động
- **Đầu vào**: username, firstname, lastname, email, createpassword=1
- **Kết quả mong đợi**: 
```json
{
    "status": "success",
    "id": 123,
    "message": "User has been successfully created"
}
```

#### Request 2: Create User - Manual Password  
- **Mô tả**: Tạo user với mật khẩu thủ công
- **Đầu vào**: username, firstname, lastname, email, createpassword=0, password
- **Yêu cầu mật khẩu**: Ít nhất 8 ký tự, có chữ hoa, chữ thường, số

#### Request 3: Test Invalid Data - Duplicate Username
- **Mô tả**: Test lỗi khi username đã tồn tại
- **Kết quả mong đợi**:
```json
{
    "status": "error", 
    "id": 0,
    "message": "Username \"admin\" is already in use"
}
```

#### Request 4: Test Weak Password
- **Mô tả**: Test lỗi khi mật khẩu yếu
- **Kết quả mong đợi**:
```json
{
    "status": "error",
    "id": 0, 
    "message": "Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, and one digit"
}
```

## 4. Cấu trúc Response

Tất cả response đều có cấu trúc:
```json
{
    "status": "success|error",
    "id": number,
    "message": "string"
}
```

### Success Response:
```json
{
    "status": "success",
    "id": 123,
    "message": "User has been successfully created"
}
```

### Error Response:
```json
{
    "status": "error", 
    "id": 0,
    "message": "Error description"
}
```

## 5. Troubleshooting

### Lỗi thường gặp:

1. **"Invalid token"**
   - Kiểm tra token đã đúng chưa
   - Kiểm tra user có quyền truy cập service không

2. **"Function not found"**  
   - Kiểm tra function đã được thêm vào service chưa
   - Kiểm tra tên function có đúng không

3. **"Access denied"**
   - Kiểm tra user có quyền `moodle/user:create` không
   - Kiểm tra user có trong danh sách authorised users không

4. **"Username already exists"**
   - Thay đổi username khác
   - Đây là lỗi bình thường khi test

5. **"Invalid email format"**
   - Kiểm tra định dạng email
   - Đảm bảo email hợp lệ

## 6. Bảo mật

- Luôn sử dụng HTTPS trong production
- Đặt thời gian hết hạn cho token
- Giới hạn IP access nếu cần
- Không share token công khai
- Sử dụng "Authorised users only" cho service
- Định kỳ rotate token