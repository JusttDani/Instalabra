<?php

namespace App\Command;

use App\Entity\Palabra;
use App\Entity\RetoDiario;
use App\Service\WordleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:seleccionar-reto', description: 'Selecciona la palabra del día para el Wordle')]
class SeleccionarRetoCommand extends Command
{
    private $wordleService;

    public function __construct(WordleService $wordleService)
    {
        parent::__construct();
        $this->wordleService = $wordleService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $hoy = new \DateTime('today');

        $reto = $this->wordleService->obtenerRetoHoy();

        if (!$reto) {
            $io->error('No se pudieron encontrar palabras de 5 letras para crear el reto.');
            return Command::FAILURE;
        }

        $io->success('¡Palabra del día activa: ' . $reto->getPalabra()->getPalabra());

        return Command::SUCCESS;
    }
}