<?php

namespace App\Service;

use App\Entity\Palabra;
use App\Entity\RetoDiario;
use App\Repository\RetoDiarioRepository;
use Doctrine\ORM\EntityManagerInterface;

class WordleService
{
    private $entityManager;
    private $retoRepository;

    public function __construct(EntityManagerInterface $entityManager, RetoDiarioRepository $retoRepository)
    {
        $this->entityManager = $entityManager;
        $this->retoRepository = $retoRepository;
    }

    /**
     * Obtiene el reto de hoy. Si no existe, lo crea automáticamente.
     */
    public function obtenerRetoHoy(): ?RetoDiario
    {
        $hoy = new \DateTime('today');
        $reto = $this->retoRepository->findOneBy(['fecha' => $hoy]);

        if (!$reto) {
            $reto = $this->crearRetoParaFecha($hoy);
        }

        return $reto;
    }

    /**
     * Crea un reto para una fecha específica seleccionando una palabra al azar.
     */
    public function crearRetoParaFecha(\DateTime $fecha): ?RetoDiario
    {
        // 1. Obtener todas las palabras de 5 letras
        $palabras = $this->entityManager->getRepository(Palabra::class)
            ->createQueryBuilder('p')
            ->where('LENGTH(p.palabra) = 5')
            ->getQuery()
            ->getResult();

        if (empty($palabras)) {
            return null;
        }

        // 2. Seleccionar una al azar
        /** @var Palabra $palabraAleatoria */
        $palabraAleatoria = $palabras[array_rand($palabras)];

        // 3. Guardar el nuevo reto
        $reto = new RetoDiario();
        $reto->setPalabra($palabraAleatoria);
        $reto->setFecha($fecha);

        $this->entityManager->persist($reto);
        $this->entityManager->flush();

        return $reto;
    }
}
