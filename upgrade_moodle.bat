@echo off
REM Batch script to upgrade Moodle from command line
REM Run this from the Moodle root directory

echo Starting Moodle upgrade...
php admin\cli\upgrade.php --non-interactive

echo Upgrade completed!
pause