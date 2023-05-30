if (!jQuery('.scw-cookie').hasClass('scw-cookie-out')) {
    jQuery(document).find('body').addClass('scw-cookie-in');
}

function isqCookieHide()
{
    jQuery.post(
        window.gdprCookieDir+'ajax.php',
        {
            action : 'hide'
        }
    ).done(function(data){
        if (data.hasOwnProperty('success') && data.success) {
            jQuery('.scw-cookie').addClass('scw-cookie-slide-out');
            jQuery(document).find('body').removeClass('scw-cookie-in');            
        }

        if (jQuery('.scw-cookie').hasClass('changed')) {
            location.reload();
        }
    });
}

function isqCookieDetails()
{
    jQuery('.scw-cookie-details').slideToggle();
}

function scwCookieToggle(element)
{
    jQuery(element).closest('.scw-cookie-toggle').find('input[type="checkbox"]').click();
}

function isqCookiePanelToggle()
{
    jQuery('.scw-cookie').removeClass('scw-cookie-out');
    if (jQuery(document).find('body').hasClass('scw-cookie-in')) {
        jQuery('.scw-cookie').addClass('scw-cookie-slide-out');
        jQuery(document).find('body').removeClass('scw-cookie-in');
    } else {
        jQuery('.scw-cookie').removeClass('scw-cookie-slide-out');
        jQuery(document).find('body').addClass('scw-cookie-in');
    }
}

jQuery(document).ready(function($){
    $('.scw-cookie-switch input').each(function(){
        if ($(this).prop('checked')) {
            $(this).closest('.scw-cookie-switch').addClass('checked');
        } else {
            $(this).closest('.scw-cookie-switch').removeClass('checked');
        }
    });
});
jQuery(document).on('change', '.scw-cookie-toggle input[type="checkbox"]', function(){
    jQuery(this).closest('.scw-cookie').addClass('changed');
    jQuery(this).closest('.scw-cookie-switch').toggleClass('checked');
    jQuery.post(
        window.gdprCookieDir+'ajax.php',
        {
            action : 'toggle',
            name   : jQuery(this).attr('name'),
            value  : jQuery(this).prop('checked')
        }
    ).done(function(data){
        if (data.hasOwnProperty('removeCookies')) {
            jQuery.each(data.removeCookies, function(key, cookie){
                Cookies.remove(cookie.name);
                Cookies.remove(cookie.name, { domain: cookie.domain });
                Cookies.remove(cookie.name, { path: cookie.path });
                Cookies.remove(cookie.name, { domain: cookie.domain, path: cookie.path });
            });
        }
    });
});

jQuery(document).ready(function($){
    $('.scw-cookie-tooltip-trigger').hover(function(){
        var label = $(this).attr('data-label');
        $(this).append('<span class="scw-cookie-tooltip">'+label+'</span>');
    }, function(){
        $(this).find('.scw-cookie-tooltip').remove();
    });
});

jQuery(document).ready(function($){
    jQuery.post(
        window.gdprCookieDir+'ajax.php',
        {
            action : 'load',
        }
    ).done(function(data){
        if (data.hasOwnProperty('removeCookies')) {
            jQuery.each(data.removeCookies, function(key, cookie){
                Cookies.remove(cookie.name);
                Cookies.remove(cookie.name, { domain: cookie.domain });
                Cookies.remove(cookie.name, { path: cookie.path });
                Cookies.remove(cookie.name, { domain: cookie.domain, path: cookie.path });
            });
        }
    });
});
