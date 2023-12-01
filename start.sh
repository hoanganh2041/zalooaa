#!/bin/bash

# Khởi động PHP-FPM
systemctl start php-fpm

# Thêm các lệnh khởi động Nginx hoặc các dịch vụ khác nếu cần

# Giữ container chạy
tail -f /dev/null
