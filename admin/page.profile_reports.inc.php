<?php

    /*!
     * ifsoft.co.uk
     *
     * http://ifsoft.com.ua, https://ifsoft.co.uk, https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2020 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    if (!defined("APP_SIGNATURE")) {

        header("Location: /");
        exit;
    }

    if (!admin::isSession()) {

        header("Location: /admin/login");
        exit;
    }

    $stats = new stats($dbo);
    $admin = new admin($dbo);

    if (isset($_GET['act'])) {

        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : 0;
        $act = isset($_GET['act']) ? $_GET['act'] : '';

        if ($accessToken === admin::getAccessToken() && !APP_DEMO) {

            switch($act) {

                case "delete": {

                    $reports = new reports($dbo);
                    $reports->clear(REPORT_TYPE_PROFILE);
                    unset($reports);

                    header("Location: /admin/profile_reports");
                    exit;

                    break;
                }

                default: {

                    header("Location: /admin/profile_reports");
                    exit;

                    break;
                }
            }
        }
    }

    $page_id = "profile_reports";

    $css_files = array("mytheme.css");
    $page_title = "Profile Reports | Admin Panel";

    include_once("../html/common/admin_header.inc.php");
?>

<body class="fix-header fix-sidebar card-no-border">

    <div id="main-wrapper">

        <?php

            include_once("../html/common/admin_topbar.inc.php");
        ?>

        <?php

            include_once("../html/common/admin_sidebar.inc.php");
        ?>

        <div class="page-wrapper">

            <div class="container-fluid">

                <div class="row page-titles">
                    <div class="col-md-5 col-8 align-self-center">
                        <h3 class="text-themecolor">Dashboard</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/admin/main">Home</a></li>
                            <li class="breadcrumb-item active">Profile Reports</li>
                        </ol>
                    </div>
                </div>

                <?php

                    include_once("../html/common/admin_banner.inc.php");
                ?>

                <?php

                    $reports = new reports($dbo);

                    $result = $reports->getItems(0, REPORT_TYPE_PROFILE);

                    $inbox_loaded = count($result['items']);

                    if ($inbox_loaded != 0) {

                        ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <a href="/admin/profile_reports/?act=delete&access_token=<?php echo admin::getAccessToken(); ?>" style="float: right">
                                            <button type="button" class="btn waves-effect waves-light btn-info">Delete all reports</button>
                                        </a>

                                        <div class="d-flex no-block">
                                            <h4 class="card-title">Profile Reports (Latest reports)</h4>
                                        </div>

                                        <div class="table-responsive m-t-20">

                                            <table class="table stylish-table">

                                                <thead>
                                                <tr>
                                                    <th colspan="2">From User</th>
                                                    <th colspan="2">To User</th>
                                                    <th>Reason</th>
                                                    <th>Date</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                    <?php

                                                        foreach ($result['items'] as $key => $value) {

                                                            draw($value);
                                                        }

                                                    ?>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <?php

                    } else {

                        ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h4 class="card-title">List is empty.</h4>
                                            <p class="card-text">This means that there is no data to display :)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                ?>

            </div> <!-- End Container fluid  -->

            <?php

                include_once("../html/common/admin_footer.inc.php");
            ?>

        </div> <!-- End Page wrapper  -->
    </div> <!-- End Wrapper -->

</body>

</html>

<?php

    function draw($item)
    {
        ?>

            <tr>
                <td style="width:50px;">

                    <?php

                        if ($item['fromUserId'] != 0 && !empty($item['owner']) && strlen($item['owner']['lowPhotoUrl']) != 0) {

                            ?>
                                <span class="round" style="background-size: cover; background-image: url(<?php echo $item['owner']['lowPhotoUrl']; ?>)"></span>
                            <?php

                        } else {

                            ?>
                                <span class="round" style="background-size: cover; background-image: url(/img/profile_default_photo.png)"></span>
                            <?php
                        }
                    ?>
                </td>
                <td>

                    <?php

                        if ($item['fromUserId'] != 0) {

                            ?>
                                <h6><a href="/admin/profile?id=<?php echo $item['fromUserId']; ?>"><?php echo $item['owner']['fullname']; ?></a></h6>
                                <small class="text-muted">@<?php echo $item['owner']['username']; ?></small>
                            <?php

                        } else {

                            ?>
                                <h6>Unknown user</h6>
                            <?php
                        }
                    ?>
                </td>
                <td style="width:50px;">

                    <?php

                        if ($item['itemId'] != 0 && !empty($item['item']) && strlen($item['item']['lowPhotoUrl']) > 0) {

                            ?>
                                <span class="round" style="background-size: cover; background-image: url(<?php echo $item['item']['lowPhotoUrl']; ?>)"></span>
                            <?php

                        } else {

                            ?>
                                <span class="round" style="background-size: cover; background-image: url(/img/profile_default_photo.png)"></span>
                            <?php
                        }
                    ?>
                </td>
                <td>
                    <h6><a href="/admin/profile?id=<?php echo $item['itemId']; ?>"><?php echo $item['item']['fullname']; ?></a></h6>
                    <small class="text-muted">@<?php echo $item['item']['username']; ?></small>
                </td>
                <td>
                    <?php

                        switch ($item['abuseId']) {

                            case 0: {

                                echo "<span class=\"label label-success\">This is spam.</span>";

                                break;
                            }

                            case 1: {

                                echo "<span class=\"label label-info\">Hate Speech or violence.</span>";

                                break;
                            }

                            case 2: {

                                echo "<span class=\"label label-danger\">Nudity or Pornography.</span>";

                                break;
                            }

                            default: {

                                echo "<span class=\"label label-warning\">Fake profile.</span>";

                                break;
                            }
                        }
                    ?>
                </td>
                <td><?php echo $item['date']; ?></td>
            </tr>

        <?php
    }