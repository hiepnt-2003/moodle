# 🚀 Test Event API Plugin

**Plugin Moodle để demo Event API với tự động quản lý đợt học và môn học**

Plugin **Test Event API** là một ví dụ hoàn chỉnh về cách sử dụng Event API của Moodle để tạo ra hệ thống tự động thông minh. Khi có sự kiện xảy ra (tạo/sửa/xóa đợt học), plugin sẽ tự động thực hiện các hành động tương ứng mà không cần can thiệp thủ công.

## 🎯 Mục tiêu chính

- **Demo Event API**: Hướng dẫn cách implement Event System trong Moodle
- **Tự động hóa**: Quản lý đợt học và môn học hoàn toàn tự động
- **Event-Driven Architecture**: Áp dụng mô hình kiến trúc hướng sự kiện
- **Email Notification**: Gửi thông báo tự động khi có thay đổi quan trọng

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

## 🔄 Luồng hoạt động chi tiết

### 📝 **1. Tạo đợt học mới (Create Batch)**

```
User Action → Form Submit → batch_manager::create_batch() → Event Trigger → Observer → Auto Actions
     ↓              ↓                    ↓                      ↓              ↓           ↓
Điền form    → POST data →     Insert DB record      → batch_created    → Observer  → Add courses
```

**Chi tiết từng bước:**

1. **User Interface**: User truy cập `manage.php` và điền form tạo đợt mới
2. **Form Processing**: `batch_form` validate dữ liệu (tên đợt, ngày bắt đầu)
3. **Database Insert**: `batch_manager::create_batch()` insert record vào `local_testeventapi_batches`
4. **Event Trigger**: Tự động trigger event `\local_testeventapi\event\batch_created`
5. **Observer Response**: `observer::batch_created()` lắng nghe và xử lý
6. **Auto Course Addition**: Tự động tìm và thêm môn học có cùng ngày bắt đầu

**🔍 Auto Course Addition Logic:**
```php
// Tìm tất cả courses có startdate = batch startdate
$courses = $DB->get_records('course', ['startdate' => $batch_startdate]);

foreach ($courses as $course) {
    // Insert vào local_testeventapi_courses với added_by_event = 1
    $DB->insert_record('local_testeventapi_courses', [
        'batchid' => $batch_id,
        'courseid' => $course->id,
        'added_by_event' => 1,
        'timecreated' => time()
    ]);
    
    // Trigger event course_added_to_batch cho mỗi course
}
```

---

### ✏️ **2. Cập nhật đợt học (Update Batch)**

```
User Edit → Form Submit → batch_manager::update_batch() → Event Trigger → Observer → Clean & Re-add
    ↓           ↓                   ↓                         ↓              ↓           ↓
Sửa form → POST data →     Update DB record      → batch_updated    → Observer → New courses
```

**Chi tiết từng bước:**

1. **Edit Form**: User click "Sửa" từ `index.php` → `manage.php?id=X`
2. **Pre-populate**: Form load dữ liệu cũ từ database
3. **Form Submit**: User thay đổi thông tin (đặc biệt là ngày bắt đầu)
4. **Database Update**: `batch_manager::update_batch()` cập nhật record
5. **Event Trigger**: Trigger event `\local_testeventapi\event\batch_updated`
6. **Observer Processing**: `observer::batch_updated()` xử lý thay đổi

**🧹 Clean & Re-add Logic:**
```php
// Xóa tất cả courses được thêm tự động (giữ lại manual courses)
$DB->delete_records('local_testeventapi_courses', [
    'batchid' => $batch_id,
    'added_by_event' => 1
]);

// Thêm lại courses theo ngày bắt đầu mới
batch_manager::auto_add_courses_by_event($batch_id, $new_startdate);
```

---

### 🗑️ **3. Xóa đợt học (Delete Batch)**

```
User Delete → Confirmation → batch_manager::delete_batch() → Event Trigger → Observer → Email Admin
     ↓              ↓                    ↓                      ↓              ↓           ↓
Click Xóa   → Confirm dialog →    Delete DB records    → batch_deleted    → Observer → Send email
```

**Chi tiết từng bước:**

1. **Delete Action**: User click "Xóa" từ `index.php` hoặc `view.php`
2. **Confirmation**: `delete.php` hiển thị confirmation dialog
3. **User Confirm**: User click "Yes" với sesskey validation
4. **Database Deletion**: 
   - Delete từ `local_testeventapi_courses` (cascade)
   - Delete từ `local_testeventapi_batches`
5. **Event Trigger**: Trigger event `\local_testeventapi\event\batch_deleted`
6. **Email Notification**: `observer::batch_deleted()` gửi email cho admin

