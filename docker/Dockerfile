FROM ubuntu:16.04

RUN apt-get update
RUN apt-get install apache2 git vim -y
RUN apt install php libapache2-mod-php -y
#sed -i 's/80/8080/' /etc/apache2/ports.conf
RUN apt-get install php-curl curl php-xml -y

WORKDIR /var/www/html
RUN rm index.html
RUN git clone https://github.com/REDignus/web-server.git ./

RUN a2enmod rewrite

COPY registry.conf /etc/apache2/conf-enabled

EXPOSE 80
CMD apachectl -D FOREGROUND
