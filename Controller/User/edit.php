<?php

    define('ROOT', dirname(__DIR__) . '/../');

    require '../auth.php';
    require '../../Model/User/user.php';
    require '../../Helpers/CryptoHelper.php';
    require '../../Helpers/UploadHelpers.php';

class Edit
    {
        /**
         * Determine if an array contains empty fields
         * @param $fields array Array to check fields
         * @param $ignored array Fields to ignore during the fields check
         * @return bool
         */
        public static function CheckFields($fields, $ignored = null)
        {
            if ($ignored != null)
            {
                foreach ($fields as $key => $value)
                {
                    if (!in_array($key, $ignored) && $value == null)
                        return true;
                }
            }
            else
            {
                foreach ($fields as $key => $value)
                {
                    if ($value == null)
                        return true;
                }
            }
            return false;
        }

    }

    // var_dump(Edit::CheckFields($_POST, ['birthdate', 'prev-password', 'new-password', 'retyped-password', 'img-file']));

    if (!Edit::CheckFields($_POST, ['birthdate', 'prev-password', 'new-password', 'retyped-password', 'img-file']) && Auth::Connected())
    {
        if (!empty($_POST['prev-password']) && User::IsValid(Auth::GetPhone(), Hash::Encrypt($_POST['prev-password'])))
        {

            if (isset($_POST['new-password']) && isset($_POST['retyped-password']) && $_POST['new-password'] === $_POST['retyped-password'])
            {
                $user = new User($_POST['username'], Auth::GetPhone(), $_POST['new-password'], $_POST['bloodgroup']);
                $user->SetBirthdate(isset($_POST['birthdate']) ? $_POST['birthdate'] : null);
                $user->SetCity($_POST['city']);
                $user->SetGender($_POST['gender']);
                if (!empty($_FILES['img-file']))
                {
                    if ($_FILES['img-file']['size'] < 300000) {
                        $upload_dir = dirname(__DIR__) . '/../View/assets/images/pp/';
                        $_FILES['img-file']['name'] = $_POST['phone'] . '.png';
                        $upload_file = $upload_dir . basename($_FILES['img-file']['name']);
                        if (move_uploaded_file($_FILES['img-file']['tmp_name'], $upload_file)) {
                            $user->SetAvatar($_FILES['img-file']['name']);
                        }
                        else
                            die('Error during upload : ' . $_FILES['img-file']['name'] . ' to ' . $upload_file);
                    }
                    else
                        die('Avatar image size is too big. Image size must be under 3mb.');
                }
                else
                    die('Avatar image not set.');
                if ($user->UpdateUser(true, $_POST['phone'])) {
                    Auth::Close();
                    Auth::Create($_POST['username'], $_POST['phone']);
                    header('Location: ../../index.php?p=dashboard');
                }
                else
                    die("Error during update. Check all the fields before save.");
            }
            else
                die("New password is invalid.");

        }
        else
        {
            $user = new User($_POST['username'], Auth::GetPhone(), User::GetByPhone(Auth::GetPhone())->GetPassword(), $_POST['bloodgroup']);
            $user->SetBirthdate(isset($_POST['birthdate']) ? $_POST['birthdate'] : null);
            $user->SetCity($_POST['city']);
            $user->SetGender($_POST['gender']);
            if (isset($_FILES['img-file']))
            {
                if ($_FILES['img-file']['size'] < 300000) {
                    $upload_dir = dirname(__DIR__) . '/../View/assets/images/pp/';
                    $_FILES['img-file']['name'] = $_POST['phone'] . '.png';
                    $upload_file = $upload_dir . basename($_FILES['img-file']['name']);
                    if (move_uploaded_file($_FILES['img-file']['tmp_name'], $upload_file)) {
                        $user->SetAvatar($_FILES['img-file']['name']);
                    }
                    else
                        die('Error during upload : ' . $_FILES['img-file']['name'] . ' to ' . $upload_file);
                }
                else
                    die('Avatar image size is too big. Image size must be under 3mb.');
            }
            else
                die('Avatar image not set.');
            if ($user->UpdateUser(false, $_POST['phone'])) {
                Auth::Close();
                Auth::Create($_POST['username'], $_POST['phone']);
                header('Location: ../../index.php?p=dashboard');
            }
            else
                die("Error during update. Check all the fields before save.");
        }

    }
    else
        die("Check all the fields before save or connect before update profile.");