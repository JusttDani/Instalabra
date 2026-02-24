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
    private $intentoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        RetoDiarioRepository $retoRepository,
        \App\Repository\IntentoWordleRepository $intentoRepository
    ) {
        $this->entityManager = $entityManager;
        $this->retoRepository = $retoRepository;
        $this->intentoRepository = $intentoRepository;
    }

    /**
     * Obtiene o crea el registro de progreso de un usuario para un reto.
     */
    public function obtenerProgreso(\App\Entity\Usuario $usuario, \App\Entity\RetoDiario $reto): \App\Entity\IntentoWordle
    {
        $intento = $this->intentoRepository->findOneBy([
            'usuario' => $usuario,
            'reto' => $reto
        ]);

        if (!$intento) {
            $intento = new \App\Entity\IntentoWordle();
            $intento->setUsuario($usuario);
            $intento->setReto($reto);
            $intento->setFecha(new \DateTime());
            $intento->setHistorial([]);
            $intento->setCompletado(false);

            $this->entityManager->persist($intento);
            $this->entityManager->flush();
        }

        return $intento;
    }

    public function guardarProgreso(\App\Entity\IntentoWordle $intento): void
    {
        $this->entityManager->persist($intento);
        $this->entityManager->flush();
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

    /**
     * Comprueba si una palabra existe en el diccionario español descargado.
     */
    public function esPalabraValida(string $intento): bool
    {
        $filePath = __DIR__ . '/../../data/palabras_5.txt';

        // Si el archivo no existe por alguna razón, usamos un fallback básico
        if (!file_exists($filePath)) {
            return true;
        }

        $intentoUpper = mb_strtoupper($intento, 'UTF-8');
        $replacements = [
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'Ü' => 'U'
        ];
        $intentoUpper = strtr($intentoUpper, $replacements);

        $words = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Búsqueda binaria o in_array, in_array es suficientemente rápido para 4700 elementos
        return in_array($intentoUpper, $words, true);
    }
}
