######################################################################
### The base PHP image for development and production
######################################################################

# Build argument: Image tag
ARG IMAGE_TAG

# Base image
FROM yiisoftware/yii2-php:$IMAGE_TAG

# Maintainer
MAINTAINER Andrew Sinukov  "andrew.sinukov.it@gmail.com"

# Installs sockets extension
RUN docker-php-ext-install sockets

# Installs network tools for debugging
RUN apt-get update && apt-get install -y netcat tcptraceroute

# Installs yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get --assume-yes install yarn

# Installs cron
RUN apt-get update && apt-get install -y cron \
    && sed -i '/session    required     pam_loginuid.so/c\#session    required   pam_loginuid.so' /etc/pam.d/cron

# Copies base scripts and configs
COPY base/image-files/ /

# Build argument: Folder with image files
ARG IMAGE_FILES_FOLDER

# Copies custom scripts and configs
COPY $IMAGE_FILES_FOLDER/image-files/ /

# Build argument: Debug mode — enables debug info display (it should be disable on production!)
ARG DEBUG_MODE=1

# Enables or disables PHP error reporting depending on `DEBUG_MODE`
RUN if [ "$DEBUG_MODE" = 0 ]; then sed -i '/display_errors = On/cdisplay_errors = Off' /usr/local/etc/php/conf.d/base-custom.ini; fi

# Build argument: Build environment (dev, prod, test)
ARG BUILD_ENV=dev

# Application environment for both Symfony 3.x and 4.x
ENV APPLICATION_ENV=$BUILD_ENV
ENV APP_ENV=$BUILD_ENV
ENV SYMFONY_ENV=$BUILD_ENV

# Sets permissions for scripts
RUN chmod +x /build-scripts/build
RUN chmod +x /usr/local/bin/*

# Builds the source code for production environment
RUN /build-scripts/build

# Starts the app
CMD ["app-bootstrap"]
