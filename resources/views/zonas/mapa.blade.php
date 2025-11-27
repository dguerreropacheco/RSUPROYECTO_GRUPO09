@extends('layouts.app')

@section('title', 'Mapa de Zonas')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    #map {
        height: calc(100vh - 200px);
        width: 100%;
        border-radius: 4px;
    }
    .zone-info-card {
        max-width: 250px;
    }
    .zone-legend {
        background: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    .zone-legend h4 {
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: bold;
    }
    .legend-item {
        margin: 5px 0;
        font-size: 12px;
    }
    .legend-color {
        display: inline-block;
        width: 20px;
        height: 15px;
        margin-right: 5px;
        border: 1px solid #333;
    }
</style>
@endpush

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Mapa General de Zonas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('zonas.index') }}">Zonas</a></li>
                    <li class="breadcrumb-item active">Mapa General</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marked-alt"></i> Visualización de Todas las Zonas Registradas
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ count($zonas) }} zonas registradas</span>
                    <a href="{{ route('zonas.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="map"></div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    $(function() {
        // Coordenadas de Chiclayo como centro inicial
        var map = L.map('map').setView([-6.7711, -79.8408], 13);

        // Agregar tiles de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Colores para las zonas
        var colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
            '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B739', '#52BE80',
            '#AF7AC5', '#5DADE2', '#48C9B0', '#F5B041', '#EC7063',
            '#3498DB', '#E74C3C', '#9B59B6', '#1ABC9C', '#F39C12'
        ];

        var zonas = {!! json_encode($zonas) !!};
        var bounds = [];
        var zonasValidas = 0;

        // Crear grupo de capas para las zonas
        var zonasLayer = L.featureGroup();

        zonas.forEach(function(zona, index) {
            if (zona.perimetro) {
                try {
                    var perimetro = JSON.parse(zona.perimetro);
                    
                    if (perimetro && perimetro.geometry && perimetro.geometry.coordinates) {
                        var coordinates = perimetro.geometry.coordinates[0];
                        
                        // Convertir coordenadas GeoJSON a formato Leaflet [lat, lng]
                        var latLngs = coordinates.map(function(point) {
                            return [point[1], point[0]]; // GeoJSON es [lng, lat], Leaflet es [lat, lng]
                        });

                        // Seleccionar color
                        var color = colors[index % colors.length];

                        // Crear polígono
                        var polygon = L.polygon(latLngs, {
                            color: color,
                            fillColor: color,
                            fillOpacity: 0.3,
                            weight: 3,
                            opacity: 0.8
                        });

                        // Crear contenido del popup
                        var popupContent = 
                            '<div class="zone-info-card">' +
                            '<h5 class="mb-2"><i class="fas fa-map-marker-alt"></i> ' + zona.nombre + '</h5>' +
                            '<hr class="my-2">' +
                            '<p class="mb-1"><strong>Código:</strong> ' + zona.codigo + '</p>' +
                            (zona.distrito ? '<p class="mb-1"><strong>Distrito:</strong> ' + zona.distrito.nombre + '</p>' : '') +
                            (zona.area ? '<p class="mb-1"><strong>Área:</strong> ' + zona.area + ' km²</p>' : '') +
                            (zona.poblacion_estimada ? '<p class="mb-1"><strong>Población:</strong> ' + zona.poblacion_estimada.toLocaleString() + '</p>' : '') +
                            (zona.descripcion ? '<p class="mb-1"><strong>Descripción:</strong> ' + zona.descripcion + '</p>' : '') +
                            '<p class="mb-0"><small class="text-muted">Estado: ' + (zona.activo ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>') + '</small></p>' +
                            '</div>';

                        polygon.bindPopup(popupContent);
                        
                        // Agregar evento hover
                        polygon.on('mouseover', function(e) {
                            this.setStyle({
                                weight: 5,
                                fillOpacity: 0.5
                            });
                        });
                        
                        polygon.on('mouseout', function(e) {
                            this.setStyle({
                                weight: 3,
                                fillOpacity: 0.3
                            });
                        });

                        // Agregar al grupo de capas
                        polygon.addTo(zonasLayer);

                        // Guardar bounds para ajustar el zoom
                        bounds.push(...latLngs);
                        zonasValidas++;
                    }
                } catch (e) {
                    console.error('Error al parsear el perímetro de la zona:', zona.nombre, e);
                }
            }
        });

        // Agregar el grupo de capas al mapa
        zonasLayer.addTo(map);

        // Ajustar el zoom para mostrar todas las zonas
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }

        // Crear leyenda
        var legend = L.control({ position: 'bottomright' });
        
        legend.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'zone-legend');
            div.innerHTML = '<h4><i class="fas fa-list"></i> Zonas (' + zonasValidas + ')</h4>';
            
            zonas.forEach(function(zona, index) {
                if (zona.perimetro) {
                    var color = colors[index % colors.length];
                    div.innerHTML += 
                        '<div class="legend-item">' +
                        '<span class="legend-color" style="background-color: ' + color + '"></span>' +
                        '<span>' + zona.nombre + '</span>' +
                        '</div>';
                }
            });
            
            return div;
        };
        
        legend.addTo(map);

        // Agregar control de escala
        L.control.scale({ imperial: false, metric: true }).addTo(map);

        // Mostrar mensaje si no hay zonas
        @if(count($zonas) === 0)
            alert('No hay zonas registradas para mostrar en el mapa.');
        @endif
    });
</script>
@endpush