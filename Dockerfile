FROM php:8.2-fpm-alpine

# تثبيت mysqli
RUN docker-php-ext-install mysqli

# نسخ ملفاتك
COPY . /var/www/html/

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html/

# تشغيل السيرفر
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]
