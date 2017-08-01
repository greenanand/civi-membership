<?php
/*
Example : Following example will loads all the membership periods of contact(id=205) into membership_periods.txt file.

  $params = array(
    'cid' => '205',
  );
     $result = civicrm_api3('MembershipPeriods', 'get', $params);

 $myfile = fopen("membership_periods.txt", "a");
for($j=0;$j<sizeof($result);$j++){
	fwrite($myfile, $result[$j][0]."\n");
	fwrite($myfile, $result[$j][1]."\n");
	fwrite($myfile, $result[$j][2]."\n");
	fwrite($myfile, $result[$j][3]."\n");
}
	fclose($myfile);

*/
function civicrm_api3_membership_periods_get($params) {
	$cid = $params['cid'];
	$check = "SELECT p.start_date, p.end_date, p.renewed_date, p.contribution_id , p.term_id
		  FROM civicrm_membership_period p
		  INNER JOIN civicrm_membership m ON m.id = p.membership_id
	      WHERE m.contact_id = $cid
			     ";
		$results = CRM_Core_DAO::executeQuery($check);

		$myarray = array();
			$i=0;
			while($results->fetch()){
				$myarray[$i][0] = $results->start_date;
				$myarray[$i][1] = $results->end_date;
				$myarray[$i][2] = $results->renewed_date;
				$myarray[$i][3] = $results->contribution_id;
				$myarray[$i][4] = $results->term_id;
				$i++;
			}
			return $myarray;
}
