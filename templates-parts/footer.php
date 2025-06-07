<?php
// Certifique-se de que a variável $base_url esteja definida antes de ser usada.
// Idealmente, ela deve ser definida uma vez no início do seu `header.php`
// ou em um arquivo de configuração/inicialização que seja incluído em todas as páginas.

// Para o propósito deste exemplo, vou replicar a lógica de detecção de ambiente aqui.
// Em um projeto real, evite duplicar essa lógica; defina $base_url apenas uma vez.
// $base_url = ''; 
// if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['SERVER_NAME'] === 'localhost') {
//     $base_url = '/site';
// } else {
//     $base_url = '/peterson';
// }
?>
        <footer id="footer" class="footer ">
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="single-footer">
                                <h2>About Us</h2>
                                <p>Lorem ipsum dolor sit am consectetur adipisicing elit do eiusmod tempor incididunt ut labore dolore magna.</p>
                                <ul class="social">
                                    <li><a href="#"><i class="icofont-facebook"></i></a></li>
                                    <li><a href="#"><i class="icofont-google-plus"></i></a></li>
                                    <li><a href="#"><i class="icofont-twitter"></i></a></li>
                                    <li><a href="#"><i class="icofont-vimeo"></i></a></li>
                                    <li><a href="#"><i class="icofont-pinterest"></i></a></li>
                                </ul>
                                </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="single-footer f-link">
                                <h2>Quick Links</h2>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <ul>
                                            <li><a href="<?php echo $base_url; ?>/default.php"><i class="fa fa-caret-right" aria-hidden="true"></i>Home</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>About Us</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Services</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Our Cases</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Other Links</a></li>    
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <ul>
                                            <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Consuling</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Finance</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Testimonials</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>FAQ</a></li>
                                            <li><a href="#"><i class="fa fa-caret-right" aria-hidden="true"></i>Contact Us</a></li> 
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="single-footer">
                                <h2>Open Hours</h2>
                                <p>Lorem ipsum dolor sit ame consectetur adipisicing elit do eiusmod tempor incididunt.</p>
                                <ul class="time-sidual">
                                    <li class="day">Monday - Fridayp <span>8.00-20.00</span></li>
                                    <li class="day">Saturday <span>9.00-18.30</span></li>
                                    <li class="day">Monday - Thusday <span>9.00-15.00</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="single-footer">
                                <h2>Newsletter</h2>
                                <p>subscribe to our newsletter to get allour news in your inbox.. Lorem ipsum dolor sit amet, consectetur adipisicing elit,</p>
                                <form action="<?php echo $base_url; ?>/mail/mail.php" method="get" target="_blank" class="newsletter-inner">
                                    <input name="email" placeholder="Informe seu Email" class="common-input" onfocus="this.placeholder = ''"
                                        onblur="this.placeholder = 'Informe seu Email'" required="" type="email">
                                    <button class="button"><i class="icofont icofont-paper-plane"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12">
                            <div class="copyright-content">
                                <p>© Copyright 2018  |  All Rights Reserved by <a href="https://www.fabianomaximiano.com.br" target="_blank">Fabiano Maximiano</a> </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </footer>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/jquery.min.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/jquery-migrate-3.0.0.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/jquery-ui.min.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/easing.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/colors.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/popper.min.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/bootstrap-datepicker.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/jquery.nav.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/slicknav.min.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/jquery.scrollUp.min.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/niceselect.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/tilt.jquery.min.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/owl-carousel.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/jquery.counterup.min.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/steller.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/wow.min.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/jquery.magnific-popup.min.js"></script>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/bootstrap.min.js"></script>
        <script src="<?php echo $base_url; ?>/templates-parts/assets/js/main.js"></script>
    </body>
</html>