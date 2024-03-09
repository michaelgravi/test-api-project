#docker build.
FROM debian:10-slim

# set the environment variables
ENV DEBIAN_FRONTEND noninteractive
ENV TERM dumb
ENV TZ=Europe/Amsterdam

#set user www-data shell
RUN usermod --shell /bin/bash www-data

# Set timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# install helper packages
RUN apt update && apt clean
RUN apt -y install software-properties-common curl nano net-tools cron unzip wget gnupg2 libgd3 gosu nginx iproute2

# Configure ssl and Generate keys for https
RUN mkdir /keys \
    && openssl req -newkey rsa:2048 -nodes -subj '/O=TestProject/CN=localhost' \
    -keyout /keys/gc.key -x509 -days 365 -out /keys/gc.crt

# Configure nginx
COPY ./.docker/settings/nginx.prod.conf /etc/nginx/sites-available/default

# Add the repository
RUN apt install -y apt-transport-https lsb-release ca-certificates
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/sury-php.list
RUN wget -qO - https://packages.sury.org/php/apt.gpg | apt-key add -
RUN apt update

# Install PHP 8.1 and packages
RUN apt -y install php8.1 php8.1-gd php8.1-mysql php8.1-zip php8.1-xml php8.1-opcache php8.1-pdo php8.1-fpm \
     php8.1-calendar php8.1-ctype php8.1-exif php8.1-fileinfo php8.1-ftp php8.1-gettext php8.1-iconv \
     php8.1-phar php8.1-posix php8.1-readline php8.1-shmop php8.1-sockets php8.1-sysvmsg php8.1-sysvshm \
     php8.1-tokenizer php8.1-mbstring php8.1-curl mariadb-client

COPY ./.docker/settings/php.prod.ini    /etc/php/8.1/cli/php.ini
COPY ./.docker/settings/php.prod.ini    /etc/php/8.1/fpm/php.ini

COPY ./.docker/settings/fpm.www.prod.conf   /etc/php/8.1/fpm/pool.d/www.conf

RUN mkdir /var/lib/php/session \
    && mkdir /var/lib/php/wsdlcache \
    && chown root:www-data /var/lib/php/session /var/lib/php/wsdlcache

# install src
COPY . /var/www/html
RUN rm -f /var/www/html/index.html

# install composer globally
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Install node, npm & bower
RUN	curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
	&& apt-get install -y nodejs \
    && npm install -g npm@7.24.2 bower

# it is required to give www-data user full rights to /var/www dirrectory
# to make it possible to install node with www-data user
RUN chown -R www-data:www-data /var/www

# Copy the src
COPY --chown=www-data:www-data . /var/www/html
RUN cp /var/www/html/.env.example /var/www/html/.env

# install run job
COPY ./.docker/runables/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["nginx", "-g", "daemon off;"]
