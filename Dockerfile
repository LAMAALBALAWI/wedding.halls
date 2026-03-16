FROM php:8.2-apache

# تثبيت mysqli وتفعيلها
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# نسخ الكود
COPY . /var/www/html/

# صلاحيات الملفات
RUN chown -R www-data:www-data /var/www/html/

# السيرفر بيعرف البورت تلقائياً
EXPOSE 80
