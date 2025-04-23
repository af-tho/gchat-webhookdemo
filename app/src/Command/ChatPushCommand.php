<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:chat-push',
    description: 'Sends a message into Google Chat Webhook.',
    hidden: false,
    aliases: ['app:chat-push']
)]
class ChatPushCommand extends Command
{
    protected static $defaultName = 'chat:push';

    protected function configure(): void
    {
        $this
            ->setDescription('Sends a message into Google Chat Webhook');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $webhookUrl = 'https://chat.googleapis.com/v1/spaces/AAQA7yLNoiY/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=9Xj_Pz0ioQBJWIzHRhgxWDnuWCgREHo5a5YY5mWiP0k';
        $message = [
            'text' => 'Hey from my custom Symfony Console 🎉 App, now testing a link https://symfony.com/doc/current/console.html#console_registering-the-command',
        ];

        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status >= 200 && $status < 300) {
            $output->writeln('<info>Message successfully send.</info>');
            return Command::SUCCESS;
        } else {
            $output->writeln('<error>Error during sending message: ' . $response . '</error>');
            return Command::FAILURE;
        }
    }
}
