<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RSU Sistema') }} - @yield('title', 'Dashboard')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.5.4/dist/select2-bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
      
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    
    <!-- Estilos personalizados para el menú -->
    <style>
        /* Ajustar el padding del texto en menús desplegables para evitar sobreposición con la flecha */
        .nav-sidebar .nav-treeview > .nav-item > .nav-link p {
            padding-right: 10px;
        }
        
        /* Asegurar que el texto del menú principal tenga espacio suficiente */
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link > p {
            padding-right: 30px;
            white-space: normal;
            line-height: 1.2;
        }
        
        /* Ajustar el tamaño de fuente si es necesario */
        .sidebar-dark-primary .nav-sidebar .nav-link p {
            font-size: 0.9rem;
        }
    </style>
    
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('dashboard') }}" class="nav-link">Inicio</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user"></i>
                        <span class="d-none d-md-inline ml-1">Grupo 09</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Mi Perfil
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="dropdown-item"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                            </a>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="brand-link">
                <i class="fas fa-recycle brand-image" style="opacity: .8; font-size: 2rem; margin-left: 0.5rem;"></i>
                <span class="brand-text font-weight-light">RSU Sistema</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle fa-2x text-white"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">Grupo 09</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- GESTIÓN DE VEHÍCULOS -->
                        <li class="nav-item {{ request()->is('vehiculos*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('vehiculos*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-car"></i>
                                <p>
                                    GESTIÓN DE VEHÍCULOS
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('vehiculos.colores.index') }}" class="nav-link {{ request()->routeIs('vehiculos.colores.*') ? 'active' : '' }}">
                                        <i class="fas fa-palette nav-icon"></i>
                                        <p>Color</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('vehiculos.marcas.index') }}" class="nav-link {{ request()->routeIs('vehiculos.marcas.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-tags nav-icon"></i>
                                        <p>Marcas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('vehiculos.modelos.index') }}" class="nav-link {{ request()->routeIs('vehiculos.modelos.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-wrench nav-icon"></i>
                                        <p>Modelos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('vehiculos.tipos.index') }}" class="nav-link {{ request()->routeIs('vehiculos.tipos.*') ? 'active' : '' }}">
                                        <i class="fas fa-car nav-icon"></i>
                                        <p>Tipo de Vehículo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('vehiculos.vehiculos.index') }}" class="nav-link {{ request()->routeIs('vehiculos.vehiculos.*') ? 'active' : '' }}">
                                        <i class="fas fa-car-side nav-icon"></i>
                                        <p>Vehículo</p>
                                    </a>
                                </li>

                                
                                <li class="nav-item">
    <a href="{{ route('admin.mantenimiento.index') }}" class="nav-link {{ request()->routeIs('admin.mantenimiento.*') ? 'active' : '' }}">
        <i class="fas fa-tools nav-icon"></i>
        <p>Mantenimiento</p>
    </a>
