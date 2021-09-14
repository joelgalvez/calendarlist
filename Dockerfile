FROM node:14-alpine AS build

WORKDIR /var/www/html/

# -- copy source files
COPY . .

# -- env variables
ENV VUE_APP_TITLE "Calendarlist"

# -- build the app
RUN npm install
RUN npm run build



FROM php:8-apache AS production

WORKDIR /var/www/html/

RUN docker-php-ext-enable opcache

# -- copy over the build, no need to keep node_modules, source files, etc
COPY --from=build /var/www/html/dist .

# -- copy over server files
COPY src/server/proxy.php proxy.php
COPY src/server/calendars.json calendars.json

# -- create cache folder and set permissions
RUN mkdir cache
RUN chown www-data:www-data cache

# -- expose port
EXPOSE 80