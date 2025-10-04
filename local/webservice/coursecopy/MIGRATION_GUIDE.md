# Course Copy Plugin - Migration to RESTful Protocol ✅

## 🔄 Thay đổi quan trọng

Plugin Course Copy đã được **chuyển đổi hoàn toàn** để sử dụng **RESTful webservice protocol chuẩn** của Moodle thay vì endpoint tùy chỉnh.

## 📋 Yêu cầu cài đặt

### 1. Plugin RESTful Webservice (BẮT BUỘC)
- **Tải về**: Plugin `webservice_restful` từ Moodle plugins directory
- **Cài đặt**: Upload vào `/webservice/restful/`
- **Kích hoạt**: Site Administration → Plugins → Webservices → Manage protocols → Enable RESTful

### 2. Plugin Course Copy
- **Đã có sẵn** trong thư mục `local/webservice/coursecopy/`
- Cài đặt qua Site Administration → Notifications

## 🚀 Endpoint mới

### Trước (DEPRECATED):
```
POST /local/webservice/coursecopy/restful.php
```

### Bây giờ (RECOMMENDED):
```
POST /webservice/restful/server.php/local_coursecopy_copy_course
```

## 📝 Cách sử dụng mới

### Headers
```
Content-Type: application/json
Accept: application/json  
Authorization: your_token_here
```
**Lưu ý**: Không cần "Bearer" prefix trong Authorization header.

### Request Body (Không thay đổi)
```json
{
  "shortname_clone": "SOURCE_COURSE",
  "fullname": "New Course Name",
  "shortname": "NEW_COURSE_2025",
  "startdate": 1704067200,
  "enddate": 1719792000
}
```

### Response (Không thay đổi)
```json
{
  "status": "success|error",
  "id": 123,
  "message": "Course copied successfully"
}
```

## 🔧 Cài đặt từng bước

### Bước 1: Cài đặt RESTful Plugin
1. **Tải plugin**: `webservice_restful_moodle40_2022052500.zip` (đã có trong thư mục)
2. **Giải nén** vào `/webservice/restful/`
3. **Cài đặt** qua Site Administration → Notifications

### Bước 2: Enable RESTful Protocol
1. Vào **Site Administration → Plugins → Webservices → Manage protocols**
2. Click **Enable** cho RESTful protocol

### Bước 3: Cấu hình Service
1. Vào **Site Administration → Server → Web services → External services**
2. Tìm service **"Course Copy RESTful Service"** hoặc tạo mới
3. Add function **local_coursecopy_copy_course**
4. Set **Enabled = Yes**

### Bước 4: Tạo Token
1. Vào **Site Administration → Server → Web services → Manage tokens**
2. **Create token** cho user và service đã tạo
3. **Copy token** để sử dụng

## 🧪 Test với Postman

### Import Collection
- File: `coursecopy_api.postman_collection.json`
- Variables:
  - `base_url`: http://your-moodle-site
  - `token`: your_actual_token

### Test Cases Included
1. ✅ Copy Course - Success Case
2. ✅ Copy Course - Token in Body  
3. ❌ Copy Course - Source Not Found
4. ❌ Copy Course - Invalid Dates

## 📊 So sánh

| Feature | Custom Endpoint | RESTful Protocol |
|---------|----------------|------------------|
| **Endpoint** | `/local/.../restful.php` | `/webservice/restful/server.php/function_name` |
| **Authorization** | `Bearer token` | `token` (no Bearer) |
| **Protocol** | Tùy chỉnh | Chuẩn Moodle |
| **Monitoring** | Tự triển khai | Built-in Moodle |
| **Security** | Tự quản lý | Moodle ecosystem |
| **Maintenance** | Phức tạp | Đơn giản |

## ✅ Lợi ích RESTful Protocol

1. **Chuẩn hóa**: Sử dụng protocol chuẩn của Moodle
2. **Tích hợp**: Hoàn toàn tương thích với Moodle webservice ecosystem  
3. **Monitoring**: Sử dụng built-in logging và monitoring
4. **Security**: Tận dụng authentication và authorization của Moodle
5. **Maintenance**: Ít code tùy chỉnh, dễ bảo trì

## 🗂️ Files đã thay đổi

### ✅ Cập nhật
- `externallib.php` - External webservice functions
- `db/services.php` - Service và function definitions
- `README.md` - Documentation mới
- `SETUP_GUIDE.md` - Hướng dẫn cài đặt
- `coursecopy_api.postman_collection.json` - Postman collection

### ❌ Deprecated
- `restful.php` - Endpoint cũ (trả về lỗi 410 Gone)

## 🎯 Migration Checklist

- [x] Cài đặt plugin `webservice_restful`
- [x] Enable RESTful protocol trong Moodle
- [x] Tạo external service với function `local_coursecopy_copy_course`
- [x] Tạo token cho service
- [x] Test với Postman collection mới
- [x] Cập nhật client applications để sử dụng endpoint mới

## 📞 Troubleshooting

### "Protocol not enabled"
- Đảm bảo RESTful protocol đã được enable
- Kiểm tra plugin webservice_restful đã cài đặt

### "Function not found"  
- Đảm bảo function đã được add vào external service
- Kiểm tra service đã enabled

### "Invalid token"
- Token phải được tạo cho đúng service
- Không sử dụng "Bearer" prefix

---

**Status**: ✅ Migration completed  
**Recommended**: Sử dụng RESTful protocol  
**Support**: Moodle 3.9+ với webservice_restful plugin