**📧 Email Content:**
```
Subject: [Moodle] Đợt học đã được xóa: {batch_name}
Body:
- Tên đợt: {batch_name}
- Ngày xóa: {current_time}
- Người xóa: {user_fullname}
- Số môn học bị xóa: {total_courses}
- Môn học qua Event API: {courses_by_event}
```

---

### 👁️ **4. Xem chi tiết đợt (View Batch)**

```
User Click View → view.php → Load Data → Display Statistics → Show Course List
       ↓             ↓          ↓            ↓                 ↓
   Click "Xem" → GET request → DB queries → Calculate stats → Table with STT
```

**Chi tiết hiển thị:**

1. **Batch Information**:
   - Tên đợt học
   - Ngày bắt đầu (format: dd/mm/yyyy)
   - Ngày tạo (format: dd/mm/yyyy HH:mm)

2. **Statistics**:
   - Tổng số môn học
   - Môn học qua Event API (badge màu xanh)
   - Môn học thêm thủ công (badge màu xám)

3. **Course List Table**:
   - STT (số thứ tự)
   - Tên môn học (link đến course)
   - Tên viết tắt
   - Ngày bắt đầu môn học
   - Ngày thêm vào đợt
   - Phương thức thêm (Event API/Manual)

---

### 📊 **5. Danh sách đợt học (Index Page)**

```
User Access → index.php → Load All Batches → Calculate Stats → Display Table
     ↓            ↓            ↓                ↓                ↓
 Menu click → GET request → DB query batch → For each batch → Table with STT
```

**Features:**

1. **Batch List Table**:
   - STT (đánh số thứ tự)
   - Tên đợt học
   - Ngày bắt đầu (dd/mm/yyyy)
   - Ngày tạo (dd/mm/yyyy HH:mm)
   - Tổng môn học
   - Môn học qua Event API
   - Actions (Xem/Sửa/Xóa)

2. **Action Buttons**:
   - **Xem**: `view.php?id=X`
   - **Sửa**: `manage.php?id=X` (chỉ với quyền manage)
   - **Xóa**: `delete.php?id=X` (chỉ với quyền manage)

3. **Statistics Query**:
```php
foreach ($batches as $batch) {
    $stats = $DB->get_record_sql("
        SELECT 
            COUNT(*) as total_courses,
            SUM(CASE WHEN added_by_event = 1 THEN 1 ELSE 0 END) as courses_by_event
        FROM {local_testeventapi_courses} 
        WHERE batchid = ?
    ", [$batch->id]);
}
```

## 🎪 Event System Architecture

### 📡 **Custom Events**

#### 1. **batch_created Event**
```php
// File: classes/event/batch_created.php
// Trigger: Khi tạo đợt học mới
// Data: batch_id, batch_name, start_date
// Purpose: Thông báo có đợt học mới để tự động thêm courses

$event = \local_testeventapi\event\batch_created::create([
    'objectid' => $batch_id,
    'context' => context_system::instance(),
    'other' => [
        'name' => $batch_name,
        'start_date' => $start_date
    ]
]);
```

#### 2. **batch_updated Event**
```php
// File: classes/event/batch_updated.php  
// Trigger: Khi cập nhật đợt học
// Data: batch_id, old_data, new_data
// Purpose: Re-sync courses khi thay đổi start_date

$event = \local_testeventapi\event\batch_updated::create([
    'objectid' => $batch_id,
    'context' => context_system::instance(),
    'other' => [
        'name' => $new_name,
        'start_date' => $new_start_date,
        'old_start_date' => $old_start_date
    ]
]);
```

#### 3. **batch_deleted Event**
```php
// File: classes/event/batch_deleted.php
// Trigger: Khi xóa đợt học  
// Data: batch_id, batch_name, deletion_info
// Purpose: Gửi notification email cho admin

$event = \local_testeventapi\event\batch_deleted::create([
    'objectid' => $batch_id,
    'context' => context_system::instance(),
    'other' => [
        'name' => $batch_name,
        'start_date' => $start_date,
        'deleted_by' => $USER->id
    ]
]);
```

#### 4. **course_added_to_batch Event**
```php
// File: classes/event/course_added_to_batch.php
// Trigger: Khi thêm course vào batch
// Data: batch_id, course_id, add_method
// Purpose: Logging và potential future processing

$event = \local_testeventapi\event\course_added_to_batch::create([
    'objectid' => $course_id,
    'context' => context_system::instance(),
    'other' => [
        'batchid' => $batch_id,
        'added_by_event' => $added_by_event
    ]
]);
```

