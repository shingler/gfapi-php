FROM shingler/php7_laravel_nginx:v2
MAINTAINER singlerwong@qq.com
RUN apt-get install -y vim
VOLUME /var/www
WORKDIR /var/www
COPY gfapi.conf /etc/nginx/conf.d
EXPOSE 8100
CMD /etc/init.d/nginx start & /etc/init.d/php7.0-fpm start & /bin/bash
