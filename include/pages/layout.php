<!DOCTYPE html>
<!--[if IE 8]>         <html class="no-js ie8"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>iXP Url Shortner</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="/css/normalize.min.css">
        <link rel="stylesheet" href="/css/gridpak.css">
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="/css/jquery.bxslider.css">
        <link rel="stylesheet" href="/css/white.css">

        <script src="/js/assets/jquery-1.10.2.min.js"></script>
        <script src="/js/assets/jquery-1.10.3.ui.js"></script>
        <script src="/js/assets/modernizr-2.6.2.min.js"></script>
        <script src="/js/assets/jquery.validate.js"></script>
        <script src="/js/assets/jquery.bxslider.min.js"></script>
        <script src="/js/assets/jquery.icheck.min.js"></script>
    </head>
    <body>
        
        <!-- Header -->

    	<header id="header" class="clearfix">
    		<div class="row">
                <div class="page">
    				<div class="col main-logo">
    			         <a class="enlace" href="/" title="">Home</a>
    				</div>
    				<div class="col main-nav">
						<ul id="nav" class="nav-menu">
							<?php if($_username){ ?>
							<li><a class="enlace" href="/" title="">Home</a></li>
							<li><a class="enlace" href="/<?php echo App::url('account', $_lang);?>" title="">Profile</a></li>
							<li><a class="enlace" href="/<?php echo App::url('logout', $_lang);?>" title="">Logout</a></li>	
							<?php } ?>				
						</ul>
    				</div>
                </div>
    		</div>

            <!-- Responsive Menu -->

            <div class="row">
                <div class="col span_12">
                    <ul id="rm-nav" class="row js">
							<?php if($_username){ ?>
							<li><a class="enlace" href="/" title="">Home</a></li>
							<li><a class="enlace" href="/<?php echo App::url('account', $_lang);?>" title="">Profile</a></li>
							<li><a class="enlace" href="/<?php echo App::url('logout', $_lang);?>" title="">Logout</a></li>	
							<?php } ?>	
                    </ul>
                </div>
            </div>

            <!-- End Responsive Menu -->

    	</header>

        <!-- End Header -->

        <!-- CONTENT -->
        
        <?php echo $_content; ?>
        
        <!-- END CONTENT -->

        <!-- Footer -->

        <footer id="footer" class="clearfix">
            <div class="row">
                <div class="page">
                    <ul class="col footer-menu">
                        <li><a class="enlace" href="<?php echo App::url('faq', $_lang) ?>" title="">FAQ</a><span>·</span></li>
                        <li><a class="enlace" href="#" title="">Contacto</a></li>
                    </ul>
                </div>
            </div>
        </footer>

        <!-- End Footer -->

        <script src="/js/main.js"></script>
    </body>
</html>