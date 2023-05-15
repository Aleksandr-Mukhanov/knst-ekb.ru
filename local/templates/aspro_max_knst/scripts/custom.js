$( document ).ready(function() {
   if ($('.add2basket').length) add2basket();
   if ($('.send_form').length) send_form();
   if ($('.cities-modal').length) cities_modal();
});

function cities_modal()
{
    $('.cities-modal .search_form input').on('keyup', function(){
        var q = $(this).val();
        if (q != '')
        {
            $('.cities-modal__list ul li').each(function(){
                var city = $(this);
                var word = $('a', city).html();
                if (word.includes(q))
                {
                    $(city).show();
                } else {
                    $(city).hide();
                }
            });
        } else {
            $('.cities-modal__list ul li').show();
        }
    });
}

function send_form()
{
    $('.send_form').on('submit', function(){
        var action = '/local/ajax/form.php';
        var this_form = $(this);
        var flag_submit = true;

        $('input.required', this_form).each(function () {
            if ($(this).val() == '')
            {
                flag_submit = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });

        alert('submit');

        if (!flag_submit) return false;

        $.ajax({
            url: action,
            method: 'post',
            type: "POST",
            data: new FormData(this_form.get(0)),
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                alert('Форма отправлена');
            }
        });

        return false;
    });
}

function add2basket()
{
    $('a.add2basket').on('click', function(){
        var this_link = $(this);
        var product_id = $(this).attr('data-id');

        $.ajax({
            type: 'post',
            url: '/local/ajax/add2basket.php',
            data: 'action=add&product_id='+product_id,
            success: function(res) {
                $(this_link).addClass('active');
                $(this_link).html('В корзине');
            }
        });

        return false;
    });
}