<?php
// Incluir o autoload do Composer para carregar as dependências
require 'vendor/autoload.php';

use GuzzleHttp\Client;

// Token do seu bot do Telegram
$botToken = '6408511720:AAEgubuvRKXtx74IAfDZAswrZHL_ZUWS_gk';

// URL do script do Google Apps Script
$googleAppsScriptUrl = 'https://script.google.com/macros/s/AKfycbwKZ1hQIyRiEwN1W7fdA5XmB5LZMk-6k6g2Z5D2tMxU6sUfjvI/exec';

// Função para lidar com mensagens recebidas
function handleIncomingMessage($bot, $message) {
    global $googleAppsScriptUrl;

    // Extrair o texto da mensagem recebida
    $text = $message->getText();
    $chat_id = $message->getChat()->getId();

    // Definir parâmetros da requisição
    $params = [
        'page' => 'teste',
        'columnID' => 'sn',
        'search' => $text,
        'user' => '',
        'date' => ''
    ];

    // Fazer a requisição HTTP POST para o script do Google Apps Script
    $client = new Client();
    try {
        $response = $client->post($googleAppsScriptUrl, [
            'json' => $params
        ]);

        // Decodificar a resposta JSON
        $responseData = json_decode($response->getBody(), true);

        // Verificar se a resposta possui o campo 'password'
        if (isset($responseData['password'])) {
            // Enviar o password como resposta para o usuário
            $bot->sendMessage([
                'chat_id' => $chat_id,
                'text' => "O password é: " . $responseData['password']
            ]);
        } else {
            // Se não houver password na resposta, enviar uma mensagem de erro
            $bot->sendMessage([
                'chat_id' => $chat_id,
                'text' => "Não foi possível obter o password do servidor."
            ]);
        }
    } catch (Exception $e) {
        // Em caso de erro na requisição, enviar uma mensagem de erro ao usuário
        $bot->sendMessage([
            'chat_id' => $chat_id,
            'text' => "Erro ao fazer a requisição HTTP: " . $e->getMessage()
        ]);
    }
}

// Criação do objeto do bot
$bot = new \TelegramBot\Api\Client($botToken);

// Lidando com mensagens recebidas
$bot->on(function ($update) use ($bot) {
    $message = $update->getMessage();
    if ($message !== null) {
        handleIncomingMessage($bot, $message);
    }
});

// Executando o bot
$bot->run();
