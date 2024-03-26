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
	&& rm -rf /var/lib/apt/lists/*

# Install the PHP extensions we need
RUN apt-get update && apt-get install -y --no-install-recommends \
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
	\
	&& apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
	&& rm -rf /var/lib/apt/lists/*

# Enable Short URLs


# Enable AllowEncodedSlashes for VisualEditor
RUN sed -i "s/<\/VirtualHost>/\tAllowEncodedSlashes NoDecode\n<\/VirtualHost>/" "$APACHE_CONFDIR/sites-available/000-default.conf"

# Set recommended PHP.ini settings
RUN { \
	echo 'opcache.memory_consumption=128'; \
	echo 'opcache.interned_strings_buffer=8'; \
	echo 'opcache.max_accelerated_files=4000'; \
	echo 'opcache.revalidate_freq=60'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

COPY ./config/php-config.ini /usr/local/etc/php/conf.d/php-config.ini
COPY ./config/robots.txt /var/www/robots.txt
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini; \
    echo 'max_execution_time = 60' >> /usr/local/etc/php/conf.d/docker-php-executiontime.ini; \
	echo 'pm.max_children = 30' >> /usr/local/etc/php-fpm.d/zz-docker.conf; \
    echo 'pm.max_requests = 200' >> /usr/local/etc/php-fpm.d/zz-docker.conf; \
	echo 'pm.start_servers = 10' >> /usr/local/etc/php-fpm.d/zz-docker.conf; \
    echo 'pm.min_spare_servers = 10' >> /usr/local/etc/php-fpm.d/zz-docker.conf; \
    echo 'pm.max_spare_servers = 30' >> /usr/local/etc/php-fpm.d/zz-docker.conf; 

RUN pecl install --configureoptions 'enable-redis-igbinary="no" enable-redis-lzf="no" enable-redis-zstd="no" enable-redis-msgpack="no" enable-redis-lz4="no" with-liblz4="yes"' redis \
	&& docker-php-ext-enable redis