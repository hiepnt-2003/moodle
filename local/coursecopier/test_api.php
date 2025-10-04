<?php
// This file is part of Moodle - http://moodle.org/
//
// Simple test script for Course Copier JSON API
//
// @package    local_coursecopier
// @copyright  2025 Course Copier
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

require_once('../../config.php');

// Require login
require_login();

// Check capabilities
$context = context_system::instance();
require_capability('moodle/course:create', $context);

$PAGE->set_url('/local/coursecopier/test_api.php');
$PAGE->set_context($context);
$PAGE->set_title('Test Course Copier API');
$PAGE->set_heading('Course Copier - API Test Interface');

echo $OUTPUT->header();

?>

<style>
.test-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    font-family: Arial, sans-serif;
}

.test-section {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin: 20px 0;
    padding: 20px;
}

.json-input {
    width: 100%;
    height: 200px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 4px;
    background: #fff;
}

.test-button {
    background: #0073aa;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin: 10px 5px;
    font-size: 14px;
}

.test-button:hover {
    background: #005177;
}

.test-button:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.result-area {
    background: #fff;
    border: 1px solid #ccc;
    padding: 15px;
    margin-top: 15px;
    border-radius: 4px;
    min-height: 120px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    white-space: pre-wrap;
    overflow-x: auto;
}

