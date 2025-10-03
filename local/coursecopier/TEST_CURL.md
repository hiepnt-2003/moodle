# Test Course Copier API với cURL

## Setup
```bash
# Set variables
export MOODLE_URL="https://your-moodle.com"
export WS_TOKEN="your_webservice_token_here"
```

## Test 1: Get Available Courses (JSON API)

```bash
curl -X POST "${MOODLE_URL}/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ${WS_TOKEN}" \
  -d '{
    "wsfunction": "local_coursecopier_get_available_courses",
    "categoryid": 0
  }' | jq .
```

## Test 2: Copy Course (JSON API)

```bash
curl -X POST "${MOODLE_URL}/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ${WS_TOKEN}" \
  -d '{
    "wsfunction": "local_coursecopier_copy_course",
    "shortname_clone": "ORIGINAL123",
    "fullname": "New Course Name 2025",
    "shortname": "NEWCOURSE2025",
    "startdate": 1704067200,
    "enddate": 1719792000
  }' | jq .
```

## Test 3: Test Invalid Dates (Should return error)

```bash
curl -X POST "${MOODLE_URL}/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ${WS_TOKEN}" \
  -d '{
    "wsfunction": "local_coursecopier_copy_course",
    "shortname_clone": "ORIGINAL123",
    "fullname": "Invalid Dates Test",
    "shortname": "INVALID_TEST",
    "startdate": 1719792000,
    "enddate": 1704067200
  }' | jq .
```

## Test 4: Traditional Web Service (Fallback)

```bash
curl -X POST "${MOODLE_URL}/webservice/rest/server.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "wstoken=${WS_TOKEN}" \
  -d "wsfunction=local_coursecopier_get_available_courses" \
  -d "moodlewsrestformat=json" \
  -d "categoryid=0" | jq .
```

## Test 5: Copy with Traditional Web Service

```bash
curl -X POST "${MOODLE_URL}/webservice/rest/server.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "wstoken=${WS_TOKEN}" \
  -d "wsfunction=local_coursecopier_copy_course" \
  -d "moodlewsrestformat=json" \
  -d "shortname_clone=ORIGINAL123" \
  -d "fullname=Traditional API Course" \
  -d "shortname=TRADITIONAL2025" \
  -d "startdate=1704067200" \
  -d "enddate=1719792000" | jq .
```

## Test 6: Invalid Token (Should return 401)

```bash
curl -X POST "${MOODLE_URL}/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer invalid_token" \
  -d '{
    "wsfunction": "local_coursecopier_get_available_courses",
    "categoryid": 0
  }' | jq .
```

## Test 7: Missing Function (Should return error)

```bash
curl -X POST "${MOODLE_URL}/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ${WS_TOKEN}" \
  -d '{
    "categoryid": 0
  }' | jq .
```

## Test 8: CORS Preflight

```bash
curl -X OPTIONS "${MOODLE_URL}/local/coursecopier/api.php" \
  -H "Access-Control-Request-Method: POST" \
  -H "Access-Control-Request-Headers: Content-Type, Authorization" \
  -v
```

## Expected Responses

### Success Response:
```json
{
  "status": "success",
  "id": 15,
  "message": "Copy môn học thành công! Đã sao chép toàn bộ nội dung từ môn học gốc."
}
```

### Error Response:
```json
{
  "status": "error",
  "id": 0,
  "message": "Không tìm thấy môn học với shortname: NOTFOUND"
}
```

### Get Courses Response:
```json
{
  "courses": [
    {
      "id": 2,
      "fullname": "Sample Course",
      "shortname": "SAMPLE123",
      "category": 1,
      "startdate": 1609459200,
      "enddate": 1617235200,
      "visible": true
    }
  ],
  "total": 1,
  "status": "success",
  "message": "Lấy danh sách môn học thành công"
}
```

## Notes:

1. **jq**: Sử dụng `jq` để format JSON response đẹp hơn
2. **Token**: Có thể đặt token trong header `Authorization: Bearer` hoặc trong JSON body
3. **CORS**: API hỗ trợ CORS headers cho frontend applications
4. **Error Codes**: 
   - 401: Invalid/missing token
   - 400: Bad request (invalid parameters)
   - 405: Method not allowed (only POST supported)
   - 500: Server error