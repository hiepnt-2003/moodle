# Hướng Dẫn Cài Đặt Plugin Hello World

## Kiểm tra sau khi cài đặt:

### ✅ Plugin được cài đặt thành công nếu:
- Xuất hiện trong danh sách "Add a block"
- Có thể thêm vào trang course
- Form cấu hình hoạt động bình thường
- Hiển thị message với placeholder đúng

### 🔧 Cấu hình mẫu:
- **Title**: "Chào mừng"
- **Message**: "Xin chào {username}! Chào mừng bạn đến với {coursename}!"
- **Show date**: ✓ (checked)

### 🐛 Troubleshooting:
1. **Không thấy plugin**: Kiểm tra đường dẫn blocks/helloworld/
2. **Lỗi cài đặt**: Xem error logs trong Site Administration > Reports > Logs
3. **Không hiển thị**: Kiểm tra permissions trong Site Administration > Users > Permissions

### 📱 Test cases:
- Thêm block vào course page
- Thêm block vào dashboard
- Test placeholder {username} và {coursename}
- Test hiển thị ngày tháng
- Test với nhiều instance khác nhau