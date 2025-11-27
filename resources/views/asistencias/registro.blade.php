<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro de Asistencia - RSU Sistema</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Source Sans Pro', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 450px;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2d3748;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header p {
            color: #4a5568;
            font-size: 16px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #4a5568;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 18px;
        }

        .form-control {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control.is-invalid {
            border-color: #f56565;
        }

        .invalid-feedback {
            color: #f56565;
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        .btn-primary {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #718096;
            font-size: 14px;
        }

        .time-display {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 10px;
        }

        .time-display .date {
            font-size: 14px;
            color: #718096;
            margin-bottom: 5px;
        }

        .time-display .time {
            font-size: 24px;
            color: #2d3748;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Bienvenido</h1>
                <p>Registro de Asistencia</p>
            </div>

            <div class="time-display">
                <div class="date" id="currentDate"></div>
                <div class="time" id="currentTime"></div>
            </div>

            <form id="formRegistroAsistencia">
                <div class="form-group">
                    <label for="dni">Documento de identidad</label>
                    <div class="input-group">
                        <i class="fas fa-id-card"></i>
                        <input type="text" 
                               class="form-control" 
                               id="dni" 
                               name="dni" 
                               placeholder="Ingrese su DNI"
                               maxlength="8"
                               pattern="[0-9]{8}"
                               required>
                    </div>
                    <small class="invalid-feedback dni-error"></small>
                </div>

                <div class="form-group">
                    <label for="clave">Contraseña</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" 
                               class="form-control" 
                               id="clave" 
                               name="clave" 
                               placeholder="Ingrese su contraseña"
                               required>
                    </div>
                    <small class="invalid-feedback clave-error"></small>
                </div>

                <button type="submit" class="btn-primary" id="btnSubmit">
                    <span id="btnText">Registrar Asistencia</span>
                    <div class="spinner" id="spinner"></div>
                </button>
            </form>

            <div class="footer-text">
                <p>Sistema de Gestión RSU - Grupo 09</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function updateDateTime() {
            const now = new Date();
            
            const optionsDate = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            const dateStr = now.toLocaleDateString('es-PE', optionsDate);
            
            const optionsTime = { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            };
            const timeStr = now.toLocaleTimeString('es-PE', optionsTime);
            
            $('#currentDate').text(dateStr.charAt(0).toUpperCase() + dateStr.slice(1));
            $('#currentTime').text(timeStr);
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);

        $('#dni').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('#dni, #clave').on('input', function() {
            $(this).removeClass('is-invalid');
            $('.invalid-feedback').text('');
        });

        $('#formRegistroAsistencia').on('submit', function(e) {
            e.preventDefault();
            
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            const dni = $('#dni').val().trim();
            const clave = $('#clave').val();
            
            if (dni.length !== 8) {
                $('#dni').addClass('is-invalid');
                $('.dni-error').text('El DNI debe tener 8 dígitos');
                return;
            }
            
            if (!clave) {
                $('#clave').addClass('is-invalid');
                $('.clave-error').text('La contraseña es obligatoria');
                return;
            }
            
            $('#btnSubmit').prop('disabled', true);
            $('#btnText').hide();
            $('#spinner').show();
            
            $.ajax({
                url: '{{ route("asistencias.registrar.proceso") }}',
                method: 'POST',
                data: {
                    dni: dni,
                    clave: clave,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        let icon = 'success';
                        let title = response.tipo === 'entrada' ? '¡Entrada Registrada!' : '¡Salida Registrada!';
                        
                        Swal.fire({
                            icon: icon,
                            title: title,
                            html: `
                                <p><strong>Empleado:</strong> ${response.empleado}</p>
                                <p><strong>Hora:</strong> ${response.hora}</p>
                                <p><strong>Fecha:</strong> ${response.fecha}</p>
                                <p><strong>Mensaje:</strong> ${response.message}</p>
                            `,
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#667eea'
                        }).then(() => {
                            $('#formRegistroAsistencia')[0].reset();
                        });
                    }
                },
                error: function(xhr) {
                    let message = 'Ocurrió un error inesperado al registrar la asistencia.';
                    let icon = 'error';
                    
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors) {
                            if (errors.dni) {
                                $('#dni').addClass('is-invalid');
                                $('.dni-error').text(errors.dni[0]);
                            }
                            if (errors.clave) {
                                $('#clave').addClass('is-invalid');
                                $('.clave-error').text(errors.clave[0]);
                            }
                        }
                        message = xhr.responseJSON.message || 'Datos incompletos o incorrectos.';
                        icon = 'warning'; 
                    } else if (xhr.status === 401) {
                        message = xhr.responseJSON.message || 'DNI o contraseña incorrectos';
                        $('#dni').addClass('is-invalid');
                        $('#clave').addClass('is-invalid');
                        $('#formRegistroAsistencia')[0].reset(); 
                    } else if (xhr.status === 403) {
                        message = xhr.responseJSON.message || 'Acceso denegado o error de programación.';
                        icon = 'warning'; 
                        $('#formRegistroAsistencia')[0].reset(); 
                    } else {
                        message = xhr.responseJSON.message || message;
                        $('#formRegistroAsistencia')[0].reset(); 
                    }
                    
                    Swal.fire({
                        icon: icon,
                        title: 'Error de Registro',
                        text: message,
                        confirmButtonColor: '#f56565'
                    });
                },
                complete: function() {
                    $('#btnSubmit').prop('disabled', false);
                    $('#btnText').show();
                    $('#spinner').hide();
                }
            });
        });
    </script>
</body>
</html>
