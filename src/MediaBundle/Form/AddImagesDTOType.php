<?php

namespace MediaBundle\Form;


use MediaBundle\DTO\AddImagesDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddImagesDTOType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('images', FileType::class, [
                'label' => 'Выберите файлы',
                'multiple' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить изменения'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AddImagesDTO::class
        ));
    }
}