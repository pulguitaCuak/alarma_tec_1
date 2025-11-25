<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="css/bootstrap.custom.min.css">
    <link rel="stylesheet" href="bootstrap-icons-1.13.1/bootstrap-icons.css">
    <style>
        /* Ajuste de layout general */
        body {
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        .sidebar {
            width: 80px;
            min-height: 100%;
        }

        .card {
            width: 100%;
            max-width: 400px;
        }

        /* Mensaje cuando no hay equipos */
        #noEquipos {
            display: none;
            width: 100%;
            text-align: center;
            color: #6c757d;
            font-size: 1.3rem;
            margin-top: 4rem;
        }

        /* Responsive: sidebar oculta en móvil */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                background-color: #6c757d;
                transition: left 0.3s ease;
                z-index: 1050;
            }

            .sidebar.show {
                left: 0;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header>
        <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <!-- Botón hamburguesa móvil -->
                    <button class="btn btn-outline-light d-lg-none me-2" id="toggleSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <a class="navbar-brand" href="#">Alarma</a>
                </div>
                <div>
                    <a class="navbar-brand">Registro</a>
                    <i class="bi bi-ui-checks-grid"></i>
                </div>
            </div>
        </nav>
    </header>

    <!-- MAIN -->
    <main>
        <div class="d-flex h-100">

            <!-- SIDEBAR -->
            <div id="idsidebar" class="bg-secondary sidebar d-flex flex-column align-items-center py-3">
                <ul id="ulsidebar" class="nav nav-pills flex-column text-center">
                    <li class="nav-item mb-3">
                        <a href="#" class="nav-link text-white">
                            <i class="bi bi-house-door fs-4"></i>
                        </a>
                    </li>
                    <li class="nav-item mb-3">
                        <a href="#" class="nav-link text-white">
                            <i class="bi bi-box fs-4"></i>
                        </a>
                    </li>
                    <li class="nav-item mb-3">
                        <a href="#" class="nav-link text-white">
                            <i class="bi bi-record-circle fs-4"></i>
                        </a>
                    </li>
                </ul>
            </div>



            <!-- CONTENEDOR DE CARDS -->

            <!-- REGISTRO DE USUARIOS -->
            <div class="flex-grow-1 d-flex flex-wrap justify-content-center gap-3 p-3" id="cardsContainer">
                <div class="card text-black bg-white p-3 w-100 rounded-5 border-2 border-black"
                    style="max-width: 38rem; margin: 5%;">
                    <div class="card-body">
                        <form action="../back-end/registro.php" method="post">
                            <fieldset>
                                <legend class="text-center fs-2">Registrarse</legend>
                                <div class="fs-4">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="col-form-label mt-1" for="usuario">Nombre</label>
                                            <input type="text" class="form-control" placeholder="Usuario" id="usuario"
                                                name="usuario" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="col-form-label mt-1" for="apellido">Apellido</label>
                                            <input type="text" class="form-control" placeholder="Apellido" id="apellido"
                                                name="apellido" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="col-form-label mt-1" for="Telefono">Teléfono</label>
                                            <input type="text" class="form-control" placeholder="+54 9 11 26811505"
                                                id="Telefono" name="telefono" required>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="DNI" class="form-label mt-1">DNI</label>
                                        <input type="text" class="form-control" id="DNI" placeholder="DNI" name="dni"
                                            required>
                                    </div>

                                    <div>
                                        <label for="Nacimiento" class="form-label mt-1">Fecha de nacimiento</label>
                                        <input type="date" class="form-control" id="Nacimiento" name="nacimiento"
                                            required>
                                    </div>

                                    <div>
                                        <label for="Email" class="form-label mt-1">Email</label>
                                        <input type="email" class="form-control" id="Email"
                                            placeholder="pepe123@gmail.com" name="mail" required>
                                    </div>

                                    <div>
                                        <label for="Cargo" class="form-label mt-1">Cargo</label>
                                        <select class="form-select" id="Cargo" name="cargo" required>
                                            <option value="1">Administrador</option>
                                            <option value="2">Técnico</option>
                                            <option value="3" selected>Usuario</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="contraseña" class="form-label mt-1">Contraseña</label>
                                        <input type="password" class="form-control" id="contraseña"
                                            placeholder="Contraseña" autocomplete="off" name="contrasenia" required>
                                    </div>

                                    <div>
                                        <label for="confirmar" class="form-label mt-1">Confirmar</label>
                                        <input type="password" class="form-control" id="confirmar"
                                            placeholder="Confirmar contraseña" autocomplete="off" name="confirmar"
                                            required>
                                    </div>
                                    <div class="text-center mt-2">
                                        <button type="submit"
                                            class="btn btn-primary w-75 rounded-4 border-0 fs-4 fw-bold mt-4"
                                            style="min-height: 3.5rem;transition:.5s;">Siguiente</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-primary text-light pt-5 pb-3 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md mb-4 text-center">
                    <h5>Créditos</h5>
                    <ul class="list-unstyled">
                        <li class="text-light">Nombre Apellido</li>
                    </ul>
                </div>

                <div class="col-md mb-4 text-center">
                    <h5>Contacto</h5>
                    <address class="text-light">
                        Soporte: ejemplo@gmail.com<br>
                        Tel: +54 9 11 3333-3333
                    </address>
                </div>

                <div class="col-md mb-4 text-center">
                    <h5>Síguenos</h5>
                    <a href="https://www.facebook.com/tecnica1grandbourg/?locale=es_LA" class="text-light me-3"><i
                            class="bi bi-facebook me-1"></i>Facebook</a><br>
                    <a href="https://www.instagram.com/tecnica1malvinasarg/" class="text-light me-3"><i
                            class="bi bi-instagram me-1"></i>Instagram</a><br>
                </div>
            </div>

            <hr class="bg-light">

            <div class="d-flex flex-column flex-md-row justify-content-between text-center">
                <p class="mb-2 mb-md-0">&copy; 2025 Escuela Técnica N°1 Gral. San Martín Inc. Todos los derechos
                    reservados.</p>
                <ul class="list-inline mb-0">
                    <li class="list-inline-item"><a href="#" class="text-light">Privacidad</a></li>
                    <li class="list-inline-item"><a href="#" class="text-light">Términos</a></li>
                </ul>
            </div>
        </div>
    </footer>