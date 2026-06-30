<?php
	require_once 'include/cabecera.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MPFN - DF AREQUIPA</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="libmenu/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="libmenu/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="libmenu/OverlayScrollbars.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed" data-panel-auto-height-mode="height">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->

    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
      	MINISTERIO PUBLICO - FISCALIA DE LA NACION
        <!--<a href="index3.html" class="nav-link">Home</a>-->
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <!--<a href="#" class="nav-link">Contact</a>-->
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <!--
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>-->

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li>



      <!-- Notifications Dropdown Menu -->
<!--      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>-->
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="img/logo_blanco.gif" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">DF Arequipa</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->

      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <!--<div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>-->
        <div class="info text-light">
          <!--<a href="#" class="d-block">Alexander Pierce</a>-->
          <? echo $_SESSION['nomb_oper']; ?>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <!--
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>-->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
<?php
	function put_subm($arra_valo_subm,$arra_subm,$rows_menu,$codi)
	{
		if(substr($arra_valo_subm[$codi][nomb_subm],0,6)=='CONST_')
			$arra_valo_subm[$codi][nomb_subm]=constant($arra_valo_subm[$codi][nomb_subm]);
		if($arra_subm[$rows_menu['iden_menu']][$codi])
		{
			//echo"<li><span><img src=\"img/icons/{$arra_valo_subm[$codi][icon_subm]}.svg\" width=14px>&nbsp;&nbsp;".$arra_valo_subm[$codi][nomb_subm]."</span><ul>";
		echo '
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-circle"></i>
              <p><img src="img/icons/'.$arra_valo_subm[$codi][icon_subm].'.svg" width=14px>
                '.$arra_valo_subm[$codi][nomb_subm].'
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
          ';

			foreach($arra_subm[$rows_menu['iden_menu']][$codi] as $codi => $nomb)
			{
				//echo"<li><a href=\"{$arra_valo_subm[$codi][page_subm]}\" target=\"body_iframe\"><img src=\"img/icons/{$arra_valo_subm[$codi][icon_subm]}.svg\" width=14px>&nbsp;&nbsp;".$arra_valo_subm[$codi][nomb_subm]."</a></li>";
				put_subm($arra_valo_subm,$arra_subm,$rows_menu,$codi);
			}
			echo"</ul></li>";
		} else {
			//echo"<li><a href=\"{$arra_valo_subm[$codi][page_subm]}\" target=\"body_iframe\"><img src=\"img/icons/{$arra_valo_subm[$codi][icon_subm]}.svg\" width=14px>&nbsp;&nbsp;".$arra_valo_subm[$codi][nomb_subm]."</a></li>";
			echo '
		  <li class="nav-item">
            <a href="'.$arra_valo_subm[$codi][page_subm].'" class="nav-link">
              <i class="nav-icon fas fa-circle"></i>
              <p><img src="img/icons/'.$arra_valo_subm[$codi][icon_subm].'.svg" width=14px>'.$arra_valo_subm[$codi][nomb_subm].'
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
          </li>
            ';
		}
	}
	//obtiene cadena de los submenus del usuario
	$result=$Db->query("select distinct iden_subm from mp_admi_oper_role as a,mp_admi_role_subm as b where a.iden_role=b.iden_role AND a.iden_oper=:oper",[':oper'=>$_SESSION['iden_oper']]);
	foreach ($result as $rows)
		$cade_oper_subm.=",".$rows['iden_subm'];
	$cade_oper_subm=substr($cade_oper_subm,1);

	//obtiene array de los submenus del usuario
	$result=$Db->query("select * from mp_admi_subm where iden_subm IN ($cade_oper_subm) order by orde_subm ASC");
	foreach ($result as $rows)
	{
		$arra_subm[$rows['iden_menu']][$rows['iden_padr']][$rows['iden_subm']]=$rows['nomb_subm'];
		$arra_valo_subm[$rows['iden_subm']]=$rows;
		$cade_oper_menu.=",".$rows['iden_menu'];
	}
	$cade_oper_menu=substr($cade_oper_menu,1);

?>
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
<?

	$result_menu=$Db->select('mp_admi_menu','','','',['orde_menu'=>'ASC']);
	foreach ($result_menu as $rows_menu)
	{
//		echo"
//				<div id=\"panel-$rows_menu[iden_menu]\">
//					<ul>
//		";
		foreach($arra_subm[$rows_menu['iden_menu']][0] as $codi => $nomb)
			put_subm($arra_valo_subm,$arra_subm,$rows_menu,$codi);

//		echo"
//					</ul>
//				</div>
//		";

	}
?>
		</ul>

      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <div class="nav navbar navbar-expand navbar-white navbar-light border-bottom p-0">
      <div class="nav-item dropdown">
        <a class="nav-link bg-danger dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Cerrar</a>
        <div class="dropdown-menu mt-0">
          <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all">Cerrar Todo</a>
          <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all-other">Cerrar Las Otras</a>
        </div>
      </div>

      <a class="nav-link bg-light" href="#" data-widget="iframe-scrollleft"><i class="fas fa-angle-double-left"></i></a>
      <ul class="navbar-nav overflow-hidden" role="tablist"></ul>
<!--      <a class="nav-link bg-light" href="#" data-widget="iframe-scrollright"><i class="fas fa-angle-double-right"></i></a>
      <a class="nav-link bg-light" href="#" data-widget="iframe-fullscreen"><i class="fas fa-expand"></i></a>
      -->
    </div>
    <div class="tab-content">
      <div class="tab-empty">
        <h2 class="display-4">No tab selected!</h2>
      </div>
      <div class="tab-loading">
        <div>
          <h2 class="display-4">Tab is loading <i class="fa fa-sync fa-spin"></i></h2>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-wrapper -->
  <!--
  <footer class="main-footer">
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.2.0
    </div>
  </footer>-->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="libmenu/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="libmenu/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="libmenu/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="libmenu/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="libmenu/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="libmenu/demo.js"></script>
</body>
</html>
