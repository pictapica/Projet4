<?php

namespace APProjet4\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('lastname',    TextType::class, [
                        'label' => 'Nom :',
                        'label_attr' => ['class' => 'label_block'],
                ])
            ->add('firstname',   TextType::class, [
                        'label' => 'Prénom :',
                        'label_attr' => ['class' => 'label_block'],
                ])
            ->add('dateOfBirth', BirthdayType::class, [
                        'label' => 'Date de naissance : ',
                        'label_attr' => ['class' => 'label_block'],
                        'placeholder' => [
                            'day' => 'Jour',
                            'month' => 'Mois',
                            'year' => 'Année'
                        ]
                ])
            ->add('country',     CountryType::class, [
                        'label' => 'Pays :',
                        'label_attr' => ['class' => 'label_block'],
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'APProjet4\BookingBundle\Entity\Ticket'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'approjet4_bookingbundle_ticket';
    }

}
