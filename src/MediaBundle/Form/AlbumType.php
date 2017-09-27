<?php
/**
 * Created by PhpStorm.
 * User: ktlle
 * Date: 9/26/17
 * Time: 11:56 PM
 */

namespace MediaBundle\Form;


use MediaBundle\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlbumType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Название'
            ])
            ->add('description', TextType::class, [
                'label' => 'Описание'
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Виден пользователям',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить изменения'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Album::class
        ));
    }
}