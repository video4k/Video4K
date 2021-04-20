		</div>
		<div id="footer">
			<div class="left">Copyright &copy; {$smarty.now|date_format:"%Y"} {$CONFIG.TITLE}&trade; &minus; All rights reserved.</div>
			<div class="right">
				<a href="/">Home</a> |
				<a href="/faq">FAQ</a> |
				<a href="/terms">General Terms of Service</a> |
				<a href="/terms">DMCA</a> |
				<a href="#" id="open-wizard">Contact</a>
			</div>
		</div>
		<script type="text/javascript">	
			{literal}(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');{/literal}
			ga ('create', '{$CONFIG.ANALYTICS_ID}', '{$CONFIG.DOMAIN}');
			ga ('set', 'anonymizeIp', true);
			ga ('send', 'pageview');
		</script>
	</body>
</html>