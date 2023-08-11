<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            // ->add('membre')
           
            // ->add('vehicule')
            ->add('date_heure_depart', DateType::class, [
                'widget' => 'single_text',])
            ->add('date_heure_fin', DateType::class, [
                'widget' => 'single_text',])
            // ->add('prix_total', MoneyType::class, [
            //     'divisor' => 100000,
            // ]);
            // ->add('date_enregistrement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
