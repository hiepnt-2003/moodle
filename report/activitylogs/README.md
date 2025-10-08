# Activity Logs Report Plugin

## Mô tả
Plugin này cho phép xem activity logs của Moodle với các bộ lọc tùy chỉnh.

## Tính năng
- **Chọn User**: Lọc logs theo user cụ thể (không bao gồm user đã bị xóa)
- **Chọn Khóa học**: Lọc logs theo khóa học cụ thể hoặc tất cả khóa học
- **Chọn Ngày**: Chọn khoảng thời gian từ ngày bắt đầu đến ngày kết thúc
- **Hiển thị Logs**: Hiển thị bảng logs với các thông tin chi tiết

## Cài đặt

1. Sao chép thư mục `activitylogs` vào thư mục `report/` của Moodle
2. Truy cập Site Administration > Notifications để cài đặt plugin
3. Sau khi cài đặt, truy cập Site Administration > Reports > Activity Logs

## Yêu cầu
- Moodle 3.8 trở lên
- Logstore standard phải được bật (Site Administration > Plugins > Logging > Standard log)

## Quyền
- `report/activitylogs:view`: Cho phép xem report activity logs
  - Mặc định được cấp cho: Manager, Course Creator, Editing Teacher, Teacher

## Sử dụng

1. Truy cập **Site Administration** > **Reports** > **Activity Logs**
2. Chọn các tiêu chí lọc:
   - **Select user**: Chọn user cụ thể hoặc "All users"
   - **Select course**: Chọn khóa học cụ thể hoặc "All courses"
   - **Date from**: Chọn ngày bắt đầu
   - **Date to**: Chọn ngày kết thúc
3. Nhấn **View logs** để hiển thị kết quả
4. Bảng logs sẽ hiển thị ngay bên dưới form với các cột:
   - Time (Thời gian)
   - User (Người dùng)
   - Event (Sự kiện)
   - Component (Thành phần)
   - Context (Ngữ cảnh/Khóa học)
   - IP Address (Địa chỉ IP)

## Giới hạn
- Hiển thị tối đa 1000 bản ghi logs để đảm bảo hiệu suất

## Tác giả
Created: 2025

## License
GNU GPL v3 or later
