<div id="wrapper">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo base_url('dashboard/index'); ?>">OSS Admin</a>
        </div>
        <!-- /.navbar-header -->

        <ul class="nav navbar-top-links navbar-right">
            <!-- /.dropdown -->
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i>  <?php echo $this->session->userdata('userLogData')['username']; ?> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="#"><i class="fa fa-user fa-fw"></i> ข้อมูลส่วนตัว</a>
                    </li>
                    <li><a href="#"><i class="fa fa-gear fa-fw"></i> ตั้งค่า</a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="<?php echo base_url('user/logout'); ?>"><i class="fa fa-sign-out fa-fw"></i> ออกจากระบบ</a>
                    </li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
        <!-- /.navbar-top-links -->

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <!-- <li class="sidebar-search">
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                        </div>
                    </li> -->
                    <li><a href="<?php echo base_url('dashboard/index'); ?>"><i class="fa fa-dashboard fa-fw"></i> แดชบอร์ด</a></li>
                    <li><a href="<?php echo base_url('product/listing'); ?>"><i class="fa fa-cube fa-fw"></i> ทรัพย์สิน</a></li>
                    <li><a href="<?php echo base_url('product/category'); ?>"><i class="fa fa-cubes fa-fw"></i> ประเภททรัพย์สิน</a></li>
                    <li><a href="<?php echo base_url('product/subcategory'); ?>"><i class="fa fa-tags fa-fw"></i> หมวดหมู่ทรัพย์สิน</a></li>
                    <li><a href="<?php echo base_url('product/report'); ?>"><i class="fa fa-line-chart fa-fw"></i> ออกรายงาน</a></li>
                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </nav>

    <div id="page-wrapper">