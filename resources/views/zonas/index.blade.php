@extends('layouts.app')

@section('title', 'Gestión de Zonas')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Zonas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                        <li class="breadcrumb-item active">Zonas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Zonas</h3>
                            <div class="card-tools">
                                <a href="{{ route('zonas.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Nueva Zona
                                </a>
                                <a href="{{ route('zonas.mapa') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-map"></i> Ver Mapa
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h5><i class="icon fas fa-check"></i> ¡Éxito!</h5>
                                    {{ session('success') }}
                                </div>
                            @endif

                            <table id="zonas-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Distrito</th>
                                        <th>Área (km²)</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($zonas as $zona)
                                        <tr>
                                            <td>{{ $zona->id }}</td>
                                            <td>{{ $zona->codigo }}</td>
                                            <td>{{ $zona->nombre }}</td>
                                            <td>{{ $zona->distrito->nombre ?? 'N/A' }}</td>
                                            <td>{{ $zona->area ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $zona->activo ? 'success' : 'danger' }}">
                                                    {{ $zona->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('zonas.edit', $zona) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('zonas.destroy', $zona) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('¿Está seguro de eliminar esta zona?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#zonas-table').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    });
</script>
@endpush