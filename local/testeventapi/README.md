# Test Event API Plugin

Plugin để test chức năng Event API của Moodle với logic tự động thêm môn học vào đợt khi có sự kiện tạo đợt mới.

## Mô tả

Plugin **Test Event API** được tạo để demo cách sử dụng Event API trong Moodle. Khi tạo một đợt học mới, hệ thống sẽ tự động quét tất cả các môn học trong Moodle và thêm những môn học có cùng ngày bắt đầu vào đợt đó thông qua Event API.

## Cấu trúc Database

Plugin sử dụng 2 bảng chính tương tự như plugin createtable:

### local_testeventapi_batches
- `id` - ID đợt học
- `name` - Tên đợt học  
- `start_date` - Ngày bắt đầu
- `timecreated` - Ngày tạo
- `timemodified` - Ngày cập nhật

### local_testeventapi_courses
- `id` - ID record
- `batchid` - ID đợt học
- `courseid` - ID môn học
- `timecreated` - Ngày thêm
- `added_by_event` - 1 nếu được thêm qua Event API, 0 nếu thêm thủ công

## Logic hoạt động

1. **Tạo đợt học mới**: Khi user tạo một đợt học mới, hệ thống trigger event `batch_created`

2. **Event Observer**: Observer lắng nghe event `batch_created` và tự động:
   - Quét tất cả môn học trong Moodle
   - Tìm các môn học có ngày bắt đầu trùng với ngày bắt đầu của đợt
   - Tự động thêm các môn học đó vào đợt với flag `added_by_event = 1`

3. **Event Processing**: Mỗi khi thêm môn học, hệ thống trigger event `course_added_to_batch` để log

4. **Cập nhật đợt**: Khi cập nhật đợt (đặc biệt là thay đổi ngày bắt đầu), event `batch_updated` được trigger và tự động làm mới danh sách môn học

## Các Event được implement

### Events
- `\local_testeventapi\event\batch_created` - Khi tạo đợt học mới
- `\local_testeventapi\event\batch_updated` - Khi cập nhật đợt học  
- `\local_testeventapi\event\batch_deleted` - Khi xóa đợt học
- `\local_testeventapi\event\course_added_to_batch` - Khi môn học được thêm vào đợt

### Observers
- `\local_testeventapi\observer::batch_created` - Xử lý event tạo đợt mới
- `\local_testeventapi\observer::batch_updated` - Xử lý event cập nhật đợt
- `\local_testeventapi\observer::course_added_to_batch` - Log khi thêm môn học

## Chức năng chính

### Giao diện quản lý
- **index.php**: Danh sách các đợt học với thống kê
- **manage.php**: Tạo/sửa đợt học
- **view.php**: Xem chi tiết đợt học và danh sách môn học
- **delete.php**: Xóa đợt học với xác nhận
- **test_api.php**: Test chức năng Event API

### Tính năng
- Tự động thêm môn học qua Event API
- Phân biệt môn học được thêm tự động vs thủ công
- Thống kê số lượng môn học theo từng loại
- Test Event API với tạo đợt mẫu
- Ghi log quá trình xử lý event

## Quyền hạn

- `local/testeventapi:view` - Xem danh sách và chi tiết đợt học
- `local/testeventapi:manage` - Quản lý đợt học (tạo, sửa, xóa)

## Cài đặt

1. Copy plugin vào thư mục `/local/testeventapi/`
2. Truy cập Site Administration > Notifications để cài đặt
3. Plugin sẽ tạo các bảng database tự động
4. Cấp quyền cho user cần thiết

## Sử dụng

1. Tạo một vài môn học với ngày bắt đầu khác nhau
2. Truy cập `/local/testeventapi/` để quản lý đợt học
3. Tạo đợt học mới với ngày bắt đầu trùng với một số môn học
4. Quan sát cách Event API tự động thêm môn học
5. Kiểm tra trong chi tiết đợt để thấy phân biệt môn được thêm tự động vs thủ công
6. Sử dụng "Test Event API" để tạo đợt test

## Demo Event API

Plugin này là một ví dụ hoàn chỉnh về cách:
- Tạo custom events trong Moodle
- Đăng ký event observers
- Xử lý events để thực hiện logic nghiệp vụ tự động
- Sử dụng Event API để tách biệt logic và tăng tính mở rộng

Event API cho phép các plugin khác cũng có thể lắng nghe các sự kiện của plugin này và thực hiện các hành động bổ sung mà không cần sửa đổi code gốc.