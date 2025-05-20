<!-- Страница FAQ  -->
<?php require 'config.php'; ?>
<?php include 'header.php'; ?>
<link rel="stylesheet" type="text/css" href="CSS/about.css">
<link rel="stylesheet" type="text/css" href="CSS/faq.css">
<div class="container mt-4">
    <div class="legla">
    <h2>Ответы на вопросы</h2>
    <h4>📦 О заказах и доставке</h4>

        <div class="faq-item">
            <div class="faq-question">Как я могу оформить заказ на вашем сайте?</div>
            <div class="faq-answer">Чтобы оформить заказ, просто добавьте нужные товары в корзину, перейдите к оформлению, укажите контактные данные, выберите способ оплаты — и подтвердите заказ.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Нужно ли создавать аккаунт для того, чтобы сделать заказ?</div>
            <div class="faq-answer">Нет, регистрация не обязательна, но она даст вам доступ к истории заказов, возможности оставлять отзывы к товарам, сохранению избранного.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Сколько стоит доставка и от чего зависит её стоимость?</div>
            <div class="faq-answer">Стоимость доставки фиксирована по всей России, её сумма 399 руб. От 2000 рублей стоимость доставки бесплатна.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">В какие сроки осуществляется доставка?</div>
            <div class="faq-answer">Сроки зависят от вашего региона: в среднем от 3 до 7 рабочих дней.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Что делать, если товар пришёл повреждённым или с браком?</div>
            <div class="faq-answer">Сфотографируйте проблему и свяжитесь с нами через поддержку — мы либо заменим товар, либо вернём деньги.</div>
        </div>

    <h4>💳 Оплата и безопасность</h4>

        <div class="faq-item">
            <div class="faq-question">Какие способы оплаты вы принимаете?</div>
            <div class="faq-answer">Мы принимаем оплату картами, а также наличными при получении.</div>
        </div>

    <h4>📦 Возврат и обмен</h4>

        <div class="faq-item">
            <div class="faq-question">Как я могу вернуть или обменять товар?</div>
            <div class="faq-answer">Напишите нам в течение 14 дней с момента получения, укажите причину, приложите фото — и мы согласуем возврат или обмен.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Какие товары не подлежат возврату или обмену?</div>
            <div class="faq-answer">По закону возврату не подлежат товары личной гигиены, нижнее бельё и товары с повреждённой упаковкой.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Кто оплачивает доставку при возврате товара?</div>
            <div class="faq-answer">Если товар с браком — мы. В остальных случаях — покупатель.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Как быстро возвращаются деньги после возврата товара?</div>
            <div class="faq-answer">Обычно в течение 3–5 рабочих дней после получения и проверки возвращённого товара.</div>
        </div>

    <h4>👤 Личный кабинет и аккаунт</h4>

        <div class="faq-item">
            <div class="faq-question">Как зарегистрироваться на сайте?</div>
            <div class="faq-answer">Нажмите "Регистрация" вверху сайта, укажите требуемые данные. Подтвердите регистрацию — и всё готово.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Как я могу изменить свои персональные данные в профиле?</div>
            <div class="faq-answer">Зайдите в личный кабинет и выберите "Редактировать профиль" — вы сможете обновить имя, адрес и контактные данные.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Можно ли полностью удалить свой аккаунт?</div>
            <div class="faq-answer">Да, в личном кабинете есть кнопка удаления. После удаления данные вашего аккаунта будут полностью удалены.</div>
        </div>

    <h4>💬 Прочее</h4>

        <div class="faq-item">
            <div class="faq-question">Где я могу почитать отзывы о товарах?</div>
            <div class="faq-answer">Отзывы находятся внизу карточки товара, вы можете их почиать, и составить своё мнение о товаре.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Как можно связаться с вашей службой поддержки?</div>
            <div class="faq-answer">Вверху сайта есть кнопка "Контакты", перейдя по ней, вы сможете связаться с нами любым удобным для вас способом.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">У вас есть физические магазины или пункты самовывоза?</div>
            <div class="faq-answer">Увы, но ни физических магазинов, ни ПВЗ у нас пока нет, но мы работаем над этим. На данный момент в нашем магазине есть только курьерская доставка.</div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Можно ли заказать товар с подарочной упаковкой?</div>
            <div class="faq-answer">Наш магазин предоставляет товары только в заводской упаковке.</div>
        </div>

    </div>
</div>

<script>
  document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
      const parent = question.parentElement;
      parent.classList.toggle('open');
    });
  });
</script>

</body>
</html>
