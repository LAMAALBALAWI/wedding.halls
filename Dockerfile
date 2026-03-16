FROM php:8.2-apache

# تثبيت mysqli وتفعيلها
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# إعداد Apache ليسمح باستخدام ملفات .htaccess
RUN a2enmod rewrite

# نسخ الملفات
COPY . /var/www/html/

# تغيير الصلاحيات
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# ضبط المنفذ الافتراضي لـ Apache ليكون متوافقاً مع Railway
ENV PORT 80
EXPOSE 80

# تشغيل Apache في المقدمة
CMD ["apache2-foreground"]
