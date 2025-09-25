# Plugin Quản lý Đợt Mở Môn (Course Batches) cho Moodle

## Mô tả
# Plugin Quản lý Đợt Mở Môn (Course Batches) cho Moodle

Plugin local Moodle để quản lý đợt mở môn theo khoảng thời gian. Plugin tự động phân tích và nhóm các khóa học có thời gian bắt đầu và kết thúc nằm trong cùng khoảng thời gian thành các đợt mở môn.

## Logic hoạt động
**Nguyên tắc phân loại**: Khóa học được tự động thêm vào đợt mở môn nếu thời gian bắt đầu (`startdate`) và thời gian kết thúc (`enddate`) của khóa học **nằm trong khoảng thời gian** của đợt mở môn.

**Ví dụ**: 
- Đợt mở môn: 01/01/2025 - 31/03/2025
- Khóa học A: 15/01/2025 - 15/02/2025 → **Được thêm vào đợt**
- Khóa học B: 15/12/2024 - 15/01/2025 → **Không được thêm** (bắt đầu trước đợt)
- Khóa học C: 15/03/2025 - 15/04/2025 → **Không được thêm** (kết thúc sau đợt)

## Tính năng chính
1. **Tạo bảng cơ sở dữ liệu mới**: 
   - Bảng `local_course_batches` để lưu trữ thông tin đợt mở môn (có ngày bắt đầu và kết thúc)
   - Bảng `local_course_batch_courses` để lưu trữ mối liên hệ giữa đợt và khóa học
2. **Tự động tạo đợt theo khoảng thời gian**: 
   - Phân tích tất cả các kết hợp (startdate, enddate) duy nhất từ khóa học
   - Tạo đợt mở môn cho mỗi khoảng thời gian
   - Tự động gán khóa học phù hợp vào đợt
3. **Quản lý đợt mở môn**: 
   - Thêm, sửa, xóa các đợt mở môn với khoảng thời gian cụ thể
   - Validation: ngày kết thúc phải sau ngày bắt đầu
   - Mô tả chi tiết cho từng đợt
4. **Tự động gán khóa học thông minh**: 
   - Gán dựa trên khoảng thời gian chứ không chỉ ngày bắt đầu
   - Khi tạo/sửa đợt → tự động cập nhật danh sách khóa học
   - Có thể gán/bỏ gán thủ công
5. **Hiển thị dữ liệu trực quan**: 
   - Dashboard thống kê tổng quan
   - Hiển thị khoảng thời gian của đợt và từng khóa học
   - Danh sách khóa học chưa được gán vào đợt nào
6. **Theo dõi và quản lý**: 
   - Ghi lại thời gian thêm khóa học vào đợt
   - Interface quản lý với tabs để dễ sử dụng

## Cấu trúc bảng cơ sở dữ liệu

### Bảng: `local_course_batches`
- `id` (int): Khóa chính
- `batch_name` (varchar 255): Tên đợt mở môn
- `start_date` (int): Ngày bắt đầu học của đợt (timestamp)
- `end_date` (int): **Ngày kết thúc học của đợt (timestamp)**
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
- **Thêm đợt mới**: Nhấn "Thêm đợt mở môn", nhập tên đợt, ngày bắt đầu và kết thúc học
- **Sửa đợt**: Nhấn nút "Sửa" trong danh sách (sẽ tự động cập nhật khóa học khi thay đổi khoảng thời gian)
- **Xóa đợt**: Nhấn nút "Xóa" (có xác nhận)
- **Xem khóa học**: Nhấn "Xem khóa học" để xem chi tiết các khóa học trong đợt
- **Quản lý khóa học**: Trong trang chi tiết đợt có thể gán/bỏ gán khóa học thủ công

### 4. Hiển thị thông tin
- **Dashboard thống kê**: Tổng số đợt, khóa học đã gán, chưa gán, tổng khóa học
- **Danh sách đợt mở môn**: Tên đợt, **khoảng thời gian học**, ngày tạo, số khóa học, các thao tác
- **Chi tiết khóa học trong đợt**: Tên khóa học, tên viết tắt, **thời gian khóa học**, trạng thái, số học viên, ngày thêm vào đợt
- **Quản lý khóa học**: 
  - Tab "Khóa học trong đợt": Danh sách khóa học đã gán với nút xóa khỏi đợt
  - Tab "Khóa học chưa gán": Danh sách khóa học chưa gán với nút thêm vào đợt

### 5. Mối liên hệ đợt - khóa học (LOGIC MỚI)
- **Liên kết theo khoảng thời gian**: Khóa học được gán vào đợt nếu thời gian bắt đầu và kết thúc nằm trong khoảng thời gian của đợt
- **Gán tự động thông minh**: Dựa trên cả `startdate` và `enddate` của khóa học so với khoảng thời gian đợt
- **Điều kiện gán**: `course.startdate >= batch.start_date AND course.enddate <= batch.end_date`
- **Gán thủ công**: Admin có thể gán/bỏ gán khóa học vào/khỏi đợt bất kỳ
- **Cập nhật tự động**: Khi sửa khoảng thời gian đợt, danh sách khóa học được cập nhật tự động
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
- **v1.0** (2025-09-25): Phiên bản đầu tiên - gán theo startdate
- **v1.1** (2025-09-25): 
  - Thêm bảng liên kết `local_course_batch_courses`
  - Cải thiện mối liên hệ giữa đợt và khóa học
  - Thêm dashboard thống kê
  - Thêm trang quản lý khóa học trong đợt
  - Thêm trường mô tả cho đợt mở môn
  - Theo dõi lịch sử thêm khóa học vào đợt
- **v1.2** (2025-09-25): **LOGIC MỚI - Gán theo khoảng thời gian**
  - Thêm trường `end_date` vào bảng `local_course_batches`
  - **Thay đổi logic gán**: từ chỉ dựa trên startdate → dựa trên khoảng thời gian (startdate + enddate)
  - Cập nhật form thêm/sửa đợt với trường ngày kết thúc
  - Cập nhật giao diện hiển thị khoảng thời gian thay vì chỉ ngày bắt đầu
  - Validation: ngày kết thúc phải sau ngày bắt đầu
  - Tự động tạo đợt dựa trên các kết hợp (startdate, enddate) duy nhất
  - Cập nhật tự động danh sách khóa học khi thay đổi khoảng thời gian đợt