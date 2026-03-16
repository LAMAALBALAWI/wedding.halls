FROM php:8.2-apache

# تثبيت مكتبة mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# نسخ ملفاتك
COPY . /var/www/html/

# إعطاء الصلاحيات
RUN chown -R www-data:www-data /var/www/html/

# السيرفر بيعرف البورت تلقائياً
EXPOSE 80
