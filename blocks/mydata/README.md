# Block### 1. **Phân quyền truy cập**
- **Admin và Manager** có quyền truy cập báo cáo
- Site Administrator có quyền truy cập đầy đủ
- Manager và Course Creator có quyền truy cập báo cáo
- Các vai trò khác sẽ thấy thông báo "Chỉ Admin và Manager mới có quyền truy cập báo cáo này"ata - Báo cáo người dùng theo khóa học

## Mô tả
Block MyData cung cấp tính năng báo cáo người dùng theo khóa học với phân quyền chỉ cho Manager truy cập.

## Tính năng chính

### 1. Phân quyền truy cập
- **Chỉ Manager** có quyền truy cập báo cáo
- Site Administrator cũng có quyền truy cập
- Các vai trò khác sẽ thấy thông báo "Chỉ Manager mới có quyền truy cập báo cáo này"

### 2. Báo cáo theo khóa học (report.php)
- **Form chọn khóa học**: Sử dụng Moodle Form API với element autocomplete
- **Chọn nhiều khóa học**: Có thể chọn nhiều khóa học cùng lúc
- **Tìm kiếm nhanh**: Nhập tên khóa học để lọc danh sách
- **Hiển thị thông tin người dùng**:
  - Tên đăng nhập (có link đến profile)
  - Họ và tên (có link đến profile)
  - Email
  - Vai trò trong khóa học

### 3. Xem tất cả (view.php)
- Danh sách tất cả khóa học trong hệ thống
- Danh sách tất cả người dùng trong hệ thống
- Thông tin chi tiết với các link liên kết

## Cấu trúc file

```
blocks/mydata/
├── block_mydata.php          # Block chính với phân quyền
├── view.php                  # Xem tất cả khóa học và người dùng  
├── report.php                # Báo cáo theo khóa học (Form API)
├── version.php               # Thông tin version plugin
├── db/
│   └── access.php           # Định nghĩa quyền truy cập
└── lang/en/
    └── block_mydata.php     # File ngôn ngữ
```

## Quyền truy cập (Capabilities)

### block/mydata:viewreports
- **Mô tả**: Quyền xem báo cáo người dùng theo khóa học (Admin và Manager)
- **Context**: System level
- **Archetypes**: Manager và Course Creator được cấp quyền
- **Captype**: read

## Cách sử dụng

1. **Đăng nhập với tài khoản Admin hoặc Manager**
2. **Thêm block MyData vào trang**
3. **Chọn một trong các tùy chọn**:
   - **"Xem tất cả"**: Xem danh sách toàn bộ khóa học và người dùng
   - **"Báo cáo theo khóa học"**: Sử dụng form để chọn khóa học cụ thể

### Sử dụng form báo cáo:
1. Nhập tên khóa học vào trường tìm kiếm
2. Chọn một hoặc nhiều khóa học từ danh sách gợi ý
3. Click "Xem báo cáo" 
4. Xem kết quả hiển thị thông tin người dùng trong các khóa học đã chọn

## Lưu ý kỹ thuật

- Sử dụng **Moodle Form API** với element `autocomplete`
- Truy vấn database tối ưu với JOIN để lấy thông tin người dùng và vai trò
- Hiển thị kết quả dưới dạng bảng HTML với styling Bootstrap
- Phân quyền được kiểm tra ở cả block level và page level
- Hỗ trợ multiple course selection với tìm kiếm autocomplete

## Version History

- **v1.1 (2025-09-25)**: Thêm báo cáo theo khóa học với Form API
- **v1.0 (2025-09-24)**: Version đầu tiên với xem tất cả