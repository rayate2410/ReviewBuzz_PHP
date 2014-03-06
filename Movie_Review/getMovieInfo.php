<?php

$mysql_host = "127.0.0.1";
$mysql_database = "movie_review_db";
$mysql_user = "root";
$mysql_password = "";

$db=$db = new PDO('mysql:dbname=movie_review_db;host=127.0.0.1', $mysql_user, $mysql_password);

$movie_name=$_GET['movie_name'];

//Query to fetch movie information based on selected movie.
$query1 = "select * from movie_info where movie_name = '$movie_name'";

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
		$data['movies_data'][$x]['year'] = $json_data->{'Year'};
		$data['movies_data'][$x]['genre'] = $json_data->{'Genre'};
		$data['movies_data'][$x]['actors'] = $json_data->{'Actors'};
		$data['movies_data'][$x]['plot'] = $json_data->{'Plot'};
		
		
	}
	else
	{
		$data['movies_data'][$x]['imdbRating'] = "NA" ; 
		$data['movies_data'][$x]['poster'] = "NA" ;
		$data['movies_data'][$x]['year'] = "NA" ;
		$data['movies_data'][$x]['genre'] = "NA" ;
		$data['movies_data'][$x]['actors'] = "NA" ;
		$data['movies_data'][$x]['plot'] = "NA" ;
	}
	
}

print json_encode($data,JSON_PRETTY_PRINT);

$db=null;

?>