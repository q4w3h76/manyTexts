# manyTexts

A small educational project on Laravel. 

REST API application for creating texts. 
Authorization is made with Sanctum, images are uploaded to s3, texts are deleted after a while using queues, texts are cached in Redis, sending messages to mail is made (also through queues)

Simple functional tests have been written

Postman was used for documentation

Docker is used for deployment
```
git clone https://github.com/q4w3h76/manyTexts
cd manyText
cat .env.example >> .env
docker-compose up -d
docker exec -it Laravel_app make init
```