---

### 👂 **Event Observers**

#### **Observer Registration** (`db/events.php`):
```php
$observers = [
    [
        'eventname' => '\local_testeventapi\event\batch_created',
        'callback' => '\local_testeventapi\observer::batch_created',
        'priority' => 0,
    ],
    [
        'eventname' => '\local_testeventapi\event\batch_updated', 
        'callback' => '\local_testeventapi\observer::batch_updated',
        'priority' => 0,
    ],
    [
        'eventname' => '\local_testeventapi\event\batch_deleted',
        'callback' => '\local_testeventapi\observer::batch_deleted', 
        'priority' => 0,
    ],
    [
        'eventname' => '\local_testeventapi\event\course_added_to_batch',
        'callback' => '\local_testeventapi\observer::course_added_to_batch',
        'priority' => 0,
    ],
];
```

#### **Observer Methods**:

**🔄 Auto Course Addition:**
```php
public static function batch_created($event) {
    $batch_id = $event->objectid;
    $start_date = $event->other['start_date'];
    
    // Tìm tất cả courses có cùng startdate
    $matching_courses = $DB->get_records('course', [
        'startdate' => $start_date
    ]);
    
    // Thêm từng course vào batch
    foreach ($matching_courses as $course) {
        batch_manager::add_course_to_batch($batch_id, $course->id, true);
    }
}
```

**♻️ Course Re-sync:**
```php
public static function batch_updated($event) {
    $batch_id = $event->objectid;
    $new_start_date = $event->other['start_date'];
    
    // Xóa tất cả courses tự động (giữ lại manual)
    $DB->delete_records('local_testeventapi_courses', [
        'batchid' => $batch_id,
        'added_by_event' => 1
    ]);
    
    // Thêm lại courses theo start_date mới
    batch_manager::auto_add_courses_by_event($batch_id, $new_start_date);
}
```

**📧 Email Notification:**
```php
public static function batch_deleted($event) {
    $batch_data = $event->other;
    $admin = get_admin();
    
    // Tạo email content
    $subject = '[Moodle] Đợt học đã được xóa: ' . $batch_data['name'];
    $message = "Đợt học '{$batch_data['name']}' đã được xóa...";
    
    // Gửi email
    email_to_user($admin, $USER, $subject, $message);
}
```

## 🎮 Chức năng và Giao diện

### 📁 **File Structure & Responsibilities**

```
local/testeventapi/
├── classes/
│   ├── batch_manager.php          # Core business logic
│   ├── observer.php               # Event listeners  
│   └── event/
│       ├── batch_created.php      # Event định nghĩa
│       ├── batch_updated.php      # Event định nghĩa
│       ├── batch_deleted.php      # Event định nghĩa
│       └── course_added_to_batch.php
├── db/
│   ├── access.php                 # Capabilities
│   ├── events.php                 # Event-Observer mapping
│   ├── install.xml                # Database schema
│   └── upgrade.php                # DB upgrade scripts
├── lang/en/
│   └── local_testeventapi.php     # Language strings
├── index.php                      # 📊 Main dashboard
├── manage.php                     # ✏️ Create/Edit batches  
├── view.php                       # 👁️ Batch detail view
├── delete.php                     # 🗑️ Delete confirmation
├── lib.php                        # Core functions
└── version.php                    # Plugin metadata
```

### 🖥️ **User Interface Components**

#### **1. Dashboard (index.php)**
**URL**: `/local/testeventapi/index.php`

**Features**:
- 📋 **Batch List Table** với số thứ tự (STT)
- 📅 **Date Format**: dd/mm/yyyy cho tất cả dates
- 📊 **Statistics**: Real-time count courses by method
- 🎛️ **Action Buttons**: View/Edit/Delete (phân quyền)
- ➕ **Add New Batch** button (cho managers)

**Table Columns**:
```
STT | Tên đợt | Ngày bắt đầu | Ngày tạo | Tổng môn học | Event API | Thao tác
 1  | Đợt 1   | 01/09/2025   | 26/09/25 |      15      |     12    | [Xem][Sửa][Xóa]
```

#### **2. Batch Management (manage.php)**
**URL**: `/local/testeventapi/manage.php[?id=X]`

**Form Fields**:
- 📝 **Batch Name**: Text input với placeholder và validation
- 📅 **Start Date**: Date-time picker
- 🔍 **Course Preview**: Real-time preview courses sẽ được auto-add

**Validation Rules**:
- Tên đợt: Required, max 255 chars
- Ngày bắt đầu: Required, valid date
- Duplicate check: Tên đợt không trùng

