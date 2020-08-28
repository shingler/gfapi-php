FROM shingler/php7_laravel_nginx:v2
MAINTAINER singlerwong@qq.com
# 安装vim，设置时区
RUN apt-get install -y vim \
    && ln -snf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
    && echo 'Asia/Shanghai' > /etc/timezone
VOLUME /var/www
WORKDIR /var/www
COPY gfapi.conf /etc/nginx/conf.d
EXPOSE 8100
CMD /etc/init.d/nginx start & /etc/init.d/php7.0-fpm start & /bin/bash
