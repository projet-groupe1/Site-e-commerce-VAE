
<footer id="footer">
			<!-- top footer -->
			<div class="section">
				<!-- container -->
				<div class="container">
					<!-- row -->
					<div class="row">
						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">A PROPOS DE NOUS</h3>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut.</p>
								<ul class="footer-links">
									<li><a href="#"><i class="fa fa-map-marker"></i>Mermoz, Dakar, Senegal</a></li>
									<li><a href="#"><i class="fa fa-phone"></i>33-800-22-22</a></li>
									<li><a href="#"><i class="fa fa-envelope-o"></i>email@email.com</a></li>
								</ul>
							</div>
						</div>

						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">CATEGORIES</h3>
								<ul class="footer-links">
									<li><a href="#">Offres exeptionnelles</a></li>
									<li><a href="#">Ordinateurs portables</a></li>
									<li><a href="#">Smartphones</a></li>
									<li><a href="#">Cameras</a></li>
									<li><a href="#">Accessoires</a></li>
								</ul>
							</div>
						</div>

						<div class="clearfix visible-xs"></div>

						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">INFORMATIONS</h3>
								<ul class="footer-links">
									<li><a href="#">Apropos de nous</a></li>
									<li><a href="#">Contactez-nous</a></li>
									<li><a href="#">Politique de confidentialite</a></li>
									<li><a href="#">Commandes et Retour</a></li>
									<li><a href="#">Conditions generales</a></li>
								</ul>
							</div>
						</div>

						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">SERVICES</h3>
								<ul class="footer-links">
									<li><a href="#">Mon compte</a></li>
									<li><a href="#">Voir le panier</a></li>
									<li><a href="#">Liste de souhaits</a></li>
									<li><a href="#">Suivre ma commande</a></li>
									<li><a href="#">Aide</a></li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /row -->
				</div>
				<!-- /container -->
			</div>
			<!-- /top footer -->

			<!-- bottom footer -->
			<div id="bottom-footer" class="section">
				<div class="container">
					<!-- row -->
					<div class="row">
						<div class="col-md-12 text-center">
							<ul class="footer-payments">
								<li><a href="#"><i class="fa fa-cc-visa"></i></a></li>
								<li><a href="#"><i class="fa fa-credit-card"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-paypal"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-mastercard"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-discover"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-amex"></i></a></li>
							</ul>
							<span class="copyright">
								<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
								Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
							<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
							</span>
						</div>
					</div>
						<!-- /row -->
				</div>
				<!-- /container -->
			</div>
			<!-- /bottom footer -->
			<script>// Dans le footer
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', async () => {
        const productId = button.dataset.id;
        
        try {
            const response = await fetch('ajax/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            });
            
            const result = await response.json();
            if (result.success) {
                updateCartCount(result.cartCount);
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    });
});

function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(el => el.textContent = count);
}</script>
		</footer>


