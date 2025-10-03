# 📋 Course Clone RESTful API - Plugin Summary

## 🎯 **Plugin Overview**

**RESTful API plugin** cho Moodle để clone courses với **Bearer Token authentication**, tuân thủ chuẩn REST API hiện đại.

## 📁 **File Structure**

```
local/courseclone/
├── README.md                           # Main documentation  
├── version.php                         # Plugin version & metadata
├── externallib.php                     # API functions implementation
├── restful_api.php                     # RESTful middleware & Bearer token handler
├── RESTFUL_BEARER_SETUP.md            # Chi tiết setup guide
├── Course_Clone_API.postman_collection.json  # Postman test collection
├── db/
│   ├── access.php                      # Capabilities definitions
│   └── services.php                    # Web service definitions  
└── lang/en/
    └── local_courseclone.php           # Language strings
```

## 🔧 **Core Components**

### **1. API Functions** (`externallib.php`)
- `local_courseclone_get_course_list` - Lấy danh sách courses
- `local_courseclone_get_clone_status` - Check course info
- `local_courseclone_clone_course` - Clone course với params mới

### **2. RESTful Middleware** (`restful_api.php`)
- **Bearer Token Parser** - Extract token từ Authorization header
- **JSON Body Handler** - Process JSON request body
- **CORS Support** - Handle preflight requests
- **Token Validation** - Verify token against database

### **3. Database Definitions**
- **Services** (`db/services.php`) - Define web service functions
- **Capabilities** (`db/access.php`) - User permissions

## 🚀 **Key Features**

### **RESTful Authentication**
```http
POST /webservice/rest/server.php
Authorization: Bearer {token}
Content-Type: application/json
```

### **JSON Request Format**
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

### **Clean Response Format**
```json
{
  "status": "success",
  "id": 123,
  "message": "Course cloned successfully!",
  "new_course": {
    "id": 123,
    "fullname": "Mathematics 101 - Copy",
    "shortname": "MATH101_COPY"
  }
}
```

## ⚙️ **Setup Process**

### **Step 1: Plugin Installation**
1. Copy plugin to `local/courseclone`
2. Visit Site Administration > Notifications
3. Plugin auto-installed

### **Step 2: Web Service Setup**
1. Enable web services & REST protocol
2. Create service: "Course Clone RESTful Service"  
3. Add functions: `local_courseclone_*`
4. Create token for API user

### **Step 3: RESTful Integration**
Edit `webservice/rest/server.php`:
```php
// RESTful Bearer Token Support
if (file_exists($CFG->dirroot . '/local/courseclone/restful_api.php')) {
    require_once($CFG->dirroot . '/local/courseclone/restful_api.php');
    local_courseclone_handle_restful_request();
}
```

## 🧪 **Testing Options**

### **1. Postman Collection**
- Import `Course_Clone_API.postman_collection.json`
- Set variables: `moodle_url`, `webservice_token`
- Execute pre-configured requests

### **2. cURL Examples**
```bash
curl -X POST "http://localhost/moodle/webservice/rest/server.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"wsfunction": "local_courseclone_get_course_list", ...}'
```

## 🔒 **Security Features**

- ✅ **Bearer Token** - Token trong header, không expose
- ✅ **Token Validation** - Verify từ database  
- ✅ **Permission Checks** - User capabilities validation
- ✅ **CORS Protection** - Configurable origins
- ✅ **Input Sanitization** - Validate all parameters

## 📊 **Benefits**

### **vs Traditional Moodle Web Services:**
- 🔐 **More Secure** - Bearer token thay vì token trong URL/body
- 🚀 **REST Standard** - Tuân thủ chuẩn REST API
- 📱 **Modern** - JSON-first, CORS-ready
- 🛠️ **Developer Friendly** - Easy integration với frontend frameworks

### **Use Cases:**
- **Learning Management Systems** integration
- **Course Automation** workflows  
- **Bulk Course Creation** từ external systems
- **API-driven Course Management**

## 📚 **Documentation**

- **README.md** - Main plugin documentation
- **RESTFUL_BEARER_SETUP.md** - Detailed setup instructions
- **Postman Collection** - Ready-to-use API tests
- **Code Comments** - Inline documentation trong source code

## 🎯 **Target Audience**

- **System Integrators** - Tích hợp Moodle với external systems
- **Developers** - Build applications on top của Moodle
- **Administrators** - Automate course management tasks
- **API Consumers** - Need modern REST API access

---

**Plugin này cung cấp foundation cho modern API-driven course management trong Moodle ecosystem.**