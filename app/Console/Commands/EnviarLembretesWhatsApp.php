<?php

namespace App\Console\Commands;

use App\Models\Agendamento;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EnviarLembretesWhatsApp extends Command
{
    protected $signature = 'cean:enviar-lembretes';
    protected $description = 'Envia lembretes via WhatsApp para agendamentos do dia seguinte';

    public function handle(): int
    {
        $amanha = now()->addDay()->toDateString();

        $agendamentos = Agendamento::with('pessoa')
            ->where('data_agendada', $amanha)
            ->where('notificacao_enviada', false)
            ->get();

        if ($agendamentos->isEmpty()) {
            $this->info('Nenhum agendamento para amanhã.');
            return self::SUCCESS;
        }

        $this->info("Encontrados {$agendamentos->count()} agendamento(s) para amanhã ({$amanha}).");

        $apiUrl = config('services.whatsapp.api_url');
        $apiKey = config('services.whatsapp.api_key');
        $instance = config('services.whatsapp.instance');

        if (empty($apiUrl) || empty($apiKey)) {
            $this->warn('⚠ API do WhatsApp não configurada. Configure WHATSAPP_API_URL e WHATSAPP_API_KEY no .env');
            $this->info('Listando agendamentos que seriam notificados:');

            foreach ($agendamentos as $agendamento) {
                $this->line("  - {$agendamento->pessoa->nome_completo} ({$agendamento->pessoa->whatsapp})");
            }

            return self::FAILURE;
        }

        foreach ($agendamentos as $agendamento) {
            $pessoa = $agendamento->pessoa;

            if (empty($pessoa->whatsapp)) {
                $this->warn("  ⚠ {$pessoa->nome_completo} não tem WhatsApp cadastrado. Pulando...");
                continue;
            }

            // Formatar número (remover caracteres especiais)
            $numero = preg_replace('/[^0-9]/', '', $pessoa->whatsapp);
            if (strlen($numero) <= 11) {
                $numero = '55' . $numero; // Adiciona código do Brasil
            }

            $mensagem = "Olá {$pessoa->nome_completo}! 🙏\n\n"
                . "Este é um lembrete do *CEAN - Centro Espírita* para o seu atendimento agendado para *amanhã, "
                . $agendamento->data_agendada->format('d/m/Y') . "*.\n\n"
                . "Contamos com a sua presença! ✨\n\n"
                . "_Mensagem automática - CEAN_";

            try {
                // Formato genérico compatível com Evolution API / Z-API
                $response = Http::withHeaders([
                    'apikey' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("{$apiUrl}/message/sendText/{$instance}", [
                    'number' => $numero,
                    'text' => $mensagem,
                ]);

                if ($response->successful()) {
                    $agendamento->update(['notificacao_enviada' => true]);
                    $this->info("  ✅ {$pessoa->nome_completo} - Notificação enviada!");
                } else {
                    Log::error("Erro ao enviar WhatsApp para {$pessoa->nome_completo}", [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    $this->error("  ❌ {$pessoa->nome_completo} - Erro ao enviar.");
                }
            } catch (\Exception $e) {
                Log::error("Exceção ao enviar WhatsApp: {$e->getMessage()}");
                $this->error("  ❌ {$pessoa->nome_completo} - {$e->getMessage()}");
            }
        }

        $this->info('Processo concluído!');
        return self::SUCCESS;
    }
}
