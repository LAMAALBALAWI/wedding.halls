FROM php:8.2-apache

# تثبيت وتفعيل إضافة mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# نسخ ملفات المشروع للسيرفر
COPY . /var/www/html/

# إعطاء الصلاحيات اللازمة
RUN chown -R www-data:www-data /var/www/html/
