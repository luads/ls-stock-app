FROM node:12.6.0-alpine

WORKDIR /app

ENV PATH /app/node_modules/.bin:$PATH

RUN apk update
RUN apk add python

COPY package.json /app/package.json
COPY package-lock.json /app/package-lock.json

RUN npm ci
RUN npm i -g serve

COPY . ./

RUN npm run build

ENTRYPOINT ["sh", "/app/bin/run.sh"]
