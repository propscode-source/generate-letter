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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/1.1.3/metisMenu.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>assets/dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/1.1.3/metisMenu.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<script src="<?= $baseUrl ?>assets/data/morris-data.js"></script>
<script src="<?= $baseUrl ?>assets/dist/js/sb-admin-2.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.71/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.71/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

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
