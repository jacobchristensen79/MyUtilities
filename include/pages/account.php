<?php //echo $trans->t('page','form_surname'); ?>

<article class="home-page">

            <section class="clearfix">
                <div class="row">                	
                    <div class="page">
                        <div class="col urlbox">
                        	<h3>User Profile</h3>
                            <div id="code-box">
                            	Username: <?php echo $_username?>
                           </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">                	
                    <div class="page">
                        <div class="col urlbox">
                        	<h3>User Links</h3>
                            <div id="code-box">
	                            <ul>                            
	                            	<?php 
	                            		foreach ($userlinks as $link){
											$urlLink = App::url('goto', $_lang, $link->shortcode, true);
	                            			echo '<li><a class="enlace" href="'.$urlLink.'" target="_blank">'.$link->shortcode.'</a>: '.$link->clicks.'</li>'."\n";
	                            		}
	                            	?>
	                            </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="clearfix">
                <div class="row">
                    <div class="page">
                        <div class="col title-box">
                            <h2>Domain Details</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="page">
                        <div class="col travel-box">
                            <h3>Top 5 TLD´s</h3>
                            <ul>
                            	<?php 
                            		foreach ($domaintypes as $domain){
                            			echo '<li>'.$domain->name.': '.$domain->quantity.'</li>'."\n";
                            		}
                            	?>
                            </ul>
                        </div>
                        <div class="col travel-box">
                            <h3>Top 5 Domains</h3>
                            <ul>
                            	<?php 
                            		foreach ($domains as $domain){
                            			echo '<li>'.$domain->name.': '.$domain->quantity.'</li>'."\n";
                            		}
                            	?>
                            </ul>
                        </div>
                        <div class="col travel-box">
                        	<h3>Top 5 Links</h3>
                            <ul>
                            	<?php 
                            		foreach ($links as $link){
										$urlLink = App::url('goto', $_lang, $link->shortcode, true);
                            			echo '<li><a class="enlace" href="'.$urlLink.'" target="_blank">'.$link->shortcode.'</a>: '.$link->clicks.'</li>'."\n";
                            		}
                            	?>
                            </ul>
                        </div>
                        <div class="col travel-box">
                        	<h3>Top 5 Your Links</h3>
                            <ul>                            
                            	<?php 
                            		foreach ($usertoplinks as $link){
										$urlLink = App::url('goto', $_lang, $link->shortcode, true);
                            			echo '<li><a class="enlace" href="'.$urlLink.'" target="_blank">'.$link->shortcode.'</a>: '.$link->clicks.'</li>'."\n";
                            		}
                            	?>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

        </article>