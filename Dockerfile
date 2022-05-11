FROM webdevops/php-nginx:7.4 as abr-api

ARG CONSUL_TEMPLATE_VERSION=0.19.5

ENV DEBIAN_FRONTEND=noninteractive

COPY . /app

ADD 	 https://releases.hashicorp.com/consul-template/${CONSUL_TEMPLATE_VERSION}/consul-template_${CONSUL_TEMPLATE_VERSION}_linux_amd64.zip /usr/bin/
RUN 	 unzip /usr/bin/consul-template_${CONSUL_TEMPLATE_VERSION}_linux_amd64.zip && \
    	 mv consul-template /usr/local/bin/consul-template && \
    	 rm -rf /usr/bin/consul-template_${CONSUL_TEMPLATE_VERSION}_linux_amd64.zip

RUN  cp /app/docker/config/nginx/sites-enabled/app1.conf /opt/docker/etc/nginx/vhost.conf \
     && cp  /app/docker/config/php/fpm/application.conf /opt/docker/etc/php/fpm/pool.d/application.conf

RUN bash /app/docker/build/clear.sh

EXPOSE 80

VOLUME ["/app/storage/logs", "/var/log/nginx", "/var/log/php"]

WORKDIR /app
