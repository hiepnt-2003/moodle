@echo off
echo Testing Course Clone API...
echo.

REM Test with existing course (change TEST_COURSE to actual course shortname)
curl -X POST "http://localhost/moodle/webservice/rest/server.php" ^
  -H "Content-Type: application/x-www-form-urlencoded" ^
  -d "wstoken=7dbe17f17c65d685d18731cefd9a2e46" ^
  -d "wsfunction=local_webservice_clone_course" ^
  -d "moodlewsrestformat=json" ^
  -d "shortname_clone=TEST_COURSE" ^
  -d "fullname=API Test Clone" ^
  -d "shortname=API_TEST_%random%" ^
  -d "startdate=1735689600" ^
  -d "enddate=1743465600"

echo.
echo Done!
pause