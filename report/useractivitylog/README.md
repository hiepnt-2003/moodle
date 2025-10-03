# Logs Report Plugin

## Mô tả
Plugin báo cáo logs cho Moodle, được thiết kế để giống với trang Logs chuẩn của Moodle (Site Administration > Reports > Logs). Plugin này cho phép quản trị viên xem chi tiết các hoạt động trong hệ thống theo cách tương tự như logs chuẩn.

## Tính năng
- **Form lọc dữ liệu tương thích với Moodle standard logs:**
  - Chọn khóa học (hoặc tất cả khóa học)
  - Chọn người dùng/participants (hoặc tất cả)
  - Chọn ngày cụ thể (single date selector)
  - Chọn hoạt động/activities (modules)
  - Chọn hành động/actions (Create/Read/Update/Delete)
  - Chọn education level
  - Chọn nguồn gốc (Web/Web service/CLI/Restore)
  - Tùy chỉnh số logs hiển thị trên một trang
  
- **Hiển thị báo cáo dạng bảng giống Moodle logs với các cột:**
  - Thời gian (Time)
  - Tên đầy đủ (Full name)
  - Người dùng bị ảnh hưởng (Affected user)
  - Ngữ cảnh sự kiện (Event context)
  - Thành phần (Component)
  - Tên sự kiện (Event name)
  - Mô tả (Description)
  - Nguồn gốc (Origin)
  - Địa chỉ IP (IP address)

## Cài đặt
1. Copy thư mục plugin vào `/report/useractivitylog/`
2. Truy cập Site Administration > Notifications để cài đặt plugin
3. Cấu hình quyền truy cập cho người dùng

## Quyền truy cập
- `report/useractivitylog:view`: Xem báo cáo nhật ký hoạt động

## Cách sử dụng
1. Truy cập vào Site Administration > Reports > Logs
2. Điền thông tin vào form lọc (giống như standard logs):
   - Chọn khóa học (hoặc tất cả khóa học)
   - Chọn người dùng (hoặc tất cả participants)
   - Chọn ngày cụ thể
   - Chọn hoạt động/module (tùy chọn)
   - Chọn hành động (Create/View/Update/Delete - tùy chọn)
   - Chọn education level (tùy chọn)
   - Chọn nguồn gốc (tùy chọn)
   - Nhập số logs hiển thị trên trang (mặc định 100)
3. Nhấn "Get these logs" để xem kết quả

## Yêu cầu hệ thống
- Moodle 4.0 trở lên
- PHP 7.4 trở lên
- Logstore Standard được kích hoạt

## Tác giả
Your Name (2025)

## Giấy phép
GPL v3 hoặc mới hơn