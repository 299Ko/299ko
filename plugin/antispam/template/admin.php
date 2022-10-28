<?php
defined('ROOT') OR exit('No direct script access allowed');

include_once(ROOT . 'admin/header.php');
?>
<script>
    function onClickRadio() {
        if (document.getElementById("radioRecaptcha").checked) {
            document.getElementById("useRecaptcha").style.display = 'block';
            document.querySelectorAll("#useRecaptcha input[type=text]").forEach(function (item) {
                item.disabled = false;
            });
        } else {
            document.getElementById("useRecaptcha").style.display = 'none';
            document.querySelectorAll("#useRecaptcha input[type=text]").forEach(function (item) {
                item.disabled = true;
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        onClickRadio();
        document.querySelectorAll("input[type=radio]").forEach(function (item) {
            item.addEventListener("click", function () {
                onClickRadio();
            });
        });
    });
</script>
<form method="post" action="index.php?p=antispam&action=saveconf">
    <?php show::adminTokenField(); ?>
    <p>
        <input <?php if ($runPlugin->getConfigVal('type') === 'useText') { ?>checked<?php } ?> type="radio" name="captcha" value="useText" id="radioText"/> Utiliser un captcha texte
    </p>
    <p>
        <input <?php if ($runPlugin->getConfigVal('type') === 'useRecaptcha') { ?>checked<?php } ?> type="radio" name="captcha" id="radioRecaptcha" value="useRecaptcha" /> Utiliser ReCaptcha de Google (<a href="https://www.google.com/recaptcha/admin/create" target="_blank">inscription</a>)
    </p>
    <div id="useRecaptcha">
        <h3>Configuration</h3>
        <p>
            <label>Clé du site (clé publique)</label><br>
            <input type="text" required="required" name="recaptchaPublicKey" value="<?php echo $runPlugin->getConfigVal('recaptchaPublicKey'); ?>" />
        </p>
        <p
            <label>Clé secrète</label><br>
            <input type="text" required="required" name="recaptchaSecretKey" value="<?php echo $runPlugin->getConfigVal('recaptchaSecretKey'); ?>" />
        </p>
    </div>
    <p> 
        <button type="submit" class="button">Enregistrer</button>
    </p>
</form>

<?php
include_once(ROOT . 'admin/footer.php');
