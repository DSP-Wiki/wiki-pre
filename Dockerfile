FROM php:8.1-fpm

LABEL maintainer="antt1995@antts.uk"

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
	git \
	librsvg2-bin \
	imagemagick \
	ffmpeg \
	webp \
	unzip \
	openssh-client \
	python3 \
	python3-pygments \
	rsync \
	nano \
	liblua5.1-0 \
	libzip4 \
	s3cmd \
	libicu-dev \
	libonig-dev \
	libcurl4-gnutls-dev \
	libmagickwand-dev \
	libwebp7 \
	libzip-dev \
	liblua5.1-0-dev \
	&& docker-php-ext-install -j "$(nproc)" \
	calendar \
	intl \
	mbstring \
	mysqli \
	opcache \
	zip \
	&& pecl install \
	APCu-5.1.23 \
	luasandbox \
	imagick \
	&& docker-php-ext-enable \
	apcu \
	luasandbox \
	imagick \
	&& rm -r /tmp/pear \
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
	&& rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
	
# Set recommended PHP.ini settings
RUN { \
	echo 'opcache.memory_consumption=128'; \
	echo 'opcache.interned_strings_buffer=8'; \
	echo 'opcache.max_accelerated_files=4000'; \
	echo 'opcache.revalidate_freq=60'; \
	echo 'memory_limit = 512M'; \
	echo 'max_execution_time = 60'; \
	echo 'pm.max_children = 30'; \
	echo 'pm.max_requests = 200'; \
	echo 'pm.start_servers = 10'; \
	echo 'pm.min_spare_servers = 10'; \
	echo 'pm.max_spare_servers = 30'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

COPY ./config/php-config.ini /usr/local/etc/php/conf.d/php-config.ini
COPY ./config/robots.txt /var/www/robots.txt

RUN pecl install --configureoptions 'enable-redis-igbinary="no" enable-redis-lzf="no" enable-redis-zstd="no" enable-redis-msgpack="no" enable-redis-lz4="no" with-liblz4="yes"' redis \
	&& docker-php-ext-enable redis