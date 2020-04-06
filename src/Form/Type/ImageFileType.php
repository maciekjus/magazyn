<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\File;

class ImageFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', FileType::class, [
            	'label' => 'Plik obrazu (.jpg)',
            	'mapped' => false,
            	'required' => false,
                /*
            	'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/pjpeg',
                        ],
                        'mimeTypesMessage' => 'Proszę podać plik obrazu w formie jpg.',
                    ])
                ],
                */
            ])
            ->add('description', TextAreaType::class, ['label' => 'Opis'])
            ->add('time', HiddenType::class)
            ->add('save', SubmitType::class, ['label' => 'Zapisz'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }

}
