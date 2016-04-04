<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="utf-8">
        <title><?php echo isset($title)?$title:''; ?></title>
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no,target-densitydpi=device-dpi" />
    
        <!-- Loading Bootstrap -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
        <!--
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
        -->
        
        <!-- Loading Flat UI -->
        <!--
        <link href="<?php echo base_url('assets/css/flat-ui.min.css'); ?>" rel="stylesheet">
        -->
        <link rel="stylesheet" href="<?php echo base_url('assets/css/flat-ui.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/ui-kit-styles.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">

        <link rel="shortcut icon" href="<?php echo base_url('assets/img/favicon.ico'); ?>">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
        <!--[if lt IE 9]>
            <script src="<?php echo base_url('assets/js/vendor/html5shiv.js'); ?>"></script>
            <script src="<?php echo base_url('assets/js/vendor/respond.min.js'); ?>"></script>
        <![endif]-->
    </head>
  
    <body>
    <?php echo $this->session->flashdata('message'); ?>
    <?php if($this->session->flashdata('message')) : ?>
    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
        </button>
        <?php echo $this->session->flashdata('message'); ?>
    </div>
    <?php endif; ?>

  <!-- <div class="container"> -->