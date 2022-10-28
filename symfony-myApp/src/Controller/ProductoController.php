<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Proveedor;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductoController extends AbstractController
{
    // Array de productos (Eliminar con la BDD creada)
    private $productos = [
        1 => ['nombre' => 'Maqueta 1', 'marca' => 'Tamiya', 'precio' => 19.90],
        2 => ['nombre' => 'Maqueta 2', 'marca' => 'Revell', 'precio' => 15.70],
        3 => ['nombre' => 'Maqueta 3', 'marca' => 'Revell', 'precio' => 87.30]
    ];

    #[Route('/producto/nuevo', name:'nuevo_producto')]
    public function nuevo(ManagerRegistry $doctrine, Request $request){
        $producto = new Producto();

        $formulario = $this->createFormBuilder($producto)
            ->add('nombre', TextType::class)
            ->add('marca', TextType::class)
            ->add('precio', NumberType::class, array('scale' => 2))
            ->add('proveedor', EntityType::class, array(
                'class' => Proveedor::class,
                'choice_label' => 'nombre',))
            ->add('save', SubmitType::class, array('label' => 'Enviar'))
            ->getForm();
            $formulario->handleRequest($request);

            if ($formulario->isSubmitted() && $formulario->isValid()) {
                $producto = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($producto);
                $entityManager->flush();
                return $this->redirectToRoute('ficha_producto', ['codigo' => $producto->getId()]);
            }
        return $this->render('nuevo.html.twig', array(
            'formulario' => $formulario->createView()
        ));
    }

    #[Route('/producto/editar/{codigo}', name:'editar_producto', requirements:['codigo'=>'\d+'])]
    public function editar(ManagerRegistry $doctrine, Request $request, $codigo){
        $repositorio = $doctrine->getRepository(Producto::class);
        $producto = $repositorio->find($codigo);

        $formulario = $this->createFormBuilder($producto)
            ->add('nombre', TextType::class)
            ->add('marca', TextType::class)
            ->add('precio', NumberType::class, array('scale' => 2))
            ->add('proveedor', EntityType::class, array(
                'class' => Proveedor::class,
                'choice_label' => 'nombre',))
            ->add('save', SubmitType::class, array('label' => 'Enviar'))
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {
            $producto = $formulario->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($producto);
            $entityManager->flush();
        }
        return $this->render('editar.html.twig', array(
            'formulario' => $formulario->createView()
        ));
    }
    #[Route('/producto/insertar', name:'insertar_producto')]
    public function insertar(ManagerRegistry $doctrine) {
        $entityManager = $doctrine->getManager();
        foreach ($this->productos as $p) {
            $producto = new Producto();
            $producto->setNombre($p['nombre']);
            $producto->setMarca($p['marca']);
            $producto->setPrecio($p['precio']);
            $entityManager->persist($producto);
        }
        
        try {
            $entityManager->flush();
            return new Response("Productos insertados");
        } catch (\Exception $e) {
            return new Response("Error insertando objetos");
        }
    }

    #[Route('producto/{codigo<\d+>?1}', name:'ficha_producto')]
    public function ficha(ManagerRegistry $doctrine, $codigo): Response{
        $repositorio = $doctrine->getRepository(Producto::class);
        $producto = $repositorio->find($codigo);

        return $this->render('ficha_producto.html.twig', [
            'producto' => $producto
        ]);
    }

    #[Route('/producto/buscar/{texto}', name:'buscar_producto')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response{
        $repositorio = $doctrine->getRepository(Producto::class);
        $productos = $repositorio->findByName($texto);

        return $this->render('lista_producto.html.twig', [
            'productos' => $productos
        ]);
    }

    #[Route('/producto/update/{id}/{nombre}', name:'modificar_contacto')]
    public function update(ManagerRegistry $doctrine, $id, $nombre): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Producto::class);
        $producto = $repositorio->find($id);
        if ($producto) {
            $producto->setNombre($nombre);
            try {
                $entityManager->flush();
                return $this->render('ficha_producto.html.twig', [
                    'producto' => $producto
                ]);
            } catch (\Exception $e) {
                return new Response("Error insertando objetos");
            }
        } else {
            return $this->render('ficha_producto.html.twig', [
                'producto' => null
            ]);
        }
    }

    #[Route('/producto/delete/{id}', name:'eliminar_contenido')]
    public function delete(ManagerRegistry $doctrine, $id): Response {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Producto::class);
        $producto = $repositorio->find($id);
        if ($producto) {
            try {
                $entityManager->remove($producto);
                $entityManager->flush();
                return new Response("Producto eliminado");
            } catch (\Exception $e) {
                return new Response("Error eliminando objeto");
            }
        } else {
            return $this->render('ficha_producto.html.twig', [
                'producto' => null
            ]);
        }
    }

    #[Route('/producto/insertarConProveedor', name:'insertar_con_proveedor_producto')]
    public function insertarConProveedor(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();

        $proveedor = new Proveedor();

        $proveedor->setNombre('Revell');
        $proveedor->setEmail('revell@revell.com');
        
        $producto = new Producto();

        $producto->setNombre('Prueba inserción con proveedor');
        $producto->setMarca('Bandai');
        $producto->setPrecio(34.65);
        $producto->setProveedor($proveedor);

        $entityManager->persist($proveedor);
        $entityManager->persist($producto);

        $entityManager->flush();
        return $this->render('ficha_producto.html.twig', [
            'producto' => $producto
        ]);
    }

    #[Route('/producto/insertarSinProveedor', name:'insertar_sin_proveedor_producto')]
    public function insertarSinProveedor(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Proveedor::class);

        $proveedor = $repositorio->findOneBy(['nombre' => 'Revell']);

        $producto = new Producto();

        $producto->setNombre('Inserción de prueba sin producto');
        $producto->setMarca('Tamiya');
        $producto->setPrecio(15.49);
        $producto->setProveedor($proveedor);

        $entityManager->persist($producto);

        $entityManager->flush();
        return $this->render('ficha_producto.html.twig', [
            'producto' => $producto
        ]);
    }
}