FROM php:8.2-apache

# تثبيت مكتبة mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# إعدادات لمنع خطأ الـ MPM
RUN a2dismod mpm_event && a2enmod mpm_prefork

# نسخ الملفات
COPY . /var/www/html/

# إعطاء الصلاحيات
RUN chown -R www-data:www-data /var/www/html/
