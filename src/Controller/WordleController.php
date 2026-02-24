<?php

namespace App\Controller;

use App\Repository\RetoDiarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WordleController extends AbstractController
{
    #[Route('/api/wordle/estado', name: 'api_wordle_estado', methods: ['GET'])]
    public function estado(\App\Service\WordleService $wordleService): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Usuario no identificado'], 401);
        }

        $reto = $wordleService->obtenerRetoHoy();
        if (!$reto) {
            return $this->json(['error' => 'No hay reto hoy'], 404);
        }

        $progreso = $wordleService->obtenerProgreso($user, $reto);

        return $this->json([
            'historial' => $progreso->getHistorial(),
            'completado' => $progreso->isCompletado(),
            'intentosRestantes' => 6 - count($progreso->getHistorial())
        ]);
    }

    #[Route('/api/wordle/comprobar', name: 'api_wordle_comprobar', methods: ['POST'])]
    public function comprobar(Request $request, \App\Service\WordleService $wordleService): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Debes iniciar sesión para jugar'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $intento = strtoupper($data['intento'] ?? '');

        if (strlen($intento) !== 5) {
            return $this->json(['error' => 'La palabra debe tener 5 letras'], 400);
        }

        if (!$wordleService->esPalabraValida($intento)) {
            return $this->json(['error' => 'La palabra no existe en el diccionario'], 400);
        }

        // Obtenemos el reto de hoy
        $reto = $wordleService->obtenerRetoHoy();
        if (!$reto) {
            return $this->json(['error' => 'No hay palabras disponibles para jugar hoy.'], 404);
        }

        // Cargamos progreso del usuario
        $progreso = $wordleService->obtenerProgreso($user, $reto);
        if ($progreso->isCompletado()) {
            return $this->json(['error' => 'Ya has completado el reto de hoy'], 403);
        }

        if (count($progreso->getHistorial()) >= 6) {
            return $this->json(['error' => 'No te quedan más intentos'], 403);
        }

        $solucion = strtoupper($reto->getPalabra()->getPalabra());
        $resultado = [];

        // Lógica de comparación
        $solucionArray = str_split($solucion);
        $intentoArray = str_split($intento);
        $estados = array_fill(0, 5, 'absent');
        $usadas = array_fill(0, 5, false);

        // Primera pasada: Verdes
        for ($i = 0; $i < 5; $i++) {
            if ($intentoArray[$i] === $solucionArray[$i]) {
                $estados[$i] = 'correct';
                $usadas[$i] = true;
            }
        }

        // Segunda pasada: Amarillos
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

        // Guardar progreso
        $nuevoHistorial = $progreso->getHistorial();
        $nuevoHistorial[] = $resultado;
        $progreso->setHistorial($nuevoHistorial);

        // Verificar si ganó o agotó intentos
        $gano = $intento === $solucion;
        if ($gano || count($nuevoHistorial) >= 6) {
            $progreso->setCompletado(true);
        }

        $wordleService->guardarProgreso($progreso);

        return $this->json($resultado);
    }
}
