<?php
/* DO NOT REMOVE */
if (!defined('QUADODO_IN_SYSTEM')) {
exit;
}
/*****************/
?>
<?php require 'includes/header_account.php'; ?>

    <div class="account-pages"></div>
    <div class="clearfix"></div>
    <div class="wrapper-page">

        <div class="account-bg">
            <div class="card-box m-b-0">
                <div class="text-xs-center m-t-20">
                    <a href="/" class="logo">
                        <i class="zmdi zmdi-group-work icon-c-logo"></i>
                        <span>Otterm8</span>
                    </a>
                </div>
                <div class="m-t-10 p-20">
					<script type="text/javascript" src="assets/plugins/duo/duo-web-v2.min.js"></script>
					<link rel="stylesheet" type="text/css" href="assets/css/duo-frame.css">
					<iframe id="duo_iframe"
						data-host="api-d392830f.duosecurity.com"
						data-sig-request="<?php echo $sigRequest; ?>"
						data-post-action="login_process.php">
					</iframe>

                </div>

                <div class="clearfix"></div>
            </div>
        </div>
        <!-- end card-box-->

    </div>
    <!-- end wrapper page -->


<?php require 'includes/footer_account.php'; ?>
