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
        $url = 'URL_DO_APPSCRIPT'; // Substitua com a URL do seu App Script

        // Parâmetros da requisição
        $params = [
            'search' => $sn,
            'link' => 'example_link', // Ajuste conforme necessário
            'user' => 'example_user', // Ajuste conforme necessário
            'date' => date('Y-m-d H:i:s'),
            'page' => 1
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