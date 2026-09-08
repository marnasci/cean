<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CriarAdmin extends Command
{
    protected $signature = 'cean:criar-admin {--email=admin@cean.com} {--senha=cean2026}';
    protected $description = 'Cria ou reseta o usuário administrador';

    public function handle(): int
    {
        $email = $this->option('email');
        $senha = $this->option('senha');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'password' => bcrypt($senha),
                'role' => 'admin',
            ]);
            $this->info("✅ Senha do admin ({$email}) atualizada com sucesso!");
        } else {
            User::create([
                'name' => 'Administrador',
                'email' => $email,
                'password' => bcrypt($senha),
                'role' => 'admin',
            ]);
            $this->info("✅ Admin criado: {$email}");
        }

        $this->info("   Senha: {$senha}");
        return self::SUCCESS;
    }
}
