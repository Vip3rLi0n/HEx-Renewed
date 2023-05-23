<?php

if (!isset($_SESSION['id'])) {
    header("Location:index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $allowedMimes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];

    if (!empty($_FILES['image_upload']['type']) && in_array($_FILES['image_upload']['type'], $allowedMimes)) {
        $tmpFile = $_FILES['image_upload']['tmp_name'];

        if (getimagesize($tmpFile) !== false) {
            require_once 'classes/PDO.class.php';
            $pdo = PDO_DB::factory();

            $type = filter_input(INPUT_POST, 't', FILTER_SANITIZE_STRING);

            if ($type == '1') {
                $imgType = 'user';
                $dir = 'images/profile/';
                $dir2 = 'images/profile/thumbnail/';
                $redirect = 'profile.php';
                $dir3 = 'images/profile/x60/';

                $sql = "SELECT login FROM users WHERE id = :id LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $_SESSION['id']]);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($data) == 1) {
                    $filename = md5($data[0]['login'] . $_SESSION['id']);
                } else {
                    die("Bad, bad error. LOL");
                }
            } elseif ($type == '2') {
                $imgType = 'admin';
                $dir = 'images/clan/';
                $dir2 = '';
                $redirect = 'clan.php';

                $sql = "SELECT clanID FROM clan_users WHERE userID = :id LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $_SESSION['id']]);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($data) == 1) {
                    $cid = $data[0]['clanID'];

                    $sql = "SELECT name FROM clan WHERE clanID = :cid";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['cid' => $cid]);
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $filename = md5($data[0]['name'] . $cid);
                } else {
                    die("You are not a clan member");
                }
            } else {
                exit();
            }

            // GD image manipulation code

            $imageInfo = getimagesize($tmpFile);
            $imageType = $imageInfo['mime'];

            switch ($imageType) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($tmpFile);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($tmpFile);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($tmpFile);
                    break;
                default:
                    die("Unsupported image type");
            }

            $destination = $dir . $filename . '.jpg';

            if ($imageType == 'image/png') {
                // Convert PNG to JPEG
                $temp = imagecreatetruecolor($imageInfo[0], $imageInfo[1]);
                imagecopy($temp, $source, 0, 0, 0, 0, $imageInfo[0], $imageInfo[1]);
                imagejpeg($temp, $destination, 100);
                imagedestroy($temp);
            } else {
                imagejpeg($source, $destination, 100);
            }

            imagedestroy($source);

            if ($dir2 != '') {
                // Create thumbnail image
                $thumbnailPath = $dir2 . $filename . '.jpg';
                $thumbnail = imagecreatetruecolor(60, 60);
                imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, 60, 60, $imageInfo[0], $imageInfo[1]);
                imagejpeg($thumbnail, $thumbnailPath, 100);
                imagedestroy($thumbnail);
            }

            header("Location:$redirect");
        } else {
            die("Can't upload this image");
        }
    } else {
        echo 'Please dont';
    }
} else {
    echo 'Not a post req';
}
?>
