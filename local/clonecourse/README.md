# 🎓 Clone Course Plugin - Moodle Web Service API

Plugin Moodle cung cấp 2 API chính để quản lý khóa học theo danh mục thông qua Web Services.

## 📋 Chức năng

### 🔍 API 1: Xem tất cả khóa học trong danh mục
- **Function:** `local_clonecourse_get_courses_by_category`
- **Parameter:** `categoryid` (int) - ID của category
- **Permission:** `moodle/course:view`

### ➕ API 2: Thêm khóa học mới
- **Function:** `local_clonecourse_create_course`
- **Parameters:** `course[fullname]`, `course[shortname]`, `course[categoryid]` (required)
- **Permission:** `moodle/course:create`

## 🚀 Cài đặt Plugin

1. **Copy plugin:** Upload folder `clonecourse` vào `moodle/local/`
2. **Install plugin:** Site Administration → Notifications → Upgrade database
3. **Enable Web Services:** Site Administration → Advanced features → Enable web services ✅
4. **Configure service:** Site Administration → Web services → External services → Enable "Clone Course Service"
5. **Create token:** Site Administration → Web services → Manage tokens

## 🔑 Quyền truy cập

- `local/clonecourse:view` - Xem khóa học trong danh mục
- `local/clonecourse:manage` - Quản lý tạo khóa học

**Roles có quyền mặc định:** Manager, Course Creator

## 🧪 Testing với Postman

Import file `Clone_Course_API.postman_collection.json` vào Postman và set variables:
- `moodle_url`: URL Moodle site của bạn
- `ws_token`: Web service token

### Sample API Calls:

**Get courses by category:**
```
GET /webservice/rest/server.php?wstoken=TOKEN&wsfunction=local_clonecourse_get_courses_by_category&moodlewsrestformat=json&categoryid=1
```

**Create course:**
```
POST /webservice/rest/server.php
wstoken=TOKEN&wsfunction=local_clonecourse_create_course&moodlewsrestformat=json&course[fullname]=Test Course&course[shortname]=test123&course[categoryid]=1
```

## 📂 Cấu trúc Plugin

```
local/clonecourse/
├── externallib.php          # API logic
├── version.php              # Plugin info  
├── README.md                # Documentation
├── Clone_Course_API.postman_collection.json # Test collection
├── db/
│   ├── services.php         # Web service config
│   └── access.php           # Permissions
└── lang/en/
    └── local_clonecourse.php # Language strings
```

---

**Plugin ready để add vào Moodle! 🚀**