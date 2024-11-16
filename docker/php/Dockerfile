FROM php:8.3-fpm


WORKDIR /back-posts/sistema

RUN apt-get update && apt-get install -y \
    software-properties-common \
    libcurl4-openssl-dev \
    libzip-dev \
    git \
    nano \
    sendmail \
    wget \
    iputils-ping \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install curl \
    && docker-php-ext-install zip;

RUN docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli;



RUN docker-php-ext-install calendar && docker-php-ext-configure calendar
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN cd ~ \
    && curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer;

RUN echo 'memory_limit = 16G ' >> /usr/local/etc/php/php.ini
RUN echo 'max_input_vars = 100000000 ' >> /usr/local/etc/php/php.ini
RUN echo 'max_execution_time = 21600' >> /usr/local/etc/php/php.ini
RUN echo 'upload_max_filesize  = 10000M ' >> /usr/local/etc/php/php.ini
RUN echo 'post_max_size  = 1000M ' >> /usr/local/etc/php/php.ini


COPY ./start.sh /usr/local/bin/start

RUN chmod +x /usr/local/bin/start



CMD ["/usr/local/bin/start"]