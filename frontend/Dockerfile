FROM node:lts-slim

ARG BUILD=prod

ENV NODE_ENV ${BUILD}

RUN npm install -g @angular/cli

RUN apt update && apt install -y git zip unzip nginx

COPY nginx.conf /etc/nginx/sites-enabled/default

COPY startup.sh /usr/local/bin/startup.sh

RUN chmod +x /usr/local/bin/startup.sh

CMD ["/usr/local/bin/startup.sh"]