# Sử dụng một image CentOS có sẵn trên Docker Hub
FROM centos:7

# Cài đặt các gói cần thiết (tuỳ thuộc vào dự án của bạn)
RUN yum -y update
RUN yum install -y https://rpms.remirepo.net/enterprise/remi-release-7.rpm
RUN yum-config-manager --enable remi-php73
RUN yum install -y epel-release && \
    yum install -y \
        php-fpm \
        sudo \
        redis-server \
        nginx \
        php \
        php-cli \
        php-pdo \
        php-pear \
        php-devel \
        systemtap-sdt-devel unzip git \
        && \
    yum clean all
RUN yum -y install gcc
# Cài đặt Oracle Instant Client (tuỳ thuộc vào dự án của bạn)
COPY oracle-instantclient12.2-basic-12.2.0.1.0-1.x86_64.rpm /tmp/
COPY oracle-instantclient12.2-devel-12.2.0.1.0-1.x86_64.rpm /tmp/
COPY oracle-instantclient12.2-sqlplus-12.2.0.1.0-1.x86_64.rpm /tmp/


RUN yum install -y /tmp/oracle-instantclient12.2-basic-12.2.0.1.0-1.x86_64.rpm
RUN yum install -y /tmp/oracle-instantclient12.2-devel-12.2.0.1.0-1.x86_64.rpm
RUN yum install -y /tmp/oracle-instantclient12.2-sqlplus-12.2.0.1.0-1.x86_64.rpm
RUN rm -f /tmp/oracle-instantclient12.2-*.rpm
RUN yum clean all
# COPY php.ini /etc/php.ini

# Thiết lập biến môi trường
ENV PHP_DTRACE=yes
ENV LD_LIBRARY_PATH=/usr/lib/oracle/12.2/client64/lib
ENV ORACLE_HOME=/usr/lib/oracle/12.2/client64
ENV LD_LIBRARY_PATH=/usr/lib/oracle/12.2/client64/lib:$LD_LIBRARY_PATH
ENV C_INCLUDE_PATH=/usr/include/oracle/12.2/client64:$C_INCLUDE_PATH

# Copy cấu hình Nginx
COPY zalooa-nginx.conf /etc/nginx/conf.d/default.conf


RUN pecl install oci8-2.2.0 && \
        echo "extension=oci8.so" > /etc/php.d/oci8.ini
# Copy và cài đặt ứng dụng Laravel
COPY . /var/www/zalooa
WORKDIR /var/www/zalooa

# Cài đặt Composer và dependencies của Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Thiết lập quyền sở hữu và quyền truy cập cho thư mục Laravel
RUN chown -R nginx:nginx /var/www/zalooa
RUN chmod -R 755 /var/www/zalooa/storage

# Expose cổng mặc định cho Nginx
EXPOSE 80

# CMD để chạy Nginx và PHP-FPM
CMD ["nginx", "-g", "daemon off;"]
CMD ["php-fpm", "-g", "daemon off;"]
