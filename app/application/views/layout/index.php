<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('includes/head'); ?>
    <body>
        <?php
        if ($topbar) {
            $this->load->view($topbar);
        }
        ?>
        <div id="content" class="box">
            <?php
            if ($left_menu) {
                $this->load->view($left_menu);
            }
            ?>
            <div id="page-container" class="box-content">
                <div id="pre-loader">
                    <div id="pre-loade" class="app-loader"><div class="loading"></div></div>
                </div>
                <div class="scrollable-page">
                    <?php
                    if (isset($content_view) && $content_view != "") {
                        $this->load->view($content_view);
                    }
                    ?>
                </div>


               <!-- Animación usada al momento de cliquear un link perteneciente a algun tipo de huella (Ambiental, Carbono, Agua)-->
               <div id="loading-gif" hidden >  <img src="<?php echo_uri('assets/images/'); ?>loading_2_text.gif" >  </div>
                <style>
                    #loading-gif {
                        /* background: url('<?php echo_uri('assets/images/'); ?>loading_2.gif') no-repeat center center; */
                        background-color: rgba(229,233,236,0.5);
                        z-index: 9999;
                        position: fixed;
                        margin: auto;
                        width: 100%;
                        height: 100%;
                        left: 0;
                        right: 0;
                        top: 0;
                        bottom: 0;
                    }
                    #loading-gif >img {
                        max-width: 40%;
                        max-height: 40%;
                        width: auto;
                        height: auto;
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        transform: translate(-50%, -50%);
                    }
                
                </style>

            </div>
        </div>
        <?php $this->load->view('modal/index'); ?>
        <?php $this->load->view('modal/confirmation'); ?>
        <?php $this->load->view('modal/file_confirmation'); ?>
        <?php $this->load->view('modal/multiple_confirmation'); ?>
        <div style='display: none;'>
            <script type='text/javascript'>
<?php
$error_message = $this->session->flashdata("error_message");
$success_message = $this->session->flashdata("success_message");
if (isset($error)) {
    echo 'appAlert.error("' . $error . '");';
}
if (isset($error_message)) {
    echo 'appAlert.error("' . $error_message . '");';
}
if (isset($success_message)) {
    echo 'appAlert.success("' . $success_message . '", {duration: 10000});';
}
?>
            </script>
        </div>

    </body>
</html>