# Course Clone RESTful API Plugin

RESTful API plugin cho Moodle để clone/copy courses với Bearer Token authentication.

**✅ Compatible với Moodle 3.10 + RESTful Protocol Plugin** (đã được test và confirm)

## 🚀 **Tính năng**

- ✅ **RESTful API** với Bearer Token authentication (`Authorization: Bearer {token}`)
- ✅ **JSON Request/Response** format chuẩn REST API
- ✅ **CORS Support** cho web applications
- ✅ **Course Cloning** với thông tin tùy chỉnh
- ✅ **Course Discovery** - lấy danh sách courses
- ✅ **Clone Status Check** - kiểm tra thông tin course

## 📡 **API Endpoints**

Base URL: `POST {{moodle_url}}/webservice/rest/server.php`

### **Authentication**
```
Authorization: Bearer {your_token}
Content-Type: application/json
```

### **1. Get Course List**
```json
{
  "wsfunction": "local_courseclone_get_course_list",
  "moodlewsrestformat": "json",
  "categoryid": 0,
  "visible": true
}
```

### **2. Get Clone Status**
```json
{
  "wsfunction": "local_courseclone_get_clone_status", 
  "moodlewsrestformat": "json",
  "courseid": 5
}
```

### **3. Clone Course**
```json
{
  "wsfunction": "local_courseclone_clone_course",
  "moodlewsrestformat": "json",
  "shortname_clone": "MATH101",
  "fullname": "Mathematics 101 - Copy",
  "shortname": "MATH101_COPY",
  "startdate": 1704067200,
  "enddate": 1719792000
}
```

## ⚙️ **Installation & Setup**

### **1. Plugin Installation**
```bash
# Copy plugin to Moodle
cp -r courseclone /path/to/moodle/local/

# Upgrade database
Visit: Site Administration > Notifications
```

### **2. Web Service Configuration**
```
Site Administration > Server > Web services > Overview:
✅ Enable web services
✅ Enable REST protocol

External services > Add service:
- Name: Course Clone RESTful Service
- Functions: local_courseclone_*
- Enabled: Yes

Manage tokens > Create token:
- Service: Course Clone RESTful Service 
- User: API User
```

### **3. RESTful Middleware Setup**

Edit `webservice/rest/server.php`, add after includes:

```php
// RESTful Bearer Token Support
if (file_exists($CFG->dirroot . '/local/courseclone/restful_api.php')) {
    require_once($CFG->dirroot . '/local/courseclone/restful_api.php');
    local_courseclone_handle_restful_request();
}
```

## 🧪 **Testing**

### **Postman Collection**
Import `Course_Clone_API.postman_collection.json`:
- Set `moodle_url` variable
- Set `webservice_token` variable
- Execute requests

### **cURL Examples**
```bash
# Get courses
curl -X POST "http://localhost/moodle/webservice/rest/server.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"wsfunction": "local_courseclone_get_course_list", "moodlewsrestformat": "json", "categoryid": 0, "visible": true}'

# Clone course
curl -X POST "http://localhost/moodle/webservice/rest/server.php" \
  -H "Content-Type: application/json" \  
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"wsfunction": "local_courseclone_clone_course", "moodlewsrestformat": "json", "shortname_clone": "MATH101", "fullname": "Math Copy", "shortname": "MATH101_COPY", "startdate": 1704067200, "enddate": 1719792000}'
```

## 🔒 **Security**

- **Bearer Token Authentication** - token trong header, không expose trong URL
- **Token Validation** - verify token từ database  
- **User Permissions** - check capabilities cho từng function
- **CORS Protection** - configurable allowed origins

## 📚 **Documentation**

- `RESTFUL_BEARER_SETUP.md` - Chi tiết setup middleware
- `Course_Clone_API.postman_collection.json` - Postman test collection

## 🛠️ **Requirements**

- **Moodle 3.10+** (tested với 3.10)
- **RESTful Protocol Plugin** (recommended - enhances API capabilities) ✅
- Web services enabled
- REST/RESTful protocol enabled
- User với quyền: `moodle/course:create`, `moodle/backup:backupcourse`, `moodle/restore:restorecourse`

## 🆕 **Enhanced với RESTful Plugin**

Nếu bạn đã cài RESTful Protocol Plugin (như trong screenshot), plugin này sẽ:
- ✅ **Auto-detect** RESTful plugin và tối ưu hóa processing
- ✅ **Better JSON handling** với enhanced parsing
- ✅ **Improved error messages** với RESTful-specific responses
- ✅ **Enhanced CORS support** cho web applications