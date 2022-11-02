<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Proveedor;


class ProductoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nombre', TextType::class)
        ->add('marca', TextType::class)
        ->add('precio', NumberType::class, array('scale' => 2))
        ->add('proveedor', EntityType::class, array(
            'class' => Proveedor::class,
            'choice_label' => 'nombre',))
        ->add('save', SubmitType::class, array('label' => 'Enviar'));
    }
}