# User Creation Web Service Plugin

Đây là plugin Moodle để tạo người dùng thông qua Web Service API.

## Tính năng

- Tạo người dùng mới với các thông tin cơ bản
- Tự động tạo mật khẩu ngẫu nhiên hoặc sử dụng mật khẩu được cung cấp
- Kiểm tra tính hợp lệ của dữ liệu đầu vào
- Kiểm tra trùng lặp username và email
- Trả về kết quả chi tiết với status, id và message

## Cài đặt

1. Sao chép thư mục plugin vào `local/usercreation/`
2. Truy cập Site administration > Notifications để cài đặt plugin
3. Kích hoạt web service và cấu quyền cho người dùng

## Sử dụng

### Web Service Function: `local_usercreation_create_user`

**Đầu vào:**
- `username`: Tên đăng nhập (bắt buộc)
- `firstname`: Tên (bắt buộc) 
- `lastname`: Họ (bắt buộc)
- `email`: Email (bắt buộc)
- `createpassword`: Tự động tạo mật khẩu (true/false)
- `password`: Mật khẩu (bắt buộc nếu createpassword = false)

**Đầu ra:**
- `status`: "success" hoặc "error"
- `id`: ID của người dùng được tạo (0 nếu lỗi)
- `message`: Thông báo chi tiết

## Kiểm tra với Postman

1. Endpoint: `{moodle_url}/webservice/rest/server.php`
2. Method: POST
3. Parameters:
   - `wstoken`: Token của web service
   - `wsfunction`: `local_usercreation_create_user`
   - `moodlewsrestformat`: `json`
   - Các tham số của function

## Yêu cầu quyền

- `moodle/user:create`: Tạo người dùng trong hệ thống