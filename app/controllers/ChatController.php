<?php
/**
 * ChatController — Proxy para el chatbot IA
 * POST /api/chat  { messages:[...], system:"...", context:"..." }
 *
 * Proveedores (configura en config/database.php):
 *   define('AI_PROVIDER',   'deepseek');          // deepseek | openai | anthropic | ollama
 *   define('AI_API_KEY',    'sk-...');             // clave del proveedor
 *   define('AI_MODEL',      'deepseek-chat');      // modelo específico
 *   define('OLLAMA_URL',    'http://localhost:11434'); // solo para ollama
 */
class ChatController extends Controller {

    private function getConfig(): array {
        return [
            'provider' => defined('AI_PROVIDER') ? AI_PROVIDER : (getenv('AI_PROVIDER') ?: 'demo'),
            'api_key'  => defined('AI_API_KEY')  ? AI_API_KEY  : (getenv('AI_API_KEY')  ?: ''),
            'model'    => defined('AI_MODEL')     ? AI_MODEL    : (getenv('AI_MODEL')    ?: ''),
            'ollama'   => defined('OLLAMA_URL')   ? OLLAMA_URL  : (getenv('OLLAMA_URL')  ?: 'http://localhost:11434'),
        ];
    }

    public function status() {
        header('Content-Type: application/json');
        $cfg = $this->getConfig();
        $ok  = $cfg['provider'] !== 'demo' && !empty($cfg['api_key'] ?: $cfg['ollama']);
        echo json_encode([
            'configured' => $ok,
            'provider'   => $cfg['provider'],
            'model'      => $cfg['model'] ?: $cfg['provider'],
        ]);
        exit();
    }

    public function proxy() {
        header('Content-Type: application/json');
        error_reporting(0); ini_set('display_errors',0);
        SessionManager::verificarAcceso();

        $body     = $this->getJsonInput();
        $messages = $body['messages'] ?? [];
        $system   = $body['system']   ?? 'Eres un asistente experto en diagramas UML. Responde en español.';
        $context  = $body['context']  ?? '';
        if ($context) $system .= "\n\nCONTEXTO:\n" . substr($context, 0, 6000);

        $cfg = $this->getConfig();

        switch ($cfg['provider']) {
            case 'deepseek':
                $this->callOpenAICompatible(
                    'https://api.deepseek.com/v1/chat/completions',
                    $cfg['api_key'],
                    $cfg['model'] ?: 'deepseek-chat',
                    $system, $messages
                );
                break;
            case 'openai':
                $this->callOpenAICompatible(
                    'https://api.openai.com/v1/chat/completions',
                    $cfg['api_key'],
                    $cfg['model'] ?: 'gpt-4o-mini',
                    $system, $messages
                );
                break;
            case 'anthropic':
                $this->callAnthropic($cfg['api_key'], $cfg['model'] ?: 'claude-haiku-4-5-20251001', $system, $messages);
                break;
            case 'ollama':
                $this->callOllama($cfg['ollama'], $cfg['model'] ?: 'llama3', $system, $messages);
                break;
            default:
                $this->demoResponse();
        }
    }

    private function callOpenAICompatible(string $url, string $key, string $model, string $sys, array $msgs) {
        $payload = json_encode([
            'model'    => $model,
            'messages' => array_merge(
                [['role'=>'system','content'=>$sys]],
                array_slice($msgs, -8)
            ),
            'max_tokens' => 1000,
            'temperature'=> 0.7,
        ]);
        $res = $this->curlPost($url, $payload, [
            'Authorization: Bearer ' . $key,
            'Content-Type: application/json',
        ]);
        if (!$res['ok']) { echo json_encode(['success'=>false,'error'=>$res['error']]); exit(); }
        $data = json_decode($res['body'], true);
        $text = $data['choices'][0]['message']['content'] ?? '';
        if (!$text) { echo json_encode(['success'=>false,'error'=>$data['error']['message']??'Sin respuesta']); exit(); }
        echo json_encode(['success'=>true,'text'=>$text]);
        exit();
    }

