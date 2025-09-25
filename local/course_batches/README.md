# Plugin Quản lý Đợt Mở Môn (Course Batches) cho Moodle

## Mô tả
Plugin local này giúp quản lý các đợt mở môn trong Moodle. Plugin sẽ tự động nhóm các khóa học có cùng ngày bắt đầu thành các đợt và cung cấp giao diện để quản lý chúng.

## Tính năng chính
1. **Tạo bảng cơ sở dữ liệu mới**: 
   - Bảng `local_course_batches` để lưu trữ thông tin đợt mở môn
   - Bảng `local_course_batch_courses` để lưu trữ mối liên hệ giữa đợt và khóa học
2. **Tự động tạo đợt**: Tự động phân tích dữ liệu khóa học và tạo đợt mở môn, tự động gán khóa học vào đợt
3. **Quản lý đợt mở môn**: Thêm, sửa, xóa các đợt mở môn với mô tả chi tiết
4. **Quản lý mối liên hệ**: Gán/bỏ gán khóa học vào/khỏi đợt mở môn
5. **Hiển thị dữ liệu**: 
   - Dashboard thống kê tổng quan
   - Danh sách đợt và khóa học trong từng đợt dưới dạng bảng
   - Danh sách khóa học chưa được gán vào đợt nào
6. **Theo dõi lịch sử**: Ghi lại thời gian thêm khóa học vào đợt

## Cấu trúc bảng cơ sở dữ liệu

### Bảng: `local_course_batches`
- `id` (int): Khóa chính
- `batch_name` (varchar 255): Tên đợt mở môn
- `start_date` (int): Ngày bắt đầu đợt (timestamp)
- `created_date` (int): Ngày tạo record (timestamp)
- `description` (text): Mô tả đợt mở môn

### Bảng: `local_course_batch_courses`
- `id` (int): Khóa chính
- `batchid` (int): ID đợt mở môn (khóa ngoại)
- `courseid` (int): ID khóa học (khóa ngoại)
- `timecreated` (int): Thời gian thêm vào đợt (timestamp)

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
- **Dashboard thống kê**: Tổng số đợt, khóa học đã gán, chưa gán, tổng khóa học
- **Danh sách đợt mở môn**: Tên đợt, ngày bắt đầu, ngày tạo, số khóa học, các thao tác
- **Chi tiết khóa học trong đợt**: Tên khóa học, tên viết tắt, trạng thái, số học viên, ngày thêm vào đợt
- **Quản lý khóa học**: 
  - Tab "Khóa học trong đợt": Danh sách khóa học đã gán với nút xóa khỏi đợt
  - Tab "Khóa học chưa gán": Danh sách khóa học chưa gán với nút thêm vào đợt

### 5. Mối liên hệ đợt - khóa học
- **Liên kết chặt chẽ**: Mỗi khóa học có thể thuộc về một đợt mở môn
- **Gán tự động**: Dựa trên ngày bắt đầu (`startdate`) của khóa học
- **Gán thủ công**: Admin có thể gán/bỏ gán khóa học vào/khỏi đợt bất kỳ
- **Theo dõi lịch sử**: Ghi lại thời gian thêm khóa học vào đợt

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
├── version.php                    # Thông tin phiên bản plugin
├── index.php                      # Trang chính hiển thị danh sách đợt + dashboard
├── manage.php                     # Trang thêm/sửa đợt mở môn
├── manage_courses.php             # Trang quản lý khóa học trong đợt
├── db/
│   ├── install.xml               # Cấu trúc bảng cơ sở dữ liệu
│   ├── upgrade.php               # Script nâng cấp database
│   └── access.php                # Định nghĩa quyền truy cập
├── lang/en/
│   └── local_course_batches.php  # Chuỗi ngôn ngữ tiếng Việt
├── classes/
│   └── batch_manager.php         # Class quản lý logic nghiệp vụ
└── README.md                     # File hướng dẫn này
```

## Ghi chú kỹ thuật

- Plugin tuân thủ coding standards của Moodle
- Sử dụng Moodle API để tương tác với cơ sở dữ liệu
- Responsive design tương thích với theme Bootstrap của Moodle
- Hỗ trợ đa ngôn ngữ (hiện tại có tiếng Việt)

## Phiên bản
- **v1.0** (2025-09-25): Phiên bản đầu tiên với tính năng cơ bản
- **v1.1** (2025-09-25): 
  - Thêm bảng liên kết `local_course_batch_courses`
  - Cải thiện mối liên hệ giữa đợt và khóa học
  - Thêm dashboard thống kê
  - Thêm trang quản lý khóa học trong đợt
  - Thêm trường mô tả cho đợt mở môn
  - Theo dõi lịch sử thêm khóa học vào đợt