.info-box {
    background: #e7f3ff;
    border: 1px solid #b3d9ff;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.success { border-left: 5px solid #4caf50; }
.error { border-left: 5px solid #f44336; }
.warning { border-left: 5px solid #ff9800; }

.endpoint-info {
    background: #f0f0f0;
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    overflow-x: auto;
}
</style>

<div class="test-container">
    <h2>🧪 Course Copier JSON API Test Interface</h2>
    
    <div class="info-box">
        <h3>📋 API Information</h3>
        <p><strong>Endpoint:</strong> <code><?php echo $CFG->wwwroot; ?>/local/coursecopier/api.php</code></p>
        <p><strong>Method:</strong> POST</p>
        <p><strong>Content-Type:</strong> application/json</p>
        <p><strong>Authentication:</strong> Bearer Token (trong header hoặc JSON body)</p>
        
        <h4>📝 Input Parameters:</h4>
        <ul>
            <li><code>shortname_clone</code>: Shortname của khóa học nguồn cần clone</li>
            <li><code>fullname</code>: Tên đầy đủ của khóa học mới</li>
            <li><code>shortname</code>: Shortname của khóa học mới (phải unique)</li>
            <li><code>startdate</code>: Ngày bắt đầu (Unix timestamp)</li>
            <li><code>enddate</code>: Ngày kết thúc (Unix timestamp)</li>
        </ul>
        
        <h4>📤 Output Format:</h4>
        <ul>
            <li><code>status</code>: "success" hoặc "error"</li>
            <li><code>id</code>: ID của khóa học mới (0 nếu lỗi)</li>
            <li><code>message</code>: Thông báo thành công hoặc mô tả lỗi</li>
        </ul>
    </div>

    <div class="test-section">
        <h3>🔑 Token Setup</h3>
        <p><strong>Current User:</strong> <?php echo fullname($USER); ?></p>
        <p><strong>Web Service Token:</strong> <span id="tokenDisplay">Click "Get Token Info" để lấy thông tin</span></p>
        <button onclick="getTokenInfo()" class="test-button">Get Token Info</button>
        <button onclick="createToken()" class="test-button">Create New Token</button>
    </div>

    <div class="test-section">
        <h3>📚 Test Clone Course (Webservice chính)</h3>
        <p>Test chức năng clone khóa học với đầu vào và đầu ra theo yêu cầu.</p>
        
        <label for="cloneCourse"><strong>JSON Request Body:</strong></label>
        <textarea id="cloneCourse" class="json-input" placeholder="JSON request body...">
{
  "wstoken": "YOUR_TOKEN_HERE",
  "wsfunction": "local_coursecopier_clone_course",
  "shortname_clone": "COURSE123",
  "fullname": "Khóa học Clone 2025",
  "shortname": "CLONE2025",
  "startdate": <?php echo strtotime('+1 day'); ?>,
  "enddate": <?php echo strtotime('+6 months'); ?>
}
        </textarea>
        
        <div>
            <button onclick="testCloneCourse()" class="test-button" id="btnClone">🚀 Test Clone Course</button>
            <button onclick="copyJson('cloneCourse')" class="test-button">📋 Copy JSON</button>
            <button onclick="updateToken('cloneCourse')" class="test-button">🔄 Update Token</button>
        </div>
        
        <div id="result-clone" class="result-area">Kết quả sẽ hiển thị ở đây...</div>
    </div>

    <div class="test-section">
        <h3>📋 Test Get Available Courses</h3>
        <p>Lấy danh sách các khóa học có thể clone.</p>
        
        <label for="getCourses"><strong>JSON Request Body:</strong></label>
        <textarea id="getCourses" class="json-input" placeholder="JSON request body...">
{
  "wstoken": "YOUR_TOKEN_HERE",
  "wsfunction": "local_coursecopier_get_available_courses",
  "categoryid": 0
}
        </textarea>
        
        <div>
            <button onclick="testGetCourses()" class="test-button" id="btnGet">🚀 Test Get Courses</button>
            <button onclick="copyJson('getCourses')" class="test-button">📋 Copy JSON</button>
            <button onclick="updateToken('getCourses')" class="test-button">🔄 Update Token</button>
        </div>
        
        <div id="result-get" class="result-area">Kết quả sẽ hiển thị ở đây...</div>
    </div>

    <div class="test-section">
        <h3>📊 cURL Examples</h3>
        
        <h4>Clone Course:</h4>
        <div class="endpoint-info">
curl -X POST "<?php echo $CFG->wwwroot; ?>/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "wsfunction": "local_coursecopier_clone_course",
    "shortname_clone": "COURSE123",
    "fullname": "Clone Course 2025",
    "shortname": "CLONE2025",
    "startdate": <?php echo strtotime('+1 day'); ?>,
    "enddate": <?php echo strtotime('+6 months'); ?>
  }'
        </div>
        
        <h4>Get Available Courses:</h4>
        <div class="endpoint-info">
curl -X POST "<?php echo $CFG->wwwroot; ?>/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "wsfunction": "local_coursecopier_get_available_courses",
    "categoryid": 0
  }'
        </div>
    </div>
</div>

<script>
let currentToken = '';

async function getTokenInfo() {
    try {
        // Try to get existing tokens for current user
        const response = await fetch('<?php echo $CFG->wwwroot; ?>/webservice/rest/server.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'wsfunction=core_webservice_get_site_info&moodlewsrestformat=json&wstoken=dummy'
        });
        
        document.getElementById('tokenDisplay').innerHTML = 
            '<span style="color: #d32f2f;">Please create token manually:<br>' +
            '1. Go to Site Administration → Server → Web Services → Manage tokens<br>' +
            '2. Create token for current user<br>' +
            '3. Copy token and click "Update Token" buttons</span>';
            
    } catch (error) {
        document.getElementById('tokenDisplay').innerHTML = 
            '<span style="color: #d32f2f;">Error getting token info: ' + error.message + '</span>';
    }
}

function createToken() {
    const url = '<?php echo $CFG->wwwroot; ?>/admin/webservice/tokens.php';
    window.open(url, '_blank');
    alert('Opened token management page. Please create a token and then use "Update Token" buttons.');
}

function updateToken(textareaId) {
    const token = prompt('Paste your web service token:');
    if (token) {
        currentToken = token;
        const textarea = document.getElementById(textareaId);
        let content = textarea.value;
        content = content.replace(/"wstoken":\s*"[^"]*"/, '"wstoken": "' + token + '"');
        textarea.value = content;
        
        document.getElementById('tokenDisplay').innerHTML = 
            '<span style="color: #4caf50;">Token updated: ' + token.substring(0, 10) + '...</span>';
    }
}

