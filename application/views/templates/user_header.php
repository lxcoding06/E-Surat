<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?php echo base_url();?>/assets/"
  data-template="vertical-menu-template-free"
>
<head>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownButton = document.getElementById('pengesahanDropdown');
        var dropdownMenuItems = document.querySelectorAll('.dropdown-menu .dropdown-item[name="pengesahan"]');

        dropdownMenuItems.forEach(function(item) {
            item.addEventListener('click', function() {
                var selectedValue = item.getAttribute('value');
                dropdownButton.innerHTML = (selectedValue == 1) ? 'Belum sah' : 'Sah';

                // Mengubah warna berdasarkan nilai yang dipilih
                dropdownButton.classList.remove('btn-outline-danger', 'btn-outline-success');
                dropdownButton.classList.add((selectedValue == 1) ? 'btn-outline-danger' : 'btn-outline-success');

                // Mengatur nilai input pengesahan pada form
                document.getElementById('inputPengesahan').value = selectedValue;
            });
        });
    });
</script>


    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title><?= $title; ?></title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url();?>/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?php echo base_url();?>/assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo base_url();?>/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo base_url();?>/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo base_url();?>/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo base_url();?>/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="<?php echo base_url();?>/assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="<?php echo base_url();?>/assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?php echo base_url();?>/assets/js/config.js"></script>
  </head>
  <body>
    