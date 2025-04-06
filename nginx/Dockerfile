FROM nginx:latest

RUN apt-get update && apt-get install -y nano

COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf

COPY ./public /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