async function testCloneCourse() {
    const jsonText = document.getElementById('cloneCourse').value;
    const resultDiv = document.getElementById('result-clone');
    const button = document.getElementById('btnClone');
    
    try {
        const jsonData = JSON.parse(jsonText);
        
        button.disabled = true;
        button.textContent = 'Testing...';
        resultDiv.textContent = 'Sending request to clone course...';
        resultDiv.className = 'result-area';
        
        const response = await fetch('<?php echo $CFG->wwwroot; ?>/local/coursecopier/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + jsonData.wstoken
            },
            body: jsonText
        });
        
        const result = await response.json();
        
        resultDiv.textContent = 
            'HTTP Status: ' + response.status + '\n' +
            'Response Headers: ' + JSON.stringify(Object.fromEntries(response.headers.entries()), null, 2) + '\n\n' +
            'Response Body:\n' + JSON.stringify(result, null, 2);
                               
        if (result.status === 'success') {
            resultDiv.className = 'result-area success';
            console.log('✅ Clone course thành công!', result);
        } else {
            resultDiv.className = 'result-area error';
            console.log('❌ Clone course thất bại:', result);
        }
        
    } catch (error) {
        resultDiv.textContent = 'Error: ' + error.message;
        resultDiv.className = 'result-area error';
        console.error('❌ Error:', error);
    } finally {
        button.disabled = false;
        button.textContent = '🚀 Test Clone Course';
    }
}

async function testGetCourses() {
    const jsonText = document.getElementById('getCourses').value;
    const resultDiv = document.getElementById('result-get');
    const button = document.getElementById('btnGet');
    
    try {
        const jsonData = JSON.parse(jsonText);
        
        button.disabled = true;
        button.textContent = 'Testing...';
        resultDiv.textContent = 'Sending request to get available courses...';
        resultDiv.className = 'result-area';
        
        const response = await fetch('<?php echo $CFG->wwwroot; ?>/local/coursecopier/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + jsonData.wstoken
            },
            body: jsonText
        });
        
        const result = await response.json();
        
        resultDiv.textContent = 
            'HTTP Status: ' + response.status + '\n' +
            'Response Headers: ' + JSON.stringify(Object.fromEntries(response.headers.entries()), null, 2) + '\n\n' +
            'Response Body:\n' + JSON.stringify(result, null, 2);
                               
        if (result.status === 'success') {
            resultDiv.className = 'result-area success';
            console.log('✅ Get courses thành công!', result);
        } else {
            resultDiv.className = 'result-area error';
            console.log('❌ Get courses thất bại:', result);
        }
        
    } catch (error) {
        resultDiv.textContent = 'Error: ' + error.message;
        resultDiv.className = 'result-area error';
        console.error('❌ Error:', error);
    } finally {
        button.disabled = false;
        button.textContent = '🚀 Test Get Courses';
    }
}

function copyJson(textareaId) {
    const text = document.getElementById(textareaId).value;
    navigator.clipboard.writeText(text).then(() => {
        alert('✅ JSON đã được copy vào clipboard!');
    }).catch(err => {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textarea = document.getElementById(textareaId);
        textarea.select();
        document.execCommand('copy');
        alert('✅ JSON đã được copy vào clipboard!');
    });
}

// Auto-update timestamps when page loads
document.addEventListener('DOMContentLoaded', function() {
    const now = Math.floor(Date.now() / 1000);
    const startdate = now + 86400; // +1 day
    const enddate = now + (86400 * 180); // +6 months
    
    // Update clone course template
    let cloneContent = document.getElementById('cloneCourse').value;
    cloneContent = cloneContent.replace(/startdate": \d+/, 'startdate": ' + startdate);
    cloneContent = cloneContent.replace(/enddate": \d+/, 'enddate": ' + enddate);
    document.getElementById('cloneCourse').value = cloneContent;
});
</script>

<?php
echo $OUTPUT->footer();
?>