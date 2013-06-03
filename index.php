<?php

include('XivelyAPI.php');

$xively = new XivelyAPI("YOUR_API_KEY");
$feed = FEED_ID;
$feed_to_delete = FEED_ID_TO_DELETE;
$user = "USER_NAME";

echo "<h2>getFeedsList(): </h2><br/>";
echo "<code>" . $xively->getFeedsList("json", 2, 1, "summary", "energy") . "</code><br/>";

echo "<h2>getFeed(): </h2><br/>";
echo "<code>" . $xively->getFeed("csv", $feed) . "</code><br/>";

echo "<h2>updateFeed(): </h2><br/>";
$data = "0,10";
echo "<code>" . $xively->_debugStatus($xively->updateFeed("csv", $feed, $data)) . "</code><br/>";

echo "<h2>deleteFeed(): </h2><br/>";
echo "<code>" . $xively->_debugStatus($xively->deleteFeed($feed_to_delete)) . "</code><br/>";

echo "<h2>getDatastreamsList(): </h2><br/>";
echo "<code>" . print_r($xively->getDatastreamsList($feed)) . "</code><br/>";

echo "<h2>createDatastream(): </h2><br/>";
$data = "energy,19";
echo "<code>" . $xively->_debugStatus($xively->createDatastream("csv", $feed, $data)) . "</code><br/>";

echo "<h2>getDatastream(): </h2><br/>";
echo "<code>" . $xively->getDatastream("json", $feed, 0) . "</code><br/>";

echo "<h2>updateDatastream(): </h2><br/>";
$data = "9";
echo "<code>" . $xively->_debugStatus($xively->updateDatastream("csv", $feed, 0, $data)) . "</code><br/>";

echo "<h2>deleteDatastream(): </h2><br/>";
echo "<code>" . $xively->_debugStatus($xively->deleteDatastream($feed, "energy")) . "</code><br/>";

echo "<h2>getUser(): </h2><br/>";
echo "<code>" . $xively->getUser("xml", $user) . "</code><br/>";

echo "<h2>getFeedHistory(): </h2><br/>";
echo "<code>" . $xively->getFeedHistory("json", $feed, false, false, false, 2) . "</code><br/>";

echo "<h2>getDatastreamHistory(): </h2><br/>";
echo "<code>" . $xively->getDatastreamHistory("json", $feed, 0, false, false, false, 2) . "</code><br/>";
?>
