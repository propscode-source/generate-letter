<?php
$notif = session()->getFlashdata('notif');
$baseUrl = base_url();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login-Sistem Monitoring Proposal dan Laporan ORMAWA FASILKOM UNSRI</title>
    
    <link href="<?= $baseUrl ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Sistem Monitoring Proposal dan Laporan ORMAWA FASILKOM UNSRI</h3>
                </div>
                <div class="panel-body">
                    <?php if ($notif !== null): ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button class="close" data-dismiss="alert" aria-hidden="true">x</button>
                        <?= $notif ?>
                    </div>
                <?php endif; ?>
                    <form role="form" method="post" action="<?= base_url('login/validate') ?>">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="Username" name="nik" type="number" autofocus>
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Kata Sandi" name="password" type="password">
                            </div>
                            <input type="submit" class="btn btn-lg btn-success btn-block" value="Masuk">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= $baseUrl ?>assets/vendor/jquery/jquery.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/metisMenu/metisMenu.min.js"></script>
<script src="<?= $baseUrl ?>assets/dist/js/sb-admin-2.js"></script>

</body>

</html>
