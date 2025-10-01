# Hướng dẫn cài đặt và test Course Clone Plugin

## Bước 1: Cài đặt Plugin

1. **Upload plugin:**
   - Copy thư mục `courseclone` vào `{moodle_root}/local/`
   - Đảm bảo đường dẫn: `{moodle_root}/local/courseclone/`

2. **Cài đặt qua Moodle Admin:**
   - Đăng nhập với quyền admin
   - Vào **Site Administration > Notifications**
   - Click **Upgrade Moodle database now**

## Bước 2: Cấu hình Webservice

### 2.1 Bật Webservice
1. Vào **Site Administration > Advanced features**
2. Tick **Enable web services**
3. Save changes

### 2.2 Bật REST Protocol  
1. Vào **Site Administration > Server > Web services > Manage protocols**
2. Enable **REST protocol**

### 2.3 Tạo Service
1. Vào **Site Administration > Server > Web services > External services**
2. Click **Add** để tạo service mới:
   - **Name:** Course Clone Service
   - **Short name:** courseclone_service  
   - **Enabled:** Yes
   - **Authorised users only:** No (hoặc Yes nếu muốn giới hạn user)

### 2.4 Thêm Function vào Service
1. Click vào service vừa tạo
2. Click **Add functions**
3. Tìm và thêm: `local_courseclone_clone_course`

### 2.5 Tạo User cho Webservice
1. Vào **Site Administration > Users > Accounts > Browse list of users**
2. Tạo user mới hoặc dùng user có sẵn
3. Gán role **Manager** hoặc role có đủ quyền:
   - `moodle/course:create`
   - `moodle/backup:backupcourse`
   - `moodle/restore:restorecourse`

### 2.6 Tạo Token
1. Vào **Site Administration > Server > Web services > Manage tokens**  
2. Click **Add**
3. Chọn:
   - **User:** User đã tạo ở bước 2.5
   - **Service:** courseclone_service
4. Save và copy **Token** được tạo

## Bước 3: Test với Postman

### 3.1 Import Collection
1. Mở Postman
2. Import file: `Course_Clone_API.postman_collection.json`

### 3.2 Cấu hình Environment Variables
1. Tạo Environment mới trong Postman
2. Thêm variables:
   ```
   moodle_url: http://localhost/moodle (thay bằng URL Moodle của bạn)
   webservice_token: [token từ bước 2.6]
   username: admin (hoặc username của bạn)
   password: [password của user]
   ```

### 3.3 Test Clone Course
1. Chuẩn bị dữ liệu test:
   - Tạo 1 môn học test với shortname `MATH101`
   
2. Chạy request **Clone Course** với parameters:
   ```
   shortname_clone: MATH101
   fullname: Toán học cơ bản - Lớp 2  
   shortname: MATH101_2
   startdate: 1704067200 (timestamp cho 01/01/2024)
   enddate: 1719792000 (timestamp cho 01/07/2024)
   ```

3. Kiểm tra response:
   ```json
   {
       "status": "success",
       "id": 123,
       "message": "Copy môn học thành công!"
   }
   ```

## Bước 4: Kiểm tra kết quả

1. **Vào Course Management:**
   - **Site Administration > Courses > Manage courses and categories**
   
2. **Verify course mới:**
   - Tìm course với shortname `MATH101_2`
   - Kiểm tra category giống với course gốc
   - Kiểm tra ngày bắt đầu/kết thúc
   - Kiểm tra nội dung đã được copy

## Troubleshooting

### Lỗi thường gặp:

1. **"Invalid token"**
   - Kiểm tra token đúng chưa
   - Kiểm tra service đã được enable chưa

2. **"Access denied"** 
   - Kiểm tra user có đủ quyền chưa
   - Kiểm tra capability trong db/access.php

3. **"Course not found"**
   - Kiểm tra shortname_clone có tồn tại không
   - Kiểm tra user có quyền truy cập course gốc không

4. **"Shortname already exists"**
   - Shortname mới đã tồn tại, đổi shortname khác

### Debug:

1. **Bật debug mode:**
   - **Site Administration > Development > Debugging**
   - Set **Debug messages: DEVELOPER**

2. **Check logs:**
   - **Site Administration > Server > Logs**

3. **Test function trực tiếp:**
   - Vào **Site Administration > Development > Web service test client**

## Ví dụ cURL

```bash
curl -X POST "http://localhost/moodle/webservice/rest/server.php" \
  -d "wstoken=your_token_here" \
  -d "wsfunction=local_courseclone_clone_course" \
  -d "moodlewsrestformat=json" \
  -d "shortname_clone=MATH101" \
  -d "fullname=Toán học cơ bản - Lớp 2" \
  -d "shortname=MATH101_2" \
  -d "startdate=1704067200" \
  -d "enddate=1719792000"
```

## Timestamp Converter

Để convert ngày sang timestamp:
- **Online tool:** https://www.epochconverter.com/
- **PHP:** `strtotime('2024-01-01')`
- **JavaScript:** `Math.floor(new Date('2024-01-01').getTime() / 1000)`