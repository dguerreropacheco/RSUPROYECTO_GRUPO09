@extends('layouts.app')

@section('title', 'Editar Zona')

@push('styles')
<style>
    #map {
        height: 400px;
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<!-- Modal de Advertencia de Superposición -->
<div class="modal fade" id="overlapWarningModal" tabindex="-1" role="dialog" aria-labelledby="overlapWarningModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="overlapWarningModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Superposición Detectada
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger mb-3">
                    <i class="icon fas fa-ban"></i> <strong>Error:</strong> La zona que intenta modificar se superpone con zonas existentes.
                </div>
                <p><strong>Zonas en conflicto:</strong></p>
                <ul id="overlappingZonesList" class="list-unstyled">
                    <!-- Lista de zonas se llenará dinámicamente -->
                </ul>
                <p class="text-muted mt-3">
                    <i class="fas fa-info-circle"></i> Por favor, ajuste el perímetro de la zona para evitar superposiciones.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <button type="button" class="btn btn-warning" data-dismiss="modal" onclick="location.reload()">
                    <i class="fas fa-undo"></i> Recargar Zona Original
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Validación Exitosa -->
<div class="modal fade" id="successValidationModal" tabindex="-1" role="dialog" aria-labelledby="successValidationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="successValidationModalLabel">
                    <i class="fas fa-check-circle"></i> Zona Válida
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <p class="mb-0">No hay superposiciones detectadas.</p>
            </div>
        </div>
    </div>
