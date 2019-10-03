//Подключаем API Google captcha v3
<script src="https://www.google.com/recaptcha/api.js?render=_YOUR_SITE_CODE_"></script>

//Верстка формы
<div id="popup-block">
    <div class="popup-bg" onclick="openbox('popup-block'); return false;"></div>
    <div class="block">
        <div class="position">
            <div class="close" onclick="openbox('popup-block'); return false;">×</div>
            <div class="title">Заказать услугу</div>
            <form class="contact-form">
                <input type="hidden" class="form-control" id="popupFormType" name="messageType" value="Запрос с сайта">
                <div class="form-group">
                    <input type="text" class="form-control" id="popupBackFormName" data-validate name="Name" placeholder="Ваше имя*">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="popupBackFormPhone" data-validate name="Phone" placeholder="Телефон*">
                </div>
                <div class="form-group">
                    <textarea class="form-control" id="popupBackFormText" data-validate name="Text" placeholder="Что будем ремонтировать?*"></textarea>
                </div>
                <div class="form-group mb-2">
                    <input type="checkbox" id="popupBackFormPolicy" name="popupBackFormPolicy" value="popupBackFormPolicy" class="checkbox" checked>
                    <label for="popupBackFormPolicy"><a href="/policy.php" target="_blank">Нажимая кнопку "Отправить" я соглашаюсь с политикой конфиденциальности</a></label>
                </div>
                <div class="form-group">
                    <input id="g-recaptcha-response-popup" class="g-recaptcha-response" name="g-recaptcha-response" hidden>
                    <button type="submit" id="popupBackForm" onclick="event.preventDefault(); sendPopupFormData(this.id)" class="btn btn-default">Отправить</button>
                </div>
                <div id="popupBackFormRes"></div>
            </form>
        </div>
    </div>
</div>

<script>
    // Получаем секретный ключ с сервера гугл и подсталяем в скрытое поле для передачи в форме
    grecaptcha.ready(function() {
        grecaptcha.execute('_YOUR_SITE_CODE_', {action: 'homepage'}).then(function(token) {
            document.getElementById('g-recaptcha-response-popup').value=token;
        });
    });
    // Получаем значения из формы и делаем проверку на заполненные поля
    function sendPopupFormData(id) {

        let yourName = $('#popupBackFormName').val();
        let yourPhone = $('#popupBackFormPhone').val();
        let yourText = $('#popupBackFormText').val();

        if (yourName.length == 0){
            $('#popupBackFormName').attr("placeholder", "поле обязательно для заполнения");
            return false;
        }

        if (yourPhone.length == 0){
            $('#popupBackFormPhone').attr("placeholder", "поле обязательно для заполнения");
            return false;
        }

        if (yourText.length == 0){
            $('#popupBackFormText').attr("placeholder", "поле обязательно для заполнения");
            return false;
        }

        if (!$('#popupBackFormPolicy').is(':checked')) {
            $('#popupBackFormRes').html('Пожалуйста согласитесь с политикой конфиденциальности');
            return false;
        }
// Пока идет обработка запроса показываем лоадер
        $('#popupBackFormRes').html('<img src="loading.gif">');

        let formType = $('#popupFormType').val();
        let gRecaptchaResponse = $('#g-recaptcha-response-popup').val();

// Отправляем запрос в обработчик через AJAX
        BX.ajax({
            url: '/local/include/contacts_forms/forms_handler.php',
            data: {
                'messageType' : formType,
                'Name': yourName,
                'Phone' : yourPhone,
                'Text' : yourText,
                'gRecaptchaResponse' : gRecaptchaResponse,
            },
            method: 'POST',
            dataType: 'html',
            // Подставляем результат выполнения формы
            onsuccess: function (data) {
                $('#popupBackFormRes').html(data);
            },
        });
    }

</script>