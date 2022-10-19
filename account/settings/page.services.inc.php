<?php

	/*!
	 * https://raccoonsquare.com
	 * raccoonsqaure@gmail.com
	 *
	 * Copyright 2012-2022 Demyanchuk Dmitry (raccoonsqaure@gmail.com)
	 */

    if (!defined("APP_SIGNATURE")) {

        header("Location: /");
        exit;
    }

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    include_once('../sys/config/gconfig.inc.php');

	$error = false;
    $error_message = '';

    $account = new account($dbo, auth::getCurrentUserId());
    $fb_id = $account->getFacebookId();
    $gl_id = $account->getGoogleFirebaseId();

    if (!empty($_POST)) {

    }

	$page_id = "settings_services";

	$css_files = array("main.css", "my.css");


    $page_title = $LANG['page-services']." | ".APP_TITLE;

	include_once("../html/common/header.inc.php");
?>

<body class="settings-page">

    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column row">

            <?php

                include_once("../html/common/sidenav.inc.php");
            ?>

            <?php

                include_once("../html/account/settings/settings_nav.inc.php");
            ?>

            <div class="row col sn-content" id="content">

                <div class="main-content">

                    <div class="profile-content standard-page">

                        <h1 class="title"><?php echo $LANG['page-services']; ?></h1>

                        <?php

                        $msg = $LANG['page-services-sub-title'];

                        if (isset($_GET['status'])) {

                            switch($_GET['status']) {

                                case "connected": {

                                    $msg = $LANG['label-services-facebook-connected'];
                                    break;
                                }

                                case "g_connected": {

                                    $msg = $LANG['label-services-google-connected'];
                                    break;
                                }

                                case "error": {

                                    $msg = $LANG['label-services-facebook-error'];
                                    break;
                                }

                                case "g_error": {

                                    $msg = $LANG['label-services-google-error'];
                                    break;
                                }

                                case "disconnected": {

                                    $msg = $LANG['label-services-facebook-disconnected'];
                                    break;
                                }

                                case "g_disconnected": {

                                    $msg = $LANG['label-services-google-disconnected'];
                                    break;
                                }

                                default: {

                                    $msg = $LANG['page-services-sub-title'];
                                    break;
                                }
                            }
                        }
                        ?>

                        <div class="warning-container">
                            <ul>
                                <?php echo $msg; ?>
                            </ul>
                        </div>

                        <header class="top-banner" style="padding: 0">

                            <div class="info">
                                <h1>Facebook</h1>

                                <?php

                                    if (strlen($fb_id) != 0) {

                                        ?>
                                            <p><?php echo $LANG['label-connected-with-facebook']; ?></p>
                                        <?php
                                    }
                                ?>

                            </div>

                            <div class="prompt">

                                <?php

                                    if (strlen($fb_id) == 0) {

                                        ?>
                                            <a class="button green " href="/facebook/connect/?access_token=<?php echo auth::getAccessToken(); ?>"><?php echo $LANG['action-connect-facebook']; ?></a>
                                        <?php

                                    } else {

                                        ?>
                                            <a class="button green" href="/facebook/disconnect/?access_token=<?php echo auth::getAccessToken(); ?>"><?php echo $LANG['action-disconnect']; ?></a>
                                        <?php
                                    }
                                ?>

                            </div>

                        </header>

                        <header class="top-banner mt-2 <?php if (!GOOGLE_AUTHORIZATION) echo "gone" ?>" style="padding: 0">

                            <div class="info">
                                <h1>Google</h1>

                                <?php

                                    if (strlen($gl_id) != 0) {

                                        ?>
                                            <p><?php echo $LANG['label-connected-with-google']; ?></p>
                                        <?php
                                    }
                                ?>

                            </div>

                            <div class="prompt">

                                <?php

                                    if (strlen($gl_id) == 0) {

                                        ?>
                                            <a class="button green" href="<?php echo $google_client->createAuthUrl(); ?>"><?php echo $LANG['action-connect-google']; ?></a>
                                        <?php

                                    } else {

                                        ?>
                                            <a class="button red" onclick="disconnect()"><?php echo $LANG['action-disconnect']; ?></a>
                                        <?php
                                    }
                                ?>

                            </div>

                        </header>

                    </div>


                </div>
            </div>
        </div>


    </div>

    <?php

        include_once("../html/common/footer.inc.php");

    ?>

    <script type="text/javascript" src="/js/firebase/config.js"></script>
    <script type="text/javascript" src="/js/firebase/google.js"></script>

    <script>

        function disconnect() {

            $.ajax({
                type: 'POST',
                url: "/api/" + options.api_version + "/method/account.google",
                data: 'account_id=' + account.id + '&access_token=' + account.accessToken + '&action=disconnect',
                dataType: 'json',
                timeout: 30000,
                success: function(response) {

                    if (response.hasOwnProperty('error')) {

                        if (!response.error) {

                            window.location = "/account/settings/services?status=g_disconnected";
                        }
                    }
                },
                error: function(xhr, type){


                }
            });
        }

    </script>

</body
</html>