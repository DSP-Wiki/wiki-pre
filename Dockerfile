FROM php:8.1-apache

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
	&& apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
	&& rm -rf /var/lib/apt/lists/*

# Enable Short URLs
RUN a2enmod rewrite \
	&& { \
	echo "<Directory /var/www/html>"; \
	echo "  RewriteEngine On"; \
	echo "  RewriteCond %{REQUEST_FILENAME} !-f"; \
	echo "  RewriteCond %{REQUEST_FILENAME} !-d"; \
	echo "  RewriteRule ^ %{DOCUMENT_ROOT}/index.php [L]"; \
	echo "</Directory>"; \
	} > "$APACHE_CONFDIR/conf-available/short-url.conf" \
	&& a2enconf short-url

# Enable RemoteIp
RUN a2enmod remoteip \
	&& { \
	echo 'RemoteIPHeader X-Real-IP'; \
	echo 'RemoteIPInternalProxy 10.0.0.0/8'; \
	echo 'RemoteIPInternalProxy 172.16.0.0/12'; \
	} > "$APACHE_CONFDIR/conf-available/remoteip.conf" \
	&& a2enconf remoteip

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

RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini; \
    echo 'max_execution_time = 60' >> /usr/local/etc/php/conf.d/docker-php-executiontime.ini



RUN pecl install --configureoptions 'enable-redis-igbinary="no" enable-redis-lzf="no" enable-redis-zstd="no" enable-redis-msgpack="no" enable-redis-lz4="no" with-liblz4="yes"' redis \
	&& docker-php-ext-enable redis

# Version
ENV MEDIAWIKI_MAJOR_VERSION 1.39
ENV MEDIAWIKI_VERSION 1.39.6

# MediaWiki setup
RUN fetchDeps=" \
	gnupg \
	dirmngr \
"; \
	apt-get update \
	&& apt-get install -y --no-install-recommends $fetchDeps \
	&& curl -fSL "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-${MEDIAWIKI_VERSION}.tar.gz" -o mediawiki.tar.gz \
	&& curl -fSL "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-${MEDIAWIKI_VERSION}.tar.gz.sig" -o mediawiki.tar.gz.sig \
	&& export GNUPGHOME="$(mktemp -d)" \
	&& gpg --batch --keyserver keyserver.ubuntu.com --recv-keys \
	D7D6767D135A514BEB86E9BA75682B08E8A3FEC4 \
	441276E9CCD15F44F6D97D18C119E1A64D70938E \
	F7F780D82EBFB8A56556E7EE82403E59F9F8CD79 \
	1D98867E82982C8FE0ABC25F9B69B3109D3BB7B0 \
	&& gpg --batch --verify mediawiki.tar.gz.sig mediawiki.tar.gz \
	&& tar -x --strip-components=1 -f mediawiki.tar.gz \
	&& gpgconf --kill all \
	&& rm -r "$GNUPGHOME" mediawiki.tar.gz.sig mediawiki.tar.gz \
	&& chown -R www-data:www-data extensions skins cache images \
	&& apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false $fetchDeps \
	&& rm -rf /var/lib/apt/lists/*

COPY ./config/LocalSettings.php /var/www/html/LocalSettings.php

RUN cd /var/www/html/ && rm FAQ HISTORY SECURITY UPGRADE INSTALL CREDITS COPYING CODE_OF_CONDUCT.md README.md RELEASE-NOTES-1.39

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.local.json /var/www/html

RUN set -eux; \
	chown -R www-data:www-data /var/www

WORKDIR /var/www/html

USER www-data

RUN set -eux; \
	/usr/bin/composer config --no-plugins allow-plugins.composer/installers true; \
	/usr/bin/composer install --no-dev \
							--ignore-platform-reqs \
							--no-ansi \
							--no-interaction \
							--no-scripts; \
	rm -f composer.lock.json ;\
	/usr/bin/composer update --no-dev \
                            --no-ansi \
                            --no-interaction \
                            --no-scripts; \
	\
	chown -R www-data:www-data /var/www


CMD ["apache2-foreground"]
