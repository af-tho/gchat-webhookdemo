<?php

namespace App\Command;

use App\Service\GoogleChatWebhookHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:chat-push',
    description: 'Sends a message into Google Chat Webhook.',
    hidden: false,
    aliases: ['app:chat-push']
)]
/**
 * Class ChatPushCommand
 * @package App\Command
 * @see https://addons.gsuite.google.com/uikit/builder?hl=de
 * @see https://developers.google.com/chat/api/guides/message-formats/cards
 */
class ChatPushCommand extends Command
{
    private GoogleChatWebhookHelper $googleChatWebhookHelper;
    protected static $defaultName = 'chat:push';

    public function __construct(GoogleChatWebhookHelper $googleChatWebhookHelper)
    {
        parent::__construct();
        $this->googleChatWebhookHelper = $googleChatWebhookHelper;

    }

    protected function configure(): void
    {
        $this
            ->setDescription('Sends a message into Google Chat Webhook')
            ->addArgument('text', InputArgument::REQUIRED, 'The message text to send')
            ->setHelp('This command allows you to send a message to Google Chat Webhook');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $messageText = $input->getArgument('text');
        $webhookUrl = $this->googleChatWebhookHelper->getWebhookUrl();
        if (empty($webhookUrl)) {
            $output->writeln('<error>Webhook URL is not set.</error>');
            return Command::FAILURE;
        }
        
        $message = [
            #'text' => $messageText,
            'cards' =>  [
                [       
                    "header" => [
                        "title" => "*CARD header title*",
                        "subtitle" => "CARD header subtitle",
                        "imageUrl" => "https://netz-giraffe.de/wp-content/uploads/2023/05/netzgiraffe-im-buro.jpg"
                    ],
                    "sections" => [
                        [
                            "widgets" => [
                                [
                                    "keyValue"=> [
                                        "topLabel" => "Status",
                                        "content" => "✅ Abgeschlossen"
                                    ],
                                ],
                                [
                                    "textParagraph" => [
                                        "text" => $messageText
                                    ],
                                ],
                                [
                                    "buttons" => [
                                        "textButton"=> [
                                            "text" => "OPEN",
                                            "onClick"=> [
                                                "openLink"=> [
                                                    "url" => "https://media.mercedes-benz.com/"
                                                ]
                                            ]
                                        ],
                                        "textButton"=> [
                                            "text" => "DO NOT OPEN",
                                            "onClick"=> [
                                                "openLink"=> [
                                                    "url" => "https://media.mercedes-benz.com/"
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "cardActions"=> [
                        "actionLabel"=> "card ACTION",
                        "onClick"=> [
                            "action"=> [
                                "actionMethodName" => "ACTION_METHOD_NAME",
                                "parameters" => [
                                    "key" => "value"
                                ]
                                ],
                            "openLink"=> [
                                "url" => "https://media.mercedes-benz.com/"
                            ]
                        ]
                    ]
                ]
            ]
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
