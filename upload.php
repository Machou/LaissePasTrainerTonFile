<?php
function p($str)
{
    echo '<pre>';
    print_r($str);
    echo '</pre>';
}


// Suppression du fichier
// On vérifie si la variable est défini
if(isset($_GET['delete']) && !empty($_GET['delete']))
{
    $avatarUploaded = __DIR__.'/uploads/'.$_GET['delete'];

    // On vérifie que la fichier existe dans le dossier /uploads
    if(file_exists($avatarUploaded))
    {
        unlink($avatarUploaded);

        $avatarDeleteSuccess = '<div><p style="color: green;">Le fichier <em>'.$avatarUploaded.'</em> a été supprimé avec succès !</p></div>';
    }

    else
    {
        echo '<p style="color: red;">Erreur : le fichier ne peut être supprimé, il n\'exsite pas.</p>';
    }
}


// On vérifie que le formulaire est initialiser
if(isset($_POST['form']))
{
    $nb_avatar = count($_FILES['avatars']['name']);

    // On vérifie qu'on upload des fichiers
    if($nb_avatar > 0)
    {
        // On parcourt les fichiers
        for($i = 0; $i < $nb_avatar; $i++)
        {
            // On récupére l'extension du fichier
            $ext = pathinfo($_FILES['avatars']['name'][$i], PATHINFO_EXTENSION);
            // On récupére le nom du fichier temporaire
            $file_tmp = $_FILES['avatars']['tmp_name'][$i];
            // On récupére le nom du fichier
            $file_name = $_FILES['avatars']['name'][$i];
            // On récupére la taille du fichier
            $file_size = $_FILES['avatars']['size'][$i];

            // On vérifie que le fichier est une image
            if(in_array($ext, ['jpg', 'png', 'gif']) && in_array(mime_content_type($file_tmp), ['image/jpg', 'image/png', 'image/gif']))
            {
                // On vérifie que le fichier est égale ou inférieur à 1 Mo
                if(filesize($file_tmp) <= 1000000)
                {
                    // On génére un nom unique de fichier pour éviter les doublons
                    $name = uniqid().'.'.$ext;

                    // Endroit ou sera stocké le fichier
                    $uploadFile = __DIR__.'/uploads/'.$name;

                    // Si tout est Ok; on peut uploader le fichier sur le serveur
                    move_uploaded_file($file_tmp, $uploadFile);

                    echo '<p style="color: green;">Le fichié <strong>'.$name.'</strong> a correctement été uploadé ! :)<br />
                    <em>La boucle est bouclée, le système a la tête sous l\'eau.</em> <strong>NTM</strong></p>';
                }

                else
                {
                    echo '<p style="color: red;">Erreur : taille incorrecte !</p>';
                }
            }

            else
            {
                echo '<p style="color: red;">Erreur : extension du fichier incorrecte !</p>';
            }
        }
    }

    else
    {
        echo '<p style="color: red;">Erreur : aucun fichier detecté !</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
</head>

<body>
<?= (isset($avatarDeleteSuccess) && !empty($avatarDeleteSuccess)) ? $avatarDeleteSuccess : null; ?>

<div>
    <form action="upload.php" method="post"  enctype="multipart/form-data" id="form_upload">
        <label for="files"></label>
        <input type="file" name="avatars[]" multiple="multiple" id="files" />

        <button name="form" form="form_upload">Valider !</button>
    </form>
</div>

<?php
echo '<div>
<h4>Liste des images</h4>

<ul>';
// On Affiche les fichiers dans le dossier /uploads
$it = new FilesystemIterator(dirname(__FILE__).'/uploads');
foreach ($it as $fileinfo)
{
    echo '<li style="background-color: #ccc;">
        <figure><img src="uploads/'.$fileinfo->getFilename().'" alt="" style="height: 100px; width: 100px;"><figcaption>Avatar uploadé le '.date('jS F Y', filemtime('uploads/'.$fileinfo->getFilename())).'</figcaption></figure>
        <a href="?delete='.$fileinfo->getFilename().'">Supprimer le fichier <em>'.$fileinfo->getFilename().'</em></a>
    </li>';
}
echo '</ul>';
?>
</div>
</body>
</html>