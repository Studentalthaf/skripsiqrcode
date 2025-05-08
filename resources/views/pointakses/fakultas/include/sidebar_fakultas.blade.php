<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light"> </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->nama_lengkap }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">
                <li class="nav-item">
                    <span>
                        <a href="{{route('fakultas.index')}}" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </span>
                </li>
                <li class="nav-item">
                    <span>
                        <a href="{{ route('fakultas.event') }}" class="nav-link">
                            <i class="nav-icon fas fa-columns"></i>
                            <p>Acara</p>
                        </a>
                    </span>
                </li>
                <li class="nav-item">
                    <span>
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Laporan Fakultas</p>
                        </a>
                    </span>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
    </div>
</aside>