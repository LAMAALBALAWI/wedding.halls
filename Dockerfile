FROM php:8.2-apache

# تثبيت مكتبة mysqli وتفعيلها
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# حل مشكلة تضارب المحركات (MPM)
RUN a2dismod mpm_event && a2enmod mpm_prefork

# نسخ ملفات المشروع
COPY . /var/www/html/

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html/
