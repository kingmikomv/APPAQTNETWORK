<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>{{config('app.name')}}</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{asset('dbs/assets/modules/bootstrap/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('dbs/assets/modules/fontawesome/css/all.min.css')}}">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{asset('dbs/assets/modules/jqvmap/dist/jqvmap.min.css')}}">
  <link rel="stylesheet" href="{{asset('dbs/assets/modules/summernote/summernote-bs4.css')}}">
  <link rel="stylesheet" href="{{asset('dbs/assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css')}}">
  <link rel="stylesheet" href="{{asset('dbs/assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css')}}">

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{asset('dbs/assets/css/style.css')}}">
  <link rel="stylesheet" href="{{asset('dbs/assets/css/components.css')}}">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" />
  <!-- Custom CSS for wider layout -->
  <style>
    /* Mengatur margin dan padding body agar lebih luas */
    body {
      font-size: 0.9em; /* Menjadikan semua teks lebih kecil */
      margin: 0;
      padding: 0;
      width: 100%;
    }

    /* Memastikan konten menggunakan lebar penuh */
    .container, .container-fluid {
      width: 100% !important; /* Memastikan konten memenuhi lebar layar */
      padding-left: 0;
      padding-right: 0;
    }

    /* Card lebih luas */
    .wide-card {
      width: 100%;
      margin: 0;
      border-radius: 5px; /* Rounded corners */
      overflow: hidden; /* Ensures the content stays within the rounded edges */
      padding: 0;
    }

    /* Mengatur padding untuk konten dalam kartu */
    .card-body, .card-header {
      padding: 1rem; /* Optional: Adjust padding */
    }

    /* Atur padding lebih kecil untuk layar kecil */
    @media (max-width: 768px) {
      .wide-card .card-body {
        padding: 1rem;
      }

      .wide-card h3 {
        font-size: 1.5rem;
      }

      .wide-card h6 {
        font-size: 1.2rem;
      }
    }

    @media (max-width: 576px) {
      .wide-card .card-body {
        padding: 0.5rem;
      }

      .wide-card h3 {
        font-size: 1.2rem;
      }
    }

    /* Menghapus spasi antar kolom */
    .no-gutters {
      margin-right: 0;
      margin-left: 0;
    }

    .no-gutters > .col, .no-gutters > [class*="col-"] {
      padding-right: 0;
      padding-left: 0;
    }

    /* Menambah lebar halaman dengan menghapus margin default */
    .container {
      margin-left: 0;
      margin-right: 0;
    }

    /* Atur bagian header dan footer agar lebar penuh */
    header, footer {
      width: 100%;
      padding: 0;
      margin: 0;
    }

  .btn-gradient {
    background: linear-gradient(to right, #6a11cb, #2575fc); /* Gradient dari ungu ke biru */
    color: white; /* Warna teks */
    border: none; /* Menghapus border */
    padding: 10px 20px; /* Padding pada tombol */
    font-size: 16px; /* Ukuran teks */
    border-radius: 5px; /* Membuat sudut tombol melengkung */
    cursor: pointer; /* Menambahkan kursor pointer saat di-hover */
    transition: background 0.3s ease; /* Transisi halus saat di-hover */
  }

  /* Efek hover pada tombol */
  .btn-gradient:hover {
    background: linear-gradient(to right, #2575fc, #6a11cb); /* Membalikkan warna gradien saat hover */
  }


  
</style>



</head>

<body class="sidebar-mini">
 