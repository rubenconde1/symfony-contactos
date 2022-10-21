<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductoController extends AbstractController
{
// Array de productos (Eliminar con la BDD creada)
    private $productos = [
        1 => ['nombre' => 'Maqueta 1', 'marca' => 'Tamiya', 'precio' => '19,90€'],
        2 => ['nombre' => 'Maqueta 2', 'marca' => 'Revell', 'precio' => '15,70€'],
        3 => ['nombre' => 'Maqueta 3', 'marca' => 'Revell', 'precio' => '87,30€']
    ];

    #[Route('/producto/{codigo<\d+>?1}', name:'ficha_producto')]
    public function ficha($codigo): Response{
        $resultado = ($this->productos[$codigo] ?? null);

            return $this->render('ficha_producto.html.twig', [
                'producto' => $resultado
            ]);
    }

    #[Route('/producto/buscar/{texto}', name:'buscar_producto')]
    public function buscar($texto): Response{
        $resultados = array_filter($this->productos,
        function ($producto) use ($texto) {
            return strpos($producto['nombre'], $texto) !== FALSE;
        });

        return $this->render('lista_productos.html.twig', [
            'productos' => $resultados
        ]);
    }
}
