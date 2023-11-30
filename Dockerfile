# Sử dụng hình ảnh CentOS 7 làm base
FROM centos:7

# Cài đặt PHP 7.3 từ Remi Repository
RUN yum install -y https://rpms.remirepo.net/enterprise/remi-release-7.rpm
RUN yum-config-manager --enable remi-php73
# Cài đặt extension oci8 cho PHP
RUN yum install -y php-oci8
# Cài đặt các gói cần thiết
RUN yum install -y epel-release && \
    yum install -y \
    nginx \
    php \
    php-cli \
    php-fpm \
    php-xml \
    php-mbstring \
    php-pdo \
    unzip \
    oracle-instantclient12.2-basic \
    oracle-instantclient12.2-devel \
    oracle-instantclient12.2-sqlplus \
    yum-plugin-ovl && \
    yum clean all && \
    rm -rf /var/cache/yum



# Thiết lập biến môi trường
ENV LD_LIBRARY_PATH=/usr/lib/oracle/12.2/client64/lib
ENV ORACLE_HOME=/usr/lib/oracle/12.2/client64
ENV PHP_DTRACE=yes

# Cấu hình Nginx
COPY zalooa-nginx.conf /etc/nginx/conf.d/default.conf

# Copy và cài đặt ứng dụng Laravel
COPY . /var/www/zalooa
WORKDIR /var/www/zalooa

# Cài đặt Composer và dependencies của Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Thiết lập quyền sở hữu và quyền truy cập cho thư mục Laravel
RUN chown -R nginx:nginx /var/www/zalooa
RUN chmod -R 755 /var/www/zalooa/storage

# Expose cổng mặc định cho Nginx
EXPOSE 80

# CMD để chạy Nginx và PHP-FPM
CMD ["nginx", "-g", "daemon off;"]
