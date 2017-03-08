<?php


use Authentication\AuthenticationHelper;
use Privilege\PrivilegeUser;




if (file_exists(__DIR__ . "/../init.php")) {
    require_once __DIR__ . "/../init.php";
} else {
    require_once __DIR__ . "/../init-fallback.php";
}



//--------------------------------------------
// CONFIG
//--------------------------------------------
$successUrl = "";


//--------------------------------------------
// SCRIPT --
//--------------------------------------------
$formValidated = false;
$credentialsInvalid = "";
$pseudo = "";
$password = "";
$tt = "authentication";


if (
    isset($_POST['pseudo']) &&
    isset($_POST['password'])
) {
    $pseudo = (string)$_POST['pseudo'];
    $password = (string)$_POST['password'];


    $profile = AuthenticationHelper::authenticationMatch($pseudo, $password);

    if (false !== $profile) {
        $formValidated = true;

        PrivilegeUser::connect([], $profile);

    } else {
        $credentialsInvalid = "activated";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo __('Welcome to {website}', $tt, ['website' => WEBSITE_NAME]); ?></title>
    <link rel="stylesheet" href="<?php echo url('/style/style.css'); ?>">
</head>

<body class="authentication-body">


<section class="authentication-section">

    <?php if (false === $formValidated): ?>
        <h1 class="centered block"><?php echo __('Welcome to {website}', $tt, ['website' => WEBSITE_NAME]); ?></h1>
        <div class="centered block">
            <form id="form-connection" action="#posted" method="post" class="form form-connection">
                <p class="error <?php echo $credentialsInvalid; ?>" id="error-credentials-invalid">
                    <?php echo __('This credentials are invalid', $tt); ?>
                </p>
                <label>
                    <span><?php echo __('Pseudo', $tt); ?></span> <input id="input-pseudo" type="text" name="pseudo"
                                                                         value="<?php echo htmlspecialchars($pseudo); ?>">
                </label>
                <label>
                    <span><?php echo __('Password', $tt); ?></span> <input id="input-password" type="password"
                                                                           name="password"
                                                                           value="<?php echo htmlspecialchars($password); ?>">
                </label>
                <div class="submit">
                    <input id="input-submit" class="input-submit" type="submit"
                           value="<?php echo ___('Log In', $tt); ?>">
                </div>
            </form>
        </div>

        <script>
            document.getElementById('input-pseudo').focus();
        </script>

    <?php else: ?>
        <script>
            window.location.href = "<?php echo $successUrl; ?>";
        </script>

    <?php endif; ?>

</section>
</body>
</html>