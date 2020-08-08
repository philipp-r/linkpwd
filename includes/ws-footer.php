</main>

<footer>
<?php echo FOOTER_HTMLCODE; ?>

<hr>
<div class="footer row">
	<div class="col">
		<p>
			<a href="https://github.com/philipp-r/linkpwd">linkpwd is open source</a> <small>v0.5.1</small>
			<?php if(!empty(LEGAL_INFO_LINK)){ ?>
		  	<br><a href="<?php echo LEGAL_INFO_LINK; ?>">legal info + data protection</a>
			<?php }
			if(!empty(DMCA_CONTACT_LINK)){ ?>
		  	<br><a href="<?php echo DMCA_CONTACT_LINK; ?>">contact + DMCA requests</a>
			<?php } ?>
	</p>
	</div>
	<div class="col">
		<p>
			This is a free service that helps you to protect your links with captcha and password.
			We will convert your links to direct links that will act as autoforwarders to your original links.
			Data on the server is encrypted.
		</p>
	</div>
</div>
</footer>

</div><!-- /container -->

</body>
</html>
