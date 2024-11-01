<?php

/**
 *  @var bool $success
 *  @var string $workspace_id
 *  @var string $api_secret
*/

?>

<div class="weberlo">
    <div class="weberlo__header">
        <img src="<?php echo WEBERLO_PLUGIN_URL; ?>admin/img/logo-sm.png">
    </div>
    <div class="weberlo__content">

        <?php if( $workspace_id_error === null ): ?>
            <h3>You’re almost ready to go!</h3>
            <p>Simply paste the workspace id given to you.</p>

        <?php elseif( $workspace_id_error ): ?>
            <p class="weberlo-error">This workspace id does not exist, please check you copied it correctly and try again!</p>

        <?php elseif( $secret_key_error ): ?>
            <p class="weberlo-error">This API Key does not exist, please check you copied it correctly and try again!</p>

        <?php else : ?>
            <p>Your account is all set up!</p>

        <?php endif; ?>

        <form class="weberlo__form" method="POST">

            <div class="weberlo__field weberlo_workspace_id">
                <label>Workspace ID:</label>
                <input type="text" name="weberlo_workspace_id" placeholder="paste here" required value="<?php echo esc_attr($workspace_id); ?>">
            </div>

            <div class="weberlo__field weberlo_api_key">
                <label>API key:</label>
                <input type="text" name="weberlo_api_key" placeholder="paste here" value="<?php echo esc_attr($secret_key); ?>">
            </div>

            <input class="weberlo__button" type="submit">
        </form>

        <?php if( $workspace_id_error === null || $workspace_id_error ): ?>
            <p>Don’t have your Workspace ID yet? <a target="_blank" href="https://app.weberlo.com/auth/register?utm_source=wordpress&utm_medium=plugin">Get it here!</a></p>
        <?php endif; ?>

        <script>
document.addEventListener("DOMContentLoaded", function(event) {
    if (window.weberlo) {
var visitorId = window.weberlo.getVisitorID();
}
});

</script>
    </div>

</div>
