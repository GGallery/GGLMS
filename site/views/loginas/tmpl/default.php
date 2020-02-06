<?php
// no direct access

defined('_JEXEC') or die('Restricted access');


echo "<h1> Pannello Utenti </h1>"; ?>


<div class="mc-main">
    <table>
        <thead>
        <tr>
            <th>User Id</th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Gruppi Utente</th>
            <th>Login</th>
            <th>Actions</th>
        </tr>
        </thead>
        <?php foreach ($this->users as $i => $item): ?>
            <tr class="row<?php echo $i % 2; ?>">
                <td>
                    <?php echo $item->id; ?>
                </td>
                <td>
                    <?php echo $item->username; ?>
                </td>

                <td>
                    <?php echo $item->name; ?>
                </td>

                <td>
                    <?php echo $item->email; ?>
                </td>

                <td>
                    <?php
                    echo $this->model->getUserGroupName($item->id, true);
                    ?>
                </td>

                <td>
                    <?php
                    $data = "id=" . $item->id . "&username=" . $item->username . "&password=" . $item->password . "&email=" . $item->email;
                    $urllogin = JUri::root() . JRoute::_('/index.php?option=com_gglms&task=users.login&' . $data);
                    $urlreset = JUri::root() . JRoute::_('/index.php?option=com_gglms&task=users.reset&' . $data);
                    $urlresetsend = JUri::root() . JRoute::_('/index.php?option=com_gglms&task=users.resetsend&' . $data);
                    ?>
                    <a style=" padding: 3px 20px;" class="btn btn-small btn-success" href="<?php echo $urllogin; ?>"
                       target="_blank">LOGIN</a>
                </td>

                <td>
                    <a style=" padding: 3px 20px;" class="btn btn-small btn-danger" href="<?php echo $urlreset; ?>"
                       target="_blank">RESET </a>
                    <a style=" padding: 3px 20px;" class="btn btn-small btn-danger" href="<?php echo $urlresetsend; ?>"
                       target="_blank">RESET &
                        SEND</a>

                </td>

            </tr>
        <?php endforeach; ?>
    </table>

</div>

<div id="cover-spin"></div>
<!---->
<!--<script type="application/javascript">-->
<!--    jQuery(document).ready(function () {-->
<!--        _monitoraCoupon.init();-->
<!--    });-->
<!---->
<!-- </script>-->
