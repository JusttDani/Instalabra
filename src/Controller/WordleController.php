<?php

namespace App\Controller;

use App\Repository\RetoDiarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WordleController extends AbstractController
{
    #[Route('/api/wordle/comprobar', name: 'api_wordle_comprobar', methods: ['POST'])]
    public function comprobar(Request $request, \App\Service\WordleService $wordleService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $intento = strtoupper($data['intento'] ?? '');

        if (strlen($intento) !== 5) {
            return $this->json(['error' => 'La palabra debe tener 5 letras'], 400);
        }

        // Obtenemos el reto de hoy (se crea automáticamente si no existe)
        $reto = $wordleService->obtenerRetoHoy();

        if (!$reto) {
            return $this->json(['error' => 'No hay palabras disponibles para jugar hoy. Contacta con el administrador.'], 404);
        }

        $solucion = strtoupper($reto->getPalabra()->getPalabra());
        $resultado = [];

        // Lógica de comparación mejorada para manejar duplicados correctamente (como el Wordle real)
        $solucionArray = str_split($solucion);
        $intentoArray = str_split($intento);
        $estados = array_fill(0, 5, 'absent');
        $usadas = array_fill(0, 5, false);

        // Primera pasada: Verdes (Correct)
        for ($i = 0; $i < 5; $i++) {
            if ($intentoArray[$i] === $solucionArray[$i]) {
                $estados[$i] = 'correct';
                $usadas[$i] = true;
            }
        }

        // Segunda pasada: Amarillos (Present)
        for ($i = 0; $i < 5; $i++) {
            if ($estados[$i] === 'correct')
                continue;

            for ($j = 0; $j < 5; $j++) {
                if (!$usadas[$j] && $intentoArray[$i] === $solucionArray[$j]) {
                    $estados[$i] = 'present';
                    $usadas[$j] = true;
                    break;
                }
            }
        }

        for ($i = 0; $i < 5; $i++) {
            $resultado[] = ['letra' => $intentoArray[$i], 'estado' => $estados[$i]];
        }

        return $this->json($resultado);
    }
}
