jQuery(document).ready(function($){
    var activeTab = window.location.hash.substr(1);
    activeTab = activeTab == '' ? 'generic' : activeTab;
    $('#'+activeTab).addClass('gdpr-tab-active');
    $('.gdpr-nav-tab[href="#'+activeTab+'"]').addClass('gdpr-nav-tab-active');    

    $('.gdpr-nav-tab').click(function(){
        $('.gdpr-nav-tab').removeClass('gdpr-nav-tab-active');
        $(this).addClass('gdpr-nav-tab-active');

        tab = $(this).attr('href');
        $('.gdpr-tab').removeClass('gdpr-tab-active');
        $(tab).addClass('gdpr-tab-active');
    });

    $('.gdpr-cookie-header').each(function(){
        active = $(this).find('input[type="checkbox"]').prop('checked');
        if (!active) {
            $(this).addClass('gdpr-disabled');
        } else {
            $(this).removeClass('gdpr-disabled');            
        }
    });
    $('.gdpr-cookie-header').change(function(){
        active = $(this).find('input[type="checkbox"]').prop('checked');
        if (!active) {
            $(this).addClass('gdpr-disabled');
        } else {
            $(this).removeClass('gdpr-disabled');            
        }
    });
});
