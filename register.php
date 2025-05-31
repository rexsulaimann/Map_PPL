<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Kodinger">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register - Aplikasi Data Mahasiswa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/my-login.css">
</head>
<body class="my-login-page">
    <section class="h-100">
        <div class="container h-100">
            <div class="row justify-content-md-center h-100">
                <div class="card-wrapper">
                    <div class="brand">
                        <img src="img/logo.jpg" alt="logo">
                    </div>
                    <div class="card fat">
                        <div class="card-body">
                            <h4 class="card-title">Register Mahasiswa Baru</h4>

                            <?php
                            // Untuk menampilkan pesan error/sukses dari proses registrasi
                            if (isset($_GET['error'])) {
                                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
                            }
                            if (isset($_GET['success'])) {
                                echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_GET['success']) . '</div>';
                            }
                            ?>

                            <form method="POST" action="php/proses_registrasi.php" class="my-login-validation" novalidate="">
                                <div class="form-group">
                                    <label for="nim">NIM</label>
                                    <input id="nim" type="text" class="form-control" name="nim" required autofocus>
                                    <div class="invalid-feedback">
                                        NIM tidak boleh kosong.
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="nama">Nama Lengkap</label>
                                    <input id="nama" type="text" class="form-control" name="nama" required>
                                    <div class="invalid-feedback">
                                        Nama lengkap tidak boleh kosong.
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tgl_lahir">Tanggal Lahir</label>
                                    <input id="tgl_lahir" type="date" class="form-control" name="tgl_lahir" required>
                                    <div class="invalid-feedback">
                                        Tanggal lahir tidak boleh kosong.
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea id="alamat" class="form-control" name="alamat" rows="3"></textarea>
                                    </div>

                                <div class="form-group">
                                    <label for="telpon">Nomor Telepon</label>
                                    <input id="telpon" type="text" class="form-control" name="telpon">
                                    </div>

                                <div class="form-group">
                                    <label for="kesukaan">Kesukaan</label>
                                    <input id="kesukaan" type="text" class="form-control" name="kesukaan">
                                    </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input id="password" type="password" class="form-control" name="password" required data-eye>
                                    <div class="invalid-feedback">
                                        Password tidak boleh kosong.
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-checkbox custom-control">
                                        <input type="checkbox" name="agree" id="agree" class="custom-control-input" required="">
                                        <label for="agree" class="custom-control-label">Saya menyetujui <a href="#">Syarat dan Ketentuan</a></label>
                                        <div class="invalid-feedback">
                                            Anda harus menyetujui Syarat dan Ketentuan.
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group m-0">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        Register
                                    </button>
                                </div>
                                <div class="mt-4 text-center">
                                    Sudah punya akun? <a href="index.php">Login</a>
                                    </div>
                            </form>
                        </div>
                    </div>
                    <div class="footer">
                        Copyright &copy; 2024 &mdash; Your Company 
                        </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="js/my-login.js"></script> </body>
</html>