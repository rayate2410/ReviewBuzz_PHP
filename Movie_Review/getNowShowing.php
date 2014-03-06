<?php

$mysql_host = "127.0.0.1";
$mysql_database = "movie_review_db";
$mysql_user = "root";
$mysql_password = "";

$db=$db = new PDO('mysql:dbname=movie_review_db;host=127.0.0.1', $mysql_user, $mysql_password);

//Query to fetch only those movies which are less than 3 weeks older.
$query1 = 'select * from movie_info where datediff(curdate(),movie_release_date) <= 21 order by Movie_release_Date DESC';

$result=$db->query($query1);

$omdbapi_call = "http://www.omdbapi.com/?i=&t=";

while ($r = $result->fetch(PDO::FETCH_ASSOC))
{
   $rows[] = $r;  
} 

$data = array('movies_data' => $rows);

for($x=0;$x<$result->rowCount();$x++)
{
	//echo ($data['movies_data'][$x]['Movie_Name']);
	$movie_name = $data['movies_data'][$x]['Movie_Name'];
	$omdbapi_call = $omdbapi_call.$movie_name;
	$api_response = file_get_contents($omdbapi_call);
	
	//parsing of api response.
	$json_data = json_decode($api_response);
	
	if($json_data->{'Response'} == 'True')
	{
		$data['movies_data'][$x]['imdbRating'] = $json_data->{'imdbRating'};
		$data['movies_data'][$x]['poster'] = $json_data->{'Poster'};
	}
	else
	{
		$data['movies_data'][$x]['imdbRating'] = 'NA';
		$data['movies_data'][$x]['poster'] = 'NA';
	}
	
}

print json_encode($data,JSON_PRETTY_PRINT);

$db=null;

?>