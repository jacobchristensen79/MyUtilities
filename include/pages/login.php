<article class="access-page">

            <section id="access" class="clearfix">
                <div class="row">
                    <div class="page">
                        <div class="col white">&nbsp</div>
                        <div class="col access">
                            <div class="access-box">
                                <h3>Acceso</h3>
                                <?php 
                                   	if ( isset($errors) && !empty($errors)){
                                   		foreach ($errors as $error) echo '<span class="error">'.$error.'</span>';
                                   	}
                                ?>
                                <form id="access-form" action="#" method="post">
                                	<input type="hidden" name="_token" value="<?php echo $_token; ?>">
                                    <input type="text" name="access_username" placeholder="Username" />
                                    <input type="password" name="access_password" placeholder="Password" />
                                    <input type="submit" name="login" value="Login" />
                                </form>
                            </div>
                        </div>
                        <div class="col white">&nbsp</div>
                    </div>
                </div>
            </section>

</article>