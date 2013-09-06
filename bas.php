<?php
if (file_exists('root.php'))
  include_once('root.php');

	$fin = getmicrotime();
	$total = $fin - $debut;
?>
</div>
<div id="preload-images"></div>
<?php
    if (isset($G_no_piwik) && $G_no_piwik != true)
{
?>
	<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://www.starshine-online.com/piwik/" : "http://www.starshine-online.com/piwik/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://www.starshine-online.com/piwik/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tag -->
<?php } /* G_no_piwik */ ?>
</body>

</html>