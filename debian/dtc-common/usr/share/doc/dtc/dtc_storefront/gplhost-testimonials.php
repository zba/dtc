<?php

include("dtc_storefront.php");
$plaf = getTestimonials();
$testims = drawTestimonials($plaf);

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
<head>
<title>GPLHost:>_ Open source hosting worldwide _ Web spaces featuring GPL control panel</title>
</head>

<body id="globalpage" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	  <?php echo $testims; ?>
	<?php echo drawTestimonialsForm("gplhost-testimonials-record.php"); ?>
</body>
</html>
