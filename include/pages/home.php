<?php //echo $trans->t('page','form_surname'); ?>

<article class="home-page">

            <section class="clearfix">
                <div class="row">
                    <div class="page">
                        <div class="col hello-box">
                            <div class="hello">
                                <div class="user-box">
                                    <h1>Hola, <span><?php echo $_username ?></span></h1>
                                </div>
                                <div class="user-codes">
                                    <p>Short Code Maker</p>
                                </div>
                            </div>
                        </div>
                        <div class="col urlbox">
                            <form id="home-form" action="#">
                                    <h3>Enter new url</h3>
                                    <div id="code-box">
                                        <input type="text" name="url" placeholder="http:// Link" />
                                    </div>
                                    <input type="submit" name="" value="Send" />
                                    <span class="btn_loader"></span>
                            </form>
                        </div>
                        
                        <div class="col" id="server_response">
                            <h4 class="result"></h4>
                            <input type="text" readonly="readonly" class="resultlink">
                        </div>
                    </div>
                </div>
            </section>
            

        </article>