# Plugin Quản lý Đợt Mở Môn (Course Batches) cho Moodle

## Mô tả
Plugin local này giúp quản lý các đợt mở môn trong Moodle. Plugin sẽ tự động nhóm các khóa học có cùng ngày bắt đầu thành các đợt và cung cấp giao diện để quản lý chúng.

## Tính năng chính
1. **Tạo bảng cơ sở dữ liệu mới**: Bảng `local_course_batches` để lưu trữ thông tin đợt mở môn
2. **Tự động tạo đợt**: Tự động phân tích dữ liệu khóa học và tạo đợt mở môn
3. **Quản lý đợt mở môn**: Thêm, sửa, xóa các đợt mở môn
4. **Hiển thị dữ liệu**: Hiển thị danh sách đợt và khóa học trong từng đợt dưới dạng bảng

## Cấu trúc bảng cơ sở dữ liệu

### Bảng: `local_course_batches`
- `id` (int): Khóa chính
- `batch_name` (varchar 255): Tên đợt mở môn
- `start_date` (int): Ngày bắt đầu đợt (timestamp)
- `created_date` (int): Ngày tạo record (timestamp)

## Cài đặt

1. **Sao chép plugin**: 
   - Sao chép thư mục `course_batches` vào `[moodle]/local/`

2. **Cài đặt qua Moodle Admin**:
   - Đăng nhập với quyền admin
   - Vào Site Administration > Notifications
   - Moodle sẽ tự động phát hiện plugin mới và yêu cầu cài đặt
   - Nhấn "Upgrade Moodle database now"

3. **Phân quyền**:
   - Vào Site Administration > Users > Permissions > Define roles
   - Gán quyền `local/course_batches:view` và `local/course_batches:manage` cho các role phù hợp

## Sử dụng

### 1. Truy cập plugin
- Vào Site Administration > Plugins > Local plugins > Course Batches
- Hoặc truy cập trực tiếp: `[moodle_url]/local/course_batches/`

### 2. Tự động tạo đợt mở môn
- Nhấn nút "Tự động tạo đợt từ khóa học"
- Plugin sẽ phân tích tất cả khóa học và tạo đợt mở môn dựa trên ngày bắt đầu

### 3. Quản lý thủ công
- **Thêm đợt mới**: Nhấn "Thêm đợt mở môn"
- **Sửa đợt**: Nhấn nút "Sửa" trong danh sách
- **Xóa đợt**: Nhấn nút "Xóa" (có xác nhận)
- **Xem khóa học**: Nhấn "Xem khóa học" để xem chi tiết các khóa học trong đợt

### 4. Hiển thị thông tin
- Danh sách đợt mở môn hiển thị: tên đợt, ngày bắt đầu, ngày tạo, số khóa học
- Chi tiết khóa học trong đợt: tên khóa học, tên viết tắt, trạng thái, số học viên

## Quyền truy cập

### `local/course_batches:view`
- Xem danh sách đợt mở môn
- Xem chi tiết khóa học trong đợt
- Mặc định gán cho: Manager, Course Creator, Editing Teacher

### `local/course_batches:manage`
- Tất cả quyền của `view`
- Thêm, sửa, xóa đợt mở môn
- Tự động tạo đợt từ dữ liệu khóa học
- Mặc định gán cho: Manager

## Cấu trúc file

```
local/course_batches/
├── version.php                 # Thông tin phiên bản plugin
├── index.php                   # Trang chính hiển thị danh sách đợt
├── manage.php                  # Trang thêm/sửa đợt mở môn
├── db/
│   ├── install.xml            # Cấu trúc bảng cơ sở dữ liệu
│   └── access.php             # Định nghĩa quyền truy cập
├── lang/en/
│   └── local_course_batches.php # Chuỗi ngôn ngữ
├── classes/
│   └── batch_manager.php      # Class quản lý logic nghiệp vụ
└── README.md                  # File hướng dẫn này
```

## Ghi chú kỹ thuật

- Plugin tuân thủ coding standards của Moodle
- Sử dụng Moodle API để tương tác với cơ sở dữ liệu
- Responsive design tương thích với theme Bootstrap của Moodle
- Hỗ trợ đa ngôn ngữ (hiện tại có tiếng Việt)

## Phiên bản
- **v1.0** (2025-09-25): Phiên bản đầu tiên với đầy đủ tính năng cơ bản