<?php

include_once "webnic_base.php";

$test_source_username="webcc-webnictest";
$test_source_password="testaccount";
$test_domain_name="brettledgerwood.com";
$test_domain_reg="tusker.org";

// echo domainQuery($test_source_username, "brettledgerwood.com");

// this will test the domain registration with the test account

$domain_details_hash["source"]="$test_source_username";
// $domain_details_hash["url"]=""; // optional
// $domain_details_hash["domainname"]="$test_domain_reg";
// $domain_details_hash["encoding"]="big5"; // optional
// $domain_details_hash["lang"]="ENG"; // optional
$domain_details_hash["term"]="1";
$domain_details_hash["ns1"]="NS1.EXPOZE.COM";
$domain_details_hash["ns2"]="NS2.EXPOZE.COM";

echo "About to call Domain Registration...\n";
echo domainRegistration($test_source_username, $test_source_password, $domain_details_hash);

?>