#### **3. Batch Detail (view.php)**
**URL**: `/local/testeventapi/view.php?id=X`

**Sections**:
1. **📋 Batch Information**:
   - Tên đợt học
   - Ngày bắt đầu (dd/mm/yyyy)
   - Ngày tạo (dd/mm/yyyy HH:mm)

2. **📊 Statistics Dashboard**:
   - Tổng số môn học
   - Môn học qua Event API (badge xanh)
   - Môn học thêm thủ công (badge xám)
   - Môn học có thể thêm

3. **📚 Course List Table**:
```
STT | Tên môn học | Tên viết tắt | Ngày bắt đầu | Ngày thêm | Phương thức
 1  | Toán cao cấp| MATH101     | 01/09/2025   | 26/09 14:30| [Event API]
```

#### **4. Delete Confirmation (delete.php)**
**URL**: `/local/testeventapi/delete.php?id=X`

**Features**:
- ⚠️ **Warning Dialog**: Hiển thị thông tin đợt học
- 🔒 **Sesskey Protection**: CSRF protection
- 📧 **Email Notification**: Tự động gửi email cho admin sau khi xóa
- 🔙 **Cancel Option**: Quay lại view.php

### 🎯 **Key Features**

#### **🤖 Automated Course Management**
- **Smart Detection**: Tự động detect courses với matching start date
- **Event-Driven**: Hoạt động through Event API, không cần manual trigger
- **Real-time Sync**: Update ngay khi có thay đổi start date
- **Conflict Resolution**: Xử lý duplicates và data consistency

#### **📊 Advanced Statistics**
- **Real-time Counting**: Dynamic statistics calculation
- **Method Tracking**: Phân biệt auto vs manual addition
- **Availability Check**: Show courses có thể thêm thêm
- **Historical Data**: Track thời gian thêm từng course

#### **🔔 Notification System**
- **Email Alerts**: Gửi email khi xóa batch (có thể expand)
- **Admin Notification**: Thông báo cho admin về changes
- **Event Logging**: Log tất cả events cho audit trail

#### **🎨 User Experience**
- **Responsive Design**: Mobile-friendly interface
- **Vietnamese Localization**: Full tiếng Việt support
- **Intuitive Icons**: Clear visual indicators
- **Confirmation Dialogs**: Prevent accidental actions
- **Breadcrumb Navigation**: Easy navigation flow

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

## 🔧 Technical Implementation

### 🏗️ **Core Architecture**

#### **MVC Pattern Implementation**:
```
Model (batch_manager.php) ←→ Controller (*.php pages) ←→ View (HTML output)
                ↕                                          ↕
            Database                                   User Interface
                ↕                                          ↕
         Event System ←→ Observer (observer.php) ←→ Email System
```

#### **Event-Driven Architecture**:
1. **Event Publishers**: batch_manager methods
2. **Event Bus**: Moodle's event system  
3. **Event Subscribers**: Observer methods
4. **Event Handlers**: Automated business logic

### 🎛️ **Configuration & Settings**

#### **Capabilities** (`db/access.php`):
```php
$capabilities = [
    'local/testeventapi:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'user' => CAP_ALLOW,
            'student' => CAP_ALLOW,
        ],
    ],
    'local/testeventapi:manage' => [
        'captype' => 'write', 
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
        ],
    ],
];
```

#### **Database Schema** (`db/install.xml`):
```xml
<!-- Batches Table -->
<TABLE NAME="local_testeventapi_batches">
    <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
        <FIELD NAME="name" TYPE="char" LENGTH="255" NOTNULL="true"/>
        <FIELD NAME="start_date" TYPE="int" LENGTH="10" NOTNULL="true"/>
        <FIELD NAME="timecreated" TYPE="int" LENGTH="10" NOTNULL="true"/>
        <FIELD NAME="timemodified" TYPE="int" LENGTH="10" NOTNULL="true"/>
    </FIELDS>
    <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
    </KEYS>
    <INDEXES>
        <INDEX NAME="start_date" UNIQUE="false" FIELDS="start_date"/>
    </INDEXES>
</TABLE>

<!-- Courses Table -->
<TABLE NAME="local_testeventapi_courses">
    <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
        <FIELD NAME="batchid" TYPE="int" LENGTH="10" NOTNULL="true"/>
        <FIELD NAME="courseid" TYPE="int" LENGTH="10" NOTNULL="true"/>  
        <FIELD NAME="added_by_event" TYPE="int" LENGTH="1" NOTNULL="true" DEFAULT="0"/>
        <FIELD NAME="timecreated" TYPE="int" LENGTH="10" NOTNULL="true"/>
    </FIELDS>
    <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
        <KEY NAME="batchid" TYPE="foreign" FIELDS="batchid" REFTABLE="local_testeventapi_batches"/>
        <KEY NAME="courseid" TYPE="foreign" FIELDS="courseid" REFTABLE="course"/>
    </KEYS>
    <INDEXES>
        <INDEX NAME="batch_course" UNIQUE="true" FIELDS="batchid,courseid"/>
    </INDEXES>
</TABLE>
```

