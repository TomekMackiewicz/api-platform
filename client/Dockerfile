#FROM node:16

# USER node

# RUN mkdir /home/node/.npm-global
# ENV PATH=/home/node/.npm-global/bin:$PATH
# ENV NPM_CONFIG_PREFIX=/home/node/.npm-global

# ENV HOME=/home/node

# WORKDIR $HOME/app

# RUN npm i -g npm

# RUN npm install -g @angular/cli && npm cache clean --force

# EXPOSE 4200 49153

# CMD [ "node" ]

FROM node:16
WORKDIR /app
COPY . .

#RUN apt-get update && apt-get upgrade -y && apt-get install -y npm
RUN npm install
#RUN npm install -g @angular/cli
RUN npm run build --prod

FROM nginx:alpine as client_nginx
COPY --from=node /app/dist/client /usr/share/nginx/html