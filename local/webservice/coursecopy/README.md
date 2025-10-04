# Course Copy RESTful API Plugin

Plugin Moodle để copy/clone môn học thông qua RESTful API endpoint đơn giản.

## 🎯 Tính năng

- ✅ **RESTful API** với JSON request/response
- ✅ **Course Cloning** với tùy chỉnh thông tin môn học mới
- ✅ **Token Authentication** bảo mật
- ✅ **CORS Support** cho cross-origin requests
- ✅ **Không cần modify core Moodle files**

## 📡 API Endpoint

**URL**: `POST /webservice/restful/server.php/local_webservice_coursecopy_copy_course`

**Headers**:
```
Content-Type: application/json
Authorization: Bearer your_token_here
```

**Request Body**:
```json
{
  "shortname_clone": "ORIGINAL_COURSE",
  "fullname": "New Course Name",
  "shortname": "NEW_COURSE_2025",
  "startdate": 1704067200,
  "enddate": 1719792000
}
```

## 🔧 Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `shortname_clone` | string | ✅ | Shortname của môn học nguồn cần copy |
| `fullname` | string | ✅ | Tên đầy đủ cho môn học mới |
| `shortname` | string | ✅ | Shortname cho môn học mới (phải unique) |
| `startdate` | integer | ✅ | Ngày bắt đầu (Unix timestamp) |
| `enddate` | integer | ✅ | Ngày kết thúc (Unix timestamp) |

## 📤 Response Format

### Success Response
```json
{
  "status": "success",
  "id": 123,
  "message": "Course copied successfully"
}
```

### Error Response
```json
{
  "status": "error",
  "id": 0,
  "message": "Source course not found with shortname: ORIGINAL_COURSE"
}
```

## 🔐 Authentication

### 1. Tạo Token
1. Vào **Site Administration → Server → Web services → Manage tokens**
2. Click **Create token**
3. Chọn user và service (hoặc để trống)
4. Copy token để sử dụng

### 2. Sử dụng Token
Truyền token qua một trong hai cách:

**Option 1: Authorization Header**
```
Authorization: Bearer your_token_here
```

**Option 2: Request Body**
```json
{
  "token": "your_token_here",
  "shortname_clone": "...",
  ...
}
```

## 🧪 Testing Examples

### cURL Example
```bash
curl -X POST "http://your-moodle-site/webservice/restful/server.php/local_webservice_coursecopy_copy_course" \\
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_token_here" \
  -d '{
    "shortname_clone": "MATH101",
    "fullname": "Mathematics 101 - Spring 2025",
    "shortname": "MATH101_2025",
    "startdate": 1704067200,
    "enddate": 1719792000
  }'
```

### JavaScript/Fetch Example
```javascript
const response = await fetch('/webservice/restful/server.php/local_webservice_coursecopy_copy_course', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer your_token_here'
  },
  body: JSON.stringify({
    shortname_clone: 'MATH101',
    fullname: 'Mathematics 101 - Spring 2025',
    shortname: 'MATH101_2025',
    startdate: 1704067200,
    enddate: 1719792000
  })
});

const result = await response.json();
console.log(result);
```

## ⚙️ Cài đặt

### 1. Upload Plugin
1. Upload thư mục `coursecopy` vào `local/webservice/`
2. Vào **Site Administration → Notifications** để cài đặt

### 2. Cấu hình Permissions
1. Vào **Site Administration → Users → Permissions → Define roles**
2. Edit role cần thiết và add capabilities:
   - `moodle/course:create`
   - `moodle/backup:backupcourse`
   - `moodle/restore:restorecourse`

### 3. Enable Web Services (để tạo token)
1. Vào **Site Administration → Advanced features**
2. Check **Enable web services**

## 🔧 Requirements

- **Moodle 3.9+**
- Web services enabled (để tạo token)
- User với quyền:
  - `moodle/course:create`
  - `moodle/backup:backupcourse`
  - `moodle/restore:restorecourse`

## 🐛 Troubleshooting

### Lỗi thường gặp:

1. **"Invalid token"**:
   - Kiểm tra token có tồn tại trong database
   - Token chưa expired

2. **"Source course not found"**:
   - Kiểm tra `shortname_clone` có chính xác không
   - Course phải visible hoặc user có quyền truy cập

3. **"Course with shortname already exists"**:
   - `shortname` mới phải unique trong hệ thống

4. **"User does not have permission"**:
   - User cần có đủ capabilities như đã liệt kê ở trên

5. **"Start date must be before end date"**:
   - Kiểm tra `startdate < enddate`
   - Cả hai đều phải là Unix timestamp

## 📁 File Structure

```
local/webservice/coursecopy/
├── restful.php              # Main RESTful API endpoint
├── version.php              # Plugin version info
├── README.md                # This documentation
├── externallib.php          # Optional traditional webservice
└── db/
    ├── access.php           # Capabilities definition
    └── services.php         # Web service definitions
```

## 🎯 Technical Details

- **Protocol**: RESTful API với JSON
- **Authentication**: Token-based (Moodle web service tokens)
- **Method**: POST only
- **Content-Type**: application/json
- **CORS**: Enabled for cross-origin requests
- **Backup Method**: Moodle's built-in backup/restore system

---

**Plugin Version**: v1.0  
**Compatible**: Moodle 3.9+  
**License**: GPL v3 or later