    private function callAnthropic(string $key, string $model, string $sys, array $msgs) {
        $payload = json_encode([
            'model'      => $model,
            'max_tokens' => 1000,
            'system'     => $sys,
            'messages'   => array_slice($msgs, -8),
        ]);
        $res = $this->curlPost('https://api.anthropic.com/v1/messages', $payload, [
            'Content-Type: application/json',
            'x-api-key: ' . $key,
            'anthropic-version: 2023-06-01',
        ]);
        if (!$res['ok']) { echo json_encode(['success'=>false,'error'=>$res['error']]); exit(); }
        $data = json_decode($res['body'], true);
        $text = $data['content'][0]['text'] ?? '';
        if (!$text) { echo json_encode(['success'=>false,'error'=>$data['error']['message']??'Sin respuesta']); exit(); }
        echo json_encode(['success'=>true,'text'=>$text]);
        exit();
    }

    private function callOllama(string $base, string $model, string $sys, array $msgs) {
        $prompt = $sys . "\n\n";
        foreach (array_slice($msgs,-6) as $m) {
            $prompt .= ($m['role']==='user'?'Usuario':'Asistente') . ': ' . $m['content'] . "\n";
        }
        $prompt .= "Asistente:";
        $payload = json_encode(['model'=>$model,'prompt'=>$prompt,'stream'=>false]);
        $res = $this->curlPost(rtrim($base,'/'). '/api/generate', $payload, ['Content-Type: application/json'], 60);
        if (!$res['ok']) { echo json_encode(['success'=>false,'error'=>'Ollama: '.$res['error']]); exit(); }
        $data = json_decode($res['body'], true);
        $text = $data['response'] ?? '';
        if (!$text) { echo json_encode(['success'=>false,'error'=>'Ollama sin respuesta']); exit(); }
        echo json_encode(['success'=>true,'text'=>$text]);
        exit();
    }

    private function curlPost(string $url, string $body, array $headers, int $timeout = 30): array {
        $ch = curl_init($url);
        curl_setopt_array($ch,[
            CURLOPT_RETURNTRANSFER=>true, CURLOPT_POST=>true,
            CURLOPT_POSTFIELDS=>$body, CURLOPT_TIMEOUT=>$timeout,
            CURLOPT_HTTPHEADER=>$headers, CURLOPT_SSL_VERIFYPEER=>true,
        ]);
        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($err) return ['ok'=>false,'error'=>$err];
        if ($code < 200||$code>=300) return ['ok'=>false,'error'=>"HTTP $code",'body'=>$resp];
        return ['ok'=>true,'body'=>$resp];
    }

    private function demoResponse() {
        echo json_encode([
            'success' => true, 'demo' => true,
            'text'    => "⚠️ **Chatbot no configurado todavía.**\n\nAñade estas líneas en `config/database.php`:\n\n**Deepseek (económico, muy bueno):**\n```php\ndefine('AI_PROVIDER', 'deepseek');\ndefine('AI_API_KEY',  'sk-...');\ndefine('AI_MODEL',    'deepseek-chat');\n```\n\n**ChatGPT:**\n```php\ndefine('AI_PROVIDER', 'openai');\ndefine('AI_API_KEY',  'sk-...');\ndefine('AI_MODEL',    'gpt-4o-mini');\n```\n\n**Ollama (gratis, sin internet):**\n```php\ndefine('AI_PROVIDER', 'ollama');\ndefine('OLLAMA_URL',  'http://localhost:11434');\ndefine('AI_MODEL',    'llama3');\n```\n\n**Anthropic Claude:**\n```php\ndefine('AI_PROVIDER', 'anthropic');\ndefine('AI_API_KEY',  'sk-ant-...');\n```",
        ]);
        exit();
    }
}
?>
