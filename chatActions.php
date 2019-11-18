<?php
spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});

require_once('config.php');


$action = isset($_POST['action']) ? $_POST['action'] : null;

if (!is_null($action)) {
    $database = new Database_queries($db);
    $tripId = isset($_POST['tripId']) ? $_POST['tripId'] : null;

    if (!is_null($tripId)) {
        switch ($action) {
            case 'save':
                $message = isset($_POST['message']) ? $_POST['message'] : null;
                if (!is_null($message)) {
                    echo $database->insertChatMessage($_SESSION['userObject']->getUserId(), $message, $tripId);
                    return;
                }
                break;
            case 'getAll':
                $messages = $database->getChatMessages($tripId);
                if (!is_array($messages) || count($messages) == 0) {
                    echo -1;
                    return;
                }

                echo $twig->render('chatMessages.twig', array('messages' => $messages, 'user' => $_SESSION['userObject']));
                break;
            case 'getUnread':
                $last_read_id = isset($_POST['lastReadId']) ? $_POST['lastReadId'] : null;
                if (is_null($last_read_id)) {
                    echo -1;
                    return;
                }

                $messages = $database->getUnreadChatMessages($tripId, $last_read_id);
                if (!is_array($messages) || count($messages) == 0) {
                    echo -1;
                    return;
                }
                echo $twig->render('chatMessages.twig', array('messages' => $messages, 'user' => $_SESSION['userObject']));
                break;
            case 'chatWindowEvent':
                if ($tripId == null) return;
                $event = isset($_POST['event']) ? $_POST['event'] : null;
                if ($event == null) return;
                if (!isset($_SESSION['chats'])) $_SESSION['chats'] = array();
                $render = false;

                if (!array_key_exists($tripId, $_SESSION['chats'])) {
                    $chat = array($tripId => new Chat($tripId));
                    $_SESSION['chats'] = $chat + $_SESSION['chats'];
                    $render = true;
                }

                switch ($event) {
                    case 'close':
                        $_SESSION['chats'][$tripId]->close();
                        break;
                    case 'minimize':
                        $_SESSION['chats'][$tripId]->minimize();
                        break;
                    case 'open':
                        $_SESSION['chats'][$tripId]->open();
                        break;

                    case 'maximize':
                        $_SESSION['chats'][$tripId]->maximize();
                        break;
                }

                if ($render) {
                    $template = $twig->loadTemplate('tripdetails.twig');
                    echo $template->renderBlock('chatwindows', array('chats' => $_SESSION['chats']));
                } else {
                    echo "1";
                }

                break;
            default:
                echo "Missing action argument";
                return;
        }
    }
}