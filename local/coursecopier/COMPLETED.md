# 🎉 PLUGIN COURSE COPIER HOÀN THÀNH

## ✅ Đã tạo thành công Plugin Webservice Moodle

### 📋 Yêu cầu đã thực hiện:

✅ **Plugin webservice moodle/local/coursecopier**  
✅ **Endpoint chứa token và body dạng JSON**  
✅ **Trả về dạng JSON**  
✅ **Webservice clone khóa học với:**
  - Đầu vào: shortname_clone, fullname, shortname, startdate, enddate
  - Đầu ra: status, id, message (báo lỗi nếu có)
✅ **Postman collection để kiểm tra webservice**

## 📁 Cấu trúc Plugin đã tạo:

```
local/coursecopier/
├── api.php                          # 🚀 JSON API endpoint chính
├── externallib.php                  # 📚 External web service functions  
├── version.php                      # 📋 Plugin version information
├── README.md                        # 📖 Documentation đầy đủ
├── demo.html                        # 🎯 Demo test interface (standalone)
├── test_api.php                     # 🧪 Test interface (trong Moodle)
├── Course_Copier_API.postman_collection.json # 📮 Postman collection
├── db/
│   ├── access.php                   # 🔐 Capabilities definition
│   └── services.php                 # ⚙️ Web service configuration
└── lang/
    └── en/
        └── local_coursecopier.php   # 🌐 Language strings
```

## 🎯 API Endpoint chính:

**URL:** `POST /local/coursecopier/api.php`  
**Content-Type:** `application/json`  
**Authentication:** Bearer Token hoặc token trong JSON body

### Request Example:
```json
{
  "wstoken": "YOUR_TOKEN",
  "wsfunction": "local_coursecopier_clone_course",
  "shortname_clone": "COURSE123",
  "fullname": "Khóa học Clone 2025",
  "shortname": "CLONE2025",
  "startdate": 1704067200,
  "enddate": 1719792000
}
```

### Response Example:
```json
{
  "status": "success",
  "id": 25,
  "message": "Copy môn học thành công! Đã sao chép toàn bộ nội dung từ môn học gốc."
}
```

## 📋 Functions Available:

1. **`local_coursecopier_clone_course`** - Clone khóa học chính
2. **`local_coursecopier_get_available_courses`** - Lấy danh sách khóa học có thể clone

## 🧪 Testing Tools:

1. **Postman Collection**: `Course_Copier_API.postman_collection.json`
   - Import vào Postman để test API
   - Cấu hình environment variables: moodle_url, ws_token

2. **Demo HTML**: `demo.html` 
   - Standalone test interface (mở trực tiếp trong browser)
   - Không cần Moodle login

3. **Test API**: `test_api.php`
   - Test interface trong Moodle admin
   - Require Moodle login và capabilities

## 🚀 Cách cài đặt và sử dụng:

### 1. Cài đặt Plugin:
```bash
# Copy plugin vào Moodle
cp -r coursecopier /path/to/moodle/local/

# Hoặc trong Moodle admin:
# Site Administration → Notifications → Upgrade
```

### 2. Cấu hình Web Services:
1. **Enable Web Services**: Site Administration → Advanced features → Enable web services
2. **Enable REST**: Site Administration → Server → Web services → Manage protocols → REST protocol  
3. **Create Token**: Site Administration → Server → Web services → Manage tokens

### 3. Test API:
- **Postman**: Import collection và test
- **Demo HTML**: Mở `demo.html` trong browser và test
- **cURL**: Sử dụng examples trong README.md

## 🔐 Security Features:

✅ **Token Authentication**: Hỗ trợ Bearer token và JSON body token  
✅ **Capability Checks**: Kiểm tra quyền user trước khi thực hiện  
✅ **Input Validation**: Validate tất cả parameters đầu vào  
✅ **CORS Support**: Cho phép cross-origin requests  
✅ **Error Handling**: Trả về lỗi rõ ràng và không expose sensitive data

## 📞 Support & Troubleshooting:

- **Documentation**: Đọc `README.md` để biết chi tiết
- **Demo**: Sử dụng `demo.html` để test nhanh API
- **Logs**: Kiểm tra Moodle logs khi có lỗi
- **Capabilities**: Đảm bảo user có đủ quyền (course:create, backup:backupcourse, restore:restorecourse)

## 🎯 Features chính đã implement:

✅ JSON API endpoint với POST method  
✅ Token authentication (Bearer header + JSON body)  
✅ CORS support cho cross-origin requests  
✅ Complete course cloning với backup/restore API  
✅ Input validation và error handling  
✅ Postman collection với test cases  
✅ Demo interface để test trực tiếp  
✅ Complete documentation  
✅ Security best practices

---

**🎉 PLUGIN ĐÃ SẴN SÀNG SỬ DỤNG!**

Bạn có thể bắt đầu test ngay bằng cách:
1. Import Postman collection
2. Mở demo.html trong browser  
3. Hoặc cài đặt plugin vào Moodle và test

**Good luck! 🚀**