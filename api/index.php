<?php

$botToken = '6408511720:AAEgubuvRKXtx74IAfDZAswrZHL_ZUWS_gk';
$apiUrl = "https://api.telegram.org/bot$botToken/";

// Função para enviar mensagem de resposta ao usuário
function sendMessage($chatId, $message) {
    global $apiUrl;
    $url = $apiUrl . "sendMessage?chat_id=" . $chatId . "&text=" . urlencode($message);
    file_get_contents($url);
}

// Captura a entrada JSON do webhook
$update = file_get_contents("php://input");
$updateArray = json_decode($update, true);

if (isset($updateArray['message'])) {
    $message = $updateArray['message'];
    $chatId = $message['chat']['id'];
    $text = $message['text'];

    // Verifica se a mensagem contém o número de série
    if (!empty($text)) {
        $sn = $text;
        $url = 'https://script.google.com/macros/s/AKfycbwVL_A2dJyq04tlasJk5-joNf2j22FhoDyHjD_XpBTh5F2FypkLpTfiwf7Q33Mb5b0arQ/exec';

        // Parâmetros da requisição
        $params = [
            'search' => $sn,
            'columnID' => 'SN',
            'url' => 'https://docs.google.com/spreadsheets/d/1tsw-O6LJ-NC3nQ4rlrQcpUUnRGBG6M3dkYQ6XPK4IXg/edit?usp=sharing',
            'user' => 'example_user',
            'date' => date('Y-m-d H:i:s'),
            'page' => 'test'
        ];

        // Configurações da requisição POST
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($params),
            ],
        ];

        // Cria o contexto da requisição
        $context  = stream_context_create($options);

        // Faz a requisição POST e obtém a resposta
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            sendMessage($chatId, "Erro ao acessar o App Script.");
            exit;
        }

        // Decodifica a resposta JSON
        $response = json_decode($result, true);

        // Verifica se a resposta contém o status 200 e a senha
        sendMessage($chatId, $result);
        if (isset($response['status']) && $response['status'] === 200 && isset($response['password'])) {
            sendMessage($chatId, "Password: " . $response['password']);
        } else {
            sendMessage($chatId, "Resposta inválida do App Script.");
        }
    } else {
        sendMessage($chatId, "Por favor, forneça um número de série (SN).");
    }
} else {
    echo "Método não permitido.";
}