</div>

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Editar Zona</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('zonas.index') }}">Zonas</a></li>
                        <li class="breadcrumb-item active">Editar</li>
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
                            <h3 class="card-title">Editar Zona: {{ $zona->nombre }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <form action="{{ route('zonas.update', $zona) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="codigo">Código</label>
                                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                                id="codigo" name="codigo" value="{{ old('codigo', $zona->codigo) }}" required>
                                            @error('codigo')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombre">Nombre</label>
                                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                                id="nombre" name="nombre" value="{{ old('nombre', $zona->nombre) }}" required>
                                            @error('nombre')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="distrito_id">Distrito</label>
                                            <select class="form-control select2bs4 @error('distrito_id') is-invalid @enderror" 
                                                id="distrito_id" name="distrito_id" required>
                                                <option value="">Seleccione un distrito</option>
                                                @foreach($distritos as $distrito)
                                                    <option value="{{ $distrito->id }}" 
                                                        {{ old('distrito_id', $zona->distrito_id) == $distrito->id ? 'selected' : '' }}>
                                                        {{ $distrito->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('distrito_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="area">Área (km²)</label>
                                            <input type="number" step="0.01" class="form-control @error('area') is-invalid @enderror" 
                                                id="area" name="area" value="{{ old('area', $zona->area) }}">
                                            @error('area')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="poblacion_estimada">Población Estimada</label>
                                            <input type="number" class="form-control @error('poblacion_estimada') is-invalid @enderror" 
                                                id="poblacion_estimada" name="poblacion_estimada" 
                                                value="{{ old('poblacion_estimada', $zona->poblacion_estimada) }}">
                                            @error('poblacion_estimada')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="activo">Estado</label>
                                            <select class="form-control @error('activo') is-invalid @enderror" 
                                                id="activo" name="activo">
                                                <option value="1" {{ old('activo', $zona->activo) == 1 ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ old('activo', $zona->activo) == 0 ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                            @error('activo')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                        id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $zona->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Perímetro de la Zona</label>
                                    <div id="map"></div>
                                    <input type="hidden" name="perimetro" id="perimetro" value="{{ old('perimetro', $zona->perimetro) }}">
                                    @error('perimetro')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <div class="alert alert-light border mt-2 mb-0">
                                        <h6 class="mb-2"><i class="fas fa-info-circle text-info"></i> Leyenda del Mapa:</h6>
                                        <ul class="mb-0 pl-3">
                                            <li><strong style="color: #0056b3;">Azul sólido:</strong> Zona actual que está editando</li>
                                            <li><strong style="color: #dc3545;">Rojo punteado:</strong> Otras zonas registradas (evitar superposición)</li>
                                        </ul>
                                        <small class="text-muted d-block mt-2">
                                            💡 Puede hacer clic en las zonas rojas para ver su información
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                <a href="{{ route('zonas.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
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
        // Initialize Select2
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        });

        // Initialize the map
        var map = L.map('map').setView([-6.7711, -79.8408], 13); // Coordenadas de Chiclayo

        // Add the OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Initialize the FeatureGroup to store editable layers
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        // Variable para almacenar la capa actual siendo dibujada
        var currentLayer = null;
        var validationTimer = null;
        var zonaId = {{ $zona->id }};
        var isZoneValidated = true; // Inicialmente true porque ya existe una zona válida

        // Initialize the draw control
        var drawControl = new L.Control.Draw({
            draw: {
                marker: false,
                circle: false,
                circlemarker: false,
                rectangle: false,
                polyline: false,
                polygon: {
                    allowIntersection: false,
                    drawError: {
                        color: '#e1e100',
                        message: '<strong>Error:</strong> ¡Los bordes no pueden cruzarse!'
                    },
                    shapeOptions: {
                        color: '#3388ff'
                    }
                }
            },
            edit: {
                featureGroup: drawnItems,
                remove: true
            }
        });
        map.addControl(drawControl);

        // Handle the created layers
        map.on('draw:created', function(e) {
            var layer = e.layer;
            
            // Validar superposición antes de agregar
            validateOverlap(layer, function(isValid, message, overlappingZones) {
                if (!isValid) {
                    // Mostrar modal de error con las zonas conflictivas
                    showOverlapModal(overlappingZones);
                    isZoneValidated = false;
                    return;
                }
                
                // Si es válido, agregar la capa
                drawnItems.clearLayers(); // Remove any existing polygons
                drawnItems.addLayer(layer);
                currentLayer = layer;
                isZoneValidated = true;
            
                // Save the GeoJSON to the hidden input
                var geoJSON = layer.toGeoJSON();
                $('#perimetro').val(JSON.stringify(geoJSON));

                // Calculate and update the area
                var area = L.GeometryUtil.geodesicArea(layer.getLatLngs()[0]);
                $('#area').val((area / 1000000).toFixed(2)); // Convert to km²
                
                // Mostrar modal de éxito
                showSuccessModal();
            });
        });

        // Handle edited layers
        map.on('draw:edited', function(e) {
            var layers = e.layers;
            var isValid = true;
            
            layers.eachLayer(function(layer) {
                // Validar superposición después de editar
                validateOverlap(layer, function(valid, message, overlappingZones) {
                    if (!valid) {
                        isValid = false;
                        isZoneValidated = false;
                        // Mostrar modal de error
                        showOverlapModal(overlappingZones);
                    } else {
                        isZoneValidated = true;
                        var geoJSON = layer.toGeoJSON();
                        $('#perimetro').val(JSON.stringify(geoJSON));

                        // Update area
                        var area = L.GeometryUtil.geodesicArea(layer.getLatLngs()[0]);
                        $('#area').val((area / 1000000).toFixed(2));
                        
                        // Mostrar modal de éxito
                        showSuccessModal();
                    }
                });
            });
        });

        // Handle deleted layers
        map.on('draw:deleted', function(e) {
            $('#perimetro').val('');
            $('#area').val('');
            isZoneValidated = false;
        });

        // Load existing perimeter if any
        var existingPerimeter = $('#perimetro').val();
        if (existingPerimeter) {
            try {
                var geoJSON = JSON.parse(existingPerimeter);
                var layer = L.geoJSON(geoJSON).getLayers()[0];
                drawnItems.addLayer(layer);
                currentLayer = layer;
                map.fitBounds(layer.getBounds());

                // Calculate and set initial area
                var area = L.GeometryUtil.geodesicArea(layer.getLatLngs()[0]);
                $('#area').val((area / 1000000).toFixed(2));
            } catch (e) {
                console.error('Error loading existing perimeter:', e);
            }
        }

        // Mostrar otras zonas registradas en el mapa (en rojo/naranja para distinguirlas)
        var otherZones = {!! json_encode($zonas) !!};
        otherZones.forEach(function(zona) {
            if (zona.perimetro) {
                try {
                    var perimetro = JSON.parse(zona.perimetro);
                    if (perimetro && perimetro.geometry && perimetro.geometry.coordinates) {
                        var latLngs = perimetro.geometry.coordinates[0].map(function(point) {
                            return [point[1], point[0]];
                        });
                        
                        // Crear polígono de zona existente con estilo diferente
                        var polygon = L.polygon(latLngs, { 
                            color: '#FF6B6B',      // Rojo coral
                            weight: 2, 
                            opacity: 0.6, 
                            fillOpacity: 0.15,
                            fillColor: '#FF6B6B',
                            dashArray: '5, 10'     // Línea punteada
                        }).addTo(map);
                        
                        // Popup con información de la zona
                        var popupContent = 
                            '<div style="min-width: 200px;">' +
                            '<h6 class="mb-2"><i class="fas fa-map-marked-alt text-danger"></i> ' + zona.nombre + '</h6>' +
                            '<small><strong>Código:</strong> ' + zona.codigo + '</small><br>' +
                            (zona.area ? '<small><strong>Área:</strong> ' + zona.area + ' km²</small><br>' : '') +
                            '<small class="text-muted">⚠️ Zona existente - Evitar superposición</small>' +
                            '</div>';
                        
                        polygon.bindPopup(popupContent);
                        
                        // Efecto hover para zonas existentes
                        polygon.on('mouseover', function(e) {
                            this.setStyle({
                                weight: 3,
                                opacity: 0.8,
                                fillOpacity: 0.3
                            });
                        });
                        
                        polygon.on('mouseout', function(e) {
                            this.setStyle({
                                weight: 2,
                                opacity: 0.6,
                                fillOpacity: 0.15
                            });
                        });
                    }
                } catch (e) {
                    console.error('Error al parsear el perímetro de la zona existente:', zona.nombre, e);
                }
            }
        });

        /**
         * Función para validar si una capa se superpone con zonas existentes
         */
        function validateOverlap(layer, callback) {
            var geoJSON = layer.toGeoJSON();
            
            $.ajax({
                url: '{{ route("zonas.check-overlap") }}',
                method: 'POST',
                data: {
                    perimetro: JSON.stringify(geoJSON),
                    zona_id: zonaId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        callback(!response.overlap, response.message, response.zones);
                    } else {
                        callback(false, 'Error al validar', []);
                    }
                },
                error: function(xhr) {
                    console.error('Error al validar superposición:', xhr);
                    callback(true, 'No se pudo validar, continuando...', []);
                }
            });
        }

        /**
         * Mostrar modal de superposición con zonas conflictivas
         */
        function showOverlapModal(overlappingZones) {
            // Limpiar lista anterior
            $('#overlappingZonesList').empty();
            
            // Agregar zonas a la lista
            overlappingZones.forEach(function(zona) {
                $('#overlappingZonesList').append(
                    '<li class="mb-2">' +
                    '<i class="fas fa-map-marked-alt text-danger"></i> ' +
                    '<strong>' + zona.nombre + '</strong> ' +
                    '<span class="badge badge-secondary">' + zona.codigo + '</span>' +
                    '</li>'
                );
            });
            
            // Mostrar el modal
            $('#overlapWarningModal').modal('show');
        }

        /**
         * Mostrar modal de validación exitosa
         */
        function showSuccessModal() {
            $('#successValidationModal').modal('show');
            
            // Auto-cerrar después de 2 segundos
            setTimeout(function() {
                $('#successValidationModal').modal('hide');
            }, 2000);
        }

        /**
         * Validar antes de enviar el formulario
         */
        $('form').on('submit', function(e) {
            var perimetro = $('#perimetro').val();
            
            if (!perimetro) {
                return true; // Permitir envío si no hay perímetro
            }
            
            // Verificar si la zona ha sido validada exitosamente
            if (!isZoneValidated) {
                e.preventDefault();
                
                // Mostrar modal de advertencia
                var $warningModal = $(
                    '<div class="modal fade" id="validationWarningModal" tabindex="-1">' +
                    '<div class="modal-dialog modal-dialog-centered">' +
                    '<div class="modal-content">' +
                    '<div class="modal-header bg-warning">' +
                    '<h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> Validación Pendiente</h5>' +
                    '<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>' +
                    '</div>' +
                    '<div class="modal-body">' +
                    '<p><i class="fas fa-info-circle"></i> La zona aún está siendo validada o tiene conflictos de superposición.</p>' +
                    '<p>Por favor, espere a que aparezca la confirmación de validación exitosa antes de guardar.</p>' +
                    '</div>' +
                    '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-secondary" data-dismiss="modal">Entendido</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>'
                );
                
                $('body').append($warningModal);
                $warningModal.modal('show');
                $warningModal.on('hidden.bs.modal', function() {
                    $(this).remove();
                });
                
                return false;
            }
            
            return true;
        });
    });
</script>
@endpush