<?php

namespace APProjet4\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class TicketType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('lastname', TextType::class, [
                    'label' => 'Nom :',
                    'constraints' => [
                        new Lenght([
                            'min' => 2,
                            'max' => 40,
                            'minMessage' => 'Votre nom doit comporter au moins 2 lettres',
                            'maxMessage' => 'Votre nom doit comporter moins de 40 lettre'
                                ]),
                        new NotBlank([
                            'message' => 'Ce champ ne peut pas être vide.',
                                ])
                    ]
                ])
                ->add('firstname', TextType::class, [
                    'label' => 'Prénom :',
                    'constraints' => [
                        new Lenght([
                            'min' => 2,
                            'max' => 40,
                            'minMessage' => 'Votre prénom doit comporter au moins 2 lettres',
                            'maxMessage' => 'Votre prénom doit comporter moins de 40 lettre'
                                ]),
                        new NotBlank([
                            'message' => 'Ce champ ne peut pas être vide.',
                                ])
                    ]
                ])
                ->add('dateOfBirth', BirthdayType::class, [
                    'label' => 'Date de naissance : ',
                    'placeholder' => [
                        'day' => 'Jour',
                        'month' => 'Mois',
                        'year' => 'Année'
                    ],
                    'contraints' => [
                        newLessThan([
                            'value' => 'today',
                            'message' => 'Veuillez vérifier la date de naissance'
                        ]),
                        //Validator
                        new isTooOld([]),
                    ]
                ])
                ->add('country', CountryType::class, [
                    'label' => 'Pays :',
                    'label_attr' => ['class' => 'label_block'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Ce champ ne peut pas être vide.'
                                ]),
                        new NotNull([
                            'message' => 'Ce champ ne peut pas être vide.'
                                ]),
                    ]
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
