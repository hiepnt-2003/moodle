# User Activity Log Report Plugin

## Mô tả
Plugin báo cáo nhật ký hoạt động của người dùng cho Moodle. Plugin này cho phép quản trị viên và giáo viên xem chi tiết các hoạt động của người dùng trong hệ thống.

## Tính năng
- **Form lọc dữ liệu:**
  - Chọn người dùng (chỉ hiển thị những người dùng chưa bị xóa)
  - Chọn ngày bắt đầu và ngày kết thúc
  - Chọn khóa học (hoặc tất cả khóa học)
  
- **Hiển thị báo cáo dạng bảng với các cột:**
  - Thời gian
  - Tên đầy đủ người dùng
  - Người dùng bị ảnh hưởng
  - Ngữ cảnh sự kiện
  - Thành phần
  - Tên sự kiện
  - Mô tả
  - Nguồn gốc
  - Địa chỉ IP

## Cài đặt
1. Copy thư mục plugin vào `/report/useractivitylog/`
2. Truy cập Site Administration > Notifications để cài đặt plugin
3. Cấu hình quyền truy cập cho người dùng

## Quyền truy cập
- `report/useractivitylog:view`: Xem báo cáo nhật ký hoạt động

## Cách sử dụng
1. Truy cập vào Site Administration > Reports > User Activity Log
2. Điền thông tin vào form lọc:
   - Chọn người dùng muốn xem
   - Chọn khoảng thời gian
   - Chọn khóa học (tùy chọn)
3. Nhấn "Filter" để xem kết quả

## Yêu cầu hệ thống
- Moodle 4.0 trở lên
- PHP 7.4 trở lên
- Logstore Standard được kích hoạt

## Tác giả
Your Name (2025)

## Giấy phép
GPL v3 hoặc mới hơn