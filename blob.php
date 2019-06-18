<?php

require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=nasystorage;AccountKey=SXWwmYuRwoohUvibFtYaOzZ8ivryEqiLWACOnt/ZgptfjkyF3AKeEjKqIP2sXfGOefQoeOBPBrQAo8ZMRV4unw==;";

$containerName = "emrizkiem";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	// echo fread($content, filesize($fileToUpload));
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: blob.php");
}
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Microsoft Azure | Dicoding</title>
    <style>
        body {
            font-family: Source Code Pro, monospace;
        }

        table {
            border-collapse: collapse;
        }

        th,
        td {
            font-size: 16px;
            border: 1px solid #DEDEDE;
            padding: 3px 5px;
            color: #303030;
        }

        th {
            background: #cccccc;
            font-size: 16px;
            border-color: #b0b0b0;
        }
    </style>
</head>

<body>
    <h2 style="font-style: bold">Analisa Cover Buku</h2>
    <p>Pilih foto cover buku yang sesuai.<br> Kemudian klik tombol <b>Upload</b>, untuk menganalisa foto klik tombol <b>Analisis</b> pada tabel.</p><br>

    <form action="blob.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" accept=".png,.jpg,.jpeg" style="font-family: Source Code Pro, monospace; font-size: 16px;" required >
        <input type="submit" name="submit" value="Upload" style="font-family: Source Code Pro, monospace; font-size: 16px;" />
    </form><br><br>

    <table>
        <thead>
            <tr>
                <th>Nama Foto</th>
                <th>Link File</th>
                <th>Aksi</th>
            </tr>    
        </thead>
        <tbody>
            <?php
                do {
                    foreach($result->getBlobs() as $blob) {
                        ?>
                        <tr>
                            <td><?php echo $blob->getName() ?></td>
                            <td><?php echo $blob->getUrl() ?></td>
                            <td>
                                <form action="computervision.php" method="post">
                                    <input type="hidden" name="url" value="<?php echo $blob->getUrl() ?>" />
                                    <input type="submit" name="submit" value="Analisis" style="font-family: Source Code Pro, monospace; font-size: 16px;" />
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                    $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                } while($result->getContinuationToken());
            ?>
        </tbody>
    </table>
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
</body>
</html>
