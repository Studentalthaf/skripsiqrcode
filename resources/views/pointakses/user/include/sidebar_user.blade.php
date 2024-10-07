<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <span class="brand-text font-weight-light"> </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('frontend/images/favicon.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">SAM {{ Auth::user()->nama_lengkap }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item ">
                    <span>
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </span>
                </li>
                </li>
                <li class="nav-item">
                    <span>
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-columns"></i>
                            <p>
                                Kategori
                            </p>
                        </a>
                    </span>
                </li>
                <li class="nav-item">
                    <span>
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Menu
                            </p>
                        </a>
                    </span>
                </li>
                <li class="nav-item">
                    <span>
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-table"></i>
                            <p>
                                Order
                            </p>
                        </a>
                    </span>
                <li class="nav-item">
                    <span>
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-table"></i>
                            <p>
                                History
                            </p>
                        </a>
                    </span>
                <li class="nav-item">
                    <span>
                        <a href="#" class="nav-link">
                            <i class="nav-icon far fa-plus-square"></i>
                            <p>
                                Seller
                            </p>
                        </a>
                    </span>
                </li>
                <li class="nav-item">
                    <span>
                        <a href="#" class="nav-link">
                            <i class="nav-icon far fa-plus-square"></i>
                            <p>
                                Pengguna
                            </p>
                        </a>
                    </span>
                </li>

                <!-- Control Sidebar -->
                <aside class="control-sidebar control-sidebar-dark">
                    <!-- Control sidebar content goes here -->
                </aside>