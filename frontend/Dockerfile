FROM node:lts-slim

RUN apt update && apt install -y git zip unzip nginx

ARG BUILD=prod

ENV NODE_ENV ${BUILD}

COPY --chown='node:node' . /workspace/frontend

WORKDIR /workspace/frontend

RUN npm install -g @angular/cli

RUN set -eux; \
    if [ "${BUILD}" = "prod" ]; \ 
    then \
        npm install; \
        ng build; \
    fi

COPY nginx.conf /etc/nginx/sites-enabled/default

COPY startup.sh /usr/local/bin/startup.sh

RUN chmod +x /usr/local/bin/startup.sh

CMD ["/usr/local/bin/startup.sh"]