<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
$connectionString = "DefaultEndpointsProtocol=https;AccountName=azuremyteststorage;AccountKey=BvcKiWLwo3eQL8NxP3HLZ4kBQ1IJzZqcCQb9pk0lhEvxMIq4cMV6lU+Q1z+KOWjJIVmD5bxEXaqS20g9q6HfHQ==;EndpointSuffix=core.windows.net";
$blobClient = BlobRestProxy::createBlobService($connectionString);
$containerName = "submission";
	
if (isset($_POST['submit'])) {
	$fileToUpload = $_FILES["fileToUpload"]["name"];
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	echo fread($content, filesize($fileToUpload));
		
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: blob.php");
}	
	
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Analyze With Upload Photo</title>
		<link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/starter-template/">
		<!-- Bootstrap core CSS -->
		<link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Custom styles for this template -->
		<link href="starter-template.css" rel="stylesheet">
		 <style>
       th {
  	background-color:#707B7C; border-right:solid 1px black; border-bottom:solid 1px black; font-size:8pt ; padding:5px;font-family: arial;border-top: solid 1px black;border-left: solid 1px black;
	} 
	td{
		border-right:solid 1px black; border-bottom:solid 1px black; font-size:8pt ; padding:5px;font-family: arial;border-left: solid 1px black;border-top: solid 1px black; text-align: right;  
	}
</style>
	</head>
	
	<body>

			Image to analyze:
					<form action="blob.php" method="post" enctype="multipart/form-data">
						<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
						<input type="submit" name="submit" value="Upload">
					</form>
			
				<br>
			<table>
			<tr>
				<th>File Name</th>
				<th>URL</th>
				<th>Action</th>
			</tr>
		
			<tbody>
						<?php
						do {
							foreach ($result->getBlobs() as $blob) {
						?>						
						<tr>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
							<td>
								<form action="vision.php" method="post">
									<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">						
									<input type="submit" name="submit"  value="Lihat">
								</form>
							</td>
						</tr>
						<?php
							} $listBlobsOptions->setContinuationToken($result->getContinuationToken());
						} while($result->getContinuationToken());
						?>
					</tbody>	
 				</table>
				</div>
			
			<!-- Placed at the end of the document so the pages load faster -->
			<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
			<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
			<script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
			<script src="https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js"></script>
			
			</body>
		</html>
