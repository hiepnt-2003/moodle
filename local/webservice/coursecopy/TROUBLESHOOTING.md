# Troubleshooting: Invalid Token Error

## 🚨 Vấn đề hiện tại
Bạn đang gặp lỗi: `"Invalid token - token not found"` khi test API.

## 🔍 Nguyên nhân có thể
1. **Token format sai** trong Authorization header
2. **Service configuration** chưa đúng
3. **Token expired** hoặc không active
4. **RESTful protocol** chưa được enable đúng cách

## ✅ Giải pháp từng bước

### Bước 1: Kiểm tra RESTful Protocol
1. Vào **Site Administration → Plugins → Web services → Manage protocols**
2. Đảm bảo **RESTful protocol** được **Enable** (có biểu tượng mắt mở)

### Bước 2: Kiểm tra Service Configuration
1. Vào **Site Administration → Server → Web services → External services**
2. Tìm service **"Course Copy RESTful Service"**
3. Click **Functions** và đảm bảo có function **`local_coursecopy_copy_course`**
4. Đảm bảo service được **Enabled**

### Bước 3: Tạo lại Token (Quan trọng!)
1. Vào **Site Administration → Server → Web services → Manage tokens**
2. **Delete** token cũ nếu có
3. Click **Create token**
4. **User**: Chọn Admin User
5. **Service**: Chọn **"Course Copy RESTful Service"** (QUAN TRỌNG!)
6. **Valid until**: Để trống hoặc chọn ngày xa
7. **IP restriction**: Để trống
8. **Save** và copy token mới

### Bước 4: Test với cURL trước
```bash
# Thay YOUR_TOKEN và YOUR_MOODLE_URL
curl -X POST "YOUR_MOODLE_URL/webservice/restful/server.php/local_coursecopy_copy_course" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: YOUR_TOKEN" \
  -d '{
    "shortname_clone": "MATH101",
    "fullname": "Test Course Copy",
    "shortname": "TEST2025",
    "startdate": 1704067200,
    "enddate": 1719792000
  }'
```

### Bước 5: Cập nhật Postman
1. **Trong Postman Variables:**
   - `base_url`: http://your-moodle-site (không có slash cuối)
   - `token`: paste token mới (không có "Bearer")

2. **Trong Authorization header:**
   ```
   Key: Authorization
   Value: {{token}}
   ```
   **KHÔNG DÙNG "Bearer {{token}}"**

### Bước 6: Kiểm tra URL đúng
Đảm bảo URL trong Postman là:
```
{{base_url}}/webservice/restful/server.php/local_coursecopy_copy_course
```

## 🎯 Checklist nhanh
- [ ] RESTful protocol enabled
- [ ] Service "Course Copy RESTful Service" tồn tại và enabled  
- [ ] Function `local_coursecopy_copy_course` được add vào service
- [ ] Token được tạo cho đúng service này
- [ ] Authorization header: `{{token}}` (không có Bearer)
- [ ] URL đúng format RESTful

## 🚨 Lưu ý quan trọng
- **KHÔNG sử dụng "Bearer"** trong Authorization header
- **Token PHẢI được tạo cho service cụ thể**, không phải "All services"
- **RESTful protocol** phải được enable trong Moodle

Thử lại sau khi thực hiện các bước trên! 🎯