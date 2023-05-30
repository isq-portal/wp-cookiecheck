<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $this->getVariable('google_analytics', 'tracking_id'); ?>"></script>
<script>
    alert('Here!');
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?= $this->getVariable('google_analytics', 'tracking_id'); ?>');
</script>
