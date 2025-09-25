# Course Batches Plugin for Moodle

## Mô tả

Plugin **local_course_batches** là một công cụ quản lý đợt mở môn học cho hệ thống Moodle. Plugin này cho phép tạo và quản lý các đợt mở môn học, tự động import môn học từ hệ thống Moodle và phân nhóm theo ngày bắt đầu.

## Tính năng chính

### 1. Quản lý đợt mở môn
- **Tạo đợt mở môn mới**: Bao gồm các trường ID, tên đợt mở môn, ngày tạo
- **Chỉnh sửa và xóa đợt**: Quản lý thông tin đợt mở môn
- **Danh sách đợt**: Hiển thị tất cả đợt mở môn với thống kê

### 2. Tự động import và phân nhóm môn học
- **Import từ Moodle**: Tự động lấy danh sách môn học từ hệ thống Moodle
- **Phân nhóm theo startdate**: Các môn học có cùng ngày bắt đầu sẽ được tự động gán vào cùng đợt
- **Linh hoạt**: Có thể chọn gán theo ngày cụ thể hoặc tất cả môn học có startdate

### 3. Hiển thị và báo cáo
- **Xem chi tiết đợt**: Hiển thị danh sách môn học trong từng đợt
- **Thống kê tổng quan**: Số lượng đợt, môn học đã gán, chưa gán
- **Thông tin chi tiết môn học**: Tên, mã, danh mục, số học viên, trạng thái

## Cấu trúc cơ sở dữ liệu

### Bảng `local_course_batches`
- `id`: ID duy nhất của đợt
- `batch_name`: Tên đợt mở môn
- `timecreated`: Thời gian tạo đợt

### Bảng `local_course_batch_courses`
- `id`: ID duy nhất
- `batch_id`: ID đợt (khóa ngoại)
- `course_id`: ID môn học Moodle (khóa ngoại)
- `timecreated`: Thời gian gán môn học vào đợt

## Quyền truy cập

### Quyền `local/course_batches:view`
- Xem danh sách đợt mở môn
- Xem chi tiết môn học trong đợt
- Xem thống kê tổng quan

### Quyền `local/course_batches:manage`
- Tất cả quyền của `view`
- Tạo đợt mở môn mới
- Chỉnh sửa và xóa đợt
- Quản lý việc gán môn học vào đợt

## Cài đặt

1. **Tải plugin**: Giải nén vào thư mục `/local/course_batches/`
2. **Cài đặt**: Truy cập Site Administration → Notifications để cài đặt
3. **Phân quyền**: Thiết lập quyền truy cập cho người dùng

## Sử dụng

### Tạo đợt mở môn mới
1. Truy cập **Site Administration → Plugins → Local plugins → Course Batches**
2. Nhấp **Thêm đợt mở môn mới**
3. Nhập tên đợt và chọn các tùy chọn:
   - **Ngày bắt đầu**: Để trống để gán tất cả môn học có startdate
   - **Tự động gán**: Tích chọn để tự động import môn học
4. Nhấp **Lưu**

### Xem môn học trong đợt
1. Từ danh sách đợt, nhấp **Xem đợt học**
2. Hiển thị danh sách môn học với thông tin chi tiết
3. Có thể truy cập trực tiếp vào môn học

### Logic tự động gán môn học
- **Cùng startdate**: Các môn học có cùng ngày bắt đầu sẽ được gán vào cùng đợt
- **Không trùng lặp**: Mỗi môn học chỉ được gán vào một đợt
- **Cập nhật tự động**: Có thể chạy lại để cập nhật danh sách môn học mới

## Cấu hình nâng cao

### Tuỷ chỉnh ngôn ngữ
Chỉnh sửa file `/lang/en/local_course_batches.php` để thêm hoặc thay đổi chuỗi ngôn ngữ.

### Mở rộng chức năng
Plugin được thiết kế với cấu trúc modular, có thể dễ dàng mở rộng:
- Thêm trường dữ liệu mới
- Tích hợp với plugin khác
- Thêm báo cáo chi tiết

## Hỗ trợ

### Yêu cầu hệ thống
- **Moodle**: Phiên bản 3.10 trở lên
- **PHP**: Phiên bản 7.4 trở lên
- **MySQL/PostgreSQL**: Cơ sở dữ liệu được Moodle hỗ trợ

### Khắc phục sự cố
1. **Không hiển thị môn học**: Kiểm tra quyền truy cập và startdate của môn học
2. **Lỗi cài đặt**: Đảm bảo thư mục plugin có quyền ghi
3. **Vấn đề hiệu suất**: Với hệ thống lớn, cân nhắc chạy import theo batch

## Phiên bản và cập nhật

- **Phiên bản hiện tại**: 2025092504
- **Tương thích**: Moodle 3.10+
- **Cập nhật**: Thông qua Site Administration → Notifications

## Giấy phép

Plugin này được phát hành dưới giấy phép GNU GPL v3 hoặc mới hơn, tuân thủ theo giấy phép của Moodle.

## Liên hệ

Để được hỗ trợ hoặc đóng góp, vui lòng liên hệ qua:
- Email: support@example.com
- Repository: https://github.com/example/course_batches