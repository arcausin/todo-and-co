<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre pour votre tâche.'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères.',
                        'max' => 255,
                        'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères.'
                    ])
                ],
                'required' => true,
            ])
            ->add('content', null, [
                'label' => 'Contenu',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un contenu pour votre tâche.'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le contenu doit contenir au moins {{ limit }} caractères.'
                    ])
                ],
                'required' => true,
            ])
            ->add('is_done', null, [
                'label' => 'Tâche terminée ?',
                'required' => false,
            ])
            //->add('created_at')
            //->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
