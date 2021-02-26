
# Содержимое папки /local/

Пример "ленивой" подгрузки блока - реализация загрузки компонента через ajax.

## Реализация

Загрузка блока "Написать нам" на странице контактов: в простом случае достаточно заменить вызов компонента

https://github.com/whateveruse/bitrix-lazy-load-staff-block-example/blob/master/_public/sect_block_contact_us.php

Загрузка блока "Ваш персональный менеджер" на детальной странице объекта:

https://github.com/whateveruse/bitrix-lazy-load-staff-block-example/blob/master/components/citrus/template/templates/lazy/script.es6.js#L27

## Теория

https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2192&LESSON_PATH=3913.3516.5062.3750.2192

https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=14014&LESSON_PATH=3913.3516.5062.3750.14014

https://dev.1c-bitrix.ru/api_d7/bitrix/main/httpresponse/component.php
