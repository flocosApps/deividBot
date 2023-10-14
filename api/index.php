<?php
$token = '6408511720:AAEgubuvRKXtx74IAfDZAswrZHL_ZUWS_gk';

$apiUrl = "https://api.telegram.org/bot$token";

$update = file_get_contents("php://input");
$update = json_decode($update, true);

if (isset($update['message'])) {
    $message = $update['message'];
    $chatId = $message['chat']['id'] ?? null;

    if (isset($message['text'])) {
        $messageText = $message['text'];

        if (strpos($messageText, '!psv') === 0) {
            $searchTerm = trim(str_replace('!psv', '', $messageText));
            $jsonUrl = "https://deivid-bot.vercel.app/api/Planilha.php";
            $jsonData = @file_get_contents($jsonUrl);

            if ($jsonData !== false) {
                $data = json_decode($jsonData, true);

                if ($data !== null) {
                    $results = array();

                    foreach ($data as $game) {
                        if (isset($game['nome']) && stripos($game['nome'], $searchTerm) !== false) {
                            $results[] = $game;
                        }
                    }

                    if (!empty($results)) {
                        $response = "Resultados encontrados:\n\n";

                        foreach ($results as $result) {
                            $response .= "Nome: " . $result['nome'] . "\n";
                            $response .= "Download Pkg: " . $result['game'] . "\n";
                            $response .= "Download WORK: " . $result['work'] . "\n";
                            $response .= "-----------\n";
                        }
                    } else {
                        $response = "Nenhum jogo encontrado para: $searchTerm";
                    }
                } else {
                    $response = "Desculpe, houve um problema na obtenção dos dados. Tente novamente mais tarde.";
                }
            } else {
                $response = "Desculpe, houve um problema na obtenção dos dados. Tente novamente mais tarde.";
            }
        } elseif (strpos($messageText, '/addgrupo') === 0) {
            $groupLink = trim(str_replace('/addgrupo', '', $messageText));
            $response = joinGroup($groupLink, $apiUrl, $chatId);
        } else {
            $response = "Desculpe, esse bot só responde a comandos /psv e /addgrupo.";
        }
    }
} else {
    $response = "Desculpe, ocorreu um erro no processamento da mensagem.";
}

if (!empty($chatId)) {
    $sendMessageUrl = $apiUrl . "/sendMessage?chat_id=$chatId&text=" . urlencode($response);
    file_get_contents($sendMessageUrl);
}

function joinGroup($groupLink, $apiUrl, $chatId) {
    $response = "Tentando ingressar no grupo...";

    if (strpos($groupLink, 'https://t.me/') === 0) {
        $groupLink = str_replace('https://t.me/', '', $groupLink);
    }

    if (!empty($chatId)) {
        $inviteUrl = $apiUrl . "/inviteChat?chat_id=$chatId&invite_link=$groupLink";
        $result = file_get_contents($inviteUrl);

        if ($result === 'true') {
            $response = "Você foi adicionado com sucesso ao grupo!";
        } else {
            $response = "Desculpe, não foi possível adicionar você ao grupo.";
        }
    } else {
        $response = "Desculpe, não foi possível encontrar o grupo.";
    }

    return $response;
}
?>
