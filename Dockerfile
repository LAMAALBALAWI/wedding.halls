FROM php:8.2-apache

# تثبيت mysqli وتفعيلها
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# إجبار Apache على استخدام محرك واحد فقط لمنع الخطأ الأحمر
RUN echo "LoadModule mpm_prefork_module modules/mod_mpm_prefork.so" > /etc/apache2/mods-available/mpm_prefork.load \
    && a2dismod mpm_event || true \
    && a2enmod mpm_prefork || true

# نسخ ملفات مشروعك
COPY . /var/www/html/

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html/

# تنبيه السيرفر إنه يشتغل على بورت 80
EXPOSE 80
