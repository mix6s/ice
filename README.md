ICE
========================

Команды
--------------
Создание админа
 
`php bin/console fos:user:create admin`

`php bin/console fos:user:promote dev@dev.ru ROLE_ADMIN`

Обновление схемы бд

`php bin/console doctrine:schema:update --force`

отправка писем:

`php bin/console swiftmailer:spool:send --env=prod`


build:

`npm install`
`bower install`
`gulp rename`
`gulp build`
