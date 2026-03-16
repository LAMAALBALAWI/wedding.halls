FROM php:8.2-apache

# تثبيت mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# السطر السحري: تعطيل المحرك المتضارب وتفعيل المستقر
RUN a2dismod mpm_event || true && a2enmod mpm_prefork

# نسخ ملفاتك
COPY . /var/www/html/

# ضبط الصلاحيات
RUN chown -R www-data:www-data /var/www/html/

# إعداد البورت
EXPOSE 80
