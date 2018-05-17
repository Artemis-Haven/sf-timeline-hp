<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Server\WebSocketServer;
use App\Server\Chat;
use App\Server\GameActions;

class WebSocketServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:websocket-server')
            ->setDescription('Start WebSocket server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = IoServer::factory(
            new HttpServer(new WsServer(new WebSocketServer(new Chat($this->getContainer()), new GameActions($this->getContainer())))),
            8080,
            '127.0.0.1'
        );
        $server->run();
    }
}