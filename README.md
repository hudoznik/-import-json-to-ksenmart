В данном решении нужно файлы картинок отправить в каталог с картинками вручную или каким нибудь скриптом.

Добавить поле 'guid' в #__ksenmart_products или в коде заменить на то по которому будет сравниваться.

URL для отправки
http://example.com/index.php?option=com_ksenmart&task=load1c.
Метод post
Headers
content-type:application/json
accept:application/json
authorization: token

<h2>Token генерируется динамически от даты</h2>

Для теста токен можно узнать раскомментировав содержимое getTokensTest() и поправив кодовую фразу


Пример отправляемых данных

Закрытие сайта
<code>
    {
        "type": "closesite"
    }
</code>

Открытие
<code>
    {
        "type": "clearstock"
    }
</code>

Отправка данных о продукте
<pre>
    {
    "type": "products",
    "row": [
        {
            "title": "Ecola DL1662 MR16",
            "guid": "2d6c2142-207d-11e8-db81-0022b050586a",
            "in_stock": "7",
            "price": "250",
            "product_code": "123",
            "promotion": 0,
            "categories": "Освещение;Светильники точечные",
            "manufacturer": "Ecola",
            "properti": [
                {
                "s": "МОЩНОСТЬ",
                "z": "850 Вт"
                },
                {
                "s": "ТИП ПАТРОНА",
                "z": "ключевой"
                },
                {
                "s": "МАХ ДИАМЕТР СВЕРЛЕНИЯ (металл)",
                "z": "13 мм"
                },
                {
                "s": "ВЕС",
                "z": "2,4 кг"
                }
            ],
            "files": "c7873bd8-4aab-11e8-3b89-0022b050586a.jpeg"
        },
        {
            "title": "Панель АКВ полноцветная",
            "guid": "a37cd518-a61b-11e5-cf95-448a5b29c303",
            "in_stock": "0",
            "price": "1470",
            "product_code": "564",
            "promotion": 0,
            "categories": "Панели стеновые",
            "manufacturer": "АКВАТОН",
            "files": "71147ded-ec5d-11e7-fc8f-0022b050586a.jpeg"
        }
    }
</pre>
