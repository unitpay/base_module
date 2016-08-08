1. Создайте в БД таблицу unitpay_payments:

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

В эту таблицу будет логироваться информация о проводимых платежах

2. Разместите скрипты в произвольной директории вебсервера,
в которую есть доступ из интернета. Убедитесь, что на сервере установлен php версии 5.x.x или выше, а также
доступно расширение mysqli (для работы с бд mysql).

3. Укажите в config.php параметры соединения с БД, стоимость одной единицы товара (предмета) и серкетный ключ
(секретный ключ можно найти в настройках проекта в личном кабинете unitpay.ru).

4. В личном кабинете unitpay.ru в настройках проекта укажите адрес обработчика, в данном случае 
это абсолютный урл по которому доступен index.php