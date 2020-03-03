## Требования
1. MySQL сервер
2. PHP версии 5.* или выше
3. Установленное PHP расширение mysqli (для работы с бд mysql)

## 1. Создайте таблицу в базе данных, используя следующий запрос
```sql
CREATE TABLE IF NOT EXISTS `unitpay_payments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `unitpayId` varchar(255) NOT NULL,
  `account` varchar(255) NOT NULL,
  `sum` float NOT NULL,
  `itemsCount` int(11) NOT NULL DEFAULT '1',
  `dateCreate` datetime NOT NULL,
  `dateComplete` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
```
В эту таблицу будет записываться информация о транзакциях
![image](https://cloud.githubusercontent.com/assets/9200174/21964771/3f44eea0-db5b-11e6-87d4-85265bd33eab.png)


## 2. Разместите файлы скрипта в любой директории веб сервера
На скриншоте используется корневая директория домена, но это не обязательно.
![image](https://cloud.githubusercontent.com/assets/9200174/21964858/86e03822-db5c-11e6-9c64-bb6641545b94.png)

## 3. Настройте скрипт через config.php
Укажите в нем данные для подключения к созданной базе данных, стоимость одной единицы товара (предмета) и секретный ключ, который можно найти в настройках проекта в личном кабинете UnitPay

## 4. В личном кабинете укажите адрес обработчика
![image](https://cloud.githubusercontent.com/assets/9200174/21965163/c38f595a-db62-11e6-816d-27b5efe94a36.png)
