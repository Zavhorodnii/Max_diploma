$(document).ready(function () {
    var submitStart = $('.choose .submit');
    var mask = $('.mask');
    var name = $('#name');
    var chose = $('.choose');

    submitStart.on('click', function () {
        mask.slideUp(1000);
        chose.slideUp(600);
        $('#nameH1').text(name.val());
    });


    $('.stage-item').on('click', function () {
        $('.stage-item').css('pointer-events', 'none');
        $(this).addClass('is-active');
        $('#nameH1-2').text($(this).text());
        $('.title-stage').fadeIn(500);
        $('.subject').fadeIn(500);
    });
    var i = $('.subject').children('.subject-line').length + 1;
    $('.subject-line').on('click', function () {
        i = i-1;
        $(this).find('.ball').text(i);
        $(this).css('pointer-events', 'none');
        $(this).addClass('is-active');

        if ( i == 1 ) {
            $('.result').fadeIn(500);
        }
    });

    $('.reg-btn').on('click', function () {
        $(this).toggleClass('is-active');
        $('.reg-btn').not($(this)).removeClass('is-active');
    });

    $('.js_appraisals').on('click', function () {
        $('.error_message').text('');

        let map = new Map();

        let $items = $('.subject').children('.subject-line');

        $items.each (
            function(index) {
                map.set($(this).attr('data-id_groups_subjects'), $(this).find('.ball').text())
            }
        );
        let __appraisals = JSON.stringify(Object.fromEntries(map));

        let data = new FormData();

        data.append('action', 'appraisals');
        data.append('appraisals', __appraisals);

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (data) {
                // console.log('result: ' + data.result);
                // console.log('result: ' + data)
                if(data.result === 'ok'){
                    $('.error_message').text('Результатыи успішно надіслано');
                    setTimeout(function () {
                        window.location.href ='/';
                    }, 2000);
                }
                if(data.result === 'error vote'){
                    $('.error_message').text('Голосування заборонено');
                    setTimeout(function () {
                        window.location.href ='/';
                    }, 2000);
                }
                if(data.result !== 'error vote' && data.result !== 'ok'){
                    $('.error_message').text('Під час відправки трапилась помилка');
                }
            },
            error: function (jqXHR, status, errorThrown) {
                console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
            }
        })


    });

    $('#btn-result').on('click', function () {

        $(this).addClass('is-active');
        $('#next').fadeIn(500);
        setTimeout(function () {
            location.reload();
        }, 5000);
    });

    $('#log-in').on('click', function () {

        if ($('.sing-in').hasClass('show')){
            $('.sing-in').removeClass('show');
            gsap.to($('.sing-in'), {
                y: 0,
            });
        }

        if ($('.log-in').hasClass('show')){
            $('.log-in').removeClass('show');
            gsap.to($('.log-in'), {
                y: 0,
            });
        } else {
            gsap.to($('.log-in'), {
                y: 30,
                ease: "bounce",
                duration: .7,
            });
            $('.log-in').addClass('show');
        }
    });


    $('#sing-in').on('click', function () {

        if ($('.log-in').hasClass('show')){
            $('.log-in').removeClass('show');
            gsap.to($('.log-in'), {
                y: 0,
            });
        }

        if ($('.sing-in').hasClass('show')){
            $('.sing-in').removeClass('show');
            gsap.to($('.sing-in'), {
                y: 0,
            })
        } else {
            gsap.to($('.sing-in'), {
                y: 30,
                ease: "bounce",
                duration: .7,
            });
            $('.sing-in').addClass('show');
        }
    });


    $('.js_register').click(function(event){
        event.preventDefault();
        let full_name = $(this).closest('.js_get_info').find('.js_full_name');
        let password = $(this).closest('.js_get_info').find('.js_password');
        let repeat_password = $(this).closest('.js_get_info').find('.js_repeat_password');
        let student_id = $(this).closest('.js_get_info').find('.js_student_id');
        let error_student_id = $(this).closest('.js_get_info').find('.js_error_student_id');
        let group = $(this).closest('.js_get_info').find('.js_group')
        let error_group = $(this).closest('.js_get_info').find('.js_error_group')
        let create_account = $(this).closest('.js_get_info').find('.js_success_create_account')

        error_student_id.text('');
        error_group.text('');

        if(full_name.val().length < 5) {
            full_name.addClass('error').removeClass('is-current');
            return;
        } else
            full_name.removeClass('error').addClass('is-current');

        if(password.val().length < 5){
            password.addClass('error').removeClass('is-current');
            return;
        } else
            password.removeClass('error').addClass('is-current');

        if(password.val() !== repeat_password.val()){
            $(this).closest('.js_get_info').find('.js_error_repeat_password').text("" +
                "Пароль не вірно повторено");
            password.addClass('error');
            repeat_password.addClass('error').val('');
            return;
        }else {
            $(this).closest('.js_get_info').find('.js_error_repeat_password').text('');
            password.removeClass('error').addClass('is-current');
            repeat_password.removeClass('error').addClass('is-current');
        }

        if(student_id.val().length < 5){
            student_id.addClass('error').removeClass('is-current');
            return;
        } else
            student_id.removeClass('error').addClass('is-current');


        if(group.val().length < 1){
            group.addClass('error').removeClass('is-current');
            return;
        } else
            group.removeClass('error').addClass('is-current');

        let data = new FormData();

        data.append('action', 'register');
        data.append('full_name', full_name.val());
        data.append('password', password.val());
        data.append('student_id', student_id.val());
        data.append('group', group.val());

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (data) {
                // console.log('result: ' + data.result);
                if(data.result === 'ok') {
                    create_account.text('Акаунт успішно створено');
                    $('.input').val('');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                }
                if(data.result === 'student exist'){
                    student_id.addClass('error').removeClass('is-current');
                    error_student_id.text('Акаунт з введеним студенським квитком вже існує');
                }
                if(data.result === 'group not found'){
                    group.addClass('error').removeClass('is-current');
                    error_group.text('Групу не знайдено');
                }
            },
            error: function (jqXHR, status, errorThrown) {
                console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
            }
        })
    });


    $('.js_login').click(function (event) {
        event.preventDefault();
        let full_name = $(this).closest('.js_get_info').find('.js_full_name');
        let password = $(this).closest('.js_get_info').find('.js_password');
        let student_id = $(this).closest('.js_get_info').find('.js_student_id');
        let error_login = $(this).closest('.js_get_info').find('.js_error_login');

        error_login.text('');

        if(full_name.val().length < 5) {
            full_name.addClass('error').removeClass('is-current');
            return;
        } else
            full_name.removeClass('error').addClass('is-current');

        if(password.val().length < 5){
            password.addClass('error').removeClass('is-current');
            return;
        } else
            password.removeClass('error').addClass('is-current');

        if(student_id.val().length < 5){
            student_id.addClass('error').removeClass('is-current');
            return;
        } else
            student_id.removeClass('error').addClass('is-current');

        let data = new FormData();

        data.append('action', 'login');
        data.append('full_name', full_name.val());
        data.append('password', password.val());
        data.append('student_id', student_id.val());

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (data) {
                // console.log('result: ' + data.result);
                if(data.result === 'error login'){
                    error_login.text('Студента не знайдено');
                    setTimeout(function () {
                        error_login.text('');
                    }, 2000);
                    $('.input').val('');
                    $('.input').removeClass('is-current');

                }
                if(data.result === 'error vote'){
                    error_login.text('Голосування заборонено');
                    setTimeout(function () {
                        error_login.text('');
                    }, 2000);
                    $('.input').val('');
                    $('.input').removeClass('is-current');
                }
                if(data.result === 'admin'){
                    $('.input').val('');
                    location.href = 'admin.php';
                }
                if(data.result === 'ok'){
                    $('.input').val('');
                    location.href = 'start_test.php';
                }
            },
            error: function (jqXHR, status, errorThrown) {
                console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
            }
        })
    });

    $('.js_next_stage').click(function (event) {
        let group = $(this).closest('.js_find_info').attr('data-group');
        let data = new FormData();
        let this__ = $(this);

        data.append('action', 'next_stage');
        data.append('group', group);

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (data) {
                // console.log('result: ' + data.result);
                if(data.result === 'ok'){
                    this__.closest('.js_find_info').children('.js_info_message').text('Групі ' + group + ' дозволено голосування')
                    setTimeout(function () {
                        this__.closest('.js_find_info').children('.js_info_message').text('');
                    }, 2000);
                } else{
                    this__.closest('.js_find_info').children('.js_info_message').text('Відбулась помилка у відправці запиту')
                    setTimeout(function () {
                        this__.closest('.js_find_info').children('.js_info_message').text('');
                    }, 2000);
                }
            },
            error: function (jqXHR, status, errorThrown) {
                console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
            }
        })
    })

    $('.js_clear_app').click(function (event) {
        let group = $(this).closest('.js_find_info').attr('data-group');
        let data = new FormData();
        let this__ = $(this);

        data.append('action', 'clear_app');
        data.append('group', group);

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (data) {
                // console.log('result: ' + data.result);
                if(data.result === 'ok'){
                    this__.closest('.js_find_info').children('.js_info_message').text('Групі ' + group + ' очищено результати голосування')
                    setTimeout(function () {
                        this__.closest('.js_find_info').children('.js_info_message').text('');
                    }, 2000);
                    this__.closest('.js_clear_info').find('[data-clear-app=' + group + ']').text('0');
                } else{
                    this__.closest('.js_find_info').children('.js_info_message').text('Відбулась помилка у відправці запиту')
                    setTimeout(function () {
                        this__.closest('.js_find_info').children('.js_info_message').text('');
                    }, 2000);
                }
            },
            error: function (jqXHR, status, errorThrown) {
                console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
            }
        })
    })

    $('.js_save_passing').click(function (event) {
        let group = $(this).closest('.js_find_info').attr('data-group');
        let save = $(this).closest('.js_find_info').children('.js_paste_passing').val();
        let data = new FormData();
        let this__ = $(this);


        data.append('action', 'save_passing');
        data.append('group', group);
        data.append('save', save);

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (data) {
                // console.log('result: ' + data.result);
                if(data.result === 'ok'){
                    this__.closest('.js_find_info').children('.js_info_message').text('Групі ' + group + ' встановлено новий поріг')
                    setTimeout(function () {
                        this__.closest('.js_find_info').children('.js_info_message').text('');
                    }, 2000);
                } else{
                    this__.closest('.js_find_info').children('.js_info_message').text('Відбулась помилка у відправці запиту')
                    setTimeout(function () {
                        this__.closest('.js_find_info').children('.js_info_message').text('');
                    }, 2000);
                }
            },
            error: function (jqXHR, status, errorThrown) {
                console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
            }
        })
    })

    $('.js_logout').click(function (event) {
        let data = new FormData();
        data.append('action', 'logout');

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (data) {
                // console.log('result: ' + data.result);
                if(data.result === 'ok')
                    window.location.href ='/';
            },
            error: function (jqXHR, status, errorThrown) {
                console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
            }
        })
    })

    $('.nav-item .nav-link').on('click', function () {
        if ($('.nav-item .nav-link').hasClass('active')){
            $('.nav-item .nav-link').removeClass('active');
            var i = $(this).parent().index();
        }
        $(this).addClass('active');
        $('.nav-content').hide();
        $('.nav-content:eq('+ i +')').show();
    });

    $('.group-item').on('click', function () {
        if ($('.group-item').not($(this)).hasClass('active')){
            $('.group-item').removeClass('active');
            $('.group-item').next('.group-result').slideUp();
        }
        $(this).toggleClass('active');
        $(this).next('.group-result').slideToggle();
    });

});