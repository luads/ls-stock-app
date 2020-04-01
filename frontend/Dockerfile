FROM node:12.6.0-alpine

ARG DEV
ENV PATH /app/node_modules/.bin:$PATH

WORKDIR /app

RUN apk update
RUN apk add python

COPY package.json /app/package.json
COPY package-lock.json /app/package-lock.json

RUN npm ci
RUN npm i -g serve

COPY . ./

RUN if [ "$DEV" != true ]; then \
    npm run build; \
    fi

ENTRYPOINT ["sh", "/app/bin/run.sh"]
