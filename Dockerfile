FROM php:8.2-apache

# تثبيت mysqli وتفعيلها
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# إعداد Apache ليقبل أي بورت يعطيه إياه Railway آلياً
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# نسخ ملفاتك
COPY . /var/www/html/

# صلاحيات الملفات
RUN chown -R www-data:www-data /var/www/html/

# الأمر لتشغيل السيرفر
CMD ["apache2-foreground"]