### 🚀 **Performance Optimizations**

#### **Database Queries**:
- **Indexed Fields**: start_date, batchid, courseid
- **Unique Constraints**: Prevent duplicate course assignments
- **Foreign Keys**: Data integrity và cascade operations
- **Bulk Operations**: Insert multiple courses in single transaction

#### **Caching Strategy**:
- **Statistics Caching**: Cache course counts để avoid expensive queries
- **Event Throttling**: Prevent spam events từ rapid updates
- **Query Optimization**: Use JOIN queries thay vì multiple selects

#### **Error Handling**:
```php
// Observer error handling - không crash main system
try {
    batch_manager::auto_add_courses_by_event($batchid, $startdate);
} catch (\Exception $e) {
    // Log error nhưng không throw exception
    \core\notification::error('Event processing failed: ' . $e->getMessage());
}
```

---

## 🎓 **Educational Value - Demo Event API**

### 📚 **Learning Objectives**

Plugin này là **complete reference implementation** cho:

#### **1. Event System Mastery**
- **Custom Event Creation**: Tạo events với proper data structure
- **Observer Pattern**: Implement loose coupling through events  
- **Event Registration**: Correct event-observer mapping
- **Event Data Handling**: Pass và retrieve data through events

#### **2. Moodle Development Best Practices**
- **Plugin Structure**: Standard Moodle plugin architecture
- **Database Design**: Proper schema with relationships
- **Form Handling**: MoodleForm implementation với validation
- **Capability System**: Role-based access control
- **Language Support**: Internationalization (i18n)

#### **3. Advanced Concepts**
- **Automated Workflows**: Event-driven business logic
- **Email Integration**: Notification system
- **UI/UX Design**: Professional interface với Vietnamese support
- **Error Handling**: Graceful failure management
- **Performance**: Optimized queries và caching

### 🔗 **Extensibility Examples**

Plugin này có thể được extend để demo thêm concepts:

#### **Integration với External Systems**:
```php
// Observer có thể call external API
public static function batch_created($event) {
    // Send to external LMS
    $api_client->sync_batch($event->other);
    
    // Update CRM system  
    $crm->create_batch_record($event->objectid);
    
    // Trigger webhook
    $webhook->notify_batch_created($event);
}
```

#### **Plugin Communication**:
```php
// Other plugins có thể listen events này
$observers = [
    [
        'eventname' => '\local_testeventapi\event\batch_created',
        'callback' => '\local_otherplugin\observer::handle_batch_created',
    ],
];
```

#### **Advanced Event Processing**:
```php
// Queue-based processing cho heavy operations
public static function batch_created($event) {
    // Add to processing queue thay vì xử lý ngay
    \core\task\manager::queue_adhoc_task(
        new \local_testeventapi\task\process_batch_created($event->objectid)
    );
}
```

### 🎯 **Real-world Applications**

Concepts trong plugin này applicable cho:
- **Course Management Systems**: Tự động enroll students
- **Resource Management**: Auto-assign teachers/resources  
- **Notification Systems**: Multi-channel alerts
- **Integration Projects**: Connect Moodle với external systems
- **Workflow Automation**: Business process automation
- **Audit Systems**: Event logging và compliance

---

## 💡 **Key Takeaways**

### ✅ **Event API Benefits**
1. **🔄 Decoupling**: Business logic tách biệt khỏi UI logic
2. **📈 Scalability**: Dễ thêm new features không modify existing code  
3. **🔌 Integration**: Other plugins có thể hook vào events
4. **🛡️ Reliability**: Error trong observer không crash main flow
5. **📊 Observability**: Built-in event logging và monitoring

### 🎨 **Design Patterns Used**
- **Observer Pattern**: Event-listener architecture
- **MVC Pattern**: Separation of concerns
- **Factory Pattern**: Event creation và management
- **Strategy Pattern**: Different handling cho different events
- **Template Method**: Consistent event processing flow

Plugin **Test Event API** không chỉ là demo - nó là **production-ready system** có thể được deploy và sử dụng thực tế, đồng thời serve như **educational resource** cho Moodle developers muốn master Event API!