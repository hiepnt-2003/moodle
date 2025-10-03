# Course Clone Plugin - RESTful API

Moodle plugin để clone/copy courses với RESTful API endpoint đơn giản.

## 🚀 Tính năng

- ✅ **RESTful API** với Bearer Token authentication
- ✅ **JSON Request/Response** format chuẩn
- ✅ **Course Cloning** với thông tin tùy chỉnh
- ✅ **Course Discovery** - lấy danh sách courses
- ✅ **Clone Status Check** - kiểm tra thông tin course
- ✅ **Không cần modify core Moodle files**

## 📡 API Endpoint

**URL**: `POST http://your-moodle-site.com/local/courseclone/simple_restful.php`

**Authentication**: 
```
Authorization: Bearer your_token_here
Content-Type: application/json
```

## 🔧 API Functions

### 1. Get Course List
```json
{
  "wsfunction": "local_courseclone_get_course_list",
  "categoryid": 0,
  "visible": true
}
```

### 2. Get Clone Status
```json
{
  "wsfunction": "local_courseclone_get_clone_status",
  "courseid": 5
}
```

### 3. Clone Course
```json
{
  "wsfunction": "local_courseclone_clone_course",
  "shortname_clone": "MATH101",
  "fullname": "Mathematics 101 - Copy",
  "shortname": "MATH101_COPY",
  "startdate": 1704067200,
  "enddate": 1719792000
}
```

## ⚙️ Cài đặt

### 1. Plugin Installation
```bash
# Copy plugin to Moodle
cp -r courseclone /path/to/moodle/local/
```

### 2. Moodle Configuration
1. Visit **Site Administration > Notifications** để cài đặt plugin
2. Vào **Site Administration > Server > Web services > Overview**:
   - ✅ Enable web services
   - ✅ Enable REST protocol
3. Tạo external service và token trong **Manage tokens**

## 🧪 Testing

Import Postman collection: `Simple_Course_Clone_API.postman_collection.json`

### cURL Example
```bash
curl -X POST "http://your-site.com/local/courseclone/simple_restful.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_token" \
  -d '{"wsfunction": "local_courseclone_get_course_list", "categoryid": 0}'
```

## 📁 Files

- `simple_restful.php` - RESTful API endpoint
- `externallib.php` - Moodle external functions
- `version.php` - Plugin version info
- `Simple_Course_Clone_API.postman_collection.json` - Postman test collection

## 🎯 Requirements

- **Moodle 3.8+**
- Web services enabled
- REST protocol enabled
- User với quyền: `moodle/course:create`, `moodle/backup:backupcourse`, `moodle/restore:restorecourse`