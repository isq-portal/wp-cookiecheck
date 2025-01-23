<?php
if (($_SERVER["SERVER_NAME"] == "isq.berlin") || ($_SERVER["SERVER_NAME"] == "www.isq.berlin")) {
    exit();
};
?>
<link href="<?php echo plugin_dir_url(__FILE__).'css/public.css'; ?>" rel="stylesheet" type="text/css">
<div class="scw-cookie<?= $this->decisionMade ? ' scw-cookie-out' : ''; ?>">
    <div class="scw-cookie-panel-toggle scw-cookie-panel-toggle-<?= $this->config['panel_toggle_position']; ?>"
         onclick="isqCookiePanelToggle()">
        <span class="isqc-icon isqc-icon-cookie"></span>
    </div>
    <div class="scw-cookie-content">
        <div class="row">
            <div class="col-md-8">
                Diese Webseite verwendet Cookies.
                Technisch notwendige Cookies sind für den funktionalen Betrieb der Website erforderlich.
                Cookies für die Webanalyse setzen wir ein, um die Nutzung unserer Website statistisch auszuwerten.
                Nähere Informationen finden Sie in unserer <span style="text-decoration: underline;"><a style="color: #FFFFFF;" href="<?= $this->config['cookie_policy_url']; ?>">Datenschutzerklärung</a></span>.
                Dort können Sie auch Ihre Cookie-Einstellungen jederzeit ändern.
                <br /><br />
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn btn-success" onclick="isqCookieActivateAll();">Alle akzeptieren</div>
                        <div class="btn btn-danger" onclick="isqCookieHide();">Speichern</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scw-cookie-details">
            <div class="scw-cookie-details-title">Verwaltung der Cookies</div>
            <div class="row">
                <div class="col-md-6 scw-cookie-toggle">
                    <div class="scw-cookie-name">technisch notwendige Cookies</div>
                    <label class="scw-cookie-switch checked disabled">
                        <input type="checkbox" name="essential" checked="checked" disabled="disabled">
                        <div></div>
                    </label>
                </div>
                <?php foreach ($this->enabledSnippets() as $slug => $label) { ?>
                    <div class="col-md-6 scw-cookie-toggle">
                        <div class="scw-cookie-name isq-toggle-switch" onclick="isqCookieToggle(this)"><?= $label; ?></div>
                        <label class="scw-cookie-switch<?= $this->isAllowed($slug) ? ' checked' : ''; ?>">
                            <input type="checkbox"
                                   name="<?= $slug; ?>"
                                <?= $this->isAllowed($slug) ? 'checked="checked"' : ''; ?>
                            >
                            <div></div>
                        </label>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
    isqCookieDir = "<?php echo $this->pluginDir; ?>";
</script>
<script src="<?php echo plugin_dir_url(__FILE__).'js/js-cookie.js'; ?>" type="text/javascript"></script>
<script src="<?php echo plugin_dir_url(__FILE__).'js/public.js'; ?>" type="text/javascript"></script>
