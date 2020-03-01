##################
### Nginx images for development and production
##################

# Build argument: Image tag
ARG IMAGE_TAG=7.2-fpm

### Stage 1: Development image

# Base image
FROM nginx:latest as nginx-dev

# Maintainer
MAINTAINER Andrew Sinukov  "andrew.sinukov.it@gmail.com"

# Installs curl
RUN apt-get update && apt-get install curl -y

# Copies scripts and configs
COPY nginx/image-files/ /

# Sets permissions for scripts
RUN chmod +x /docker-entrypoint.sh

# Changes entrypoint
ENTRYPOINT ["/docker-entrypoint.sh"]

# Starts Nginx
CMD ["nginx", "-g", "daemon off;"]

### Stage 2: Interim builder image for warming-up

# Base image
FROM yiisoftware/yii2-php:$IMAGE_TAG as builder

# Installs sockets extension
RUN docker-php-ext-install sockets

# Copies base scripts and configs
COPY base/image-files/ /

# Copies core scripts and configs
COPY core/image-files/ /

# Copies initial source files
COPY --from=nginx-dev /app /app

# Build argument: Build environment (dev, prod, test)
ARG BUILD_ENV=prod

# Application environment for both Symfony 3.x and 4.x
ENV APPLICATION_ENV=$BUILD_ENV
ENV APP_ENV=$BUILD_ENV
ENV SYMFONY_ENV=$BUILD_ENV

# Sets permissions for scripts
RUN chmod +x /build-scripts/build
RUN chmod +x /usr/local/bin/*

# Builds the source code for production environment
RUN /build-scripts/build

### Stage 3: Production image

# Base image
FROM nginx-dev as nginx-prod

# Copies warmed-up source files
COPY --from=builder /app /app
