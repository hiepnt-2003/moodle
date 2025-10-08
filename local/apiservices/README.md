# API Services Plugin for Moodle

Plugin tích hợp 2 webservice: **Course Copy** và **User Creation** vào một dịch vụ duy nhất.

## Mô tả

Plugin `local_apiservices` cung cấp 2 API chính:
1. **Copy Course** - Sao chép khóa học với thông tin mới
2. **Create User** - Tạo người dùng mới

## Cài đặt

1. Copy thư mục `apiservices` vào `local/` của Moodle
2. Truy cập **Site administration > Notifications** để cài đặt plugin
3. Cấu hình Web Service:
   - Vào **Site administration > Server > Web services > Overview**
   - Enable web services
   - Enable protocols (REST, SOAP, etc.)
   - Tạo một service mới hoặc sử dụng service "API Services" có sẵn
   - Thêm các functions:
     - `local_apiservices_copy_course`
     - `local_apiservices_create_user`
   - Tạo token cho user có quyền phù hợp

## API Functions

### 1. Copy Course (`local_apiservices_copy_course`)

Sao chép một khóa học với thông tin mới.

**Tham số:**
- `shortname_clone` (string) - Shortname của khóa học nguồn
- `fullname` (string) - Tên đầy đủ cho khóa học mới
- `shortname` (string) - Shortname cho khóa học mới
- `startdate` (int) - Ngày bắt đầu (Unix timestamp)
- `enddate` (int) - Ngày kết thúc (Unix timestamp)

**Trả về:**
```json
{
  "status": "success|error",
  "id": 123,
  "message": "Thông báo"
}
```

**Ví dụ REST API:**
```bash
curl -X POST "https://your-moodle-site/webservice/rest/server.php" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=local_apiservices_copy_course" \
  -d "moodlewsrestformat=json" \
  -d "shortname_clone=COURSE001" \
  -d "fullname=New Course Name" \
  -d "shortname=COURSE002" \
  -d "startdate=1704067200" \
  -d "enddate=1735689600"
```

### 2. Create User (`local_apiservices_create_user`)

Tạo người dùng mới trong hệ thống.

**Tham số:**
- `username` (string) - Username (chỉ chữ, số, dấu chấm, gạch dưới, gạch ngang)
- `firstname` (string) - Họ
- `lastname` (string) - Tên
- `email` (string) - Email
- `createpassword` (boolean) - Tự động tạo mật khẩu (true/false)
- `password` (string) - Mật khẩu (bắt buộc nếu createpassword=false)

**Trả về:**
```json
{
  "status": "success|error",
  "id": 456,
  "message": "Thông báo"
}
```

**Ví dụ REST API:**
```bash
# Tạo user với mật khẩu tự động
curl -X POST "https://your-moodle-site/webservice/rest/server.php" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=local_apiservices_create_user" \
  -d "moodlewsrestformat=json" \
  -d "username=johndoe" \
  -d "firstname=John" \
  -d "lastname=Doe" \
  -d "email=john.doe@example.com" \
  -d "createpassword=1"

# Tạo user với mật khẩu tùy chỉnh
curl -X POST "https://your-moodle-site/webservice/rest/server.php" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=local_apiservices_create_user" \
  -d "moodlewsrestformat=json" \
  -d "username=janedoe" \
  -d "firstname=Jane" \
  -d "lastname=Doe" \
  -d "email=jane.doe@example.com" \
  -d "createpassword=0" \
  -d "password=SecurePass123"
```

## Quyền truy cập

Plugin yêu cầu các quyền sau:
- **Course Copy**: `moodle/course:create`
- **User Creation**: `moodle/user:create`

## Postman Collection

Bạn có thể import Postman collection từ các plugin gốc để test:
- `Course_Copy_API.postman_collection.json` (từ local/coursecopy)
- `User_Creation_API.postman_collection.json` (từ local/usercreation)

**Lưu ý:** Cần thay đổi tên function trong collection:
- `local_coursecopy_copy_course` → `local_apiservices_copy_course`
- `local_usercreation_create_user` → `local_apiservices_create_user`

## Cấu trúc thư mục

```
local/apiservices/
├── db/
│   ├── access.php          # Định nghĩa capabilities
│   └── services.php        # Định nghĩa web services
├── lang/
│   └── en/
│       └── local_apiservices.php  # Language strings
├── externallib.php         # External API functions
├── version.php             # Plugin version
└── README.md              # Tài liệu này
```

## Hỗ trợ

- **Moodle version**: 3.8 trở lên
- **Plugin type**: Local
- **Maturity**: Stable

## License

GPL v3 or later

## Credits

Plugin này được tạo bằng cách gộp 2 plugin:
- `local_coursecopy` - Course Copy Service
- `local_usercreation` - User Creation Service