</li>
                           
                            </ul>
                        </li>

                        <!-- GESTIÓN DE EMPLEADOS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    GESTIÓN DE EMPLEADOS
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                 <li class="nav-item">
                                    <a href="{{ route('personal.funciones.index') }}" class="nav-link {{ request()->routeIs('personal.funciones.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-user-tie nav-icon"></i>
                                        <p>Tipo de Empleados</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('personal.personal.index') }}" class="nav-link {{ request()->routeIs('personal.personal.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-user nav-icon"></i>
                                        <p>Empleados</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('personal.contratos.index') }}" class="nav-link {{ request()->routeIs('personal.contratos.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-file-contract nav-icon"></i>
                                        <p>Contratos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('asistencias.index') }}" class="nav-link {{ request()->routeIs('asistencias.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-user-clock nav-icon"></i>
                                        <p>Asistencia</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('personal.vacaciones.index') }}" class="nav-link {{ request()->routeIs('personal.vacaciones.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-plane nav-icon"></i>
                                        <p>Vacaciones</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <!-- PROGRAMACIÓN -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>
                                    PROGRAMACIÓN
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('turnos.index') }}" class="nav-link {{ request()->routeIs('turnos.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-clock nav-icon"></i>
                                        <p>Turnos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('zonas.index') }}" class="nav-link {{ request()->routeIs('zonas.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-map-marker-alt nav-icon"></i>
                                        <p>Zonas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('grupospersonal.index') }}" class="nav-link {{ request()->routeIs('grupospersonal.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-users nav-icon"></i>
                                        <p>Grupo de Personal</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('programaciones.index') }}" class="nav-link {{ request()->routeIs('zonas.*') ? 'active' : '' }}">
                                        <i class="fas fa-fw fa-clock nav-icon"></i>
                                        <p>Programación</p>
                                    </a>
                                </li>
                                
                            </ul>
                        </li>

                        <!-- GESTIÓN DE CAMBIOS -->
                        <!-- GESTIÓN DE CAMBIOS -->
<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-exchange-alt"></i>
        <p>
            GESTIÓN DE CAMBIOS
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('motivos.index') }}" class="nav-link {{ request()->routeIs('motivos.*') ? 'active' : '' }}">
                <i class="fas fa-fw fa-clipboard-list nav-icon"></i>
                <p>Motivos</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('cambios.index') }}" class="nav-link {{ request()->routeIs('cambios.*') ? 'active' : '' }}">
                <i class="fas fa-fw fa-retweet nav-icon"></i>
                <p>Cambios</p>
            </a>
        </li>
    </ul>
</li>

                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">RSU Sistema</a>.</strong>
            Gestión de Residuos Sólidos Urbanos - José Leonardo Ortiz, Chiclayo.
            <div class="float-right d-none d-sm-inline-block">
                <b>Versión</b> 1.0.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-geometryutil@0.10.2/src/leaflet.geometryutil.min.js"></script>

    <!-- Global Functions -->
    <script>
        // Configurar CSRF token para todas las peticiones AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Funciones globales para SweetAlert2
        function showSuccessAlert(message, title = 'Éxito') {
            Swal.fire({
                icon: 'success',
                title: title,
                text: message,
                confirmButtonText: 'Aceptar'
            });
        }

        function showErrorAlert(message, title = 'Error') {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message,
                confirmButtonText: 'Aceptar'
            });
        }

        function showValidationErrors(errors) {
            let errorList = '<ul class="text-left">';
            $.each(errors, function(key, value) {
                errorList += '<li>' + value[0] + '</li>';
            });
            errorList += '</ul>';
            
            Swal.fire({
                icon: 'error',
                title: 'Errores de Validación',
                html: errorList,
                confirmButtonText: 'Aceptar'
            });
        }

        function confirmDelete(callback) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }

        function resetForm(formId) {
            $('#' + formId)[0].reset();
            $('#' + formId + ' .is-invalid').removeClass('is-invalid');
            $('#' + formId + ' .invalid-feedback').remove();
        }

        function showFieldErrors(errors, formId = '') {
            // Limpiar errores previos
            if (formId) {
                $('#' + formId + ' .is-invalid').removeClass('is-invalid');
                $('#' + formId + ' .invalid-feedback').remove();
            } else {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            }

            // Mostrar nuevos errores
            $.each(errors, function(field, messages) {
                let input = formId ? $('#' + formId + ' [name="' + field + '"]') : $('[name="' + field + '"]');
                input.addClass('is-invalid');
                input.after('<div class="invalid-feedback d-block">' + messages[0] + '</div>');
            });
        }

        function reloadDataTable(tableId) {
            if ($.fn.DataTable.isDataTable('#' + tableId)) {
                $('#' + tableId).DataTable().ajax.reload(null, false);
            }
        }

        // Configuración global de DataTables en español
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });

        // Configuración global de Select2 en español
        $.fn.select2.defaults.set('language', 'es');
        $.fn.select2.defaults.set('theme', 'bootstrap4');
    </script>

    @stack('scripts')
</body>
</html>