<?php

include('CosmAPI.php');

$cosm = new CosmAPI("YOUR_API_KEY");
$feed = FEED_ID;
$feed_to_delete = FEED_ID_TO_DELETE;
$user = "USER_NAME";

echo "<h2>getFeedsList(): </h2><br/>";
echo "<code>" . $cosm->getFeedsList("json", 2, 1, "summary", "energy") . "</code><br/>";

echo "<h2>getFeed(): </h2><br/>";
echo "<code>" . $cosm->getFeed("csv", $feed) . "</code><br/>";

echo "<h2>updateFeed(): </h2><br/>";
$data = "0,10";
echo "<code>" . $cosm->_debugStatus($cosm->updateFeed("csv", $feed, $data)) . "</code><br/>";

echo "<h2>deleteFeed(): </h2><br/>";
echo "<code>" . $cosm->_debugStatus($cosm->deleteFeed($feed_to_delete)) . "</code><br/>";

echo "<h2>getDatastreamsList(): </h2><br/>";
echo "<code>" . print_r($cosm->getDatastreamsList($feed)) . "</code><br/>";

echo "<h2>createDatastream(): </h2><br/>";
$data = "energy,19";
echo "<code>" . $cosm->_debugStatus($cosm->createDatastream("csv", $feed, $data)) . "</code><br/>";

echo "<h2>getDatastream(): </h2><br/>";
echo "<code>" . $cosm->getDatastream("json", $feed, 0) . "</code><br/>";

echo "<h2>updateDatastream(): </h2><br/>";
$data = "9";
echo "<code>" . $cosm->_debugStatus($cosm->updateDatastream("csv", $feed, 0, $data)) . "</code><br/>";

echo "<h2>deleteDatastream(): </h2><br/>";
echo "<code>" . $cosm->_debugStatus($cosm->deleteDatastream($feed, "energy")) . "</code><br/>";

echo "<h2>getUser(): </h2><br/>";
echo "<code>" . $cosm->getUser("xml", $user) . "</code><br/>";

echo "<h2>getFeedHistory(): </h2><br/>";
echo "<code>" . $cosm->getFeedHistory("json", $feed, false, false, false, 2) . "</code><br/>";

echo "<h2>getDatastreamHistory(): </h2><br/>";
echo "<code>" . $cosm->getDatastreamHistory("json", $feed, 0, false, false, false, 2) . "</code><br/>";
?>