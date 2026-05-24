<?php
$session = session();
$isSekretaris = $session->get('nama_jabatan') === 'Sekretaris';
$isOrmawa     = $session->get('level') == 4;
$notif = $session->getFlashdata('notif');
$baseUrl = base_url();

$sidebarMenus = $isSekretaris ? [
    [
        'url' => base_url('home'),
        'icon' => 'fa-dashboard',
        'label' => 'Dashboard',
    ],
    [
        'url' => base_url('home/surat_masuk'),
        'icon' => 'fa-envelope',
        'label' => 'Proposal',
    ],
    [
        'url' => base_url('home/surat_keluar'),
        'icon' => 'fa-envelope',
        'label' => 'Laporan',
    ],
] : [
    [
        'url' => base_url('home'),
        'icon' => 'fa-dashboard',
        'label' => 'Dashboard',
    ],
    [
        'url' => base_url('home/surat_masuk'),
        'icon' => 'fa-envelope',
        'label' => 'Proposal',
    ],
    [
        'url' => base_url('home/surat_keluar'),
        'icon' => 'fa-envelope',
        'label' => 'Laporan',
    ],
];


// ] : [
//     [
//         'url' => base_url('home'),
//         'icon' => 'fa-dashboard',
//         'label' => 'Dashboard',
//     ],
//     [
//         'url' => base_url('home/disposisi_keluar'),
//         'icon' => 'fa-mail-forward',
//         'label' => 'Disposisi Keluar',
//     ],
//     [
//         'url' => base_url('home/disposisi_masuk'),
//         'icon' => 'fa-mail-reply',
//         'label' => 'Disposisi Masuk',
//     ],
// ];
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Sistem Monitoring Proposal dan Laporan ORMAWA FASILKOM</title>
    <link rel="icon" type="image/png" href="<?= $baseUrl ?>assets/img/favicon.png">
    <link href="<?= $baseUrl ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/vendor/morrisjs/morris.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/vendor/datatables/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

<div id="wrapper">
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= base_url('home') ?>">SIMPLO
                (<?= esc((string) $session->get('nama_jabatan')) ?>)</a>
        </div>

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i> <?= esc((string) $session->get('nama_pegawai')) ?> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="<?= base_url('logout') ?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                </ul>
            </li>
        </ul>

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <?php foreach ($sidebarMenus as $menu): ?>
                        <li>
                            <a href="<?= $menu['url'] ?>"><i class="fa <?= $menu['icon'] ?> fa-fw"></i> <?= esc($menu['label']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <?= esc((string) $judul) ?>
                    <?php if (isset($data_surat->nomor_surat)): ?>
                        <?= ' Nomor ' . esc((string) $data_surat->nomor_surat) ?>
                    <?php endif; ?>
                </h1>
            </div>
        </div>

        <?php if ($notif !== null): ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-info alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?= $notif ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </div>
</div>

<script src="<?= $baseUrl ?>assets/vendor/jquery/jquery.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/metisMenu/metisMenu.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/raphael/raphael.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/morrisjs/morris.min.js"></script>
<script src="<?= $baseUrl ?>assets/data/morris-data.js"></script>
<script src="<?= $baseUrl ?>assets/dist/js/sb-admin-2.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/datatables-responsive/dataTables.responsive.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/datatables/js/dataTables.buttons.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/datatables/js/buttons.flash.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/datatables/js/jszip.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/datatables/js/pdfmake.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/datatables/js/vfs_fonts.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/datatables/js/buttons.html5.min.js"></script>
<script src="<?= $baseUrl ?>assets/vendor/datatables/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        $('#dataTables-example').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
    });
</script>

</body>

</html>
