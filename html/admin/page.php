<form method="POST">
    <div class="wrap">
        <h1>ISQ Cookiecheck</h1>
        <?php if (isset($message)) { ?>
            <div class="notice <?php echo $message[1]; ?>">
                <p><?php echo $message[0]; ?></p>
            </div>
        <?php } ?>
        <div class="main-content">
            <div class="gdpr-nav-tabs">
                <?php
                $tabs = array(
                    'generic' => 'Generic',
                    'cookies' => 'Cookies',
                );
                foreach ($tabs as $tab => $name) {
                    echo '<a class="gdpr-nav-tab" href="#'.$tab.'">'.$name.'</a>';
                }
                ?>
            </div>
            <div id="generic" class="gdpr-tab">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th><label for="domain_name">Domain name</label></th>
                            <td>
                                <input name="domain_name"
                                    class="regular-text"
                                    type="text"
                                    value="<?php echo $this->getConfig('domain_name'); ?>"
                                >
                                <p class="description">This site's domain name with no subdomain prefix. <strong>e.g. wordpress.com</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="cookie_policy_url">Cookie policy URL</label></th>
                            <td>
                                <input name="cookie_policy_url"
                                    class="regular-text"
                                    type="text"
                                    value="<?php echo $this->getConfig('cookie_policy_url'); ?>"
                                >
                                <p class="description">URL to direct users to for the cookie policy. <strong>e.g. /cookie-policy</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="panel_toggle_position">Cookie toggle icon position</label></th>
                            <td>
                                <?php
                                $options = array(
                                    'left'   => 'Left',
                                    'center' => 'Center',
                                    'right'  => 'Right',
                                );
                                ?>
                                <select name="panel_toggle_position">
                                    <?php
                                    foreach ($options as $value => $label) {
                                        $selected = $this->getConfig('panel_toggle_position') == $value
                                            ? 'selected="selected"'
                                            : '';
                                        echo '<option value="'.$value.'" '.$selected.'>'.$label.'</option>';    
                                    }
                                    ?>
                                </select>
                                <p class="description">Where on the page would you like the cookie policy toggle button to display?</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="default_setting">Default setting for cookies</label></th>
                            <td>
                                <?php
                                $options = array(
                                    'enabled'  => 'Enabled',
                                    'disabled' => 'Disabled',
                                );
                                ?>
                                <select name="default_setting">
                                    <?php
                                    foreach ($options as $value => $label) {
                                        $selected = $this->getConfig('default_setting') == $value
                                            ? 'selected="selected"'
                                            : '';
                                        echo '<option value="'.$value.'" '.$selected.'>'.$label.'</option>';    
                                    }
                                    ?>
                                </select>
                                <p class="description">On initial site load, would you like the cookies to be enabled or disabled?</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="cookies" class="gdpr-tab">
                <?php
                foreach ($this->snippets as $snippet) {
                    ?>
                    <div class="postbox gdpr-cookie-container">
                        <h2 class="gdpr-cookie-header">
                            <?php echo $snippet->title; ?>
                            <div class="gdpr-switch">
                                <input type="checkbox"
                                    name="cookies[<?php echo $snippet->id; ?>][display]"
                                    class="gdpr-switch-checkbox"
                                    id="cookies[<?php echo $snippet->id; ?>][display]"
                                    <?php echo $snippet->display ? 'checked="checked"' : ''; ?>
                                >
                                <label class="gdpr-switch-label" for="cookies[<?php echo $snippet->id; ?>][display]"></label>
                            </div>
                        </h2>
                        <div class="gdpr-cookie-content">
                            <div class="gdpr-input">
                                <label for="cookies[<?php echo $snippet->id; ?>][label]">Label</label>
                                <span class="gdpr-tooltip" title="Label to display for this cookie within the popup cookie settings area"></span>
                                <input type="text" name="cookies[<?php echo $snippet->id; ?>][label]" value="<?php echo $snippet->label; ?>">
                            </div>
                            <?php foreach ($snippet->variables as $variable) { ?>
                                <div class="gdpr-input">
                                    <label for="cookies[<?php echo $snippet->id; ?>][variables][<?php echo $variable->slug; ?>]">
                                        <?php echo $variable->label; ?>
                                    </label>
                                    <span class="gdpr-tooltip" title="<?php echo $variable->description; ?>"></span>
                                    <input type="text"
                                        name="cookies[<?php echo $snippet->id; ?>][variables][<?php echo $variable->slug; ?>]"
                                        value="<?php echo $variable->value; ?>"
                                    >
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="clear"></div>
            </div>
        </div>
        <div class="side-content">
            <div class="gdpr-block">
                <input type="hidden" name="save-gdpr-cookie" value="save-gdpr-cookie">
                <input class="button button-primary button-large"
                    value="Save changes"
                    type="submit"
                >
                <hr>
                <div class="fine-print">
                    Developed by <a href="https://www.isq.berlin/wordpress/" target="_blank">ISQ BB IT</a><br>
                    Version: <?php echo get_plugin_data(__DIR__.'/../../wp-cookiecheck.php')['Version']; ?>
                </div>
            </div>
        </div>
    </div>
</form>
